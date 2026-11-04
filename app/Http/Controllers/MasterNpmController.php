<?php

namespace App\Http\Controllers;

use App\Jobs\GenerateMasterNpmBatchJob;
use App\Models\Employee;
use App\Models\MasterNpm;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MasterNpmController extends Controller
{
    /**
     * Tampilkan daftar Master NPM PDM Kota Yogyakarta.
     */
    public function index(Request $request)
    {
        $query = MasterNpm::with(['allocatedUnit', 'employee']);

        if ($request->filled('status')) {
            if ($request->status === 'used') {
                $query->where('is_used', true);
            } elseif ($request->status === 'available') {
                $query->where('is_used', false);
            }
        }

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('npm', 'like', "%{$search}%")
                  ->orWhere('registered_name', 'like', "%{$search}%")
                  ->orWhere('nbm', 'like', "%{$search}%");
            });
        }

        $npms = $query->orderBy('npm', 'asc')->paginate(25)->withQueryString();
        $totalCount = MasterNpm::count();
        $usedCount = MasterNpm::where('is_used', true)->count();
        $availableCount = $totalCount - $usedCount;

        return view('master_npms.index', compact('npms', 'totalCount', 'usedCount', 'availableCount'));
    }

    /**
     * Form entri / generate batch range Master NPM.
     */
    public function create()
    {
        $user = Auth::user();
        if (!$user->canManageMasterNpm()) {
            abort(403, 'Akses ditolak: Pengelolaan dan penambahan Bank Data Master NPM hanya dapat dilakukan oleh Admin Utama atau Admin Yayasan.');
        }

        $units = Unit::where('is_active', true)->orderBy('name', 'asc')->get();
        return view('master_npms.create', compact('units'));
    }

    /**
     * Simpan NPM manual atau batch range nomor.
     * Menerapkan antrian background job (Queue) jika batch > 20 nomor untuk mencegah server freeze / database lock.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        if (!$user->canManageMasterNpm()) {
            abort(403, 'Akses ditolak: Pembuatan nomor NPM hanya dapat dilakukan oleh Admin Utama atau Admin Yayasan.');
        }

        $mode = $request->input('mode', 'single');

        if ($mode === 'batch') {
            $request->validate([
                'start_number' => 'required|integer|min:1',
                'end_number' => 'required|integer|gte:start_number|max:999999',
                'prefix' => 'nullable|string|max:10',
                'pad_length' => 'required|integer|min:1|max:10',
                'allocated_unit_id' => 'nullable|exists:units,id',
            ]);

            $start = (int) $request->start_number;
            $end = (int) $request->end_number;
            $prefix = (string) $request->input('prefix', '');
            $pad = (int) $request->pad_length;
            $allocatedUnitId = $request->filled('allocated_unit_id') ? (int) $request->allocated_unit_id : null;
            $totalNumbers = ($end - $start + 1);

            // Jika batch lebih dari 20 nomor, masukkan ke Antrian Latar Belakang (Queue)
            // agar proses admin lain tidak terganggu dan terhindar dari lock database.
            if ($totalNumbers > 20) {
                GenerateMasterNpmBatchJob::dispatch(
                    $start,
                    $end,
                    $prefix,
                    $pad,
                    $allocatedUnitId,
                    'Dibuat melalui Antrian Sistem (Queue Batch Job)'
                );

                return redirect()->route('master-npms.index')
                    ->with('success', "Proses pembuatan {$totalNumbers} nomor NPM telah dimasukkan ke dalam antrian sistem (Queue) dan sedang diproses di latar belakang.");
            }

            // Jika batch kecil (<= 20), eksekusi langsung dalam transaksi aman
            $inserted = DB::transaction(function () use ($start, $end, $prefix, $pad, $allocatedUnitId) {
                $rows = [];
                $now = now();
                for ($i = $start; $i <= $end; $i++) {
                    $npmFormatted = $prefix . str_pad((string) $i, $pad, '0', STR_PAD_LEFT);
                    $rows[] = [
                        'npm' => $npmFormatted,
                        'is_used' => false,
                        'allocated_unit_id' => $allocatedUnitId,
                        'notes' => 'Generated via Direct Batch (<= 20)',
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }

                $existingNpms = MasterNpm::whereIn('npm', array_column($rows, 'npm'))
                    ->pluck('npm')
                    ->all();
                $existingSet = array_flip($existingNpms);
                $toInsert = array_filter($rows, fn($r) => !isset($existingSet[$r['npm']]));

                if (!empty($toInsert)) {
                    MasterNpm::insert($toInsert);
                    return count($toInsert);
                }
                return 0;
            }, 5);

            return redirect()->route('master-npms.index')
                ->with('success', "Berhasil menambahkan {$inserted} nomor NPM baru ke Bank Data Master.");
        }

        // Single entry dalam transaksi aman dengan retry otomatis
        $validated = $request->validate([
            'npm' => 'required|string|max:50|unique:master_npms,npm',
            'registered_name' => 'nullable|string|max:150',
            'nbm' => 'nullable|string|max:50',
            'allocated_unit_id' => 'nullable|exists:units,id',
            'notes' => 'nullable|string',
        ]);

        DB::transaction(function () use ($validated) {
            MasterNpm::create($validated);
        }, 5);

        return redirect()->route('master-npms.index')
            ->with('success', "Nomor NPM {$validated['npm']} berhasil ditambahkan ke Bank Data.");
    }

    /**
     * Endpoint API / JSON untuk validasi instan NPM oleh TU Sekolah.
     */
    public function check(Request $request)
    {
        $npm = trim((string) $request->query('npm', ''));
        $employeeId = $request->query('employee_id') ? (int) $request->query('employee_id') : null;

        if (empty($npm)) {
            return response()->json([
                'status' => 'empty',
                'message' => 'NPM belum diisi.',
            ]);
        }

        // Cek duplikasi di tabel employees
        $existingEmp = Employee::where('npm', $npm)
            ->when($employeeId, fn($q) => $q->where('id', '!=', $employeeId))
            ->with('unit')
            ->first();

        if ($existingEmp) {
            return response()->json([
                'status' => 'duplicate',
                'is_valid' => false,
                'message' => "NPM {$npm} sudah digunakan oleh {$existingEmp->full_name} ({$existingEmp->unit?->name}).",
            ]);
        }

        // Cek di Master NPM
        $result = MasterNpm::checkAvailability($npm, $employeeId);

        return response()->json([
            'status' => $result['is_valid'] ? ($result['exists_in_master'] ? 'valid' : 'warning') : 'invalid',
            'is_valid' => $result['is_valid'],
            'exists_in_master' => $result['exists_in_master'],
            'message' => $result['message'],
            'registered_name' => $result['registered_name'] ?? null,
        ]);
    }
}

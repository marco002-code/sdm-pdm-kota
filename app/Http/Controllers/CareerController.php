<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\PerformanceAppraisal;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CareerController extends Controller
{
    /**
     * Tampilkan Portofolio Prestasi Pegawai (Lokal, Nasional, Internasional).
     */
    public function achievements(Request $request)
    {
        $user = Auth::user();
        $query = Employee::whereNotNull('achievements')->where('achievements', '!=', '[]')->with('unit');

        if ($user->isUnitLevel() && $user->unit_id) {
            $query->where('unit_id', $user->unit_id);
        } elseif ($request->filled('unit_id')) {
            $query->where('unit_id', $request->unit_id);
        }

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('npm', 'like', "%{$search}%")
                  ->orWhere('achievements', 'like', "%{$search}%");
            });
        }

        $employeesWithAchievements = $query->orderBy('full_name', 'asc')->paginate(15)->withQueryString();
        $units = Unit::where('is_active', true)->orderBy('name', 'asc')->get();

        return view('career.achievements', compact('employeesWithAchievements', 'units'));
    }

    /**
     * Tampilkan Penilaian Kinerja (DP3 Pegawai & Penilaian Kinerja Kepala Sekolah / PKKS).
     * Dapat diakses oleh TU unit masing-masing (hanya menampilkan pegawai di unitnya), Kepsek, HR Pusat, dan Yayasan/Admin.
     */
    public function performance(Request $request)
    {
        $user = Auth::user();

        // Pegawai (guru/staf biasa) dilarang mengelola input penilaian kinerja
        if ($user->isPegawai()) {
            abort(403, 'Akses ditolak: Pegawai tidak memiliki izin untuk menginput atau mengelola penilaian kinerja.');
        }

        $type = $request->input('type', 'dp3'); // 'dp3' atau 'pkks'

        $query = PerformanceAppraisal::with(['employee', 'unit', 'evaluator']);

        // Isolasi Domain Unit Kerja: User unit-level (TU / Kepsek) hanya dapat melihat riwayat unitnya sendiri
        if ($user->isUnitLevel() && $user->unit_id) {
            $query->where('unit_id', $user->unit_id);
            $units = Unit::where('id', $user->unit_id)->get();
        } else {
            if ($request->filled('unit_id')) {
                $query->where('unit_id', $request->unit_id);
            }
            $units = Unit::where('is_active', true)->orderBy('name', 'asc')->get();
        }

        if ($type === 'pkks') {
            $query->where('appraisal_type', 'pkks');
        } else {
            $query->whereIn('appraisal_type', ['dp3', 'reguler']);
        }

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->whereHas('employee', function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('npm', 'like', "%{$search}%");
            });
        }

        $appraisals = $query->orderBy('appraisal_date', 'desc')->paginate(15)->withQueryString();

        // Data pegawai untuk form input cepat: TU hanya menampilkan seluruh pegawai di unitnya sendiri
        $employeesQuery = Employee::where('status', 'aktif');
        if ($user->isUnitLevel() && $user->unit_id) {
            $employeesQuery->where('unit_id', $user->unit_id);
        } elseif ($request->filled('unit_id')) {
            $employeesQuery->where('unit_id', $request->unit_id);
        }
        $employees = $employeesQuery->orderBy('full_name', 'asc')->get(['id', 'full_name', 'npm', 'unit_id', 'position']);

        return view('career.performance', compact('appraisals', 'units', 'employees', 'type'));
    }

    /**
     * Simpan input Nilai DP3 atau Penilaian Kinerja Kepala Sekolah (PKKS).
     * Dijalankan dalam transaksi atomik dengan pessimistic locking pada Employee untuk mencegah race condition JSON.
     */
    public function storePerformance(Request $request)
    {
        $user = Auth::user();

        if ($user->isPegawai()) {
            abort(403, 'Akses ditolak: Pegawai tidak memiliki izin untuk menginput penilaian kinerja.');
        }

        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'appraisal_type' => 'required|in:dp3,pkks,reguler',
            'period' => 'required|string|max:50',
            'appraisal_date' => 'required|date',
            'final_score' => 'required|numeric|min:0|max:100',
            'grade' => 'required|in:A,B,C,D,E',
            'notes' => 'nullable|string',
            'strengths' => 'nullable|string',
            'improvements_needed' => 'nullable|string',
        ]);

        $employee = DB::transaction(function () use ($validated, $user) {
            $emp = Employee::lockForUpdate()->findOrFail($validated['employee_id']);

            // Validasi Isolasi Domain: User unit-level (TU / Kepsek) hanya berhak menginput pegawai pada unitnya sendiri
            if ($user->isUnitLevel() && $user->unit_id && (int) $emp->unit_id !== (int) $user->unit_id) {
                abort(403, 'Akses ditolak: Anda hanya berhak menginput penilaian untuk pegawai pada unit kerja Anda sendiri.');
            }

            $validated['unit_id'] = $emp->unit_id;
            $validated['evaluator_id'] = $user->id;
            $validated['status'] = 'finalized';

            PerformanceAppraisal::create($validated);

            $targetYear = (int) substr($validated['period'], 0, 4) ?: (int) date('Y');

            // Sinkronkan juga ke array dp3_scores atau pkks_scores di tabel employee secara terproteksi
            if ($validated['appraisal_type'] === 'dp3') {
                $dp3List = $emp->dp3_scores ?? [];
                // Update jika sudah ada untuk tahun tersebut, atau append jika baru
                $found = false;
                foreach ($dp3List as $idx => $d) {
                    if (($d['year'] ?? null) == $targetYear) {
                        $dp3List[$idx]['score'] = (float) $validated['final_score'];
                        $dp3List[$idx]['predicate'] = $validated['grade'] === 'A' ? 'Amat Baik' : ($validated['grade'] === 'B' ? 'Baik' : 'Cukup');
                        $dp3List[$idx]['notes'] = $validated['notes'];
                        $found = true;
                        break;
                    }
                }
                if (!$found) {
                    $dp3List[] = [
                        'year' => $targetYear,
                        'score' => (float) $validated['final_score'],
                        'predicate' => $validated['grade'] === 'A' ? 'Amat Baik' : ($validated['grade'] === 'B' ? 'Baik' : 'Cukup'),
                        'notes' => $validated['notes'],
                    ];
                }
                $emp->update(['dp3_scores' => array_values($dp3List)]);
            } elseif ($validated['appraisal_type'] === 'pkks') {
                $pkksList = $emp->pkks_scores ?? [];
                $found = false;
                foreach ($pkksList as $idx => $d) {
                    if (($d['year'] ?? null) == $targetYear) {
                        $pkksList[$idx]['score'] = (float) $validated['final_score'];
                        $pkksList[$idx]['grade'] = $validated['grade'];
                        $pkksList[$idx]['notes'] = $validated['notes'];
                        $found = true;
                        break;
                    }
                }
                if (!$found) {
                    $pkksList[] = [
                        'year' => $targetYear,
                        'score' => (float) $validated['final_score'],
                        'grade' => $validated['grade'],
                        'notes' => $validated['notes'],
                    ];
                }
                $emp->update(['pkks_scores' => array_values($pkksList)]);
            }

            return $emp;
        }, 5);

        $typeName = $validated['appraisal_type'] === 'pkks' ? 'Penilaian Kinerja Kepala Sekolah (PKKS)' : 'Nilai DP3 Pegawai';

        return redirect()->route('career.performance', ['type' => $validated['appraisal_type']])
            ->with('success', "{$typeName} untuk {$employee->full_name} berhasil disimpan.");
    }
}

<?php

namespace App\Http\Controllers;

use App\Jobs\UpdateUnitAuditMetricsJob;
use App\Models\Employee;
use App\Models\EmployeeMutation;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class EmployeeMutationController extends Controller
{
    /**
     * Tampilkan daftar riwayat mutasi pegawai.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        if (! $user->isYayasanLevel()) {
            abort(403, 'Akses ditolak: Modul Riwayat Mutasi hanya dapat diakses oleh Admin dan Pengurus Yayasan/Majelis.');
        }

        $query = EmployeeMutation::with(['employee', 'originUnit', 'destinationUnit', 'approver']);

        if ($user->isUnitLevel() && $user->unit_id) {
            $query->where(function ($q) use ($user) {
                $q->where('origin_unit_id', $user->unit_id)
                  ->orWhere('destination_unit_id', $user->unit_id);
            });
        }

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->whereHas('employee', function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('npm', 'like', "%{$search}%")
                  ->orWhere('nbm', 'like', "%{$search}%");
            });
        }

        $mutations = $query->orderBy('effective_date', 'desc')->paginate(15)->withQueryString();

        return view('mutations.index', compact('mutations'));
    }

    /**
     * Formulir mutasi baru (Hanya PDM/Yayasan).
     */
    public function create(Request $request)
    {
        $user = Auth::user();
        if (!$user->canManageMutations()) {
            abort(403, 'Akses ditolak: Pencatatan mutasi pegawai lintas unit hanya dapat dilakukan oleh Admin Utama atau Admin Yayasan.');
        }

        $selectedEmployee = null;
        if ($request->filled('employee_id')) {
            $selectedEmployee = Employee::with('unit')->findOrFail($request->employee_id);
        }

        $employees = Employee::where('status', 'aktif')->orderBy('full_name', 'asc')->get(['id', 'full_name', 'npm', 'unit_id', 'position']);
        $units = Unit::where('is_active', true)->orderBy('name', 'asc')->get();

        return view('mutations.create', compact('employees', 'units', 'selectedEmployee'));
    }

    /**
     * Simpan data mutasi dan sinkronkan unit pegawai secara atomik dengan antrian audit latar belakang.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        if (!$user->canManageMutations()) {
            abort(403, 'Akses ditolak: Pencatatan mutasi pegawai lintas unit hanya dapat dilakukan oleh Admin Utama atau Admin Yayasan.');
        }

        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'destination_unit_id' => 'required|exists:units,id',
            'destination_position' => 'required|string|max:100',
            'effective_date' => 'required|date',
            'sk_number' => 'nullable|string|max:100',
            'sk_date' => 'nullable|date',
            'reason' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        [$employee, $originUnitId] = DB::transaction(function () use ($validated) {
            $emp = Employee::lockForUpdate()->findOrFail($validated['employee_id']);
            $origId = $emp->unit_id;

            if ($origId == $validated['destination_unit_id']) {
                throw ValidationException::withMessages([
                    'destination_unit_id' => 'Unit tujuan harus berbeda dengan unit asal saat ini.',
                ]);
            }

            EmployeeMutation::create([
                'employee_id' => $emp->id,
                'origin_unit_id' => $origId,
                'destination_unit_id' => $validated['destination_unit_id'],
                'origin_position' => $emp->position ?? '-',
                'destination_position' => $validated['destination_position'],
                'effective_date' => $validated['effective_date'],
                'sk_number' => $validated['sk_number'],
                'sk_date' => $validated['sk_date'],
                'reason' => $validated['reason'],
                'status' => 'disetujui',
                'approved_by' => Auth::id(),
                'notes' => $validated['notes'],
            ]);

            // Perbarui unit & jabatan pegawai saat ini
            $emp->update([
                'unit_id' => $validated['destination_unit_id'],
                'position' => $validated['destination_position'],
            ]);

            return [$emp, $origId];
        }, 5);

        // Rekalkulasi data audit untuk unit asal dan unit tujuan via Queue
        if ($originUnitId) {
            UpdateUnitAuditMetricsJob::dispatch($originUnitId, Auth::id());
        }
        UpdateUnitAuditMetricsJob::dispatch((int) $validated['destination_unit_id'], Auth::id());

        return redirect()->route('mutations.index')
            ->with('success', "Mutasi untuk {$employee->full_name} berhasil dicatat dan unit kerja telah diperbarui.");
    }
}

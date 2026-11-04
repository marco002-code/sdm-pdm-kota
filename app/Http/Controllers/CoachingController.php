<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\EmployeeCoaching;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CoachingController extends Controller
{
    /**
     * Tampilkan daftar riwayat pembinaan pegawai.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        if ($user && $user->isPegawai()) {
            abort(403, 'Akses ditolak: Pegawai tidak memiliki izin untuk mengakses modul pembinaan pegawai.');
        }

        $query = EmployeeCoaching::with(['employee', 'unit', 'creator']);

        if ($user->isUnitLevel() && $user->unit_id) {
            $query->where('unit_id', $user->unit_id);
        } elseif ($request->filled('unit_id')) {
            $query->where('unit_id', $request->unit_id);
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('organizer', 'like', "%{$search}%")
                  ->orWhereHas('employee', function ($eq) use ($search) {
                      $eq->where('full_name', 'like', "%{$search}%")
                         ->orWhere('npm', 'like', "%{$search}%")
                         ->orWhere('nbm', 'like', "%{$search}%");
                  });
            });
        }

        $coachings = $query->orderBy('coaching_date', 'desc')->paginate(15)->withQueryString();
        $units = Unit::where('is_active', true)->orderBy('name', 'asc')->get();
        $categories = EmployeeCoaching::getCategories();

        return view('coachings.index', compact('coachings', 'units', 'categories'));
    }

    /**
     * Form catat pembinaan pegawai baru.
     */
    public function create(Request $request)
    {
        $user = Auth::user();
        if ($user->isKetuaMajelis()) {
            abort(403, 'Akses ditolak: Ketua Majelis memiliki hak akses peninjauan (Read-Only). Pencatatan pembinaan dilakukan oleh Petugas TU Unit atau Admin.');
        }

        $units = $user->isUnitLevel() && $user->unit_id
            ? Unit::where('id', $user->unit_id)->get()
            : Unit::where('is_active', true)->orderBy('name', 'asc')->get();

        $employeesQuery = Employee::where('status', 'aktif');
        if ($user->isUnitLevel() && $user->unit_id) {
            $employeesQuery->where('unit_id', $user->unit_id);
        }
        $employees = $employeesQuery->orderBy('full_name', 'asc')->get(['id', 'full_name', 'npm', 'unit_id', 'position']);
        $categories = EmployeeCoaching::getCategories();

        return view('coachings.create', compact('units', 'employees', 'categories'));
    }

    /**
     * Simpan data pembinaan pegawai dalam transaksi aman.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        if ($user->isKetuaMajelis()) {
            abort(403, 'Akses ditolak: Ketua Majelis memiliki hak akses peninjauan (Read-Only).');
        }

        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'coaching_date' => 'required|date',
            'category' => 'required|in:ideologi_aik,komitmen_loyalitas,kompetensi_pelatihan,disiplin_sanksi,lainnya',
            'title' => 'required|string|max:200',
            'organizer' => 'nullable|string|max:150',
            'notes' => 'nullable|string',
            'follow_up' => 'nullable|string',
            'status' => 'required|in:terjadwal,terlaksana,evaluasi',
        ]);

        $employee = DB::transaction(function () use ($validated) {
            $emp = Employee::findOrFail($validated['employee_id']);
            $validated['unit_id'] = $emp->unit_id;
            $validated['created_by'] = Auth::id();

            EmployeeCoaching::create($validated);

            return $emp;
        }, 5);

        return redirect()->route('coachings.index')
            ->with('success', "Catatan pembinaan untuk {$employee->full_name} berhasil disimpan.");
    }
}

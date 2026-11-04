<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use Illuminate\Http\Request;

class UnitController extends Controller
{
    /**
     * Menampilkan master data unit kerja (sekolah & kantor yayasan).
     */
    public function index(Request $request)
    {
        $user = auth()->user();

        if ($user && $user->isPegawai()) {
            abort(403, 'Akses ditolak: Pegawai tidak memiliki izin untuk mengakses direktori master unit kerja.');
        }

        // Isolasi Domain: TU / Staf unit langsung diarahkan ke profil unit kerjanya sendiri
        if ($user && $user->isUnitLevel() && $user->unit_id) {
            return redirect()->route('units.show', $user->unit_id);
        }

        $query = Unit::withCount(['employees', 'users']);

        // Filter Jenjang / Level
        if ($request->filled('level')) {
            $query->where('level', $request->level);
        }

        // Filter Kecamatan
        if ($request->filled('district')) {
            $query->where('district', $request->district);
        }

        // Pencarian (Nama Sekolah, NPSN, Kode)
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('npsn', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('district', 'like', "%{$search}%");
            });
        }

        $units = $query->orderByRaw("CASE 
            WHEN level = 'YAYASAN' THEN 1
            WHEN level = 'TPA' THEN 2
            WHEN level = 'TK' THEN 3
            WHEN level = 'SD' THEN 4
            WHEN level = 'SMP' THEN 5
            WHEN level = 'SMA' THEN 6
            WHEN level = 'SMK' THEN 7
            WHEN level = 'MA' THEN 8
            ELSE 9 END")
            ->orderBy('name', 'asc')
            ->paginate(20)
            ->withQueryString();

        // Data rekap statistik jenjang
        $levelCounts = Unit::selectRaw('level, count(*) as count')
            ->groupBy('level')
            ->pluck('count', 'level')
            ->toArray();

        // Daftar kecamatan unik untuk dropdown filter
        $districts = Unit::whereNotNull('district')
            ->distinct()
            ->orderBy('district', 'asc')
            ->pluck('district');

        return view('units.index', compact('units', 'levelCounts', 'districts'));
    }

    /**
     * Menampilkan profil lengkap unit kerja beserta pegawai di dalamnya.
     */
    public function show(Unit $unit)
    {
        $user = auth()->user();

        if ($user && $user->isPegawai()) {
            abort(403, 'Akses ditolak: Pegawai tidak memiliki izin untuk mengakses profil unit kerja.');
        }

        // Validasi Akses Domain Unit
        if ($user && $user->isUnitLevel() && $user->unit_id && $user->unit_id !== $unit->id) {
            abort(403, 'Akses ditolak: Anda hanya berhak mengakses profil unit kerja Anda sendiri.');
        }

        $unit->load(['employees' => function ($q) {
            $q->orderBy('employee_type', 'asc')->orderBy('full_name', 'asc');
        }]);

        return view('units.show', compact('unit'));
    }

    /**
     * Mengambil / menyinkronkan data identitas unit sekolah berdasarkan NPSN.
     */
    public function syncNpsn(Unit $unit, \App\Services\NpsnLookupService $service)
    {
        $user = auth()->user();

        if ($user && $user->isKetuaMajelis()) {
            abort(403, 'Akses ditolak: Ketua Majelis memiliki hak akses peninjauan (Read-Only). Sinkronisasi NPSN dilakukan oleh Petugas TU Unit atau Admin.');
        }

        // Validasi Akses Domain Unit
        if ($user && $user->isUnitLevel() && $user->unit_id && $user->unit_id !== $unit->id) {
            abort(403, 'Akses ditolak: Anda hanya dapat menyinkronkan data NPSN unit kerja Anda sendiri.');
        }

        if (empty($unit->npsn)) {
            return back()->with('error', 'Unit ini tidak memiliki nomor NPSN untuk disinkronkan.');
        }

        $success = $service->syncUnit($unit);
        if ($success) {
            return back()->with('success', "Identitas Unit Kerja berhasil diambil dan diperbarui berdasarkan NPSN ({$unit->npsn})!");
        }

        return back()->with('info', "Data NPSN {$unit->npsn} sudah sesuai dengan data terkini.");
    }
}

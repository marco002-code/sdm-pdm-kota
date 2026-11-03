<?php

namespace App\Http\Controllers;

use App\Jobs\UpdateUnitAuditMetricsJob;
use App\Models\Employee;
use App\Models\MasterNpm;
use App\Models\Unit;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class EmployeeController extends Controller
{
    /**
     * Menampilkan daftar data induk pegawai dengan filter unit, tipe, dan pencarian.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // RBAC: Jika peran Pegawai (ESS), langsung arahkan ke profil pribadi
        if ($user->isPegawai()) {
            $employee = $user->employee;
            if ($employee) {
                return redirect()->route('employees.show', $employee->id);
            }
            return redirect()->route('dashboard')->with('error', 'Profil pegawai Anda belum terhubung.');
        }

        $query = Employee::with(['unit', 'user']);

        // Isolasi Unit: Kepala Sekolah dan TU hanya melihat unit kerjanya sendiri
        if ($user->isUnitLevel() && $user->unit_id) {
            $query->where('unit_id', $user->unit_id);
            $units = Unit::where('id', $user->unit_id)->get();
        } else {
            // Filter Jenis AUM (TPA, TK, SD, SMP, SMA, SMK, MA)
            if ($request->filled('jenis_aum')) {
                $query->whereHas('unit', function ($q) use ($request) {
                    $q->where('level', $request->jenis_aum);
                });
            }

            // Filter Unit Kerja AUM spesifik
            if ($request->filled('unit_id')) {
                $query->where('unit_id', $request->unit_id);
            }

            $units = Unit::where('is_active', true)->orderBy('name', 'asc')->get(['id', 'name', 'code', 'level', 'npsn', 'district']);
        }

        // Filter Kategori Pegawai
        if ($request->filled('type')) {
            $t = $request->type;
            if ($t === 'guru') {
                $query->whereIn('employee_type', ['GTY', 'GTT', 'guru']);
            } elseif ($t === 'tendik') {
                $query->whereIn('employee_type', ['PTY', 'tendik']);
            } elseif ($t === 'karyawan') {
                $query->whereIn('employee_type', ['PTT', 'LAINNYA', 'karyawan']);
            } else {
                $query->where('employee_type', $t);
            }
        }

        // Filter Status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Pencarian (NPM, Nama, NBM, NIK, NIP)
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('npm', 'like', "%{$search}%")
                  ->orWhere('full_name', 'like', "%{$search}%")
                  ->orWhere('nbm', 'like', "%{$search}%")
                  ->orWhere('nik', 'like', "%{$search}%")
                  ->orWhere('nip', 'like', "%{$search}%")
                  ->orWhere('nuptk', 'like', "%{$search}%")
                  ->orWhere('simpatika_id', 'like', "%{$search}%")
                  ->orWhere('position', 'like', "%{$search}%");
            });
        }

        $employees = $query->orderBy('full_name', 'asc')->paginate(15)->withQueryString();

        return view('employees.index', compact('employees', 'units'));
    }

    /**
     * Menampilkan lembar formulir lengkap Data Induk Pegawai (Biodata & Cetak).
     */
    public function show(Employee $employee)
    {
        $user = Auth::user();

        // Validasi Akses (RBAC & Isolasi Unit Domain)
        if ($user->isPegawai() && $user->employee && $user->employee->id !== $employee->id) {
            abort(403, 'Akses ditolak: Anda hanya berhak melihat data profil Anda sendiri.');
        }

        if ($user->isUnitLevel() && $user->unit_id && $user->unit_id !== $employee->unit_id) {
            abort(403, 'Akses ditolak: Anda hanya berhak mengakses data pegawai pada unit kerja Anda sendiri.');
        }

        $employee->load(['unit', 'user', 'documents']);

        return view('employees.show', compact('employee'));
    }

    /**
     * Cetak berkas dokumen resmi Formulir Data Induk Pegawai ke format PDF.
     */
    public function printPdf(Request $request, Employee $employee)
    {
        $user = Auth::user();

        // Validasi Akses (RBAC & Isolasi Unit Domain)
        if ($user->isPegawai() && $user->employee && $user->employee->id !== $employee->id) {
            abort(403, 'Akses ditolak: Anda hanya berhak mencetak data profil Anda sendiri.');
        }

        if ($user->isUnitLevel() && $user->unit_id && $user->unit_id !== $employee->unit_id) {
            abort(403, 'Akses ditolak: Anda hanya berhak mencetak data pegawai pada unit kerja Anda sendiri.');
        }

        $employee->load(['unit', 'user']);

        $pdf = Pdf::loadView('employees.pdf', compact('employee'), [], 'UTF-8')
            ->setPaper('a4', 'portrait');

        $filename = 'Dokumen-Formulir-Data-Induk-' . Str::slug($employee->full_name) . '.pdf';

        if ($request->has('download') && $request->download == '1') {
            return $pdf->download($filename);
        }

        return $pdf->stream($filename);
    }

    /**
     * Formulir penambahan data induk pegawai baru.
     */
    public function create()
    {
        $user = Auth::user();
        if ($user->isPegawai()) {
            abort(403, 'Akses ditolak.');
        }

        $units = $user->isUnitLevel() && $user->unit_id
            ? Unit::where('id', $user->unit_id)->get()
            : Unit::where('is_active', true)->get();

        return view('employees.create', compact('units'));
    }

    /**
     * Menyimpan data induk pegawai baru.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        if ($user->isPegawai()) {
            abort(403, 'Akses ditolak.');
        }

        // Proteksi unit untuk Kepsek / TU: Kunci unit_id sebelum validasi
        if ($user->isUnitLevel() && $user->unit_id) {
            $request->merge(['unit_id' => $user->unit_id]);
        }

        $validated = $request->validate([
            'npm' => 'nullable|string|max:50|unique:employees,npm',
            'full_name' => 'required|string|max:150',
            'unit_id' => 'required|exists:units,id',
            'nik' => 'required|string|max:20|unique:employees,nik',
            'nbm' => 'nullable|string|max:50|unique:employees,nbm',
            'nip' => 'nullable|string|max:50|unique:employees,nip',
            'nuptk' => 'nullable|string|max:50',
            'nrg' => 'nullable|string|max:50',
            'simpatika_id' => 'nullable|string|max:50',
            'employee_type' => 'nullable|in:PDM,GTY,GTT,PTY,PTT,LAINNYA,guru,tendik,karyawan',
            'gender' => 'required|in:L,P',
            'birth_place' => 'nullable|string|max:100',
            'birth_date' => 'nullable|date',
            'religion' => 'nullable|string|max:30',
            'marital_status' => 'nullable|in:lajang,menikah,cerai_hidup,cerai_mati',
            'address' => 'nullable|string',
            'phone_number' => 'nullable|string|max:30',
            'email' => 'nullable|email|max:100',
            'position' => 'required|string|max:100',
            'join_date' => 'required|date',
            'basic_salary' => 'nullable|numeric|min:0',
            'retirement_year' => 'nullable|integer|min:1950|max:2100',
            'status' => 'nullable|in:aktif,cuti,nonaktif,mutasi,pensiun,wafat',
            'sim_certificate_number' => 'nullable|string|max:100',
            'sim_grade' => 'nullable|string|max:30',
            'sim_date' => 'nullable|date',
            'sim_certificate_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        // Validasi NPM terhadap Bank Data Master NPM
        if ($request->filled('npm')) {
            $npmCheck = \App\Models\MasterNpm::checkAvailability($request->npm);
            if (!$npmCheck['is_valid']) {
                return back()->withInput()->withErrors(['npm' => $npmCheck['message']]);
            }
        }

        // Upload berkas sertifikat SIM jika ada
        if ($request->hasFile('sim_certificate_file')) {
            $file = $request->file('sim_certificate_file');
            $filename = 'sim_' . time() . '_' . Str::slug($request->full_name) . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('certificates/sim', $filename, 'public');
            $validated['sim_certificate_file'] = '/storage/' . $path;
        }

        // Proteksi unit untuk Kepsek / TU
        if ($user->isUnitLevel() && $user->unit_id) {
            $validated['unit_id'] = $user->unit_id;
        }

        // Format Riwayat Pendidikan
        $validated['education_history'] = [
            's3' => [
                'school' => $request->input('edu_s3_school'),
                'major'  => $request->input('edu_s3_major'),
            ],
            's2' => [
                'school' => $request->input('edu_s2_school'),
                'major'  => $request->input('edu_s2_major'),
            ],
            's1' => [
                'school' => $request->input('edu_s1_school'),
                'major'  => $request->input('edu_s1_major'),
            ],
            'd3' => [
                'school' => $request->input('edu_d3_school'),
                'major'  => $request->input('edu_d3_major'),
            ],
            'sma' => $request->input('edu_sma'),
            'smp' => $request->input('edu_smp'),
            'sd'  => $request->input('edu_sd'),
        ];

        // Format Pengalaman Kerja & Organisasi (filter string kosong)
        $validated['work_experiences'] = array_values(array_filter((array) $request->input('work_experiences', [])));
        $validated['organizational_experiences'] = array_values(array_filter((array) $request->input('organizational_experiences', [])));

        // Prestasi Pegawai (Lokal, Nasional, Internasional)
        if ($request->has('achievements')) {
            $achievements = [];
            foreach ((array) $request->input('achievements', []) as $ach) {
                if (!empty($ach['title'])) {
                    $achievements[] = [
                        'level' => $ach['level'] ?? 'lokal',
                        'title' => $ach['title'],
                        'year' => $ach['year'] ?? date('Y'),
                        'organizer' => $ach['organizer'] ?? null,
                        'description' => $ach['description'] ?? null,
                    ];
                }
            }
            $validated['achievements'] = $achievements;
        }

        // Input Nilai DP3
        if ($request->has('dp3_scores')) {
            $dp3List = [];
            foreach ((array) $request->input('dp3_scores', []) as $d) {
                if (!empty($d['year']) && isset($d['score'])) {
                    $dp3List[] = [
                        'year' => (int) $d['year'],
                        'score' => (float) $d['score'],
                        'predicate' => $d['predicate'] ?? 'Baik',
                        'notes' => $d['notes'] ?? null,
                    ];
                }
            }
            $validated['dp3_scores'] = $dp3List;
        }

        // Eksekusi penyimpanan atomik dalam antrian transaksi DB dengan proteksi concurrency retry 5x
        $employee = DB::transaction(function () use ($validated) {
            // Validasi & kunci baris NPM di Master NPM saat transaksi aktif
            if (!empty($validated['npm'])) {
                $master = MasterNpm::where('npm', $validated['npm'])->lockForUpdate()->first();
                if ($master && $master->is_used) {
                    throw ValidationException::withMessages([
                        'npm' => 'Nomor NPM ini baru saja digunakan oleh pegawai lain.',
                    ]);
                }
            }

            $emp = Employee::create($validated);

            // Tandai NPM di Master NPM sebagai terpakai
            if (!empty($emp->npm)) {
                $master = MasterNpm::where('npm', $emp->npm)->first();
                if ($master) {
                    $master->update([
                        'is_used' => true,
                        'used_by_employee_id' => $emp->id,
                        'used_at' => now(),
                    ]);
                }
            }

            return $emp;
        }, 5);

        // Delegasikan rekap kepatuhan audit unit ke Antrian Latar Belakang (Queue)
        if ($employee->unit_id) {
            UpdateUnitAuditMetricsJob::dispatch($employee->unit_id, Auth::id());
        }

        return redirect()->route('employees.show', $employee->id)
            ->with('success', 'Data Induk Pegawai berhasil disimpan.');
    }

    /**
     * Formulir ubah data induk pegawai.
     */
    public function edit(Employee $employee)
    {
        $user = Auth::user();

        // RBAC: Pegawai hanya boleh edit data dirinya sendiri
        if ($user->isPegawai() && (!$user->employee || $user->employee->id !== $employee->id)) {
            abort(403, 'Akses ditolak: Anda hanya berhak mengedit data profil Anda sendiri.');
        }

        // Isolasi Unit: TU / Kepsek hanya boleh edit pegawai di unitnya sendiri
        if ($user->isUnitLevel() && $user->unit_id && $user->unit_id !== $employee->unit_id) {
            abort(403, 'Akses ditolak: Anda hanya berhak mengedit data pegawai pada unit kerja Anda sendiri.');
        }

        $units = $user->isUnitLevel() && $user->unit_id
            ? Unit::where('id', $user->unit_id)->get()
            : Unit::where('is_active', true)->get();

        return view('employees.edit', compact('employee', 'units'));
    }

    /**
     * Memperbarui data induk pegawai.
     */
    public function update(Request $request, Employee $employee)
    {
        $user = Auth::user();

        if ($user->isPegawai() && (!$user->employee || $user->employee->id !== $employee->id)) {
            abort(403, 'Akses ditolak: Anda hanya berhak memperbarui data profil Anda sendiri.');
        }

        // Isolasi Unit: TU / Kepsek dilarang mengedit pegawai unit lain
        if ($user->isUnitLevel() && $user->unit_id && $user->unit_id !== $employee->unit_id) {
            abort(403, 'Akses ditolak: Anda hanya berhak memperbarui data pegawai pada unit kerja Anda sendiri.');
        }

        // Kunci unit untuk TU / Kepsek: Kunci unit_id sebelum validasi
        if ($user->isUnitLevel() && $user->unit_id) {
            $request->merge(['unit_id' => $user->unit_id]);
        }

        $rules = [
            'npm' => 'nullable|string|max:50|unique:employees,npm,' . $employee->id,
            'full_name' => 'required|string|max:150',
            'nik' => 'required|string|max:20|unique:employees,nik,' . $employee->id,
            'nbm' => 'nullable|string|max:50|unique:employees,nbm,' . $employee->id,
            'nip' => 'nullable|string|max:50|unique:employees,nip,' . $employee->id,
            'nuptk' => 'nullable|string|max:50',
            'nrg' => 'nullable|string|max:50',
            'simpatika_id' => 'nullable|string|max:50',
            'gender' => 'required|in:L,P',
            'birth_place' => 'nullable|string|max:100',
            'birth_date' => 'nullable|date',
            'religion' => 'nullable|string|max:30',
            'marital_status' => 'nullable|in:lajang,menikah,cerai_hidup,cerai_mati',
            'address' => 'nullable|string',
            'phone_number' => 'nullable|string|max:30',
            'email' => 'nullable|email|max:100',
            'sim_certificate_number' => 'nullable|string|max:100',
            'sim_grade' => 'nullable|string|max:30',
            'sim_date' => 'nullable|date',
            'sim_certificate_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'retirement_year' => 'nullable|integer|min:1950|max:2100',
            'status' => 'nullable|in:aktif,cuti,nonaktif,mutasi,pensiun,wafat',
        ];

        // Jika level yayasan / unit admin, bisa ubah unit, jabatan, gaji
        if (!$user->isPegawai()) {
            $rules['unit_id'] = 'required|exists:units,id';
            $rules['employee_type'] = 'nullable|in:PDM,GTY,GTT,PTY,PTT,LAINNYA,guru,tendik,karyawan';
            $rules['position'] = 'required|string|max:100';
            $rules['join_date'] = 'required|date';
            $rules['basic_salary'] = 'nullable|numeric|min:0';
        }

        $validated = $request->validate($rules);

        // Validasi NPM terhadap Bank Data Master NPM
        if ($request->filled('npm') && $request->npm !== $employee->npm) {
            $npmCheck = \App\Models\MasterNpm::checkAvailability($request->npm, $employee->id);
            if (!$npmCheck['is_valid']) {
                return back()->withInput()->withErrors(['npm' => $npmCheck['message']]);
            }
        }

        // Upload berkas sertifikat SIM baru jika ada
        if ($request->hasFile('sim_certificate_file')) {
            $file = $request->file('sim_certificate_file');
            $filename = 'sim_' . time() . '_' . Str::slug($request->full_name) . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('certificates/sim', $filename, 'public');
            $validated['sim_certificate_file'] = '/storage/' . $path;
        }

        // Kunci unit untuk TU / Kepsek: Pegawai tidak dapat dipindahkan ke sekolah lain oleh TU
        if ($user->isUnitLevel() && $user->unit_id) {
            $validated['unit_id'] = $user->unit_id;
        }

        // Format Riwayat Pendidikan
        $validated['education_history'] = [
            's3' => [
                'school' => $request->input('edu_s3_school'),
                'major'  => $request->input('edu_s3_major'),
            ],
            's2' => [
                'school' => $request->input('edu_s2_school'),
                'major'  => $request->input('edu_s2_major'),
            ],
            's1' => [
                'school' => $request->input('edu_s1_school'),
                'major'  => $request->input('edu_s1_major'),
            ],
            'd3' => [
                'school' => $request->input('edu_d3_school'),
                'major'  => $request->input('edu_d3_major'),
            ],
            'sma' => $request->input('edu_sma'),
            'smp' => $request->input('edu_smp'),
            'sd'  => $request->input('edu_sd'),
        ];

        // Format Pengalaman Kerja & Organisasi
        $validated['work_experiences'] = array_values(array_filter((array) $request->input('work_experiences', [])));
        $validated['organizational_experiences'] = array_values(array_filter((array) $request->input('organizational_experiences', [])));

        // Prestasi Pegawai
        if ($request->has('achievements')) {
            $achievements = [];
            foreach ((array) $request->input('achievements', []) as $ach) {
                if (!empty($ach['title'])) {
                    $achievements[] = [
                        'level' => $ach['level'] ?? 'lokal',
                        'title' => $ach['title'],
                        'year' => $ach['year'] ?? date('Y'),
                        'organizer' => $ach['organizer'] ?? null,
                        'description' => $ach['description'] ?? null,
                    ];
                }
            }
            $validated['achievements'] = $achievements;
        }

        // Input Nilai DP3
        if ($request->has('dp3_scores')) {
            $dp3List = [];
            foreach ((array) $request->input('dp3_scores', []) as $d) {
                if (!empty($d['year']) && isset($d['score'])) {
                    $dp3List[] = [
                        'year' => (int) $d['year'],
                        'score' => (float) $d['score'],
                        'predicate' => $d['predicate'] ?? 'Baik',
                        'notes' => $d['notes'] ?? null,
                    ];
                }
            }
            $validated['dp3_scores'] = $dp3List;
        }

        // Eksekusi pembaruan atomik dalam antrian transaksi DB dengan proteksi concurrency retry 5x
        DB::transaction(function () use ($employee, $validated) {
            $oldNpm = $employee->npm;
            $newNpm = $validated['npm'] ?? null;

            // Lepas relasi master NPM lama jika NPM diubah
            if ($oldNpm && $oldNpm !== $newNpm) {
                MasterNpm::where('npm', $oldNpm)->update([
                    'is_used' => false,
                    'used_by_employee_id' => null,
                    'used_at' => null,
                ]);
            }

            // Kunci baris master NPM baru jika berubah
            if (!empty($newNpm) && $newNpm !== $oldNpm) {
                $masterNew = MasterNpm::where('npm', $newNpm)->lockForUpdate()->first();
                if ($masterNew && $masterNew->is_used && $masterNew->used_by_employee_id !== $employee->id) {
                    throw ValidationException::withMessages([
                        'npm' => 'Nomor NPM ini baru saja digunakan oleh pegawai lain.',
                    ]);
                }
            }

            $employee->update($validated);

            // Sinkronkan ke master NPM baru
            if (!empty($employee->npm)) {
                $master = MasterNpm::where('npm', $employee->npm)->first();
                if ($master) {
                    $master->update([
                        'is_used' => true,
                        'used_by_employee_id' => $employee->id,
                        'used_at' => now(),
                    ]);
                }
            }
        }, 5);

        // Delegasikan rekap kepatuhan audit unit ke Antrian Latar Belakang (Queue)
        if ($employee->unit_id) {
            UpdateUnitAuditMetricsJob::dispatch($employee->unit_id, Auth::id());
        }

        return redirect()->route('employees.show', $employee->id)
            ->with('success', 'Data Induk Pegawai berhasil diperbarui.');
    }

    /**
     * Hapus data pegawai (Soft Delete) dengan transaksi aman & antrian audit.
     */
    public function destroy(Employee $employee)
    {
        $user = Auth::user();
        if (!$user->isYayasanLevel()) {
            abort(403, 'Akses ditolak: Tenaga Tata Usaha tidak memiliki wewenang untuk menghapus data pegawai. Penghapusan hanya dapat dilakukan oleh HR Pusat atau Admin Yayasan.');
        }

        $unitId = $employee->unit_id;

        DB::transaction(function () use ($employee) {
            // Lepas relasi master NPM jika ada
            if ($employee->npm) {
                MasterNpm::where('npm', $employee->npm)->update([
                    'is_used' => false,
                    'used_by_employee_id' => null,
                    'used_at' => null,
                ]);
            }

            $employee->delete();
        }, 5);

        if ($unitId) {
            UpdateUnitAuditMetricsJob::dispatch($unitId, Auth::id());
        }

        return redirect()->route('employees.index')
            ->with('success', 'Data Pegawai berhasil dihapus.');
    }
}

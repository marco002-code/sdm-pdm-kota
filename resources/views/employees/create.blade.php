@extends('layouts.app')

@section('title', 'Tambah Pegawai Baru - SDM PDM Kota Yogyakarta')

@section('content')
<div class="mb-6 flex items-center justify-between">
    <div>
        <h1 class="text-2xl font-bold text-slate-800 tracking-tight">Tambah Pegawai Baru</h1>
        <p class="text-xs text-slate-500 mt-1">Input data induk pegawai baru (Guru, Tendik, atau Karyawan AUM)</p>
    </div>
    <a href="{{ route('employees.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-semibold rounded-xl transition">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        <span>Batal / Kembali</span>
    </a>
</div>

@if (isset($errors) && $errors->any())
    <div class="mb-6 p-4 rounded-xl bg-red-50 border border-red-200 text-red-700 text-sm">
        <div class="font-bold mb-1">Terjadi kesalahan validasi:</div>
        <ul class="list-disc list-inside space-y-0.5 text-xs">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form method="POST" action="{{ route('employees.store') }}" enctype="multipart/form-data" class="space-y-6">
    @csrf

    <!-- 1. IDENTITAS MULTI-ID (NPM SEBAGAI ID UTAMA) -->
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
        <div class="flex items-center justify-between mb-4 pb-2 border-b border-slate-100">
            <h3 class="text-base font-bold text-slate-800 flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                <span>Sistem Identitas Pegawai (Multi-ID)</span>
            </h3>
            <span class="text-[11px] font-bold px-2 py-0.5 rounded bg-amber-100 text-amber-800 border border-amber-300">
                NPM = ID Primer PDM
            </span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
            <!-- NPM (ID UTAMA) dengan Real-time Validation Checker -->
            <div class="sm:col-span-2 bg-gradient-to-r from-amber-50/60 to-yellow-50/30 p-4 rounded-xl border border-amber-200">
                <div class="flex items-center justify-between mb-1">
                    <label class="block text-xs font-black text-amber-950 uppercase tracking-wide">
                        N P M (Nomor Pegawai Muhammadiyah) <span class="text-rose-600 font-bold">* ID UTAMA</span>
                    </label>
                    <span id="npm_status_badge" class="text-[10px] font-bold px-2 py-0.5 rounded bg-slate-200 text-slate-600 hidden">
                        Mengecek...
                    </span>
                </div>
                <div class="relative">
                    <input type="text" id="input_npm" name="npm" value="{{ old('npm') }}" placeholder="Masukkan NPM resmi dari PDM (misal: 000001)..." class="w-full px-3.5 py-2 text-sm bg-white border border-amber-300 rounded-xl focus:ring-2 focus:ring-amber-500 focus:outline-none font-mono font-bold text-slate-800 @error('npm') border-rose-500 @enderror">
                    <div id="npm_loading" class="absolute right-3 top-2.5 hidden">
                        <svg class="animate-spin h-4 w-4 text-amber-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    </div>
                </div>
                <div id="npm_feedback" class="text-[11px] mt-1.5 font-medium text-slate-600">
                    Sistem akan memvalidasi nomor ini secara otomatis terhadap Bank Data Master NPM PDM Kota Yogyakarta.
                </div>
                @error('npm')
                    <p class="text-[11px] text-rose-500 mt-1 font-semibold">{{ $message }}</p>
                @enderror
            </div>

            <!-- NBM -->
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">N B M (Nomor Baku Muhammadiyah)</label>
                <input type="text" name="nbm" value="{{ old('nbm') }}" placeholder="Contoh: 1089234" class="w-full px-3.5 py-2 text-sm bg-slate-50 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:bg-white focus:outline-none font-mono">
            </div>

            <!-- NIK -->
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">N I K (KTP 16 Digit) <span class="text-red-500">*</span></label>
                <input type="text" name="nik" value="{{ old('nik') }}" required maxlength="20" placeholder="16 digit NIK..." class="w-full px-3.5 py-2 text-sm bg-slate-50 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:bg-white focus:outline-none font-mono">
            </div>

            <!-- NRG -->
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">N R G (Registrasi Guru)</label>
                <input type="text" name="nrg" value="{{ old('nrg') }}" placeholder="Nomor Registrasi Guru..." class="w-full px-3.5 py-2 text-sm bg-slate-50 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:bg-white focus:outline-none font-mono">
            </div>
        </div>
    </div>

    <!-- 2. BIODATA PRIBADI -->
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
        <h3 class="text-base font-bold text-slate-800 mb-4 pb-2 border-b border-slate-100 flex items-center gap-2">
            <span class="w-2.5 h-2.5 rounded-full bg-blue-600"></span>
            <span>Biodata Pribadi</span>
        </h3>

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
            <div class="sm:col-span-2">
                <label class="block text-xs font-semibold text-slate-600 mb-1">Nama Lengkap Sesuai KTP <span class="text-red-500">*</span></label>
                <input type="text" name="full_name" value="{{ old('full_name') }}" required class="w-full px-3.5 py-2 text-sm bg-slate-50 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:bg-white focus:outline-none font-medium">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Jenis Kelamin <span class="text-red-500">*</span></label>
                <select name="gender" required class="w-full px-3.5 py-2 text-sm bg-slate-50 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:bg-white focus:outline-none">
                    <option value="L" {{ old('gender') === 'L' ? 'selected' : '' }}>Laki-laki</option>
                    <option value="P" {{ old('gender') === 'P' ? 'selected' : '' }}>Perempuan</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Gelar Depan</label>
                <input type="text" name="title_prefix" value="{{ old('title_prefix') }}" placeholder="Dr., Drs., dll" class="w-full px-3.5 py-2 text-sm bg-slate-50 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:bg-white focus:outline-none">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Gelar Belakang</label>
                <input type="text" name="title_suffix" value="{{ old('title_suffix') }}" placeholder="S.Pd., M.Pd., dll" class="w-full px-3.5 py-2 text-sm bg-slate-50 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:bg-white focus:outline-none">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Status Pernikahan</label>
                <select name="marital_status" class="w-full px-3.5 py-2 text-sm bg-slate-50 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:bg-white focus:outline-none">
                    <option value="lajang" {{ old('marital_status') === 'lajang' ? 'selected' : '' }}>Lajang</option>
                    <option value="menikah" {{ old('marital_status') === 'menikah' ? 'selected' : '' }}>Menikah</option>
                    <option value="cerai_hidup" {{ old('marital_status') === 'cerai_hidup' ? 'selected' : '' }}>Cerai Hidup</option>
                    <option value="cerai_mati" {{ old('marital_status') === 'cerai_mati' ? 'selected' : '' }}>Cerai Mati</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Tempat Lahir</label>
                <input type="text" name="birth_place" value="{{ old('birth_place') }}" class="w-full px-3.5 py-2 text-sm bg-slate-50 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:bg-white focus:outline-none">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Tanggal Lahir</label>
                <input type="date" name="birth_date" value="{{ old('birth_date') }}" class="w-full px-3.5 py-2 text-sm bg-slate-50 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:bg-white focus:outline-none">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">No. Handphone (WhatsApp)</label>
                <input type="text" name="phone_number" value="{{ old('phone_number') }}" placeholder="08..." class="w-full px-3.5 py-2 text-sm bg-slate-50 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:bg-white focus:outline-none">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">E-mail</label>
                <input type="email" name="email" value="{{ old('email') }}" class="w-full px-3.5 py-2 text-sm bg-slate-50 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:bg-white focus:outline-none">
            </div>

            <div class="sm:col-span-3">
                <label class="block text-xs font-semibold text-slate-600 mb-1">Alamat Tinggal / Domisili</label>
                <textarea name="address" rows="2" class="w-full px-3.5 py-2 text-sm bg-slate-50 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:bg-white focus:outline-none">{{ old('address') }}</textarea>
            </div>
        </div>
    </div>

    <!-- 3. PENUGASAN, UNIT KERJA & STATUS KEPEGAWAIAN -->
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
        <h3 class="text-base font-bold text-slate-800 mb-4 pb-2 border-b border-slate-100 flex items-center gap-2">
            <span class="w-2.5 h-2.5 rounded-full bg-blue-600"></span>
            <span>Penugasan & Unit Kerja AUM</span>
        </h3>

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
            @if(auth()->user()->isYayasanLevel())
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">1. Jenis Jenjang AUM <span class="text-blue-600 font-bold">(Filter)</span></label>
                <select id="create_jenis_aum" onchange="filterCreateUnits()" class="w-full px-3.5 py-2 text-sm bg-blue-50/50 border border-blue-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:bg-white focus:outline-none font-semibold text-slate-800">
                    <option value="">-- Semua Jenis AUM --</option>
                    <option value="TK">TK / ABA (Taman Kanak-Kanak)</option>
                    <option value="SD">SD / MI (Sekolah Dasar)</option>
                    <option value="SMP">SMP (Sekolah Menengah Pertama)</option>
                    <option value="MTS">MTs (Madrasah Tsanawiyah)</option>
                    <option value="SMA">SMA (Sekolah Menengah Atas)</option>
                    <option value="SMK">SMK (Sekolah Menengah Kejuruan)</option>
                    <option value="PKBM">PKBM (Pusat Kegiatan Belajar)</option>
                    <option value="TPA">TPA / KB</option>
                    <option value="YAYASAN">Kantor PDM Kota Yogyakarta</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">2. Nama Unit Kerja AUM <span class="text-red-500">*</span></label>
                <select name="unit_id" id="create_unit_id" required class="w-full px-3.5 py-2 text-sm bg-slate-50 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:bg-white focus:outline-none">
                    <option value="">-- Pilih Unit Kerja AUM --</option>
                    @foreach($units as $u)
                        <option value="{{ $u->id }}" data-level="{{ $u->level }}" {{ old('unit_id') == $u->id ? 'selected' : '' }}>
                            {{ $u->name }} ({{ $u->level }})
                        </option>
                    @endforeach
                </select>
            </div>
            @else
            <!-- Unit Kerja Terkunci Sesuai Domain Lembaga TU -->
            <div class="sm:col-span-2">
                <label class="block text-xs font-semibold text-slate-600 mb-1">Unit Kerja AUM <span class="text-xs text-blue-600 font-bold">(Domain Lembaga Anda)</span></label>
                <input type="text" readonly value="{{ auth()->user()->unit->name ?? 'Unit Sekolah' }} ({{ auth()->user()->unit->level ?? 'UNIT' }})" class="w-full px-3.5 py-2 text-sm bg-blue-50/60 border border-blue-200 text-blue-900 rounded-xl font-bold cursor-not-allowed">
                <input type="hidden" name="unit_id" value="{{ auth()->user()->unit_id }}">
            </div>
            @endif

            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Status Kepegawaian <span class="text-red-500">*</span></label>
                <select name="employee_type" required class="w-full px-3.5 py-2 text-sm bg-slate-50 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:bg-white focus:outline-none font-semibold">
                    <option value="GTY" {{ old('employee_type', 'GTY') === 'GTY' ? 'selected' : '' }}>GTY (Guru Tetap Yayasan)</option>
                    <option value="GTT" {{ old('employee_type') === 'GTT' ? 'selected' : '' }}>GTT (Guru Tidak Tetap / Kontrak 11 Bln)</option>
                    <option value="PTY" {{ old('employee_type') === 'PTY' ? 'selected' : '' }}>PTY (Pegawai Tetap Yayasan)</option>
                    <option value="PTT" {{ old('employee_type') === 'PTT' ? 'selected' : '' }}>PTT (Pegawai Tidak Tetap / Kontrak 11 Bln)</option>
                    <option value="LAINNYA" {{ old('employee_type') === 'LAINNYA' ? 'selected' : '' }}>GPKS/M / TKPKS/M (Kontrak Mandiri Sekolah)</option>
                    <option value="PDM" {{ old('employee_type') === 'PDM' ? 'selected' : '' }}>PDM (Pengurus Majelis)</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Jabatan / Posisi <span class="text-red-500">*</span></label>
                <input type="text" name="position" value="{{ old('position') }}" required placeholder="Contoh: Guru Matematika / Staf TU" class="w-full px-3.5 py-2 text-sm bg-slate-50 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:bg-white focus:outline-none">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Tanggal Mulai Tugas (TMT) <span class="text-red-500">*</span></label>
                <input type="date" name="join_date" value="{{ old('join_date', now()->format('Y-m-d')) }}" required class="w-full px-3.5 py-2 text-sm bg-slate-50 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:bg-white focus:outline-none">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Nominal Gaji Pokok (Rp)</label>
                <input type="number" name="basic_salary" value="{{ old('basic_salary', 0) }}" min="0" step="10000" class="w-full px-3.5 py-2 text-sm bg-slate-50 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:bg-white focus:outline-none">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1 flex items-center justify-between">
                    <span>Tahun Pensiun (BUP)</span>
                    <span class="text-[10px] text-blue-600 font-normal">Otomatis jika kosong</span>
                </label>
                <input type="number" id="retirement_year" name="retirement_year" value="{{ old('retirement_year') }}" placeholder="Contoh: 2045" min="1970" max="2100" class="w-full px-3.5 py-2 text-sm bg-slate-50 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:bg-white focus:outline-none font-mono">
                <span class="text-[10px] text-slate-400 mt-1 block">Guru: 60 th | Karyawan/Tendik: 58 th</span>
            </div>
        </div>
    </div>

    <!-- 4. SERTIFIKASI SEKOLAH IDEOLOGI MUHAMMADIYAH (SIM) -->
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
        <div class="flex items-center justify-between mb-4 pb-2 border-b border-slate-100">
            <h3 class="text-base font-bold text-slate-800 flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-600"></span>
                <span>Sertifikasi Sekolah Ideologi Muhammadiyah (SIM)</span>
            </h3>
            <span class="text-[11px] font-bold px-2 py-0.5 rounded bg-emerald-100 text-emerald-800 border border-emerald-300">
                Sertifikat CMI / Baitul Arqam
            </span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Nomor Sertifikat SIM</label>
                <input type="text" name="sim_certificate_number" value="{{ old('sim_certificate_number') }}" placeholder="Contoh: 007012026..." class="w-full px-3.5 py-2 text-sm bg-slate-50 border border-slate-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:outline-none font-mono">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Nilai / Predikat Kelulusan</label>
                <input type="text" name="sim_grade" value="{{ old('sim_grade') }}" placeholder="Contoh: A / Sangat Baik / 88.5" class="w-full px-3.5 py-2 text-sm bg-slate-50 border border-slate-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:outline-none">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Tanggal Terbit Sertifikat</label>
                <input type="date" name="sim_date" value="{{ old('sim_date') }}" class="w-full px-3.5 py-2 text-sm bg-slate-50 border border-slate-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:outline-none">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Upload Berkas PDF Sertifikat</label>
                <input type="file" name="sim_certificate_file" accept=".pdf,.jpg,.jpeg,.png" class="w-full px-3 py-1.5 text-xs bg-slate-50 border border-slate-300 rounded-xl focus:outline-none file:mr-3 file:py-1 file:px-2.5 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100">
                <p class="text-[10px] text-slate-400 mt-1">Maks. 5 MB (PDF/Gambar)</p>
            </div>
        </div>
    </div>

    <!-- 5. PRESTASI PEGAWAI (LOKAL, NASIONAL, INTERNASIONAL) -->
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
        <h3 class="text-base font-bold text-slate-800 mb-2 flex items-center gap-2">
            <span class="w-2.5 h-2.5 rounded-full bg-indigo-600"></span>
            <span>Portofolio Prestasi Pegawai</span>
        </h3>
        <p class="text-xs text-slate-500 mb-4">Catatan prestasi kejuaraan, penghargaan keilmuan, atau inovasi (Lokal, Nasional, Internasional)</p>

        <div class="space-y-3">
            @for($i = 0; $i < 2; $i++)
            <div class="p-3 bg-slate-50 rounded-xl border border-slate-200 grid grid-cols-1 sm:grid-cols-4 gap-3">
                <div>
                    <label class="block text-[11px] font-semibold text-slate-600 mb-0.5">Tingkat Prestasi</label>
                    <select name="achievements[{{ $i }}][level]" class="w-full px-2.5 py-1.5 text-xs bg-white border border-slate-300 rounded-lg">
                        <option value="lokal">Tingkat Lokal / Kota</option>
                        <option value="provinsi">Tingkat Provinsi</option>
                        <option value="nasional">Tingkat Nasional</option>
                        <option value="internasional">Tingkat Internasional</option>
                    </select>
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-[11px] font-semibold text-slate-600 mb-0.5">Nama Prestasi / Penghargaan</label>
                    <input type="text" name="achievements[{{ $i }}][title]" placeholder="Contoh: Juara 1 Guru Inovatif..." class="w-full px-2.5 py-1.5 text-xs bg-white border border-slate-300 rounded-lg">
                </div>
                <div>
                    <label class="block text-[11px] font-semibold text-slate-600 mb-0.5">Tahun</label>
                    <input type="number" name="achievements[{{ $i }}][year]" value="{{ date('Y') }}" class="w-full px-2.5 py-1.5 text-xs bg-white border border-slate-300 rounded-lg">
                </div>
            </div>
            @endfor
        </div>
    </div>

    <!-- 6. INPUT NILAI DP3 (PENILAIAN KINERJA) -->
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
        <h3 class="text-base font-bold text-slate-800 mb-2 flex items-center gap-2">
            <span class="w-2.5 h-2.5 rounded-full bg-purple-600"></span>
            <span>Penilaian Prestasi Kerja (DP3)</span>
        </h3>
        <p class="text-xs text-slate-500 mb-4">Input skor evaluasi kinerja berkala pegawai</p>

        <div class="p-3 bg-slate-50 rounded-xl border border-slate-200 grid grid-cols-1 sm:grid-cols-4 gap-3">
            <div>
                <label class="block text-[11px] font-semibold text-slate-600 mb-0.5">Tahun Penilaian</label>
                <input type="number" name="dp3_scores[0][year]" value="{{ date('Y') - 1 }}" class="w-full px-2.5 py-1.5 text-xs bg-white border border-slate-300 rounded-lg">
            </div>
            <div>
                <label class="block text-[11px] font-semibold text-slate-600 mb-0.5">Nilai Angka DP3</label>
                <input type="number" step="0.01" name="dp3_scores[0][score]" placeholder="Contoh: 86.50" class="w-full px-2.5 py-1.5 text-xs bg-white border border-slate-300 rounded-lg">
            </div>
            <div>
                <label class="block text-[11px] font-semibold text-slate-600 mb-0.5">Predikat</label>
                <select name="dp3_scores[0][predicate]" class="w-full px-2.5 py-1.5 text-xs bg-white border border-slate-300 rounded-lg">
                    <option value="Amat Baik">Amat Baik</option>
                    <option value="Baik" selected>Baik</option>
                    <option value="Cukup">Cukup</option>
                    <option value="Kurang">Kurang</option>
                </select>
            </div>
            <div>
                <label class="block text-[11px] font-semibold text-slate-600 mb-0.5">Catatan Evaluasi</label>
                <input type="text" name="dp3_scores[0][notes]" placeholder="Catatan Kepala Sekolah..." class="w-full px-2.5 py-1.5 text-xs bg-white border border-slate-300 rounded-lg">
            </div>
        </div>
    </div>

    <!-- 7. RIWAYAT PENDIDIKAN -->
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
        <h3 class="text-base font-bold text-slate-800 mb-4 pb-2 border-b border-slate-100 flex items-center gap-2">
            <span class="w-2.5 h-2.5 rounded-full bg-emerald-600"></span>
            <span>Riwayat Pendidikan</span>
        </h3>

        <div class="space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs text-slate-600 mb-0.5 font-medium">- S1 di (Universitas / Institut / STKIP)</label>
                    <input type="text" name="edu_s1_school" value="{{ old('edu_s1_school') }}" placeholder="Nama Kampus..." class="w-full px-3 py-1.5 text-sm bg-slate-50 border border-slate-300 rounded-lg">
                </div>
                <div>
                    <label class="block text-xs text-slate-600 mb-0.5 font-medium">Bidang Ilmu / Jurusan</label>
                    <input type="text" name="edu_s1_major" value="{{ old('edu_s1_major') }}" placeholder="Bidang Ilmu..." class="w-full px-3 py-1.5 text-sm bg-slate-50 border border-slate-300 rounded-lg">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs text-slate-600 mb-0.5 font-medium">- S2 di (Universitas / Institut)</label>
                    <input type="text" name="edu_s2_school" value="{{ old('edu_s2_school') }}" placeholder="Nama Kampus..." class="w-full px-3 py-1.5 text-sm bg-slate-50 border border-slate-300 rounded-lg">
                </div>
                <div>
                    <label class="block text-xs text-slate-600 mb-0.5 font-medium">Bidang Ilmu / Jurusan</label>
                    <input type="text" name="edu_s2_major" value="{{ old('edu_s2_major') }}" placeholder="Bidang Ilmu..." class="w-full px-3 py-1.5 text-sm bg-slate-50 border border-slate-300 rounded-lg">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-3 border-t border-slate-100">
                <div>
                    <label class="block text-xs text-slate-600 mb-0.5 font-medium">- SMA / MA / SMK di</label>
                    <input type="text" name="edu_sma" value="{{ old('edu_sma') }}" placeholder="Nama SMA/MA/SMK..." class="w-full px-3 py-1.5 text-sm bg-slate-50 border border-slate-300 rounded-lg">
                </div>
                <div>
                    <label class="block text-xs text-slate-600 mb-0.5 font-medium">- SMP / MTs di</label>
                    <input type="text" name="edu_smp" value="{{ old('edu_smp') }}" placeholder="Nama SMP/MTs..." class="w-full px-3 py-1.5 text-sm bg-slate-50 border border-slate-300 rounded-lg">
                </div>
                <div>
                    <label class="block text-xs text-slate-600 mb-0.5 font-medium">- SD / MI di</label>
                    <input type="text" name="edu_sd" value="{{ old('edu_sd') }}" placeholder="Nama SD/MI..." class="w-full px-3 py-1.5 text-sm bg-slate-50 border border-slate-300 rounded-lg">
                </div>
            </div>
        </div>
    </div>

    <!-- Tombol Simpan -->
    <div class="flex items-center justify-end gap-3 pt-4">
        <a href="{{ route('employees.index') }}" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-semibold rounded-xl transition">
            Batal
        </a>
        <button type="submit" id="btnSubmit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-xl shadow-sm transition shadow-blue-500/20 flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            <span>Simpan Pegawai Baru</span>
        </button>
    </div>
</form>
@endsection

@push('scripts')
<script>
// Filter Unit Kerja Berdasarkan Jenjang AUM
function filterCreateUnits() {
    const jenis = document.getElementById('create_jenis_aum')?.value;
    const unitSelect = document.getElementById('create_unit_id');
    if (!unitSelect) return;

    const options = unitSelect.querySelectorAll('option');
    options.forEach(opt => {
        if (!opt.value) {
            opt.hidden = false;
            opt.disabled = false;
            opt.style.display = '';
            return;
        }
        const level = opt.getAttribute('data-level');
        if (!jenis || level === jenis) {
            opt.hidden = false;
            opt.disabled = false;
            opt.style.display = '';
        } else {
            opt.hidden = true;
            opt.disabled = true;
            opt.style.display = 'none';
        }
    });

    if (unitSelect.selectedOptions.length > 0 && unitSelect.selectedOptions[0].disabled) {
        unitSelect.value = '';
    }
}

// Real-time NPM Verification Checker via AJAX
let npmTimer = null;
const inputNpm = document.getElementById('input_npm');
const npmBadge = document.getElementById('npm_status_badge');
const npmFeedback = document.getElementById('npm_feedback');
const npmLoading = document.getElementById('npm_loading');

if (inputNpm) {
    inputNpm.addEventListener('input', function() {
        clearTimeout(npmTimer);
        const val = this.value.trim();

        if (!val) {
            npmBadge.classList.add('hidden');
            npmFeedback.innerHTML = 'Sistem akan memvalidasi nomor ini secara otomatis terhadap Bank Data Master NPM PDM Kota Yogyakarta.';
            npmFeedback.className = 'text-[11px] mt-1.5 font-medium text-slate-600';
            return;
        }

        npmLoading.classList.remove('hidden');
        npmTimer = setTimeout(() => {
            fetch(`/api/check-npm?npm=${encodeURIComponent(val)}`)
                .then(res => res.json())
                .then(data => {
                    npmLoading.classList.add('hidden');
                    npmBadge.classList.remove('hidden');

                    if (data.status === 'valid') {
                        npmBadge.className = 'text-[10px] font-bold px-2 py-0.5 rounded bg-emerald-100 text-emerald-800 border border-emerald-300';
                        npmBadge.textContent = '✓ Terdaftar di Bank Data PDM';
                        npmFeedback.className = 'text-[11px] mt-1.5 font-medium text-emerald-600';
                        npmFeedback.innerHTML = `<strong>Tervalidasi:</strong> ${data.message} ${data.registered_name ? '(Nama di Buku Induk: ' + data.registered_name + ')' : ''}`;
                    } else if (data.status === 'warning') {
                        npmBadge.className = 'text-[10px] font-bold px-2 py-0.5 rounded bg-amber-100 text-amber-800 border border-amber-300';
                        npmBadge.textContent = 'ℹ️ NPM Baru';
                        npmFeedback.className = 'text-[11px] mt-1.5 font-medium text-amber-700';
                        npmFeedback.innerHTML = `<strong>Informasi:</strong> ${data.message} Tetap dapat disimpan dan akan disinkronkan ke Bank Data PDM.`;
                    } else {
                        npmBadge.className = 'text-[10px] font-bold px-2 py-0.5 rounded bg-rose-100 text-rose-800 border border-rose-300';
                        npmBadge.textContent = '✕ Duplikat / Tidak Valid';
                        npmFeedback.className = 'text-[11px] mt-1.5 font-medium text-rose-600';
                        npmFeedback.innerHTML = `<strong>Peringatan:</strong> ${data.message}`;
                    }
                })
                .catch(() => {
                    npmLoading.classList.add('hidden');
                });
        }, 400);
    });
}

// Auto-kalkulasi Tahun Batas Usia Pensiun (BUP) berdasarkan Tanggal Lahir & Jenis Pegawai
const birthInput = document.querySelector('input[name="birth_date"]');
const typeSelect = document.querySelector('select[name="employee_type"]');
const retirementInput = document.getElementById('retirement_year');

function calculateRetirementYear() {
    if (!birthInput || !retirementInput) return;
    const bDate = birthInput.value;
    if (!bDate) return;

    const bYear = parseInt(bDate.split('-')[0], 10);
    if (!bYear || isNaN(bYear)) return;

    const isGuru = typeSelect ? ['GTY', 'GTT', 'guru'].includes(typeSelect.value) : true;
    const pensionAge = isGuru ? 60 : 58;
    const calculatedYear = bYear + pensionAge;

    if (!retirementInput.value || retirementInput.dataset.autoFilled === 'true') {
        retirementInput.value = calculatedYear;
        retirementInput.dataset.autoFilled = 'true';
    }
}

if (birthInput) birthInput.addEventListener('change', calculateRetirementYear);
if (typeSelect) typeSelect.addEventListener('change', calculateRetirementYear);
if (retirementInput) {
    retirementInput.addEventListener('input', function() {
        this.dataset.autoFilled = 'false';
    });
}

document.addEventListener('DOMContentLoaded', function() {
    filterCreateUnits();
    calculateRetirementYear();
});
</script>
@endpush

@extends('layouts.app')

@section('title', 'Biodata Pegawai - ' . $employee->full_name)

@section('content')
<div class="max-w-5xl mx-auto space-y-6">

    <!-- Top Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('employees.index') }}" class="p-2.5 bg-white border border-slate-200 hover:bg-slate-50 text-slate-600 rounded-xl transition shadow-xs" title="Kembali ke Daftar Pegawai">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <div>
                <h1 class="text-xl sm:text-2xl font-bold text-slate-800 tracking-tight">Biodata Pegawai</h1>
                <p class="text-xs text-slate-500 mt-0.5">{{ $employee->unit->name ?? 'Unit Sekolah PDM Kota Yogyakarta' }}</p>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('employees.print_pdf', $employee->id) }}" target="_blank" class="inline-flex items-center gap-2 px-3.5 py-2 bg-slate-800 hover:bg-slate-900 text-white text-xs font-semibold rounded-xl shadow-xs transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                <span>Cetak PDF</span>
            </a>

            @if(!auth()->user()->isKetuaMajelis())
            <a href="{{ route('employees.edit', $employee->id) }}" class="inline-flex items-center gap-2 px-3.5 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-xl shadow-xs transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                <span>Ubah Data</span>
            </a>
            @endif

            @if(auth()->user()->isAdminUtama())
            <form action="{{ route('employees.destroy', $employee->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data pegawai {{ $employee->full_name }}?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="p-2 bg-rose-50 hover:bg-rose-600 text-rose-600 hover:text-white border border-rose-200 text-xs font-semibold rounded-xl transition" title="Hapus Data Pegawai (Khusus Admin Utama)">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                </button>
            </form>
            @endif
        </div>
    </div>

    <!-- Header Profil Sederhana (Avatar + Nama & Jabatan) -->
    <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs flex flex-col sm:flex-row items-start sm:items-center gap-4">
        <div class="w-16 h-16 rounded-2xl bg-gradient-to-tr from-blue-600 to-indigo-600 text-white flex items-center justify-center font-bold text-xl shadow-sm flex-shrink-0">
            {{ substr($employee->full_name, 0, 2) }}
        </div>
        <div class="flex-1 min-w-0">
            <h2 class="text-lg sm:text-xl font-bold text-slate-900 truncate">
                {{ $employee->full_name_with_titles }}
            </h2>
            <div class="flex flex-wrap items-center gap-2 mt-1 text-xs text-slate-500">
                <span class="font-medium text-slate-700">{{ $employee->position ?? 'Tenaga Pendidik / Kependidikan' }}</span>
                <span>•</span>
                <span>{{ $employee->unit->name ?? '-' }}</span>
                <span>•</span>
                @if($employee->status === 'wafat')
                    <span class="px-2.5 py-0.5 rounded-md font-bold bg-stone-900 text-stone-100 shadow-xs">
                        Wafat (Meninggal Dunia)
                    </span>
                @elseif($employee->status === 'pensiun')
                    <span class="px-2.5 py-0.5 rounded-md font-bold bg-purple-100 text-purple-800 border border-purple-200">
                        Purna Tugas / Pensiun
                    </span>
                @elseif($employee->status === 'cuti')
                    <span class="px-2.5 py-0.5 rounded-md font-semibold bg-amber-50 text-amber-800 border border-amber-200">
                        Cuti
                    </span>
                @elseif($employee->status === 'mutasi')
                    <span class="px-2.5 py-0.5 rounded-md font-semibold bg-blue-50 text-blue-800 border border-blue-200">
                        Mutasi Keluar
                    </span>
                @elseif($employee->status === 'nonaktif')
                    <span class="px-2.5 py-0.5 rounded-md font-semibold bg-slate-100 text-slate-600">
                        Non-Aktif
                    </span>
                @else
                    <span class="px-2.5 py-0.5 rounded-md font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                        Pegawai Aktif
                    </span>
                @endif
            </div>
        </div>
    </div>

    <!-- Grid Data: Identitas & Penugasan -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

        <!-- 1. IDENTITAS DIRI & MULTI-ID -->
        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-xs space-y-4">
            <h3 class="text-sm font-bold text-slate-800 pb-2 border-b border-slate-100 flex items-center gap-2">
                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                <span>Identitas & Nomor Registrasi</span>
            </h3>

            <dl class="space-y-2.5 text-xs">
                <div class="flex justify-between py-1 border-b border-slate-50">
                    <dt class="text-slate-500 font-medium">NPM (Nomor Pegawai Muhammadiyah)</dt>
                    <dd class="font-mono font-bold text-slate-900">{{ $employee->npm ?: '-' }}</dd>
                </div>
                <div class="flex justify-between py-1 border-b border-slate-50">
                    <dt class="text-slate-500 font-medium">NBM (Nomor Baku Muhammadiyah)</dt>
                    <dd class="font-mono font-bold text-slate-900">{{ $employee->nbm ?: '-' }}</dd>
                </div>
                <div class="flex justify-between py-1 border-b border-slate-50">
                    <dt class="text-slate-500 font-medium">NIK (KTP)</dt>
                    <dd class="font-mono font-semibold text-slate-900">{{ $employee->nik ?: '-' }}</dd>
                </div>
                <div class="flex justify-between py-1">
                    <dt class="text-slate-500 font-medium">NRG (Nomor Registrasi Guru)</dt>
                    <dd class="font-mono text-slate-800">{{ $employee->nrg ?: '-' }}</dd>
                </div>
            </dl>
        </div>

        <!-- 2. DATA PRIBADI & KONTAK -->
        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-xs space-y-4">
            <h3 class="text-sm font-bold text-slate-800 pb-2 border-b border-slate-100 flex items-center gap-2">
                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                <span>Biodata Pribadi & Kontak</span>
            </h3>

            <dl class="space-y-2.5 text-xs">
                <div class="flex justify-between py-1 border-b border-slate-50">
                    <dt class="text-slate-500 font-medium">Tempat, Tanggal Lahir</dt>
                    <dd class="font-semibold text-slate-900">
                        @if($employee->birth_place || $employee->birth_date)
                            {{ $employee->birth_place ?? '-' }}, {{ $employee->birth_date ? $employee->birth_date->translatedFormat('d F Y') : '-' }}
                        @else
                            -
                        @endif
                    </dd>
                </div>
                <div class="flex justify-between py-1 border-b border-slate-50">
                    <dt class="text-slate-500 font-medium">Jenis Kelamin</dt>
                    <dd class="text-slate-800">{{ $employee->gender === 'L' ? 'Laki-laki' : 'Perempuan' }}</dd>
                </div>
                <div class="flex justify-between py-1 border-b border-slate-50">
                    <dt class="text-slate-500 font-medium">Agama</dt>
                    <dd class="text-slate-800">{{ $employee->religion ?? 'Islam' }}</dd>
                </div>
                <div class="flex justify-between py-1 border-b border-slate-50">
                    <dt class="text-slate-500 font-medium">Nomor Handphone / WhatsApp</dt>
                    <dd class="font-mono text-slate-900">{{ $employee->phone_number ?: '-' }}</dd>
                </div>
                <div class="flex justify-between py-1 border-b border-slate-50">
                    <dt class="text-slate-500 font-medium">Alamat Email</dt>
                    <dd class="text-slate-900">{{ $employee->email ?: '-' }}</dd>
                </div>
                <div class="flex justify-between py-1">
                    <dt class="text-slate-500 font-medium">Alamat Domisili</dt>
                    <dd class="text-slate-800 text-right max-w-[200px] sm:max-w-xs truncate">{{ $employee->address ?: '-' }}</dd>
                </div>
            </dl>
        </div>

        <!-- 3. INFORMASI PENUGASAN & STATUS KEPEGAWAIAN -->
        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-xs space-y-4">
            <h3 class="text-sm font-bold text-slate-800 pb-2 border-b border-slate-100 flex items-center gap-2">
                <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                <span>Penugasan & Status Kerja</span>
            </h3>

            <dl class="space-y-2.5 text-xs">
                <div class="flex justify-between py-1 border-b border-slate-50">
                    <dt class="text-slate-500 font-medium">Unit Kerja (AUM)</dt>
                    <dd class="font-semibold text-slate-900">{{ $employee->unit->name ?? '-' }}</dd>
                </div>
                <div class="flex justify-between py-1 border-b border-slate-50">
                    <dt class="text-slate-500 font-medium">Jabatan</dt>
                    <dd class="font-semibold text-slate-900">{{ $employee->position ?? '-' }}</dd>
                </div>
                <div class="flex justify-between py-1 border-b border-slate-50">
                    <dt class="text-slate-500 font-medium">Status Kepegawaian</dt>
                    <dd class="font-semibold uppercase text-slate-800">{{ $employee->employment_status ?? '-' }}</dd>
                </div>
                <div class="flex justify-between py-1 border-b border-slate-50">
                    <dt class="text-slate-500 font-medium">Tanggal Mulai Tugas (TMT)</dt>
                    <dd class="text-slate-800">{{ $employee->join_date ? $employee->join_date->translatedFormat('d F Y') : '-' }}</dd>
                </div>
                <div class="flex justify-between py-1 border-b border-slate-50">
                    <dt class="text-slate-500 font-medium">Tahun Pensiun (BUP)</dt>
                    <dd class="font-mono font-bold text-slate-900">
                        {{ $employee->retirement_year ?? $employee->calculateRetirementYear() ?? '-' }}
                        <span class="text-[10px] text-slate-400 font-normal">({{ $employee->isTeacher() ? 'BUP 60 th' : 'BUP 58 th' }})</span>
                    </dd>
                </div>
                <div class="flex justify-between py-1">
                    <dt class="text-slate-500 font-medium">Gaji Pokok</dt>
                    <dd class="font-bold text-slate-900">Rp {{ number_format((float)$employee->basic_salary, 0, ',', '.') }}</dd>
                </div>
            </dl>
        </div>

        <!-- 4. SERTIFIKASI SEKOLAH IDEOLOGI MUHAMMADIYAH (SIM) -->
        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-xs space-y-4">
            <h3 class="text-sm font-bold text-slate-800 pb-2 border-b border-slate-100 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    <span>Sertifikasi Ideologi (SIM / CMI)</span>
                </div>
                @if($employee->sim_certificate_number)
                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-md bg-emerald-50 text-emerald-700 border border-emerald-200">Lulus</span>
                @else
                    <span class="text-[10px] font-medium px-2 py-0.5 rounded-md bg-slate-100 text-slate-500">Belum Ada</span>
                @endif
            </h3>

            <dl class="space-y-2.5 text-xs">
                <div class="flex justify-between py-1 border-b border-slate-50">
                    <dt class="text-slate-500 font-medium">Nomor Sertifikat</dt>
                    <dd class="font-mono font-bold text-slate-900">{{ $employee->sim_certificate_number ?: '-' }}</dd>
                </div>
                <div class="flex justify-between py-1 border-b border-slate-50">
                    <dt class="text-slate-500 font-medium">Predikat / Nilai Kelulusan</dt>
                    <dd class="font-semibold text-slate-800">{{ $employee->sim_grade ?: '-' }}</dd>
                </div>
                <div class="flex justify-between py-1 border-b border-slate-50">
                    <dt class="text-slate-500 font-medium">Tanggal Sertifikat</dt>
                    <dd class="text-slate-800">{{ $employee->sim_date ? \Carbon\Carbon::parse($employee->sim_date)->translatedFormat('d F Y') : '-' }}</dd>
                </div>
                <div class="flex justify-between py-1">
                    <dt class="text-slate-500 font-medium">Berkas Sertifikat</dt>
                    <dd>
                        @if($employee->sim_certificate_file)
                            <a href="{{ $employee->sim_certificate_file }}" target="_blank" class="text-blue-600 hover:underline font-semibold flex items-center gap-1">
                                <span>Unduh PDF</span>
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                            </a>
                        @else
                            <span class="text-slate-400">Belum diunggah</span>
                        @endif
                    </dd>
                </div>
            </dl>
        </div>

    </div>

    <!-- 5. PRESTASI & PENILAIAN DP3 (JIKA ADA DATA) -->
    @php
        $achs = $employee->achievements ?? [];
        $dp3s = $employee->dp3_scores ?? [];
    @endphp

    @if(count($achs) > 0 || count($dp3s) > 0)
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        
        <!-- Prestasi -->
        @if(count($achs) > 0)
        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-xs space-y-3">
            <h3 class="text-sm font-bold text-slate-800 pb-2 border-b border-slate-100 flex items-center gap-2">
                <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
                <span>Portofolio Prestasi Pegawai</span>
            </h3>
            <ul class="space-y-2 text-xs">
                @foreach($achs as $ach)
                    <li class="p-2.5 rounded-xl bg-slate-50 border border-slate-100 flex items-start justify-between gap-2">
                        <div>
                            <span class="font-bold text-slate-900 block">{{ $ach['title'] ?? '-' }}</span>
                            <span class="text-[11px] text-slate-500">{{ $ach['description'] ?? 'Penghargaan resmi' }}</span>
                        </div>
                        <div class="text-right flex-shrink-0">
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded bg-blue-100 text-blue-800 uppercase">{{ $ach['level'] ?? 'Lokal' }}</span>
                            <span class="text-[11px] text-slate-400 block mt-0.5">{{ $ach['year'] ?? '-' }}</span>
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>
        @endif

        <!-- DP3 -->
        @if(count($dp3s) > 0)
        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-xs space-y-3">
            <h3 class="text-sm font-bold text-slate-800 pb-2 border-b border-slate-100 flex items-center gap-2">
                <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                <span>Penilaian Prestasi Kerja (DP3)</span>
            </h3>
            <div class="space-y-2 text-xs">
                @foreach($dp3s as $d)
                    <div class="flex items-center justify-between p-2.5 rounded-xl bg-slate-50 border border-slate-100">
                        <div>
                            <span class="font-bold text-slate-900">Tahun {{ $d['year'] ?? '-' }}</span>
                            <span class="text-[11px] text-slate-500 block">{{ $d['notes'] ?? 'Nilai Evaluasi Kinerja Tahunan' }}</span>
                        </div>
                        <div class="text-right">
                            <span class="font-black text-blue-700 text-sm">{{ $d['score'] ?? '-' }}</span>
                            <span class="text-[10px] font-bold px-1.5 py-0.2 rounded bg-emerald-100 text-emerald-800 block mt-0.5">{{ $d['predicate'] ?? 'Baik' }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        @endif

    </div>
    @endif

    <!-- 6. RIWAYAT MUTASI PEGAWAI (JIKA ADA) -->
    @if($employee->mutations && $employee->mutations->count() > 0)
    <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-xs space-y-3">
        <h3 class="text-sm font-bold text-slate-800 pb-2 border-b border-slate-100 flex items-center gap-2">
            <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
            <span>Riwayat Mutasi Antar-Unit AUM</span>
        </h3>
        <div class="space-y-2 text-xs">
            @foreach($employee->mutations as $mut)
                <div class="flex flex-col sm:flex-row sm:items-center justify-between p-3 rounded-xl bg-slate-50 border border-slate-100 gap-2">
                    <div>
                        <div class="font-semibold text-slate-900">
                            Dari <span class="font-bold">{{ $mut->originUnit?->name ?? 'Unit Asal' }}</span> 
                            ke <span class="font-bold text-blue-700">{{ $mut->destinationUnit?->name ?? 'Unit Tujuan' }}</span>
                        </div>
                        <div class="text-[11px] text-slate-500 mt-0.5">
                            Posisi: {{ $mut->destination_position ?? '-' }} • SK: {{ $mut->sk_number ?? '-' }}
                        </div>
                    </div>
                    <div class="text-right">
                        <span class="font-bold text-slate-700">{{ $mut->effective_date ? $mut->effective_date->translatedFormat('d M Y') : '-' }}</span>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endif

</div>
@endsection

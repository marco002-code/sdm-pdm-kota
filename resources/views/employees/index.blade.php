@extends('layouts.app')

@section('content')
<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <h1 class="text-2xl md:text-3xl font-bold text-slate-800 tracking-tight">Data Induk Pegawai</h1>
        <p class="text-sm text-slate-500 mt-1">Bank Data Induk (Single Source of Truth) Pegawai PDM Kota Yogyakarta & Seluruh Unit Sekolah</p>
    </div>
    @if(!auth()->user()->isPegawai() && !auth()->user()->isKetuaMajelis())
    <div class="flex items-center gap-3">
        <a href="{{ route('employees.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-xl shadow-sm transition shadow-blue-500/20">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            <span>Tambah Pegawai Baru</span>
        </a>
    </div>
    @elseif(auth()->user()->isKetuaMajelis())
    <div class="flex items-center gap-2 px-3 py-1.5 bg-amber-50 text-amber-800 border border-amber-200 rounded-xl text-xs font-semibold shadow-2xs">
        <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
        <span>Mode Peninjauan (Read-Only)</span>
    </div>
    @endif
</div>

<!-- Filter & Search Bar -->
<div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200/80 mb-6">
    <form method="GET" action="{{ route('employees.index') }}" id="filterForm" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-12 gap-4">
        <!-- Search Input -->
        <div class="md:col-span-3">
            <label class="block text-xs font-semibold text-slate-600 mb-1.5">Cari Pegawai</label>
            <div class="relative">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Nama, NPM, NBM, NIK..." class="w-full pl-9 pr-3 py-2 text-sm bg-slate-50 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:bg-white focus:outline-none transition">
                <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>
        </div>

        @if(auth()->user()->isYayasanLevel())
        <!-- 1. Filter Jenis AUM (Pilih Jenis AUM Dulu) -->
        <div class="md:col-span-3">
            <label class="block text-xs font-semibold text-slate-600 mb-1.5 flex items-center justify-between">
                <span>1. Jenis AUM</span>
                <span class="text-[10px] text-blue-600 font-bold">Pilih Jenjang</span>
            </label>
            <select name="jenis_aum" id="filter_jenis_aum" onchange="filterUnitsByJenisAum()" class="w-full px-3 py-2 text-sm bg-blue-50/50 border border-blue-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:bg-white focus:outline-none transition font-semibold text-slate-800">
                <option value="">-- Semua Jenis AUM --</option>
                <option value="SD" {{ request('jenis_aum') === 'SD' ? 'selected' : '' }}>SD (Sekolah Dasar)</option>
                <option value="SMP" {{ request('jenis_aum') === 'SMP' ? 'selected' : '' }}>SMP (Sekolah Menengah Pertama)</option>
                <option value="SMA" {{ request('jenis_aum') === 'SMA' ? 'selected' : '' }}>SMA (Sekolah Menengah Atas)</option>
                <option value="SMK" {{ request('jenis_aum') === 'SMK' ? 'selected' : '' }}>SMK (Sekolah Menengah Kejuruan)</option>
                <option value="TK" {{ request('jenis_aum') === 'TK' ? 'selected' : '' }}>TK / ABA (Taman Kanak-Kanak)</option>
                <option value="TPA" {{ request('jenis_aum') === 'TPA' ? 'selected' : '' }}>TPA (Taman Pengasuhan Anak)</option>
                <option value="MA" {{ request('jenis_aum') === 'MA' ? 'selected' : '' }}>MA (Madrasah Aliyah)</option>
                <option value="YAYASAN" {{ request('jenis_aum') === 'YAYASAN' ? 'selected' : '' }}>Kantor PDM Kota Yogyakarta</option>
            </select>
        </div>

        <!-- 2. Filter Nama Unit Kerja AUM (Otomatis Tersortir Sesuai Jenis AUM) -->
        <div class="md:col-span-3">
            <label class="block text-xs font-semibold text-slate-600 mb-1.5 flex items-center justify-between">
                <span>2. Nama Unit Kerja AUM</span>
                <span class="text-[10px] text-slate-400" id="unitCountBadge">{{ count($units) }} unit</span>
            </label>
            <select name="unit_id" id="filter_unit_id" class="w-full px-3 py-2 text-sm bg-slate-50 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:bg-white focus:outline-none transition text-slate-800">
                <option value="">-- Semua Unit Kerja --</option>
                @foreach($units as $unit)
                    <option value="{{ $unit->id }}" data-level="{{ $unit->level }}" {{ request('unit_id') == $unit->id ? 'selected' : '' }}>
                        {{ $unit->name }} {{ $unit->npsn ? "({$unit->npsn})" : '' }}
                    </option>
                @endforeach
            </select>
        </div>
        @else
        <!-- Banner Domain Unit Kerja Tenaga Tata Usaha -->
        <div class="md:col-span-6 flex items-center">
            <div class="w-full px-4 py-2.5 bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200/80 rounded-xl flex items-center justify-between shadow-xs">
                <div class="flex items-center gap-2.5">
                    <div class="w-7 h-7 rounded-lg bg-blue-600 flex items-center justify-center text-white text-xs font-bold shadow-xs">
                        TU
                    </div>
                    <div>
                        <div class="text-[10px] uppercase font-bold text-blue-600 tracking-wider">Domain Unit Kerja Anda</div>
                        <div class="text-xs font-bold text-slate-800">{{ auth()->user()->unit->name ?? 'Unit Sekolah Anda' }}</div>
                    </div>
                </div>
                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-white border border-blue-200 text-blue-700 text-[10px] font-bold shadow-2xs">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                    <span>Terkunci Unit Sendiri</span>
                </span>
            </div>
        </div>
        @endif

        <!-- Kategori Pegawai -->
        <div class="{{ auth()->user()->isYayasanLevel() ? 'md:col-span-2' : 'md:col-span-2' }}">
            <label class="block text-xs font-semibold text-slate-600 mb-1.5">Kategori Pegawai</label>
            <select name="type" class="w-full px-3 py-2 text-sm bg-slate-50 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:bg-white focus:outline-none transition">
                <option value="">-- Semua Kategori --</option>
                <option value="PDM" {{ request('type') == 'PDM' ? 'selected' : '' }}>PDM (Majelis)</option>
                <option value="GTY" {{ request('type') == 'GTY' ? 'selected' : '' }}>GTY (Guru Tetap Yayasan)</option>
                <option value="GTT" {{ request('type') == 'GTT' ? 'selected' : '' }}>GTT (Guru Tidak Tetap)</option>
                <option value="PTY" {{ request('type') == 'PTY' ? 'selected' : '' }}>PTY (Pegawai Tetap Yayasan)</option>
                <option value="PTT" {{ request('type') == 'PTT' ? 'selected' : '' }}>PTT (Pegawai Tidak Tetap)</option>
                <option value="LAINNYA" {{ request('type') == 'LAINNYA' ? 'selected' : '' }}>Lainnya</option>
            </select>
        </div>

        <!-- Filter Status Keaktifan -->
        <div class="{{ auth()->user()->isYayasanLevel() ? 'md:col-span-2' : 'md:col-span-2' }}">
            <label class="block text-xs font-semibold text-slate-600 mb-1.5">Status Keaktifan</label>
            <select name="status" class="w-full px-3 py-2 text-sm bg-slate-50 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:bg-white focus:outline-none transition">
                <option value="">-- Semua Status --</option>
                <option value="aktif" {{ request('status') === 'aktif' ? 'selected' : '' }}>Aktif</option>
                <option value="cuti" {{ request('status') === 'cuti' ? 'selected' : '' }}>Cuti</option>
                <option value="mutasi" {{ request('status') === 'mutasi' ? 'selected' : '' }}>Mutasi</option>
                <option value="pensiun" {{ request('status') === 'pensiun' ? 'selected' : '' }}>Pensiun / Purna Tugas</option>
                <option value="nonaktif" {{ request('status') === 'nonaktif' ? 'selected' : '' }}>Non-Aktif</option>
                <option value="wafat" {{ request('status') === 'wafat' ? 'selected' : '' }}>Wafat (Meninggal)</option>
            </select>
        </div>

        <!-- Action Buttons -->
        <div class="flex items-end gap-2 {{ auth()->user()->isYayasanLevel() ? 'md:col-span-1' : 'md:col-span-1' }}">
            <button type="submit" class="w-full py-2 px-3 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-xl transition flex items-center justify-center gap-1 shadow-sm shadow-blue-500/20">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                <span>Cari</span>
            </button>
            @if(request()->anyFilled(['search', 'jenis_aum', 'unit_id', 'type', 'status']))
                <a href="{{ route('employees.index') }}" title="Reset Filter" class="py-2 px-2.5 bg-slate-200 hover:bg-slate-300 text-slate-700 text-sm font-semibold rounded-xl transition flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </a>
            @endif
        </div>
    </form>
</div>

<!-- Employees Table -->
<div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse text-sm">
            <thead>
                <tr class="bg-slate-50/80 border-b border-slate-200 text-xs font-bold text-slate-500 uppercase tracking-wider">
                    <th class="py-3.5 px-4">Pegawai</th>
                    <th class="py-3.5 px-4">NBM & Identitas</th>
                    <th class="py-3.5 px-4">Unit & Jabatan</th>
                    <th class="py-3.5 px-4">Status</th>
                    <th class="py-3.5 px-4">Kontak</th>
                    <th class="py-3.5 px-4 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($employees as $employee)
                <tr class="hover:bg-slate-50/60 transition">
                    <!-- Pegawai Name & Prefix/Suffix -->
                    <td class="py-4 px-4">
                        <div class="font-bold text-slate-800 text-base">
                            {{ $employee->full_name_with_titles }}
                        </div>
                    </td>

                    <!-- NPM, NBM & Identitas -->
                    <td class="py-4 px-4">
                        @if($employee->npm)
                        <div class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-amber-50 text-amber-900 border border-amber-300 rounded-lg text-xs font-mono font-bold">
                            <span class="text-amber-700 font-bold">NPM:</span>
                            <span>{{ $employee->npm }}</span>
                        </div>
                        @else
                        <div class="text-[11px] text-slate-400 italic">
                            Belum ada NPM
                        </div>
                        @endif
                        <div class="text-xs text-slate-600 mt-1 flex items-center gap-2">
                            <span>NBM: <strong>{{ $employee->nbm ?? '-' }}</strong></span>
                            <span class="text-slate-300">•</span>
                            <span class="font-mono text-[11px]">NIK: {{ $employee->nik }}</span>
                        </div>
                    </td>

                    <!-- Unit & Jabatan -->
                    <td class="py-4 px-4">
                        <span class="inline-block px-2.5 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                            {{ $employee->unit->code ?? 'Unit' }}
                        </span>
                        <div class="text-xs font-medium text-slate-700 mt-1">
                            {{ $employee->position }}
                        </div>
                    </td>

                    <!-- Kategori Pegawai -->
                    <td class="py-4 px-4">
                        @switch($employee->employee_type)
                            @case('PDM')
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold bg-slate-100 text-slate-800 border border-slate-300">
                                    <span class="w-1.5 h-1.5 rounded-full bg-slate-600"></span>
                                    <span>PDM (Majelis)</span>
                                </span>
                                @break
                            @case('GTY')
                            @case('guru')
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-blue-600"></span>
                                    <span>GTY</span>
                                    @if($employee->is_certified_educator)
                                        <span class="text-amber-500 font-bold ml-0.5" title="Tersertifikasi Pendidik">★</span>
                                    @endif
                                </span>
                                @break
                            @case('GTT')
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold bg-cyan-50 text-cyan-700 border border-cyan-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-cyan-500"></span>
                                    <span>GTT</span>
                                </span>
                                @break
                            @case('PTY')
                            @case('tendik')
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                                    <span>PTY</span>
                                </span>
                                @break
                            @case('PTT')
                            @case('karyawan')
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold bg-amber-50 text-amber-800 border border-amber-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-600"></span>
                                    <span>PTT</span>
                                </span>
                                @break
                            @default
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold bg-violet-50 text-violet-700 border border-violet-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-violet-600"></span>
                                    <span>{{ $employee->category_label ?? 'Lainnya' }}</span>
                                </span>
                        @endswitch

                        <div class="mt-1 flex flex-wrap items-center gap-1">
                            @if($employee->status === 'wafat')
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold bg-stone-900 text-stone-100 shadow-xs">
                                    <span>Wafat</span>
                                </span>
                            @elseif($employee->status === 'pensiun')
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold bg-purple-100 text-purple-800 border border-purple-200">
                                    <span>Purna Tugas</span>
                                </span>
                            @elseif($employee->status === 'nonaktif')
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-medium bg-slate-200 text-slate-700">
                                    <span>Non-Aktif</span>
                                </span>
                            @elseif($employee->status === 'cuti')
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-medium bg-amber-100 text-amber-800 border border-amber-200">
                                    <span>Cuti</span>
                                </span>
                            @elseif($employee->status === 'mutasi')
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-medium bg-blue-100 text-blue-800 border border-blue-200">
                                    <span>Mutasi</span>
                                </span>
                            @endif

                            @if($employee->retirement_year)
                                <span class="text-[10px] font-mono text-slate-500 bg-slate-100 px-1.5 py-0.5 rounded" title="Tahun Batas Usia Pensiun">
                                    BUP {{ $employee->retirement_year }}
                                </span>
                            @endif
                        </div>
                    </td>

                    <!-- Kontak -->
                    <td class="py-4 px-4 text-xs text-slate-600">
                        <div>{{ $employee->phone_number ?? '-' }}</div>
                        <div class="text-slate-400">{{ $employee->email ?? '-' }}</div>
                    </td>

                    <!-- Action Buttons -->
                    <td class="py-4 px-4 text-center">
                        <div class="inline-flex items-center gap-1.5">
                            <a href="{{ route('employees.show', $employee->id) }}" title="Lihat Formulir Biodata" class="p-2 bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white rounded-lg transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            </a>
                            <a href="{{ route('employees.print_pdf', $employee->id) }}" target="_blank" title="Cetak Dokumen Formulir (PDF)" class="p-2 bg-slate-100 text-slate-700 hover:bg-slate-800 hover:text-white rounded-lg transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                            </a>
                            @if(!auth()->user()->isKetuaMajelis())
                            <a href="{{ route('employees.edit', $employee->id) }}" title="Ubah Data" class="p-2 bg-slate-100 text-slate-600 hover:bg-slate-700 hover:text-white rounded-lg transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </a>
                            @endif
                            @if(auth()->user()->isAdminUtama())
                            <form action="{{ route('employees.destroy', $employee->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data pegawai {{ $employee->full_name }}? Data dapat dipulihkan melalui soft delete.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" title="Hapus Pegawai (Khusus Admin Utama)" class="p-2 bg-rose-50 text-rose-600 hover:bg-rose-600 hover:text-white rounded-lg transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="py-12 text-center text-slate-400">
                        <svg class="w-12 h-12 mx-auto text-slate-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        <p class="font-medium text-slate-600">Tidak ada data pegawai yang ditemukan.</p>
                        <p class="text-xs text-slate-400 mt-1">Coba sesuaikan kata kunci pencarian atau filter.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($employees->hasPages())
    <div class="p-4 border-t border-slate-200 bg-slate-50/50">
        {{ $employees->links() }}
    </div>
    @endif
</div>
@endsection

@if(auth()->user()->isYayasanLevel())
@push('scripts')
<script>
function filterUnitsByJenisAum() {
    const jenisSelect = document.getElementById('filter_jenis_aum');
    const unitSelect = document.getElementById('filter_unit_id');
    const badge = document.getElementById('unitCountBadge');
    if (!jenisSelect || !unitSelect) return;

    const selectedLevel = jenisSelect.value;
    const currentUnitVal = unitSelect.value;

    let visibleCount = 0;
    let hasSelectedVisible = false;

    Array.from(unitSelect.options).forEach(opt => {
        if (!opt.value) {
            opt.hidden = false;
            opt.disabled = false;
            opt.style.display = '';
            return;
        }
        const optLevel = opt.getAttribute('data-level');
        if (!selectedLevel || optLevel === selectedLevel) {
            opt.hidden = false;
            opt.disabled = false;
            opt.style.display = '';
            visibleCount++;
            if (opt.value === currentUnitVal) hasSelectedVisible = true;
        } else {
            opt.hidden = true;
            opt.disabled = true;
            opt.style.display = 'none';
        }
    });

    if (badge) {
        badge.textContent = selectedLevel ? `${visibleCount} unit ${selectedLevel}` : `${visibleCount} unit`;
    }

    if (currentUnitVal && !hasSelectedVisible) {
        unitSelect.value = '';
    }
}

document.addEventListener('DOMContentLoaded', function() {
    filterUnitsByJenisAum();
});
</script>
@endpush
@endif

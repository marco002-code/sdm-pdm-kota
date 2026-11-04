@extends('layouts.app')

@section('content')
<div class="space-y-4">
    <!-- Header Modul Kenaikan Pangkat -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2 py-0.5 rounded-md text-[10px] font-bold uppercase tracking-wider bg-emerald-100 text-emerald-800">Karier & Kepangkatan</span>
                <span class="text-xs text-slate-400">•</span>
                <span class="text-xs text-slate-500 font-medium">Majelis Dikdasmen & PNF PDM Kota Yogyakarta</span>
            </div>
            <h1 class="text-xl lg:text-2xl font-bold text-slate-800 tracking-tight mt-0.5">Modul Kenaikan Pangkat & Alih Status Pegawai</h1>
            <p class="text-xs text-slate-500 mt-0.5">Kenaikan pangkat berkala, penyesuaian ijazah, dan alih status GTT &rarr; GTY serta PTT &rarr; PTY</p>
        </div>
        @if(!auth()->user()->isPegawai() && !auth()->user()->isKetuaMajelis())
        <div class="flex items-center gap-2">
            <a href="{{ route('promotions.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded-xl shadow-xs transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>Usulkan Kenaikan Pangkat / Alih Status</span>
            </a>
        </div>
        @elseif(auth()->user()->isKetuaMajelis())
        <div class="flex items-center gap-2 px-3 py-1.5 bg-amber-50 text-amber-800 border border-amber-200 rounded-xl text-xs font-semibold shadow-2xs">
            <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
            <span>Hak Akses: Sidang & Persetujuan SK PDM</span>
        </div>
        @endif
    </div>

    <!-- Metrik Kenaikan Pangkat -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        <div class="bg-white/90 backdrop-blur-xl p-3.5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500">Total Pengajuan</span>
                <div class="text-xl font-black text-slate-800 mt-0.5">{{ $counts['total'] }} <span class="text-xs font-normal text-slate-400">usulan</span></div>
            </div>
            <div class="w-8 h-8 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center font-bold text-xs">
                ALL
            </div>
        </div>

        <div class="bg-white/90 backdrop-blur-xl p-3.5 rounded-2xl border border-amber-200/80 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-amber-700">Menunggu Verifikasi</span>
                <div class="text-xl font-black text-amber-700 mt-0.5">{{ $counts['diajukan'] }} <span class="text-xs font-normal text-amber-500">usulan</span></div>
            </div>
            <div class="w-8 h-8 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center font-bold text-xs">
                NEW
            </div>
        </div>

        <div class="bg-white/90 backdrop-blur-xl p-3.5 rounded-2xl border border-purple-200/80 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-purple-700">Sidang Tim Majelis</span>
                <div class="text-xl font-black text-purple-700 mt-0.5">{{ $counts['sidang'] }} <span class="text-xs font-normal text-purple-500">berkas</span></div>
            </div>
            <div class="w-8 h-8 rounded-xl bg-purple-100 text-purple-700 flex items-center justify-center font-bold text-xs">
                REV
            </div>
        </div>

        <div class="bg-white/90 backdrop-blur-xl p-3.5 rounded-2xl border border-emerald-200/80 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-700">Disetujui & SK Terbit</span>
                <div class="text-xl font-black text-emerald-700 mt-0.5">{{ $counts['disetujui'] }} <span class="text-xs font-normal text-emerald-500">pegawai</span></div>
            </div>
            <div class="w-8 h-8 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold text-xs">
                ACC
            </div>
        </div>
    </div>

    <!-- Filter & Tabel Pengajuan Kenaikan Pangkat -->
    <div class="bg-white/95 backdrop-blur-xl rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="p-4 border-b border-slate-100 flex flex-col md:flex-row md:items-center md:justify-between gap-3">
            <div>
                <h2 class="text-base font-bold text-slate-800">Daftar Usulan Kenaikan Pangkat & Alih Status</h2>
                <p class="text-xs text-slate-500">Penilaian Angka Kredit (PAK), Penilaian Kinerja Guru (PKG), dan keaktifan Persyarikatan Muhammadiyah</p>
            </div>

            <!-- Filter -->
            <form method="GET" action="{{ route('promotions.index') }}" class="flex flex-wrap items-center gap-2">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari Nama / NBM..." class="px-3 py-1.5 text-xs rounded-xl border border-slate-200 focus:outline-none focus:border-blue-500 w-36">
                <select name="type" class="px-2.5 py-1.5 text-xs rounded-xl border border-slate-200 focus:outline-none focus:border-blue-500">
                    <option value="">Semua Jenis Usulan</option>
                    <option value="reguler_berkala" {{ request('type') == 'reguler_berkala' ? 'selected' : '' }}>Reguler Berkala</option>
                    <option value="alih_status_gty" {{ request('type') == 'alih_status_gty' ? 'selected' : '' }}>Alih Status GTY</option>
                    <option value="alih_status_pty" {{ request('type') == 'alih_status_pty' ? 'selected' : '' }}>Alih Status PTY</option>
                    <option value="penyesuaian_ijazah" {{ request('type') == 'penyesuaian_ijazah' ? 'selected' : '' }}>Penyesuaian Ijazah</option>
                </select>
                <select name="status" class="px-2.5 py-1.5 text-xs rounded-xl border border-slate-200 focus:outline-none focus:border-blue-500">
                    <option value="">Semua Status</option>
                    <option value="diajukan" {{ request('status') == 'diajukan' ? 'selected' : '' }}>Diajukan</option>
                    <option value="diverifikasi_sekolah" {{ request('status') == 'diverifikasi_sekolah' ? 'selected' : '' }}>Verifikasi Sekolah</option>
                    <option value="sidang_majelis" {{ request('status') == 'sidang_majelis' ? 'selected' : '' }}>Sidang Majelis</option>
                    <option value="disetujui_pdm" {{ request('status') == 'disetujui_pdm' ? 'selected' : '' }}>Disetujui PDM</option>
                </select>
                <button type="submit" class="px-3 py-1.5 bg-slate-800 hover:bg-slate-900 text-white rounded-xl text-xs font-semibold transition">
                    Filter
                </button>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50/80 text-slate-600 font-semibold border-b border-slate-200/60 uppercase tracking-wider text-[10px]">
                    <tr>
                        <th class="py-3 px-4">Nama Pegawai & Unit Asal</th>
                        <th class="py-3 px-3">Jenis Usulan</th>
                        <th class="py-3 px-3">Pangkat / Kategori Saat Ini</th>
                        <th class="py-3 px-3">Usulan Pangkat Baru</th>
                        <th class="py-3 px-2 text-center">Masa Kerja</th>
                        <th class="py-3 px-2 text-center">Nilai PKG/SKP</th>
                        <th class="py-3 px-2 text-center">Angka Kredit</th>
                        <th class="py-3 px-3 text-center">Status Verifikasi</th>
                        <th class="py-3 px-3">SK Penetapan</th>
                        <th class="py-3 px-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($promotions as $promo)
                        <tr class="hover:bg-slate-50/60 transition">
                            <td class="py-3 px-4">
                                <div class="font-bold text-slate-800 text-sm">{{ $promo->employee ? $promo->employee->full_name : '-' }}</div>
                                <div class="text-[11px] text-slate-500 mt-0.5">
                                    NBM: <span class="font-mono font-medium">{{ $promo->employee->nbm ?? '-' }}</span> •
                                    <span class="font-semibold text-slate-700">{{ $promo->unit ? $promo->unit->name : '-' }}</span>
                                </div>
                            </td>
                            <td class="py-3 px-3">
                                @php
                                    $typeBadge = match($promo->promotion_type) {
                                        'alih_status_gty'    => 'bg-blue-50 text-blue-700 border-blue-200',
                                        'alih_status_pty'    => 'bg-teal-50 text-teal-700 border-teal-200',
                                        'penyesuaian_ijazah' => 'bg-purple-50 text-purple-700 border-purple-200',
                                        default              => 'bg-slate-100 text-slate-700 border-slate-200',
                                    };
                                @endphp
                                <span class="px-2 py-0.5 rounded-md text-[10px] font-bold border {{ $typeBadge }}">
                                    {{ $promo->promotion_type_label }}
                                </span>
                            </td>
                            <td class="py-3 px-3">
                                <div class="font-medium text-slate-700">{{ $promo->current_rank ?? '-' }}</div>
                                <span class="text-[10px] text-slate-400 font-semibold uppercase">{{ $promo->current_employee_type }}</span>
                            </td>
                            <td class="py-3 px-3">
                                <div class="font-bold text-blue-700">{{ $promo->proposed_rank }}</div>
                                <span class="text-[10px] text-emerald-600 font-semibold uppercase">{{ $promo->proposed_employee_type }}</span>
                            </td>
                            <td class="py-3 px-2 text-center font-mono font-semibold text-slate-700">
                                {{ $promo->years_of_service }} thn
                            </td>
                            <td class="py-3 px-2 text-center font-mono font-bold {{ $promo->performance_score >= 85 ? 'text-emerald-700' : 'text-slate-700' }}">
                                {{ $promo->performance_score ?? '-' }}
                            </td>
                            <td class="py-3 px-2 text-center font-mono font-bold text-blue-700">
                                {{ $promo->credit_points ?? '-' }}
                            </td>
                            <td class="py-3 px-3 text-center">
                                @php
                                    $statusBadge = match($promo->status) {
                                        'disetujui_pdm'        => 'bg-emerald-100 text-emerald-800 border-emerald-300 font-black',
                                        'sidang_majelis'       => 'bg-purple-100 text-purple-800 border-purple-300',
                                        'diverifikasi_sekolah' => 'bg-blue-100 text-blue-800 border-blue-300',
                                        'diajukan'             => 'bg-amber-100 text-amber-800 border-amber-300',
                                        default                => 'bg-slate-100 text-slate-700 border-slate-300',
                                    };
                                @endphp
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold border {{ $statusBadge }}">
                                    {{ $promo->status_label }}
                                </span>
                            </td>
                            <td class="py-3 px-3 font-mono text-[11px]">
                                @if($promo->sk_number)
                                    <span class="font-bold text-emerald-700">{{ $promo->sk_number }}</span>
                                    <div class="text-[10px] text-slate-400">{{ $promo->sk_date ? $promo->sk_date->format('d/m/Y') : '' }}</div>
                                @else
                                    <span class="text-slate-400 italic">-</span>
                                @endif
                            </td>
                            <td class="py-3 px-3 text-right">
                                <a href="{{ route('promotions.show', $promo->id) }}" class="inline-flex items-center gap-1 px-2.5 py-1 bg-slate-100 hover:bg-slate-200 text-slate-800 rounded-lg text-xs font-semibold transition">
                                    <span>Detail</span>
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="py-8 text-center text-slate-400">Belum ada usulan kenaikan pangkat dengan kriteria ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($promotions->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $promotions->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

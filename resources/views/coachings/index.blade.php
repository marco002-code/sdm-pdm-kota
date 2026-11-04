@extends('layouts.app')

@section('title', 'Pembinaan Pegawai - PDM Kota Yogyakarta')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 bg-white border border-slate-200 p-6 rounded-2xl shadow-xs">
        <div>
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-teal-500 to-teal-600 flex items-center justify-center text-white shadow-md shadow-teal-500/20 flex-shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                </div>
                <div>
                    <h1 class="text-xl font-bold text-slate-800 tracking-tight">Pembinaan Pegawai</h1>
                    <p class="text-xs text-slate-500 mt-0.5">Pencatatan pembinaan ideologi AIK, komitmen persyarikatan, peningkatan kompetensi/pelatihan, dan pembinaan disiplin</p>
                </div>
            </div>
        </div>

        @if(!auth()->user()->isKetuaMajelis())
        <a href="{{ route('coachings.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-teal-600 hover:bg-teal-700 text-white font-semibold text-xs shadow-xs transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            <span>Catat Pembinaan Baru</span>
        </a>
        @else
        <div class="flex items-center gap-2 px-3 py-1.5 bg-amber-50 text-amber-800 border border-amber-200 rounded-xl text-xs font-semibold shadow-2xs">
            <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
            <span>Mode Peninjauan (Read-Only)</span>
        </div>
        @endif
    </div>

    <!-- Filter & Pencarian -->
    <div class="bg-white border border-slate-200 p-4 rounded-2xl shadow-xs">
        <form method="GET" action="{{ route('coachings.index') }}" class="flex flex-wrap items-center gap-3">
            <div class="relative flex-1 min-w-[240px]">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari materi pembinaan, nama pegawai, atau penyelenggara..." class="w-full pl-10 pr-4 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs text-slate-800 placeholder-slate-400 focus:bg-white focus:outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500 transition">
                <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>

            <select name="category" class="bg-slate-50 border border-slate-300 text-xs text-slate-700 font-medium rounded-xl px-3 py-2 focus:bg-white focus:outline-none focus:border-teal-500">
                <option value="">-- Semua Kategori Pembinaan --</option>
                @foreach($categories as $key => $lbl)
                    <option value="{{ $key }}" {{ request('category') == $key ? 'selected' : '' }}>{{ $lbl }}</option>
                @endforeach
            </select>

            @if(auth()->user()->isYayasanLevel())
            <select name="unit_id" class="bg-slate-50 border border-slate-300 text-xs text-slate-700 font-medium rounded-xl px-3 py-2 focus:bg-white focus:outline-none focus:border-teal-500">
                <option value="">-- Semua Unit Sekolah --</option>
                @foreach($units as $u)
                    <option value="{{ $u->id }}" {{ request('unit_id') == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
                @endforeach
            </select>
            @endif

            <button type="submit" class="px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white rounded-xl text-xs font-semibold transition">
                Filter
            </button>
            @if(request()->anyFilled(['search', 'category', 'unit_id']))
            <a href="{{ route('coachings.index') }}" class="px-3 py-2 text-slate-500 hover:text-slate-800 text-xs font-medium transition">Reset</a>
            @endif
        </form>
    </div>

    <!-- Tabel Catatan Pembinaan -->
    <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-xs">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-700">
                <thead class="bg-slate-50/75 text-slate-500 font-bold border-b border-slate-200 uppercase tracking-wider text-[10px]">
                    <tr>
                        <th class="px-5 py-3.5">Pegawai</th>
                        <th class="px-5 py-3.5">Kategori Pembinaan</th>
                        <th class="px-5 py-3.5">Materi / Agenda Kegiatan</th>
                        <th class="px-5 py-3.5">Penyelenggara</th>
                        <th class="px-5 py-3.5">Tanggal</th>
                        <th class="px-5 py-3.5 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($coachings as $c)
                    <tr class="hover:bg-slate-50/70 transition">
                        <td class="px-5 py-3.5">
                            <a href="{{ route('employees.show', $c->employee_id) }}" class="font-bold text-slate-800 hover:text-teal-600 transition">
                                {{ $c->employee?->full_name }}
                            </a>
                            <div class="font-mono text-[11px] text-amber-700 font-semibold mt-0.5">NPM: {{ $c->employee?->npm ?? '-' }}</div>
                            <div class="text-[10px] text-slate-400">{{ $c->unit?->name }}</div>
                        </td>
                        <td class="px-5 py-3.5">
                            <span class="px-2.5 py-0.5 rounded text-[10px] font-bold bg-teal-50 text-teal-700 border border-teal-200">
                                {{ $categories[$c->category] ?? $c->category }}
                            </span>
                        </td>
                        <td class="px-5 py-3.5 font-medium text-slate-800">
                            {{ $c->title }}
                            @if($c->notes)
                                <div class="text-[11px] text-slate-500 mt-0.5 line-clamp-1">{{ $c->notes }}</div>
                            @endif
                        </td>
                        <td class="px-5 py-3.5 text-slate-600">
                            {{ $c->organizer ?: 'Majelis Dikdasmen PDM' }}
                        </td>
                        <td class="px-5 py-3.5 whitespace-nowrap text-slate-500">
                            {{ $c->coaching_date ? $c->coaching_date->format('d M Y') : '-' }}
                        </td>
                        <td class="px-5 py-3.5 text-center">
                            @if($c->status === 'terlaksana')
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">Terlaksana</span>
                            @elseif($c->status === 'terjadwal')
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-200">Terjadwal</span>
                            @else
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">Evaluasi</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-5 py-10 text-center text-slate-400">
                            Belum ada agenda atau catatan pembinaan pegawai yang terdaftar.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($coachings->hasPages())
        <div class="p-4 border-t border-slate-200 bg-slate-50/50">
            {{ $coachings->links() }}
        </div>
        @endif
    </div>
</div>
@endsection

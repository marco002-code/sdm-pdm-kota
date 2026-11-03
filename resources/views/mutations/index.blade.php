@extends('layouts.app')

@section('title', 'Riwayat Mutasi Pegawai - PDM Kota Yogyakarta')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 bg-white border border-slate-200 p-6 rounded-2xl shadow-xs">
        <div>
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-500 to-indigo-600 flex items-center justify-center text-white shadow-md shadow-indigo-500/20 flex-shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                </div>
                <div>
                    <h1 class="text-xl font-bold text-slate-800 tracking-tight">Riwayat Mutasi Pegawai AUM</h1>
                    <p class="text-xs text-slate-500 mt-0.5">Pencatatan rekam jejak perpindahan tugas guru dan tenaga kependidikan antar-sekolah di Kota Yogyakarta</p>
                </div>
            </div>
        </div>
        @if(auth()->user()->isYayasanLevel())
        <div class="flex items-center gap-2">
            <a href="{{ route('mutations.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs shadow-xs transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>Catat Mutasi Pegawai Baru</span>
            </a>
        </div>
        @endif
    </div>

    <!-- Filter Pencarian -->
    <div class="bg-white border border-slate-200 p-4 rounded-2xl shadow-xs">
        <form method="GET" action="{{ route('mutations.index') }}" class="flex items-center gap-3">
            <div class="relative flex-1">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama pegawai, NPM, atau NBM..." class="w-full pl-10 pr-4 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs text-slate-800 placeholder-slate-400 focus:bg-white focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition">
                <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>
            <button type="submit" class="px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white rounded-xl text-xs font-semibold transition">
                Cari
            </button>
            @if(request('search'))
            <a href="{{ route('mutations.index') }}" class="px-3 py-2 text-slate-500 hover:text-slate-800 text-xs font-medium transition">Reset</a>
            @endif
        </form>
    </div>

    <!-- Tabel Riwayat Mutasi -->
    <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-xs">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-700">
                <thead class="bg-slate-50/75 text-slate-500 font-bold border-b border-slate-200 uppercase tracking-wider text-[10px]">
                    <tr>
                        <th class="px-5 py-3.5">Pegawai</th>
                        <th class="px-5 py-3.5">Sekolah / Unit Asal</th>
                        <th class="px-5 py-3.5">Sekolah / Unit Tujuan</th>
                        <th class="px-5 py-3.5">TMT Mutasi</th>
                        <th class="px-5 py-3.5">Nomor & Tanggal SK</th>
                        <th class="px-5 py-3.5">Alasan Mutasi</th>
                        <th class="px-5 py-3.5 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($mutations as $m)
                    <tr class="hover:bg-slate-50/70 transition">
                        <td class="px-5 py-3.5">
                            <a href="{{ route('employees.show', $m->employee_id) }}" class="font-bold text-slate-800 hover:text-blue-600 transition">
                                {{ $m->employee?->full_name }}
                            </a>
                            <div class="font-mono text-[11px] text-amber-700 font-semibold mt-0.5">NPM: {{ $m->employee?->npm ?? '-' }}</div>
                        </td>
                        <td class="px-5 py-3.5">
                            <div class="font-medium text-slate-800">{{ $m->originUnit?->name }}</div>
                            <div class="text-[11px] text-slate-400">{{ $m->origin_position }}</div>
                        </td>
                        <td class="px-5 py-3.5">
                            <div class="font-semibold text-emerald-700">{{ $m->destinationUnit?->name }}</div>
                            <div class="text-[11px] text-slate-500">{{ $m->destination_position }}</div>
                        </td>
                        <td class="px-5 py-3.5 whitespace-nowrap font-medium text-slate-800">
                            {{ $m->effective_date ? $m->effective_date->format('d M Y') : '-' }}
                        </td>
                        <td class="px-5 py-3.5">
                            <div class="font-mono text-slate-700">{{ $m->sk_number ?? '-' }}</div>
                            <div class="text-[10px] text-slate-400">{{ $m->sk_date ? $m->sk_date->format('d/m/Y') : '' }}</div>
                        </td>
                        <td class="px-5 py-3.5 text-slate-500">
                            {{ $m->reason ?? '-' }}
                        </td>
                        <td class="px-5 py-3.5 text-center">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                Disetujui PDM
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-5 py-10 text-center text-slate-400">
                            Belum ada riwayat mutasi pegawai yang tercatat.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($mutations->hasPages())
        <div class="p-4 border-t border-slate-200 bg-slate-50/50">
            {{ $mutations->links() }}
        </div>
        @endif
    </div>
</div>
@endsection

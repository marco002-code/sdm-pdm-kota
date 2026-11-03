@extends('layouts.app')

@section('title', 'Bank Data Master NPM - PDM Kota Yogyakarta')

@section('content')
<div class="space-y-6">
    <!-- Header Page -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 bg-white border border-slate-200 p-6 rounded-2xl shadow-xs">
        <div>
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-amber-500 to-amber-600 flex items-center justify-center text-white shadow-md shadow-amber-500/20 flex-shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"/></svg>
                </div>
                <div>
                    <h1 class="text-xl font-bold text-slate-800 tracking-tight">Bank Data Master NPM</h1>
                    <p class="text-xs text-slate-500 mt-0.5">Nomor Pegawai Muhammadiyah (ID Utama) sebagai rujukan validasi input operator sekolah/madrasah</p>
                </div>
            </div>
        </div>
        @if(auth()->user()->isYayasanLevel())
        <div class="flex items-center gap-2">
            <a href="{{ route('master-npms.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs shadow-xs transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>Tambah / Generate Range NPM</span>
            </a>
        </div>
        @endif
    </div>

    <!-- Statistik Ringkasan -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white border border-slate-200 p-5 rounded-2xl shadow-xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-blue-50 border border-blue-100 flex items-center justify-center text-blue-600 flex-shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            </div>
            <div>
                <div class="text-2xl font-black text-slate-800">{{ number_format($totalCount, 0, ',', '.') }}</div>
                <div class="text-xs text-slate-500 font-medium">Total NPM Terdaftar di Bank Data</div>
            </div>
        </div>

        <div class="bg-white border border-slate-200 p-5 rounded-2xl shadow-xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-emerald-50 border border-emerald-100 flex items-center justify-center text-emerald-600 flex-shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <div class="text-2xl font-black text-emerald-600">{{ number_format($usedCount, 0, ',', '.') }}</div>
                <div class="text-xs text-slate-500 font-medium">NPM Terpakai oleh Pegawai</div>
            </div>
        </div>

        <div class="bg-white border border-slate-200 p-5 rounded-2xl shadow-xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-amber-50 border border-amber-100 flex items-center justify-center text-amber-600 flex-shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <div class="text-2xl font-black text-amber-600">{{ number_format($availableCount, 0, ',', '.') }}</div>
                <div class="text-xs text-slate-500 font-medium">NPM Tersedia / Belum Digunakan</div>
            </div>
        </div>
    </div>

    <!-- Filter & Pencarian -->
    <div class="bg-white border border-slate-200 p-4 rounded-2xl shadow-xs flex flex-col md:flex-row items-center justify-between gap-4">
        <form method="GET" action="{{ route('master-npms.index') }}" class="flex flex-wrap items-center gap-3 w-full md:w-auto flex-1">
            <div class="relative flex-1 min-w-[240px]">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nomor NPM, nama terdaftar, atau NBM..." class="w-full pl-10 pr-4 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs text-slate-800 placeholder-slate-400 focus:bg-white focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition">
                <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>

            <select name="status" class="bg-slate-50 border border-slate-300 text-xs text-slate-700 font-medium rounded-xl px-3 py-2 focus:bg-white focus:outline-none focus:border-blue-500">
                <option value="">Semua Status</option>
                <option value="available" {{ request('status') === 'available' ? 'selected' : '' }}>Tersedia</option>
                <option value="used" {{ request('status') === 'used' ? 'selected' : '' }}>Terpakai</option>
            </select>

            <button type="submit" class="px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white rounded-xl text-xs font-semibold transition">
                Filter
            </button>
            @if(request()->hasAny(['search', 'status']))
            <a href="{{ route('master-npms.index') }}" class="px-3 py-2 text-slate-500 hover:text-slate-800 text-xs font-medium transition">
                Reset
            </a>
            @endif
        </form>
    </div>

    <!-- Tabel Data Master NPM -->
    <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-xs">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-700">
                <thead class="bg-slate-50/75 text-slate-500 font-bold border-b border-slate-200 uppercase tracking-wider text-[10px]">
                    <tr>
                        <th class="px-5 py-3.5">Nomor NPM</th>
                        <th class="px-5 py-3.5">Nama Terdaftar di Buku Induk</th>
                        <th class="px-5 py-3.5">NBM</th>
                        <th class="px-5 py-3.5">Status Penggunaan</th>
                        <th class="px-5 py-3.5">Pemegang Saat Ini (Pegawai Aktif)</th>
                        <th class="px-5 py-3.5">Unit Alokasi / Sekolah</th>
                        <th class="px-5 py-3.5 text-right">Catatan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($npms as $item)
                    <tr class="hover:bg-slate-50/70 transition">
                        <td class="px-5 py-3.5 font-mono font-bold text-amber-700">
                            {{ $item->npm }}
                        </td>
                        <td class="px-5 py-3.5 font-semibold text-slate-900">
                            {{ $item->registered_name ?? '-' }}
                        </td>
                        <td class="px-5 py-3.5 font-mono text-slate-500">
                            {{ $item->nbm ?? '-' }}
                        </td>
                        <td class="px-5 py-3.5">
                            @if($item->is_used)
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                    Terpakai
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-slate-100 text-slate-600 border border-slate-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                                    Tersedia
                                </span>
                            @endif
                        </td>
                        <td class="px-5 py-3.5">
                            @if($item->employee)
                                <a href="{{ route('employees.show', $item->employee->id) }}" class="text-blue-600 hover:text-blue-700 font-semibold hover:underline">
                                    {{ $item->employee->full_name }}
                                </a>
                                <div class="text-[10px] text-slate-400 mt-0.5">{{ $item->employee->position }}</div>
                            @else
                                <span class="text-slate-400">-</span>
                            @endif
                        </td>
                        <td class="px-5 py-3.5 text-slate-600">
                            {{ $item->employee?->unit?->name ?? ($item->allocatedUnit?->name ?? '-') }}
                        </td>
                        <td class="px-5 py-3.5 text-right text-slate-400 text-[11px]">
                            {{ $item->notes ?? '-' }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-5 py-10 text-center text-slate-500">
                            Belum ada data Master NPM yang terdaftar. Silakan klik tombol "Tambah / Generate Range NPM" di atas.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($npms->hasPages())
        <div class="p-4 border-t border-slate-200 bg-slate-50/50">
            {{ $npms->links() }}
        </div>
        @endif
    </div>
</div>
@endsection

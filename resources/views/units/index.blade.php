@extends('layouts.app')

@section('content')
<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <h1 class="text-2xl md:text-3xl font-bold text-slate-800 tracking-tight">Master Data Unit Kerja AUM</h1>
        <p class="text-sm text-slate-500 mt-1">Daftar 76 Unit Kerja, Sekolah & Madrasah di Lingkungan PDM Kota Yogyakarta</p>
    </div>
</div>

<!-- Jenjang Statistik Badges -->
<div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-8 gap-3 mb-6">
    <a href="{{ route('units.index') }}" class="p-3 bg-white rounded-xl border border-slate-200 text-center hover:border-blue-500 transition shadow-sm {{ !request('level') ? 'ring-2 ring-blue-500' : '' }}">
        <div class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Total</div>
        <div class="text-xl font-black text-slate-800 mt-0.5">{{ array_sum($levelCounts) }}</div>
    </a>
    <a href="{{ route('units.index', ['level' => 'TPA']) }}" class="p-3 bg-pink-50/50 rounded-xl border border-pink-200 text-center hover:border-pink-500 transition shadow-sm {{ request('level') === 'TPA' ? 'ring-2 ring-pink-500' : '' }}">
        <div class="text-[11px] font-bold uppercase tracking-wider text-pink-600">TPA</div>
        <div class="text-xl font-black text-pink-700 mt-0.5">{{ $levelCounts['TPA'] ?? 0 }}</div>
    </a>
    <a href="{{ route('units.index', ['level' => 'TK']) }}" class="p-3 bg-amber-50/50 rounded-xl border border-amber-200 text-center hover:border-amber-500 transition shadow-sm {{ request('level') === 'TK' ? 'ring-2 ring-amber-500' : '' }}">
        <div class="text-[11px] font-bold uppercase tracking-wider text-amber-600">TK / ABA</div>
        <div class="text-xl font-black text-amber-700 mt-0.5">{{ $levelCounts['TK'] ?? 0 }}</div>
    </a>
    <a href="{{ route('units.index', ['level' => 'SD']) }}" class="p-3 bg-blue-50/50 rounded-xl border border-blue-200 text-center hover:border-blue-500 transition shadow-sm {{ request('level') === 'SD' ? 'ring-2 ring-blue-500' : '' }}">
        <div class="text-[11px] font-bold uppercase tracking-wider text-blue-600">SD</div>
        <div class="text-xl font-black text-blue-700 mt-0.5">{{ $levelCounts['SD'] ?? 0 }}</div>
    </a>
    <a href="{{ route('units.index', ['level' => 'SMP']) }}" class="p-3 bg-cyan-50/50 rounded-xl border border-cyan-200 text-center hover:border-cyan-500 transition shadow-sm {{ request('level') === 'SMP' ? 'ring-2 ring-cyan-500' : '' }}">
        <div class="text-[11px] font-bold uppercase tracking-wider text-cyan-600">SMP</div>
        <div class="text-xl font-black text-cyan-700 mt-0.5">{{ $levelCounts['SMP'] ?? 0 }}</div>
    </a>
    <a href="{{ route('units.index', ['level' => 'SMA']) }}" class="p-3 bg-indigo-50/50 rounded-xl border border-indigo-200 text-center hover:border-indigo-500 transition shadow-sm {{ request('level') === 'SMA' ? 'ring-2 ring-indigo-500' : '' }}">
        <div class="text-[11px] font-bold uppercase tracking-wider text-indigo-600">SMA</div>
        <div class="text-xl font-black text-indigo-700 mt-0.5">{{ $levelCounts['SMA'] ?? 0 }}</div>
    </a>
    <a href="{{ route('units.index', ['level' => 'SMK']) }}" class="p-3 bg-violet-50/50 rounded-xl border border-violet-200 text-center hover:border-violet-500 transition shadow-sm {{ request('level') === 'SMK' ? 'ring-2 ring-violet-500' : '' }}">
        <div class="text-[11px] font-bold uppercase tracking-wider text-violet-600">SMK</div>
        <div class="text-xl font-black text-violet-700 mt-0.5">{{ $levelCounts['SMK'] ?? 0 }}</div>
    </a>
    <a href="{{ route('units.index', ['level' => 'MA']) }}" class="p-3 bg-emerald-50/50 rounded-xl border border-emerald-200 text-center hover:border-emerald-500 transition shadow-sm {{ request('level') === 'MA' ? 'ring-2 ring-emerald-500' : '' }}">
        <div class="text-[11px] font-bold uppercase tracking-wider text-emerald-600">MA</div>
        <div class="text-xl font-black text-emerald-700 mt-0.5">{{ $levelCounts['MA'] ?? 0 }}</div>
    </a>
</div>

<!-- Filter Bar -->
<div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200/80 mb-6">
    <form method="GET" action="{{ route('units.index') }}" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
        <!-- Search Input -->
        <div class="md:col-span-2">
            <label class="block text-xs font-semibold text-slate-600 mb-1.5">Cari Unit / Sekolah</label>
            <div class="relative">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Nama sekolah, NPSN, atau kode unit..." class="w-full pl-9 pr-3 py-2 text-sm bg-slate-50 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:bg-white focus:outline-none transition">
                <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>
        </div>

        <!-- Filter Kecamatan -->
        <div>
            <label class="block text-xs font-semibold text-slate-600 mb-1.5">Kecamatan (Kemantren)</label>
            <select name="district" class="w-full px-3 py-2 text-sm bg-slate-50 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:bg-white focus:outline-none transition">
                <option value="">-- Semua Kecamatan --</option>
                @foreach($districts as $district)
                    <option value="{{ $district }}" {{ request('district') === $district ? 'selected' : '' }}>{{ $district }}</option>
                @endforeach
            </select>
        </div>

        <!-- Action Buttons -->
        <div class="flex items-end gap-2">
            <button type="submit" class="w-full py-2 px-4 bg-slate-800 hover:bg-slate-900 text-white text-sm font-semibold rounded-xl transition flex items-center justify-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                <span>Filter</span>
            </button>
            @if(request()->anyFilled(['search', 'level', 'district']))
                <a href="{{ route('units.index') }}" class="py-2 px-3 bg-slate-200 hover:bg-slate-300 text-slate-700 text-sm font-semibold rounded-xl transition">Reset</a>
            @endif
        </div>
    </form>
</div>

<!-- Units Table -->
<div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse text-sm">
            <thead>
                <tr class="bg-slate-50/80 border-b border-slate-200 text-xs font-bold text-slate-500 uppercase tracking-wider">
                    <th class="py-3.5 px-4 w-12 text-center">No</th>
                    <th class="py-3.5 px-4">Jenis AUM</th>
                    <th class="py-3.5 px-4">NPSN</th>
                    <th class="py-3.5 px-4">Nama Unit</th>
                    <th class="py-3.5 px-4">Url / Link</th>
                    <th class="py-3.5 px-4">Kecamatan</th>
                    <th class="py-3.5 px-4 text-center">Pegawai</th>
                    <th class="py-3.5 px-4 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($units as $index => $unit)
                <tr class="hover:bg-slate-50/60 transition">
                    <td class="py-3.5 px-4 text-center text-xs text-slate-400 font-medium">
                        {{ $units->firstItem() + $index }}
                    </td>

                    <td class="py-3.5 px-4">
                        @php
                            $badgeStyles = [
                                'YAYASAN' => 'bg-purple-100 text-purple-800 border-purple-200',
                                'TPA' => 'bg-pink-100 text-pink-800 border-pink-200',
                                'TK' => 'bg-amber-100 text-amber-800 border-amber-200',
                                'SD' => 'bg-blue-100 text-blue-800 border-blue-200',
                                'SMP' => 'bg-cyan-100 text-cyan-800 border-cyan-200',
                                'SMA' => 'bg-indigo-100 text-indigo-800 border-indigo-200',
                                'SMK' => 'bg-violet-100 text-violet-800 border-violet-200',
                                'MA' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                            ];
                        @endphp
                        <span class="inline-block px-2.5 py-0.5 rounded-full text-xs font-semibold border {{ $badgeStyles[$unit->level] ?? 'bg-slate-100 text-slate-700' }}">
                            {{ $unit->level }}
                        </span>
                    </td>

                    <td class="py-3.5 px-4 font-mono text-xs">
                        @if($unit->npsn)
                            <span class="inline-block px-2 py-0.5 bg-slate-100 text-slate-700 rounded font-semibold border border-slate-200">
                                {{ $unit->npsn }}
                            </span>
                        @else
                            <span class="text-slate-400 italic">-</span>
                        @endif
                    </td>

                    <td class="py-3.5 px-4">
                        <div class="font-bold text-slate-900 text-sm">
                            <a href="{{ route('units.show', $unit->id) }}" class="hover:text-blue-600 transition">
                                {{ $unit->name }}
                            </a>
                        </div>
                        @if($unit->address)
                            <div class="text-[11px] text-slate-500 mt-0.5 truncate max-w-xs">
                                {{ $unit->address }}{{ $unit->village ? ' • Kel. ' . $unit->village : '' }}
                            </div>
                        @endif
                    </td>

                    <td class="py-3.5 px-4">
                        @if($unit->website)
                            <a href="{{ $unit->website }}" target="_blank" rel="noopener noreferrer" title="{{ $unit->website }}" class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-blue-50 text-blue-700 hover:bg-blue-100 rounded-lg text-xs font-medium border border-blue-200 transition max-w-[170px]">
                                <svg class="w-3.5 h-3.5 text-blue-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                <span class="truncate">{{ parse_url($unit->website, PHP_URL_HOST) ?? 'Kunjungi Web' }}</span>
                            </a>
                        @else
                            <span class="text-slate-400 text-xs italic">-</span>
                        @endif
                    </td>

                    <td class="py-3.5 px-4 text-xs">
                        <div class="font-medium text-slate-800">{{ $unit->district ?? '-' }}</div>
                        @if($unit->accreditation)
                            <span class="inline-block mt-0.5 px-2 py-0.5 rounded text-[10px] font-bold {{ $unit->accreditation === 'A' || str_contains($unit->accreditation, 'A') ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-amber-50 text-amber-700 border border-amber-200' }}">
                                Akreditasi {{ $unit->accreditation }}
                            </span>
                        @endif
                    </td>

                    <td class="py-3.5 px-4 text-center">
                        <span class="inline-block px-2 py-0.5 bg-slate-50 text-slate-600 rounded-full text-xs font-bold border border-slate-200">
                            {{ $unit->employees_count }}
                        </span>
                    </td>

                    <td class="py-3.5 px-4 text-center">
                        <a href="{{ route('units.show', $unit->id) }}" class="inline-flex items-center gap-1 px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-xs font-semibold transition">
                            <span>Detail</span>
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="py-12 text-center text-slate-400">
                        <p class="font-semibold text-slate-600 text-base">Tidak ada data unit kerja ditemukan</p>
                        <p class="text-xs mt-1">Coba sesuaikan kata kunci pencarian atau filter jenjang/kecamatan.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($units->hasPages())
    <div class="p-4 border-t border-slate-200 bg-slate-50/50">
        {{ $units->links() }}
    </div>
    @endif
</div>
@endsection

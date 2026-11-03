@extends('layouts.app')

@section('title', 'Prestasi Pegawai - PDM Kota Yogyakarta')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 bg-white border border-slate-200 p-6 rounded-2xl shadow-xs">
        <div>
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-amber-500 to-amber-600 flex items-center justify-center text-white shadow-md shadow-amber-500/20 flex-shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
                </div>
                <div>
                    <h1 class="text-xl font-bold text-slate-800 tracking-tight">Portofolio Prestasi Pegawai</h1>
                    <p class="text-xs text-slate-500 mt-0.5">Daftar rekam jejak prestasi guru dan tenaga kependidikan: Tingkat Lokal, Provinsi, Nasional, dan Internasional</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Pencarian -->
    <div class="bg-white border border-slate-200 p-4 rounded-2xl shadow-xs">
        <form method="GET" action="{{ route('career.achievements') }}" class="flex flex-wrap items-center gap-3">
            <div class="relative flex-1 min-w-[240px]">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama pegawai, NPM, atau judul kejuaraan/prestasi..." class="w-full pl-10 pr-4 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs text-slate-800 placeholder-slate-400 focus:bg-white focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition">
                <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>

            @if(auth()->user()->isYayasanLevel())
            <select name="unit_id" class="bg-slate-50 border border-slate-300 text-xs text-slate-700 font-medium rounded-xl px-3 py-2 focus:bg-white focus:outline-none focus:border-amber-500">
                <option value="">-- Semua Unit Sekolah --</option>
                @foreach($units as $u)
                    <option value="{{ $u->id }}" {{ request('unit_id') == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
                @endforeach
            </select>
            @endif

            <button type="submit" class="px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white rounded-xl text-xs font-semibold transition">
                Filter
            </button>
            @if(request()->anyFilled(['search', 'unit_id']))
            <a href="{{ route('career.achievements') }}" class="px-3 py-2 text-slate-500 hover:text-slate-800 text-xs font-medium transition">Reset</a>
            @endif
        </form>
    </div>

    <!-- Tabel Prestasi Pegawai -->
    <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-xs">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-700">
                <thead class="bg-slate-50/75 text-slate-500 font-bold border-b border-slate-200 uppercase tracking-wider text-[10px]">
                    <tr>
                        <th class="px-5 py-3.5">Pegawai</th>
                        <th class="px-5 py-3.5">Unit Kerja AUM</th>
                        <th class="px-5 py-3.5">Tingkat</th>
                        <th class="px-5 py-3.5">Nama Prestasi / Penghargaan</th>
                        <th class="px-5 py-3.5 text-center">Tahun</th>
                        <th class="px-5 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @php $hasData = false; @endphp
                    @foreach($employeesWithAchievements as $emp)
                        @foreach($emp->achievements as $ach)
                            @php $hasData = true; @endphp
                            <tr class="hover:bg-slate-50/70 transition">
                                <td class="px-5 py-3.5">
                                    <a href="{{ route('employees.show', $emp->id) }}" class="font-bold text-slate-800 hover:text-blue-600 transition">
                                        {{ $emp->full_name }}
                                    </a>
                                    <div class="font-mono text-[11px] text-amber-700 font-semibold mt-0.5">NPM: {{ $emp->npm ?? '-' }}</div>
                                </td>
                                <td class="px-5 py-3.5 text-slate-600">
                                    {{ $emp->unit?->name ?? '-' }}
                                    <div class="text-[11px] text-slate-400">{{ $emp->position }}</div>
                                </td>
                                <td class="px-5 py-3.5">
                                    @php $lvl = strtolower($ach['level'] ?? 'lokal'); @endphp
                                    @if($lvl === 'internasional')
                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-purple-50 text-purple-700 border border-purple-200">
                                            Internasional
                                        </span>
                                    @elseif($lvl === 'nasional')
                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                            Nasional
                                        </span>
                                    @elseif($lvl === 'provinsi')
                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-200">
                                            Provinsi
                                        </span>
                                    @else
                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            Lokal / Kota
                                        </span>
                                    @endif
                                </td>
                                <td class="px-5 py-3.5 font-medium text-slate-800">
                                    {{ $ach['title'] ?? '-' }}
                                    @if(!empty($ach['organizer']))
                                        <div class="text-[11px] text-slate-400">Penyelenggara: {{ $ach['organizer'] }}</div>
                                    @endif
                                </td>
                                <td class="px-5 py-3.5 text-center font-bold text-slate-700">
                                    {{ $ach['year'] ?? '-' }}
                                </td>
                                <td class="px-5 py-3.5 text-right">
                                    <a href="{{ route('employees.show', $emp->id) }}" class="text-blue-600 hover:text-blue-700 font-semibold text-xs hover:underline">
                                        Lihat Profil
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    @endforeach

                    @if(!$hasData)
                    <tr>
                        <td colspan="6" class="px-5 py-10 text-center text-slate-400">
                            Belum ada riwayat prestasi pegawai yang tercatat. Prestasi dapat diinput melalui formulir Ubah Data Pegawai.
                        </td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>

        @if($employeesWithAchievements->hasPages())
        <div class="p-4 border-t border-slate-200 bg-slate-50/50">
            {{ $employeesWithAchievements->links() }}
        </div>
        @endif
    </div>
</div>
@endsection

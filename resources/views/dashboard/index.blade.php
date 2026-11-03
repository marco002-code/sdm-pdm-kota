@extends('layouts.app')

@section('content')
<!-- Header Dasbor (Besar, Elegan & Seimbang) -->
<div class="mb-3 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2.5">
    <div>
        <h1 class="text-xl lg:text-2xl font-bold text-slate-800 tracking-tight">Dasbor Kepegawaian & AUM</h1>
        <p class="text-xs text-slate-500 mt-0.5">
            Selamat datang, <span class="font-semibold text-slate-700">{{ auth()->user()->name ?? 'Pengguna' }}</span>
            <span class="inline-block mx-1.5 text-slate-300">•</span>
            Role: <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase bg-blue-100 text-blue-800 tracking-wide">{{ auth()->user()->role ?? 'Admin' }}</span>
        </p>
    </div>
    <div class="flex items-center gap-2">
        <a href="{{ route('employees.index') }}" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-xl shadow-sm transition shadow-blue-500/20">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
            <span>Kelola Pegawai</span>
        </a>
        <a href="{{ route('units.index') }}" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-slate-800 hover:bg-slate-900 text-white text-xs font-semibold rounded-xl transition">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
            <span>Master AUM</span>
        </a>
    </div>
</div>

<!-- 8 Quick Stat Cards: 2 Baris x 4 Kolom (Tampilan Besar Seperti Semula, Proporsional 100% Layar) -->
<div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5 mb-3">
    <!-- 1. Total Pegawai -->
    <div class="relative overflow-hidden rounded-2xl px-4 py-3 border border-indigo-200/50 bg-gradient-to-br from-white/95 via-indigo-50/40 to-purple-50/50 backdrop-blur-xl shadow-xs hover:shadow-md hover:-translate-y-0.5 hover:border-indigo-300 transition-all duration-200 group">
        <div class="absolute -top-10 -right-10 w-24 h-24 bg-gradient-to-br from-indigo-400/20 to-purple-400/10 rounded-full blur-xl pointer-events-none"></div>
        <div class="flex items-center justify-between relative z-10">
            <span class="text-[11px] font-bold uppercase tracking-wider text-indigo-700/80">Total Pegawai</span>
            <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-indigo-500 to-blue-600 text-white flex items-center justify-center shadow-md shadow-indigo-500/25 group-hover:scale-105 transition duration-300">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            </div>
        </div>
        <div class="mt-2 flex items-baseline gap-1.5 relative z-10">
            <span class="text-2xl lg:text-3xl font-black text-slate-800 tracking-tight">{{ $totalEmployees }}</span>
            <span class="text-xs text-slate-500 font-medium">Orang</span>
        </div>
        <p class="text-[11px] text-slate-400 mt-0.5 relative z-10 truncate">Semua kategori SDM terdaftar</p>
    </div>

    <!-- 2. Total Unit AUM -->
    <a href="{{ route('units.index') }}" class="relative overflow-hidden rounded-2xl px-4 py-3 border border-teal-200/50 bg-gradient-to-br from-white/95 via-teal-50/40 to-emerald-50/50 backdrop-blur-xl shadow-xs hover:shadow-md hover:-translate-y-0.5 hover:border-teal-300 transition-all duration-200 group">
        <div class="absolute -top-10 -right-10 w-24 h-24 bg-gradient-to-br from-teal-400/20 to-emerald-400/10 rounded-full blur-xl pointer-events-none"></div>
        <div class="flex items-center justify-between relative z-10">
            <span class="text-[11px] font-bold uppercase tracking-wider text-teal-700/80">Unit Kerja AUM</span>
            <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-teal-500 to-emerald-600 text-white flex items-center justify-center shadow-md shadow-teal-500/25 group-hover:scale-105 transition duration-300">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
            </div>
        </div>
        <div class="mt-2 flex items-baseline gap-1.5 relative z-10">
            <span class="text-2xl lg:text-3xl font-black text-slate-800 tracking-tight">{{ $totalUnits }}</span>
            <span class="text-xs text-slate-500 font-medium">Unit</span>
        </div>
        <p class="text-[11px] text-teal-600 mt-0.5 relative z-10 font-medium truncate">Sekolah & Kantor PDM</p>
    </a>

    <!-- 3. PDM (Majelis) -->
    <a href="{{ route('employees.index', ['type' => 'PDM']) }}" class="relative overflow-hidden rounded-2xl px-4 py-3 border border-slate-300/50 bg-gradient-to-br from-white/95 via-slate-100/50 to-indigo-50/40 backdrop-blur-xl shadow-xs hover:shadow-md hover:-translate-y-0.5 hover:border-slate-400 transition-all duration-200 group">
        <div class="absolute -top-10 -right-10 w-24 h-24 bg-gradient-to-br from-slate-400/20 to-slate-600/10 rounded-full blur-xl pointer-events-none"></div>
        <div class="flex items-center justify-between relative z-10">
            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-600 truncate mr-1">PDM (Majelis)</span>
            <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-slate-600 to-slate-800 text-white flex items-center justify-center shadow-md shadow-slate-600/25 group-hover:scale-105 transition duration-300">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
            </div>
        </div>
        <div class="mt-2 flex items-baseline gap-1.5 relative z-10">
            <span class="text-2xl lg:text-3xl font-black text-slate-800 tracking-tight">{{ $categoryBreakdown['PDM']['count'] ?? 0 }}</span>
            <span class="text-xs text-slate-500 font-medium">Pengurus</span>
        </div>
        <p class="text-[11px] text-slate-500 mt-0.5 relative z-10 truncate">Pimpinan & Majelis Dikdasmen</p>
    </a>

    <!-- 4. GTY (Guru Tetap Yayasan) -->
    <a href="{{ route('employees.index', ['type' => 'GTY']) }}" class="relative overflow-hidden rounded-2xl px-4 py-3 border border-sky-200/50 bg-gradient-to-br from-white/95 via-sky-50/40 to-blue-50/50 backdrop-blur-xl shadow-xs hover:shadow-md hover:-translate-y-0.5 hover:border-sky-300 transition-all duration-200 group">
        <div class="absolute -top-10 -right-10 w-24 h-24 bg-gradient-to-br from-sky-400/20 to-blue-500/10 rounded-full blur-xl pointer-events-none"></div>
        <div class="flex items-center justify-between relative z-10">
            <span class="text-[11px] font-bold uppercase tracking-wider text-sky-700/80 truncate mr-1">GTY</span>
            <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-sky-500 to-blue-600 text-white flex items-center justify-center shadow-md shadow-sky-500/25 group-hover:scale-105 transition duration-300">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
            </div>
        </div>
        <div class="mt-2 flex items-baseline gap-1.5 relative z-10">
            <span class="text-2xl lg:text-3xl font-black text-slate-800 tracking-tight">{{ $categoryBreakdown['GTY']['count'] ?? 0 }}</span>
            <span class="text-xs text-slate-500 font-medium">Guru</span>
        </div>
        <p class="text-[11px] text-sky-600 mt-0.5 relative z-10 font-medium truncate">Guru Tetap Yayasan</p>
    </a>

    <!-- 5. GTT (Guru Tidak Tetap) -->
    <a href="{{ route('employees.index', ['type' => 'GTT']) }}" class="relative overflow-hidden rounded-2xl px-4 py-3 border border-cyan-200/50 bg-gradient-to-br from-white/95 via-cyan-50/40 to-teal-50/50 backdrop-blur-xl shadow-xs hover:shadow-md hover:-translate-y-0.5 hover:border-cyan-300 transition-all duration-200 group">
        <div class="absolute -top-10 -right-10 w-24 h-24 bg-gradient-to-br from-cyan-400/20 to-teal-400/10 rounded-full blur-xl pointer-events-none"></div>
        <div class="flex items-center justify-between relative z-10">
            <span class="text-[11px] font-bold uppercase tracking-wider text-cyan-700/80 truncate mr-1">GTT</span>
            <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-cyan-500 to-teal-600 text-white flex items-center justify-center shadow-md shadow-cyan-500/25 group-hover:scale-105 transition duration-300">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
            </div>
        </div>
        <div class="mt-2 flex items-baseline gap-1.5 relative z-10">
            <span class="text-2xl lg:text-3xl font-black text-slate-800 tracking-tight">{{ $categoryBreakdown['GTT']['count'] ?? 0 }}</span>
            <span class="text-xs text-slate-500 font-medium">Guru</span>
        </div>
        <p class="text-[11px] text-cyan-600 mt-0.5 relative z-10 font-medium truncate">Guru Tidak Tetap</p>
    </a>

    <!-- 6. PTY (Pegawai Tetap Yayasan) -->
    <a href="{{ route('employees.index', ['type' => 'PTY']) }}" class="relative overflow-hidden rounded-2xl px-4 py-3 border border-emerald-200/50 bg-gradient-to-br from-white/95 via-emerald-50/40 to-teal-50/50 backdrop-blur-xl shadow-xs hover:shadow-md hover:-translate-y-0.5 hover:border-emerald-300 transition-all duration-200 group">
        <div class="absolute -top-10 -right-10 w-24 h-24 bg-gradient-to-br from-emerald-400/20 to-teal-400/10 rounded-full blur-xl pointer-events-none"></div>
        <div class="flex items-center justify-between relative z-10">
            <span class="text-[11px] font-bold uppercase tracking-wider text-emerald-700/80 truncate mr-1">PTY</span>
            <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 text-white flex items-center justify-center shadow-md shadow-emerald-500/25 group-hover:scale-105 transition duration-300">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            </div>
        </div>
        <div class="mt-2 flex items-baseline gap-1.5 relative z-10">
            <span class="text-2xl lg:text-3xl font-black text-slate-800 tracking-tight">{{ $categoryBreakdown['PTY']['count'] ?? 0 }}</span>
            <span class="text-xs text-slate-500 font-medium">Staf</span>
        </div>
        <p class="text-[11px] text-emerald-600 mt-0.5 relative z-10 font-medium truncate">Pegawai Tetap Yayasan</p>
    </a>

    <!-- 7. PTT (Pegawai Tidak Tetap) -->
    <a href="{{ route('employees.index', ['type' => 'PTT']) }}" class="relative overflow-hidden rounded-2xl px-4 py-3 border border-amber-200/50 bg-gradient-to-br from-white/95 via-amber-50/40 to-orange-50/50 backdrop-blur-xl shadow-xs hover:shadow-md hover:-translate-y-0.5 hover:border-amber-300 transition-all duration-200 group">
        <div class="absolute -top-10 -right-10 w-24 h-24 bg-gradient-to-br from-amber-400/20 to-orange-400/10 rounded-full blur-xl pointer-events-none"></div>
        <div class="flex items-center justify-between relative z-10">
            <span class="text-[11px] font-bold uppercase tracking-wider text-amber-700/80 truncate mr-1">PTT</span>
            <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-amber-500 to-orange-600 text-white flex items-center justify-center shadow-md shadow-amber-500/25 group-hover:scale-105 transition duration-300">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
            </div>
        </div>
        <div class="mt-2 flex items-baseline gap-1.5 relative z-10">
            <span class="text-2xl lg:text-3xl font-black text-slate-800 tracking-tight">{{ $categoryBreakdown['PTT']['count'] ?? 0 }}</span>
            <span class="text-xs text-slate-500 font-medium">Staf</span>
        </div>
        <p class="text-[11px] text-amber-600 mt-0.5 relative z-10 font-medium truncate">Pegawai Tidak Tetap</p>
    </a>

    <!-- 8. Lainnya -->
    <a href="{{ route('employees.index', ['type' => 'LAINNYA']) }}" class="relative overflow-hidden rounded-2xl px-4 py-3 border border-purple-200/50 bg-gradient-to-br from-white/95 via-purple-50/40 to-fuchsia-50/50 backdrop-blur-xl shadow-xs hover:shadow-md hover:-translate-y-0.5 hover:border-purple-300 transition-all duration-200 group">
        <div class="absolute -top-10 -right-10 w-24 h-24 bg-gradient-to-br from-purple-400/20 to-fuchsia-400/10 rounded-full blur-xl pointer-events-none"></div>
        <div class="flex items-center justify-between relative z-10">
            <span class="text-[11px] font-bold uppercase tracking-wider text-purple-700/80 truncate mr-1">Lainnya</span>
            <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-purple-500 to-fuchsia-600 text-white flex items-center justify-center shadow-md shadow-purple-500/25 group-hover:scale-105 transition duration-300">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            </div>
        </div>
        <div class="mt-2 flex items-baseline gap-1.5 relative z-10">
            <span class="text-2xl lg:text-3xl font-black text-slate-800 tracking-tight">{{ $categoryBreakdown['LAINNYA']['count'] ?? 0 }}</span>
            <span class="text-xs text-slate-500 font-medium">Staf</span>
        </div>
        <p class="text-[11px] text-purple-600 mt-0.5 relative z-10 font-medium truncate">Satpam & Operasional</p>
    </a>
</div>

<!-- Charts Section: Besar, Megah, dan Proporsional (Muat Utuh Tanpa Perlu Scroll) -->
<div class="grid grid-cols-1 xl:grid-cols-12 gap-3 mb-0">
    <!-- GRAFIK PIE: DISTRIBUSI JENIS AUM -->
    <div class="xl:col-span-6 bg-white/90 backdrop-blur-xl p-4 rounded-2xl shadow-xs border border-slate-200/80 flex flex-col justify-between">
        <div>
            <div class="flex items-center justify-between pb-2.5 mb-2.5 border-b border-slate-100">
                <div>
                    <h3 class="text-sm font-bold text-slate-800 flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-gradient-to-tr from-blue-600 to-indigo-500"></span>
                        <span>Grafik Pie: Distribusi Jenis AUM</span>
                    </h3>
                    <p class="text-[11px] text-slate-500 mt-0.5">Komposisi 76 Unit Kerja / Sekolah berdasarkan Jenjang</p>
                </div>
                <a href="{{ route('units.index') }}" class="text-xs font-semibold text-blue-600 hover:text-blue-800 flex items-center gap-1">
                    <span>Lihat Master AUM</span>
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-center">
                <!-- Pie Canvas: Ukuran Besar & Jelas -->
                <div class="md:col-span-5 flex justify-center items-center py-1">
                    <div class="relative w-full max-w-[185px] h-[185px] aspect-square">
                        <canvas id="aumPieChart"></canvas>
                    </div>
                </div>

                <!-- List Kategori & Rincian AUM (8 Item Vertikal Lengkap Seperti Tampilan Asli) -->
                <div class="md:col-span-7">
                    <div class="grid grid-cols-1 gap-1">
                        @php
                            $badgeColors = [
                                'SD' => ['bg' => 'bg-gradient-to-r from-blue-500 to-blue-600', 'text' => 'text-blue-700', 'glass' => 'bg-gradient-to-r from-blue-50/80 via-white to-blue-50/40 border-blue-100'],
                                'SMP' => ['bg' => 'bg-gradient-to-r from-cyan-500 to-teal-500', 'text' => 'text-cyan-700', 'glass' => 'bg-gradient-to-r from-cyan-50/80 via-white to-cyan-50/40 border-cyan-100'],
                                'TK' => ['bg' => 'bg-gradient-to-r from-amber-500 to-orange-500', 'text' => 'text-amber-700', 'glass' => 'bg-gradient-to-r from-amber-50/80 via-white to-amber-50/40 border-amber-100'],
                                'SMA' => ['bg' => 'bg-gradient-to-r from-indigo-500 to-purple-600', 'text' => 'text-indigo-700', 'glass' => 'bg-gradient-to-r from-indigo-50/80 via-white to-indigo-50/40 border-indigo-100'],
                                'TPA' => ['bg' => 'bg-gradient-to-r from-pink-500 to-rose-500', 'text' => 'text-pink-700', 'glass' => 'bg-gradient-to-r from-pink-50/80 via-white to-pink-50/40 border-pink-100'],
                                'SMK' => ['bg' => 'bg-gradient-to-r from-violet-500 to-purple-600', 'text' => 'text-violet-700', 'glass' => 'bg-gradient-to-r from-violet-50/80 via-white to-violet-50/40 border-violet-100'],
                                'MA' => ['bg' => 'bg-gradient-to-r from-emerald-500 to-teal-600', 'text' => 'text-emerald-700', 'glass' => 'bg-gradient-to-r from-emerald-50/80 via-white to-emerald-50/40 border-emerald-100'],
                                'YAYASAN' => ['bg' => 'bg-gradient-to-r from-slate-600 to-slate-700', 'text' => 'text-slate-700', 'glass' => 'bg-gradient-to-r from-slate-50/80 via-white to-slate-100/50 border-slate-200'],
                            ];
                        @endphp
                        @foreach($aumBreakdown as $key => $item)
                            @php
                                $colors = $badgeColors[$key] ?? ['bg' => 'bg-slate-400', 'text' => 'text-slate-700', 'glass' => 'bg-slate-50 border-slate-200'];
                                $pct = $totalUnits > 0 ? round(($item['count'] / $totalUnits) * 100, 1) : 0;
                            @endphp
                            <a href="{{ route('units.index', ['level' => $key]) }}" class="flex items-center justify-between px-2.5 py-1 rounded-xl {{ $colors['glass'] }} border hover:shadow-xs hover:scale-[1.01] transition-all group">
                                <div class="flex items-center gap-2 overflow-hidden mr-2">
                                    <span class="w-2 h-2 rounded-full {{ $colors['bg'] }} shadow-xs flex-shrink-0"></span>
                                    <span class="text-xs font-semibold text-slate-700 group-hover:text-slate-900 truncate">{{ $item['label'] }}</span>
                                </div>
                                <div class="flex items-center gap-1.5 flex-shrink-0">
                                    <span class="text-xs font-bold {{ $colors['text'] }}">{{ $item['count'] }} unit</span>
                                    <span class="text-[10px] text-slate-400 font-mono">({{ $pct }}%)</span>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-2.5 pt-2 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
            <span>Total Cakupan AUM:</span>
            <span class="font-bold text-slate-800">{{ $totalUnits }} Lembaga Pendidikan & Kantor PDM</span>
        </div>
    </div>

    <!-- DIAGRAM BATANG: KOMPOSISI PEGAWAI SESUAI KATEGORI -->
    <div class="xl:col-span-6 bg-white/90 backdrop-blur-xl p-4 rounded-2xl shadow-xs border border-slate-200/80 flex flex-col justify-between">
        <div>
            <div class="flex items-center justify-between pb-2.5 mb-2.5 border-b border-slate-100">
                <div>
                    <h3 class="text-sm font-bold text-slate-800 flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-gradient-to-tr from-emerald-500 to-teal-400"></span>
                        <span>Diagram Batang: Komposisi Pegawai</span>
                    </h3>
                    <p class="text-[11px] text-slate-500 mt-0.5">Jumlah SDM berdasarkan Kategori Pegawai PDM Kota Yogyakarta</p>
                </div>
                <a href="{{ route('employees.index') }}" class="text-xs font-semibold text-emerald-600 hover:text-emerald-800 flex items-center gap-1">
                    <span>Semua Pegawai</span>
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
            </div>

            <!-- Canvas Bar Chart (Diagram Batang) -->
            <div>
                <div class="relative w-full h-[175px]">
                    <canvas id="employeeBarChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Rincian Jumlah Kategori Pegawai (2 Baris x 3 Kolom Seperti Tampilan Asli) -->
        <div class="grid grid-cols-2 sm:grid-cols-3 gap-2 mt-2.5 pt-2 border-t border-slate-100 text-left">
            @php
                $catGlassMeta = [
                    'PDM'     => ['glass' => 'bg-gradient-to-br from-slate-50/90 via-white to-slate-100/60 border-slate-200/70', 'badge' => 'bg-gradient-to-tr from-slate-600 to-slate-800', 'text' => 'text-slate-800', 'sub' => 'text-slate-500'],
                    'GTY'     => ['glass' => 'bg-gradient-to-br from-sky-50/90 via-white to-blue-50/50 border-sky-200/70', 'badge' => 'bg-gradient-to-tr from-sky-500 to-blue-600', 'text' => 'text-blue-900', 'sub' => 'text-sky-600'],
                    'GTT'     => ['glass' => 'bg-gradient-to-br from-cyan-50/90 via-white to-teal-50/50 border-cyan-200/70', 'badge' => 'bg-gradient-to-tr from-cyan-500 to-teal-600', 'text' => 'text-cyan-900', 'sub' => 'text-cyan-600'],
                    'PTY'     => ['glass' => 'bg-gradient-to-br from-emerald-50/90 via-white to-teal-50/50 border-emerald-200/70', 'badge' => 'bg-gradient-to-tr from-emerald-500 to-teal-600', 'text' => 'text-emerald-900', 'sub' => 'text-emerald-600'],
                    'PTT'     => ['glass' => 'bg-gradient-to-br from-amber-50/90 via-white to-orange-50/50 border-amber-200/70', 'badge' => 'bg-gradient-to-tr from-amber-500 to-orange-600', 'text' => 'text-amber-900', 'sub' => 'text-amber-600'],
                    'LAINNYA' => ['glass' => 'bg-gradient-to-br from-purple-50/90 via-white to-fuchsia-50/50 border-purple-200/70', 'badge' => 'bg-gradient-to-tr from-purple-500 to-fuchsia-600', 'text' => 'text-purple-900', 'sub' => 'text-purple-600'],
                ];
            @endphp
            @foreach($categoryBreakdown as $typeKey => $catData)
                @php
                    $m = $catGlassMeta[$typeKey] ?? ['glass' => 'bg-slate-50 border-slate-200', 'badge' => 'bg-slate-500', 'text' => 'text-slate-800', 'sub' => 'text-slate-500'];
                    $pct = $totalEmployees > 0 ? round(($catData['count'] / $totalEmployees) * 100, 1) : 0;
                @endphp
                <a href="{{ route('employees.index', ['type' => $typeKey]) }}" class="px-2.5 py-1.5 rounded-xl {{ $m['glass'] }} border hover:shadow-sm hover:scale-[1.01] transition-all group relative overflow-hidden">
                    <div class="flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full {{ $m['badge'] }}"></span>
                        <div class="text-[10px] uppercase font-bold text-slate-600 tracking-wider truncate">{{ $catData['label'] ?? $typeKey }}</div>
                    </div>
                    <div class="flex items-baseline justify-between mt-1">
                        <div class="text-sm font-black {{ $m['text'] }}">{{ $catData['count'] }} <span class="text-[10px] font-medium text-slate-400">org</span></div>
                        <span class="text-[10px] font-bold {{ $m['sub'] }} font-mono">{{ $pct }}%</span>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener("DOMContentLoaded", function() {
    // 1. GRAFIK PIE: JENIS AUM
    const aumPieElem = document.getElementById('aumPieChart');
    if (aumPieElem) {
        const aumLabels = @json($aumLabels);
        const aumCounts = @json($aumCounts);

        new Chart(aumPieElem.getContext('2d'), {
            type: 'pie',
            data: {
                labels: aumLabels,
                datasets: [{
                    data: aumCounts,
                    backgroundColor: [
                        '#2563eb', // SD (Blue)
                        '#06b6d4', // SMP (Cyan)
                        '#f59e0b', // TK / ABA (Amber)
                        '#6366f1', // SMA (Indigo)
                        '#ec4899', // TPA (Pink)
                        '#8b5cf6', // SMK (Violet)
                        '#10b981', // MA (Emerald)
                        '#64748b'  // Kantor PDM Kota Yogyakarta (Slate)
                    ],
                    borderWidth: 2,
                    borderColor: '#ffffff',
                    hoverOffset: 5
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.parsed || 0;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                                return ` ${label}: ${value} unit (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });
    }

    // 2. DIAGRAM BATANG (BAR CHART): KOMPOSISI PEGAWAI SESUAI KATEGORI
    const barElem = document.getElementById('employeeBarChart');
    if (barElem) {
        const catLabels = @json($categoryLabels);
        const catCounts = @json($categoryCounts);

        // Plugin kustom untuk menampilkan angka jumlah langsung di samping diagram batang
        const countTextPlugin = {
            id: 'countTextPlugin',
            afterDatasetsDraw(chart) {
                const { ctx } = chart;
                chart.data.datasets.forEach((dataset, i) => {
                    const meta = chart.getDatasetMeta(i);
                    meta.data.forEach((bar, index) => {
                        const count = dataset.data[index];
                        if (count !== undefined && count !== null) {
                            ctx.save();
                            ctx.font = 'bold 11px ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif';
                            ctx.fillStyle = '#1e293b';
                            ctx.textAlign = 'left';
                            ctx.textBaseline = 'middle';
                            ctx.fillText(`${count} orang`, bar.x + 6, bar.y);
                            ctx.restore();
                        }
                    });
                });
            }
        };

        new Chart(barElem.getContext('2d'), {
            type: 'bar',
            data: {
                labels: ['PDM (Majelis)', 'GTY (Guru Tetap)', 'GTT (Guru Tdk Tetap)', 'PTY (Pegawai Tetap)', 'PTT (Pegawai Tdk Tetap)', 'Lainnya'],
                datasets: [{
                    label: 'Jumlah Pegawai',
                    data: catCounts,
                    backgroundColor: [
                        '#64748b', // PDM (Slate)
                        '#0284c7', // GTY (Sky Blue)
                        '#0891b2', // GTT (Cyan)
                        '#059669', // PTY (Emerald)
                        '#d97706', // PTT (Amber)
                        '#7c3aed'  // LAINNYA (Violet)
                    ],
                    borderRadius: 7,
                    borderSkipped: false,
                    barThickness: 13,
                    maxBarThickness: 16
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                layout: {
                    padding: {
                        right: 65,
                        top: 0,
                        bottom: 0
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        grid: {
                            color: '#f8fafc'
                        },
                        ticks: {
                            precision: 0,
                            font: { size: 9, weight: '500' },
                            color: '#94a3b8'
                        }
                    },
                    y: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            font: { size: 10, weight: '600' },
                            color: '#334155'
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const val = context.parsed.x || 0;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = total > 0 ? ((val / total) * 100).toFixed(1) : 0;
                                return ` Jumlah: ${val} orang (${percentage}%)`;
                            }
                        }
                    }
                }
            },
            plugins: [countTextPlugin]
        });
    }
});
</script>
@endpush

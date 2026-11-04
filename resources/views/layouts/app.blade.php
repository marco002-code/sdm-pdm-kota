<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Sistem Informasi SDM PDM Kota Yogyakarta' }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Inter', sans-serif; }
        /* Custom sleek scrollbar for sidebar nav if needed */
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #334155; border-radius: 4px; }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased min-h-screen flex flex-col">
    <!-- 1. STICKY MOBILE TOPBAR (Always visible on mobile without scrolling) -->
    <header class="md:hidden sticky top-0 z-40 bg-slate-900 text-white border-b border-slate-800 px-4 py-2.5 flex items-center justify-between shadow-md">
        <div class="flex items-center gap-2.5">
            <button id="mobileMenuToggle" type="button" class="p-1.5 rounded-xl text-slate-300 hover:text-white hover:bg-slate-800 transition" aria-label="Buka Menu">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>
            <div class="flex items-center gap-2">
                <div class="w-7 h-7 rounded-lg bg-blue-600 flex items-center justify-center font-black text-xs text-white shadow-sm shadow-blue-500/30">SDM</div>
                <span class="font-bold text-sm tracking-tight">PDM Kota Yogya</span>
            </div>
        </div>

        <!-- Profil & Logout Langsung di Mobile Topbar (Tanpa Perlu Scroll) -->
        <div class="flex items-center gap-2">
            <div class="flex items-center gap-1.5 bg-slate-800/80 px-2 py-1 rounded-xl border border-slate-700/60">
                <div class="w-6 h-6 rounded-full bg-gradient-to-tr from-blue-500 to-indigo-600 text-white text-[10px] font-bold flex items-center justify-center shadow-xs">
                    {{ substr(auth()->user()->name ?? 'U', 0, 2) }}
                </div>
                <span class="text-xs font-semibold text-slate-200 truncate max-w-[85px]">{{ strtok(auth()->user()->name ?? 'Pengguna', ' ') }}</span>
            </div>
            <form action="{{ route('logout') }}" method="POST" class="inline">
                @csrf
                <button type="submit" title="Keluar (Logout)" class="p-1.5 rounded-xl bg-rose-500/20 hover:bg-rose-600 text-rose-400 hover:text-white transition flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                </button>
            </form>
        </div>
    </header>

    <!-- Backdrop Overlay untuk Mobile Drawer -->
    <div id="mobileBackdrop" class="fixed inset-0 z-40 bg-slate-950/60 backdrop-blur-xs hidden md:hidden transition-opacity duration-300"></div>

    <div class="flex-1 flex flex-col md:flex-row min-h-screen">
        <!-- 2. SIDEBAR NAVIGATION (Sticky h-screen di Desktop; Drawer di Mobile) -->
        <aside id="sidebarNav" class="fixed inset-y-0 left-0 z-50 w-64 bg-slate-900 text-white flex flex-col justify-between flex-shrink-0 transition-transform duration-300 transform -translate-x-full md:translate-x-0 md:sticky md:top-0 md:h-screen md:max-h-screen">
            <!-- Sidebar Top: Brand & Close Button (Mobile) -->
            <div class="p-5 border-b border-slate-800 flex items-center justify-between flex-shrink-0">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center font-black text-lg text-white shadow-md shadow-blue-500/30">
                        SDM
                    </div>
                    <div>
                        <div class="font-bold text-base leading-tight">PDM Kota Yogyakarta</div>
                        <div class="text-[11px] text-slate-400 font-medium">Sistem Informasi SDM</div>
                    </div>
                </div>
                <button id="closeMobileMenu" type="button" class="md:hidden p-1.5 text-slate-400 hover:text-white hover:bg-slate-800 rounded-lg transition" aria-label="Tutup Menu">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <!-- Sidebar Middle: Menu Navigasi (Sesuai update.txt) -->
            <nav class="flex-1 overflow-y-auto p-4 space-y-1 text-sm custom-scrollbar">
                <!-- 1. MENU UTAMA -->
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-4 py-2.5 {{ request()->routeIs('dashboard') ? 'bg-blue-600 text-white font-semibold shadow-sm' : 'text-slate-300 hover:text-white hover:bg-slate-800 font-medium' }} rounded-xl transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    <span>Dashboard</span>
                </a>

                <a href="{{ route('employees.index') }}" class="flex items-center gap-3 px-4 py-2 {{ request()->routeIs('employees.*') ? 'bg-blue-600 text-white font-semibold shadow-sm' : 'text-slate-300 hover:text-white hover:bg-slate-800 font-medium' }} rounded-xl transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    <span>Data Induk Pegawai</span>
                </a>

                @if(!auth()->user()->isPegawai())
                <a href="{{ route('career.performance', ['type' => 'dp3']) }}" class="flex items-center gap-3 px-4 py-2 {{ (request()->routeIs('career.performance*') && request('type') !== 'pkks') ? 'bg-blue-600 text-white font-semibold shadow-sm' : 'text-slate-300 hover:text-white hover:bg-slate-800 font-medium' }} rounded-xl transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <span>Input Nilai DP3</span>
                </a>

                <a href="{{ route('career.performance', ['type' => 'pkks']) }}" class="flex items-center gap-3 px-4 py-2 {{ (request()->routeIs('career.performance*') && request('type') === 'pkks') ? 'bg-blue-600 text-white font-semibold shadow-sm' : 'text-slate-300 hover:text-white hover:bg-slate-800 font-medium' }} rounded-xl transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                    <span>Input Nilai PKKS</span>
                </a>
                @endif

                <a href="{{ route('audits.quarterly') }}" class="flex items-center justify-between px-4 py-2 {{ request()->routeIs('audits.*') ? 'bg-blue-600 text-white font-semibold shadow-sm' : 'text-slate-300 hover:text-white hover:bg-slate-800 font-medium' }} rounded-xl transition">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                        <span>Pembaruan Data Berkala</span>
                    </div>
                </a>

                <!-- 2. PROSES -->
                <div class="pt-3 pb-1 px-4 text-[12px] font-bold uppercase tracking-wider text-slate-400 flex items-center gap-1.5">
                    <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                    <span>PROSES</span>
                </div>

                <a href="{{ route('recruitments.index') }}" class="flex items-center justify-between px-4 py-2 {{ request()->routeIs('recruitments.*') ? 'bg-blue-600 text-white font-semibold shadow-sm' : 'text-slate-300 hover:text-white hover:bg-slate-800 font-medium' }} rounded-xl transition opacity-85 hover:opacity-100">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                        <span>Rekrutmen</span>
                    </div>
                    <span class="text-[9px] px-1.5 py-0.5 rounded font-bold bg-slate-700 text-amber-300 border border-amber-500/30">Beta</span>
                </a>

                <a href="{{ route('coachings.index') }}" class="flex items-center gap-3 px-4 py-2 {{ request()->routeIs('coachings.*') ? 'bg-blue-600 text-white font-semibold shadow-sm' : 'text-slate-300 hover:text-white hover:bg-slate-800 font-medium' }} rounded-xl transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    <span>Pembinaan Pegawai</span>
                </a>

                <!-- 3. KARIR -->
                <div class="pt-3 pb-1 px-4 text-[12px] font-bold uppercase tracking-wider text-slate-400 flex items-center gap-1.5">
                    <span class="w-1.5 h-1.5 rounded-full bg-purple-500"></span>
                    <span>KARIR</span>
                </div>

                <a href="{{ route('promotions.index') }}" class="flex items-center gap-3 px-4 py-2 {{ request()->routeIs('promotions.*') ? 'bg-blue-600 text-white font-semibold shadow-sm' : 'text-slate-300 hover:text-white hover:bg-slate-800 font-medium' }} rounded-xl transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                    <span>Kenaikan Pangkat</span>
                </a>

                <a href="{{ route('leadership.headmasters') }}" class="flex items-center gap-3 px-4 py-2 {{ request()->routeIs('leadership.headmasters*') ? 'bg-blue-600 text-white font-semibold shadow-sm' : 'text-slate-300 hover:text-white hover:bg-slate-800 font-medium' }} rounded-xl transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
                    <span>Promosi KS</span>
                </a>

                <a href="{{ route('leadership.vice_headmasters') }}" class="flex items-center gap-3 px-4 py-2 {{ request()->routeIs('leadership.vice_headmasters*') ? 'bg-blue-600 text-white font-semibold shadow-sm' : 'text-slate-300 hover:text-white hover:bg-slate-800 font-medium' }} rounded-xl transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    <span>Promosi Tim Manajemen</span>
                </a>

                <a href="{{ route('proposals.recap') }}" class="flex items-center justify-between px-4 py-2 {{ request()->routeIs('proposals.*') ? 'bg-indigo-600 text-white font-semibold shadow-sm' : 'text-slate-300 hover:text-white hover:bg-slate-800 font-medium' }} rounded-xl transition">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-indigo-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <span>Rekapitulasi Usulan</span>
                    </div>
                    <span class="text-[9px] px-1.5 py-0.5 rounded font-bold bg-indigo-500/30 text-indigo-200 border border-indigo-400/30">Terpadu</span>
                </a>

                <a href="{{ route('career.achievements') }}" class="flex items-center justify-between px-4 py-2 {{ request()->routeIs('career.achievements*') ? 'bg-blue-600 text-white font-semibold shadow-sm' : 'text-slate-300 hover:text-white hover:bg-slate-800 font-medium' }} rounded-xl transition">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
                        <span>Prestasi Pegawai</span>
                    </div>
                </a>

                <!-- 4. MASTER -->
                <div class="pt-3 pb-1 px-4 text-[12px] font-bold uppercase tracking-wider text-slate-400 flex items-center gap-1.5">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                    <span>MASTER</span>
                </div>

                <a href="{{ route('units.index') }}" class="flex items-center gap-3 px-4 py-2 {{ request()->routeIs('units.*') ? 'bg-blue-600 text-white font-semibold shadow-sm' : 'text-slate-300 hover:text-white hover:bg-slate-800 font-medium' }} rounded-xl transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    <span>Unit Kerja AUM</span>
                </a>

                <a href="{{ route('master-npms.index') }}" class="flex items-center justify-between px-4 py-2 {{ request()->routeIs('master-npms.*') ? 'bg-blue-600 text-white font-semibold shadow-sm' : 'text-slate-300 hover:text-white hover:bg-slate-800 font-medium' }} rounded-xl transition">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"/></svg>
                        <span>Bank Data NPM</span>
                    </div>
     
                </a>

                <a href="{{ route('mutations.index') }}" class="flex items-center gap-3 px-4 py-2 {{ request()->routeIs('mutations.*') ? 'bg-blue-600 text-white font-semibold shadow-sm' : 'text-slate-300 hover:text-white hover:bg-slate-800 font-medium' }} rounded-xl transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                    <span>Riwayat Mutasi</span>
                </a>
            </nav>

            <!-- Sidebar Bottom: Profil Pengguna & Tombol Logout (Selalu Pinned & Langsung Terlihat Tanpa Scroll) -->
            <div class="p-4 border-t border-slate-800 bg-slate-950/80 flex-shrink-0">
                <div class="flex items-center gap-2.5 mb-3 overflow-hidden">
                    <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-blue-600 to-indigo-600 flex items-center justify-center font-bold text-xs text-white shadow-sm flex-shrink-0">
                        {{ substr(auth()->user()->name ?? 'U', 0, 2) }}
                    </div>
                    <div class="overflow-hidden">
                        <div class="text-xs font-bold text-white truncate">{{ auth()->user()->name ?? 'Pengguna' }}</div>
                        <div class="text-[10px] text-slate-400 truncate flex items-center gap-1">
                            <span class="w-1.5 h-1.5 rounded-full {{ auth()->user()->isYayasanLevel() ? 'bg-amber-400' : 'bg-emerald-400' }} inline-block"></span>
                            <span>{{ auth()->user()->role_title }}</span>
                            @if(auth()->user()?->unit)
                                <span>• {{ auth()->user()->unit->code }}</span>
                            @endif
                            @if(auth()->user()->isKetuaMajelis())
                                <span class="text-[9px] px-1 py-0.5 rounded bg-amber-500/20 text-amber-300 font-bold border border-amber-500/30">Approval</span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="space-y-1.5">
                    <a href="{{ route('landing') }}" class="w-full py-1.5 px-3 bg-slate-800/80 hover:bg-slate-700 text-slate-300 hover:text-white text-xs font-semibold rounded-xl transition flex items-center justify-center gap-1.5">
                        <svg class="w-3.5 h-3.5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                        <span>Lihat Beranda Publik</span>
                    </a>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full py-2 px-3 bg-slate-800 hover:bg-rose-600 text-slate-300 hover:text-white text-xs font-semibold rounded-xl transition flex items-center justify-center gap-2 shadow-sm group">
                            <svg class="w-4 h-4 text-slate-400 group-hover:text-white transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                            <span>Keluar (Logout)</span>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <!-- 3. MAIN CONTENT AREA -->
        <main class="flex-1 flex flex-col min-w-0 min-h-screen">
            <!-- Desktop Sticky Topbar (Selalu terlihat di Desktop/Laptop tanpa perlu scroll) -->
            <header class="hidden md:flex sticky top-0 z-30 bg-white/85 backdrop-blur-xl border-b border-slate-200/80 px-6 py-2 items-center justify-between shadow-xs flex-shrink-0">
                <div class="flex items-center gap-2 text-xs text-slate-500 font-medium">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span>Portal SIM SDM • Pimpinan Daerah Muhammadiyah Kota Yogyakarta</span>
                    @if(auth()->user()->unit)
                        <span class="px-2 py-0.5 rounded-md bg-blue-50 text-blue-700 font-semibold border border-blue-200">{{ auth()->user()->unit->name }}</span>
                    @endif
                </div>

                <!-- Quick Profile Chip & Quick Logout di Top Header Desktop -->
                <div class="flex items-center gap-2.5">
                    <a href="{{ route('landing') }}" class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-semibold text-slate-600 hover:text-blue-600 bg-slate-100 hover:bg-blue-50 border border-slate-200 hover:border-blue-200 rounded-xl transition" title="Lihat Beranda Publik">
                        <svg class="w-3.5 h-3.5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                        <span>Beranda Publik</span>
                    </a>
                    <div class="flex items-center gap-2 px-2.5 py-1 rounded-xl bg-slate-100/90 border border-slate-200/80">
                        <div class="w-6 h-6 rounded-lg bg-gradient-to-br from-blue-600 to-indigo-600 text-white text-[11px] font-bold flex items-center justify-center shadow-xs">
                            {{ substr(auth()->user()->name ?? 'U', 0, 2) }}
                        </div>
                        <div class="text-left">
                            <div class="text-xs font-bold text-slate-800 leading-none">{{ auth()->user()->name ?? 'Pengguna' }}</div>
                            <div class="text-[10px] text-slate-500 leading-tight mt-0.5 flex items-center gap-1">
                                <span>{{ auth()->user()->role_title }}</span>
                                @if(auth()->user()?->unit) • {{ auth()->user()->unit->code }} @endif
                                @if(auth()->user()->isKetuaMajelis())
                                    <span class="text-[9px] px-1 py-0.5 rounded bg-amber-100 text-amber-800 font-bold border border-amber-200">Approval</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <form action="{{ route('logout') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" title="Keluar dari Sistem (Logout)" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl bg-rose-50 hover:bg-rose-600 text-rose-600 hover:text-white border border-rose-200 text-xs font-semibold transition-all duration-200 group shadow-xs">
                            <svg class="w-3.5 h-3.5 text-rose-500 group-hover:text-white transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                            <span>Logout</span>
                        </button>
                    </form>
                </div>
            </header>

            <!-- Flash Alerts -->
            @if (session('success'))
                <div class="m-3.5 mb-0 p-3 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs flex items-center gap-2.5">
                    <svg class="w-4 h-4 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span class="font-medium">{{ session('success') }}</span>
                </div>
            @endif

            <div class="p-3 lg:px-4 lg:py-2.5 flex-1">
                @yield('content')
            </div>
        </main>
    </div>

    <script>
        // Interaktivitas Mobile Drawer Menu (Responsive Toggle)
        document.addEventListener('DOMContentLoaded', function () {
            const toggleBtn = document.getElementById('mobileMenuToggle');
            const closeBtn = document.getElementById('closeMobileMenu');
            const sidebar = document.getElementById('sidebarNav');
            const backdrop = document.getElementById('mobileBackdrop');

            function openMenu() {
                sidebar.classList.remove('-translate-x-full');
                backdrop.classList.remove('hidden');
                document.body.classList.add('overflow-hidden');
            }

            function closeMenu() {
                sidebar.classList.add('-translate-x-full');
                backdrop.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
            }

            if (toggleBtn) toggleBtn.addEventListener('click', openMenu);
            if (closeBtn) closeBtn.addEventListener('click', closeMenu);
            if (backdrop) backdrop.addEventListener('click', closeMenu);

            // Proteksi Form Concurrency & Double-Submit: Mencegah multi-klik bersamaan oleh admin
            document.querySelectorAll('form').forEach(form => {
                form.addEventListener('submit', function (e) {
                    // Hanya proteksi form yang bukan download PDF atau target _blank
                    if (this.target === '_blank') return;
                    
                    const submitButtons = this.querySelectorAll('button[type="submit"], input[type="submit"]');
                    if (submitButtons.length > 0) {
                        setTimeout(() => {
                            submitButtons.forEach(btn => {
                                btn.dataset.originalHtml = btn.innerHTML;
                                btn.disabled = true;
                                btn.classList.add('opacity-75', 'cursor-not-allowed');
                                if (btn.tagName.toLowerCase() === 'button') {
                                    btn.innerHTML = `
                                        <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        <span>Memproses Antrian...</span>
                                    `;
                                }
                            });
                        }, 50);

                        // Failsafe pemulihan jika form gagal validasi browser
                        setTimeout(() => {
                            submitButtons.forEach(btn => {
                                if (btn.disabled) {
                                    btn.disabled = false;
                                    btn.classList.remove('opacity-75', 'cursor-not-allowed');
                                    if (btn.dataset.originalHtml) {
                                        btn.innerHTML = btn.dataset.originalHtml;
                                    }
                                }
                            });
                        }, 12000);
                    }
                });
            });
        });
    </script>
    @stack('scripts')
</body>
</html>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk ke Sistem • SIM-SDM PDM Kota Yogyakarta</title>
    <meta name="description" content="Portal Masuk Terpadu Sistem Informasi Manajemen Kepegawaian PDM Kota Yogyakarta">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        muh: {
                            green: '#047857',
                            dark: '#031c13',
                            darker: '#02130d',
                            gold: '#f59e0b',
                            goldLight: '#fef08a',
                            greenLight: '#34d399',
                        }
                    },
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'Inter', 'sans-serif'],
                    }
                }
            }
        }
    </script>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .hero-pattern {
            background-color: #031c13;
            background-image: radial-gradient(rgba(16, 185, 129, 0.16) 1px, transparent 1px), radial-gradient(rgba(245, 158, 11, 0.10) 1px, #031c13 1px);
            background-size: 36px 36px;
            background-position: 0 0, 18px 18px;
        }
        .glow-circle {
            position: absolute;
            filter: blur(95px);
            opacity: 0.28;
            pointer-events: none;
            border-radius: 9999px;
        }
    </style>
</head>
<body class="hero-pattern min-h-screen text-slate-100 flex flex-col justify-between relative overflow-x-hidden selection:bg-emerald-600 selection:text-amber-100">

    <!-- Glow background effects (Muhammadiyah Green & Radiant Gold) -->
    <div class="glow-circle w-96 h-96 bg-emerald-600 top-0 left-10"></div>
    <div class="glow-circle w-96 h-96 bg-amber-500 bottom-10 right-10 opacity-20"></div>
    <div class="glow-circle w-64 h-64 bg-emerald-400 top-1/2 left-1/3 opacity-15"></div>

    <!-- 1. TOP NAV / HEADER BAR -->
    <nav class="relative z-20 w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-5 flex items-center justify-between">
        <a href="{{ route('landing') }}" class="inline-flex items-center gap-2 text-xs font-semibold text-slate-200 hover:text-amber-300 bg-emerald-950/70 hover:bg-emerald-900/60 px-3.5 py-2 rounded-xl border border-emerald-700/50 transition shadow-sm backdrop-blur-md group">
            <svg class="w-4 h-4 text-amber-400 group-hover:-translate-x-0.5 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            <span>Kembali ke Halaman Beranda</span>
        </a>

        <div class="flex items-center gap-2">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-950/80 border border-emerald-500/40 text-[11px] font-semibold text-emerald-300 backdrop-blur-md shadow-sm">
                <span class="w-2 h-2 rounded-full bg-amber-400 animate-pulse"></span>
                <span>Portal Kepegawaian Aktif</span>
            </span>
        </div>
    </nav>

    <!-- 2. MAIN CONTAINER -->
    <main class="relative z-10 flex-1 flex items-center justify-center px-4 py-8 sm:py-12">
        <div class="w-full max-w-xl">

            <!-- Logo & Brand Header -->
            <div class="text-center mb-7">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-gradient-to-tr from-emerald-700 via-emerald-600 to-amber-500 shadow-xl shadow-emerald-950/60 text-white text-2xl font-black mb-3 border border-amber-400/40">
                    SDM
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight flex items-center justify-center gap-2">
                    <span>Sistem Informasi SDM</span>
                </h1>
                <p class="text-emerald-200/90 text-xs sm:text-sm mt-1 max-w-md mx-auto">
                    Pimpinan Daerah Muhammadiyah Kota Yogyakarta • 76 Amal Usaha Muhammadiyah (AUM)
                </p>
            </div>

            <!-- Main Auth Card -->
            <div class="bg-slate-900/95 backdrop-blur-xl rounded-3xl shadow-2xl shadow-emerald-950/80 border border-emerald-900/60 overflow-hidden">
                
                <!-- Tab Selector: Masuk Akun vs Demo Reviewer -->
                <div class="flex border-b border-emerald-950 bg-slate-950/70 p-1.5 gap-1.5">
                    <button type="button" onclick="switchAuthTab('credentials')" id="tabBtnCredentials"
                        class="flex-1 py-2.5 px-4 rounded-2xl text-xs font-bold transition flex items-center justify-center gap-2 bg-gradient-to-r from-emerald-600 to-emerald-700 text-amber-200 shadow-md shadow-emerald-950/50 border border-amber-400/30">
                        <svg class="w-4 h-4 text-amber-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        <span>Masuk Akun Resmi</span>
                    </button>
                    <button type="button" onclick="switchAuthTab('demo')" id="tabBtnDemo"
                        class="flex-1 py-2.5 px-4 rounded-2xl text-xs font-bold transition flex items-center justify-center gap-2 text-slate-400 hover:text-amber-300 hover:bg-slate-800/80">
                        <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        <span>Akses Demo (1-Click)</span>
                        <span class="text-[10px] px-1.5 py-0.2 rounded-md bg-amber-500/20 text-amber-300 border border-amber-500/30 font-mono font-semibold">Uji Coba</span>
                    </button>
                </div>

                <!-- Flash & Alert Messages -->
                <div class="p-6 pb-0">
                    @if (session('error'))
                        <div class="mb-4 p-4 rounded-2xl bg-rose-950/80 border border-rose-800/80 text-rose-200 text-xs flex items-center gap-3">
                            <svg class="w-5 h-5 flex-shrink-0 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span class="flex-1">{{ session('error') }}</span>
                        </div>
                    @endif

                    @if (session('info'))
                        <div class="mb-4 p-4 rounded-2xl bg-emerald-950/80 border border-emerald-800/80 text-emerald-200 text-xs flex items-center gap-3">
                            <svg class="w-5 h-5 flex-shrink-0 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span class="flex-1">{{ session('info') }}</span>
                        </div>
                    @endif

                    @if (session('success'))
                        <div class="mb-4 p-4 rounded-2xl bg-emerald-950/80 border border-emerald-800/80 text-emerald-200 text-xs flex items-center gap-3">
                            <svg class="w-5 h-5 flex-shrink-0 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <span class="flex-1">{{ session('success') }}</span>
                        </div>
                    @endif

                    @if (isset($errors) && $errors->any())
                        <div class="mb-4 p-4 rounded-2xl bg-rose-950/80 border border-rose-800/80 text-rose-200 text-xs">
                            <div class="font-bold mb-1">Gagal Masuk:</div>
                            <ul class="list-disc list-inside space-y-1 text-[11px]">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>

                <!-- TAB CONTENT 1: MASUK KREDENSIAL RESMI -->
                <div id="tabContentCredentials" class="p-6 sm:p-8 pt-4">
                    <form action="{{ route('login.post') }}" method="POST" class="space-y-5" id="loginForm">
                        @csrf
                        
                        <div>
                            <label for="email" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">
                                Email atau Username
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-emerald-500/70">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.206"/></svg>
                                </div>
                                <input type="text" name="email" id="email" value="{{ old('email', 'admin@yayasan.sch.id') }}" required autofocus
                                    class="w-full pl-10 pr-4 py-3 bg-slate-950 border border-emerald-900/70 rounded-xl text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-amber-400 transition text-sm font-medium"
                                    placeholder="nama@sekolah.sch.id / username">
                            </div>
                        </div>

                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <label for="password" class="block text-xs font-bold uppercase tracking-wider text-slate-300">
                                    Kata Sandi
                                </label>
                                <button type="button" onclick="openForgotModal()" class="text-amber-400 hover:text-yellow-300 text-xs font-semibold">
                                    Lupa kata sandi?
                                </button>
                            </div>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-emerald-500/70">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                </div>
                                <input type="password" name="password" id="password" value="password" required
                                    class="w-full pl-10 pr-12 py-3 bg-slate-950 border border-emerald-900/70 rounded-xl text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-amber-400 transition text-sm font-medium"
                                    placeholder="••••••••">
                                <button type="button" onclick="togglePasswordVisibility()" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-amber-300 transition" aria-label="Lihat kata sandi">
                                    <svg id="eyeIcon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </button>
                            </div>
                        </div>

                        <div class="flex items-center justify-between text-xs">
                            <label class="flex items-center gap-2 cursor-pointer text-slate-300 select-none">
                                <input type="checkbox" name="remember" class="w-4 h-4 text-emerald-600 bg-slate-950 rounded border-emerald-800 focus:ring-emerald-500">
                                <span>Ingat sesi masuk saya</span>
                            </label>
                            <span class="text-emerald-400/80 font-medium flex items-center gap-1">
                                <svg class="w-3.5 h-3.5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                Sesi Terenkripsi SSL
                            </span>
                        </div>

                        <button type="submit"
                            class="w-full py-3.5 px-4 bg-gradient-to-r from-emerald-600 via-emerald-500 to-emerald-600 hover:from-emerald-500 hover:to-emerald-400 text-white font-bold rounded-xl shadow-lg shadow-emerald-950/60 hover:shadow-emerald-800/80 border border-amber-400/30 active:scale-[0.99] transition duration-200 text-sm flex items-center justify-center gap-2 group">
                            <span>Masuk ke Sistem Kepegawaian</span>
                            <svg class="w-4 h-4 text-amber-300 group-hover:translate-x-0.5 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </button>
                    </form>

                    <!-- Notice & Fast-Switch to Demo -->
                    <div class="mt-6 pt-5 border-t border-emerald-950 text-center">
                        <p class="text-xs text-slate-400 mb-2">
                            Sedang melakukan pengujian atau simulasi evaluasi sistem?
                        </p>
                        <button type="button" onclick="switchAuthTab('demo')" class="text-xs font-semibold text-amber-400 hover:text-yellow-300 underline underline-offset-2 flex items-center justify-center gap-1.5 mx-auto group">
                            <span>Buka Panel Akses Cepat Akun Demo (1-Click)</span>
                            <span class="group-hover:translate-x-0.5 transition">→</span>
                        </button>
                    </div>
                </div>

                <!-- TAB CONTENT 2: MODE PENGUJIAN / AKUN DEMO -->
                <div id="tabContentDemo" class="hidden p-6 sm:p-8 pt-4 space-y-6">
                    
                    <div class="p-3.5 rounded-2xl bg-emerald-950/60 border border-emerald-600/40 text-xs text-emerald-200 flex items-start gap-3">
                        <svg class="w-5 h-5 flex-shrink-0 text-amber-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <div class="leading-relaxed">
                            <strong class="font-bold text-amber-300">Pusat Uji Coba Terpadu:</strong>
                            Pilih salah satu peran pengguna di bawah untuk login langsung tanpa perlu mengetik kata sandi (semua akun demo menggunakan sandi: <code class="bg-emerald-900/80 px-1 py-0.5 rounded font-mono text-amber-300 border border-emerald-700/50">password</code>).
                        </div>
                    </div>

                    <!-- 1. Enam Akun Utama Pusat & Pimpinan -->
                    <div>
                        <div class="flex items-center justify-between mb-3">
                            <h2 class="text-xs font-extrabold uppercase tracking-wider text-slate-200 flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-amber-400"></span>
                                <span>Akun Utama & Pimpinan Eksekutif</span>
                            </h2>
                            <span class="text-[10px] text-emerald-400 font-medium">Pusat, Yayasan & Unit</span>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                            @foreach ($demoUsers as $demo)
                                <form action="{{ route('login.quick') }}" method="POST" class="m-0">
                                    @csrf
                                    <input type="hidden" name="email" value="{{ $demo['email'] }}">
                                    <button type="submit"
                                        class="w-full text-left p-3 rounded-2xl bg-slate-950 border border-emerald-950 hover:border-amber-400/50 hover:bg-emerald-950/30 transition duration-150 group">
                                        <div class="flex items-start justify-between gap-1 mb-1">
                                            <span class="text-[10px] font-bold px-2 py-0.5 rounded-md border {{ $demo['badge_color'] }}">
                                                {{ $demo['role_title'] }}
                                            </span>
                                            <span class="text-[10px] text-slate-400 group-hover:text-amber-300 transition font-medium">
                                                {{ $demo['level'] }}
                                            </span>
                                        </div>
                                        <div class="font-bold text-white text-xs truncate group-hover:text-amber-200 transition">
                                            {{ $demo['name'] }}
                                        </div>
                                        <div class="text-[11px] text-emerald-400/80 truncate mt-0.5 font-mono">
                                            {{ $demo['email'] }}
                                        </div>
                                        <div class="text-[10px] text-slate-400 mt-2 flex items-center justify-between border-t border-emerald-950 pt-1.5">
                                            <span class="italic text-slate-400 truncate max-w-[170px]">{{ $demo['desc'] }}</span>
                                            <span class="font-mono text-amber-400 group-hover:text-yellow-300 group-hover:translate-x-0.5 transition font-bold flex-shrink-0">Masuk →</span>
                                        </div>
                                    </button>
                                </form>
                            @endforeach
                        </div>
                    </div>

                    <!-- 2. Selector Tata Usaha (76 AUM) -->
                    <div class="p-4 rounded-2xl bg-emerald-950/60 border border-emerald-600/50">
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-amber-400 animate-pulse"></span>
                                <span class="text-xs font-bold uppercase tracking-wider text-emerald-300">
                                    Operator Tata Usaha (76 AUM)
                                </span>
                            </div>
                            <span class="text-[10px] text-amber-300 font-mono bg-amber-500/20 px-2 py-0.5 rounded-md border border-amber-500/30">Lengkap 76 Unit</span>
                        </div>
                        <p class="text-[11px] text-slate-300 mb-3">
                            Pilih nama sekolah untuk masuk sebagai operator TU dengan hak akses input biodata & validasi Master NPM unit tersebut.
                        </p>

                        <form action="{{ route('login.quick') }}" method="POST" id="tuQuickForm" class="space-y-2">
                            @csrf
                            <div>
                                <select name="email" id="tuSelect" onchange="updateTuEmail(this.value)"
                                    class="w-full px-3.5 py-2.5 text-xs bg-slate-950 border border-emerald-700/60 rounded-xl text-white font-medium focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                                    <option value="">-- Pilih Satuan Pendidikan / AUM untuk Login TU --</option>
                                    @foreach($allTuUsers as $tuUser)
                                        <option value="{{ $tuUser->email }}">
                                            [{{ $tuUser->unit->level ?? 'Unit' }}] {{ $tuUser->unit->name ?? 'Unit' }} • {{ $tuUser->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="flex items-center justify-between gap-2 pt-1">
                                <button type="button" onclick="fillSelectedTuToMainForm()" class="text-[11px] text-amber-400 hover:text-yellow-300 hover:underline font-semibold">
                                    Salin ke Form Utama
                                </button>
                                <button type="submit" id="tuLoginBtn"
                                    class="px-4 py-2 bg-gradient-to-r from-emerald-600 to-emerald-500 hover:from-emerald-500 hover:to-emerald-400 text-white font-bold text-xs rounded-xl border border-amber-400/30 shadow-md shadow-emerald-950/50 transition flex items-center justify-center gap-1.5 flex-shrink-0">
                                    <span>Masuk Sebagai TU Unit</span>
                                    <svg class="w-3.5 h-3.5 text-amber-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Return to credentials button -->
                    <div class="text-center pt-2">
                        <button type="button" onclick="switchAuthTab('credentials')" class="text-xs text-emerald-400 hover:text-amber-300 font-semibold underline underline-offset-2">
                            ← Kembali ke Form Masuk Kredensial
                        </button>
                    </div>

                </div>

                <!-- Card Footer / Security Guarantee -->
                <div class="bg-slate-950/80 border-t border-emerald-950 p-4 text-center text-[11px] text-slate-400 flex items-center justify-center gap-2">
                    <svg class="w-4 h-4 text-amber-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    <span>Dilindungi enkripsi sesi aman • Majelis Dikdasmen & PNF PDM Kota Yogyakarta</span>
                </div>

            </div>

            <!-- Page Bottom Copyright -->
            <p class="text-center text-xs text-slate-400 mt-6">
                &copy; {{ date('Y') }} Majelis Dikdasmen & PNF PDM Kota Yogyakarta
            </p>

        </div>
    </main>

    <!-- 3. MODAL LUPA KATA SANDI -->
    <div id="forgotPasswordModal" class="fixed inset-0 z-50 bg-slate-950/85 backdrop-blur-sm hidden items-center justify-center p-4">
        <div class="bg-slate-900 border border-emerald-700/60 rounded-3xl max-w-md w-full p-6 sm:p-7 shadow-2xl relative text-slate-200">
            <div class="w-12 h-12 rounded-2xl bg-emerald-500/20 text-amber-400 border border-emerald-500/30 flex items-center justify-center mb-4">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
            </div>
            
            <h3 class="text-lg font-bold text-white mb-2">Prosedur Reset Kata Sandi</h3>
            <p class="text-xs text-slate-300 leading-relaxed mb-4">
                Demi keamanan data kepegawaian dan ketertiban administrasi, pengaturan ulang kata sandi dikelola secara terpusat:
            </p>

            <ul class="text-xs text-slate-300 space-y-2.5 mb-6">
                <li class="flex items-start gap-2">
                    <span class="text-amber-400 font-bold">•</span>
                    <span><strong>Untuk Guru / Pegawai:</strong> Hubungi Kepala Tata Usaha (TU) di satuan pendidikan Anda untuk verifikasi identitas dan reset kredensial.</span>
                </li>
                <li class="flex items-start gap-2">
                    <span class="text-amber-400 font-bold">•</span>
                    <span><strong>Untuk Kepala Sekolah & TU:</strong> Hubungi Tim IT / HR Pusat Majelis Dikdasmen & PNF PDM Kota Yogyakarta melalui email resmi: <code class="text-amber-300 font-mono bg-emerald-950 px-1.5 py-0.5 rounded border border-emerald-800">dikdasmen@pdmkotayogya.or.id</code>.</span>
                </li>
            </ul>

            <button type="button" onclick="closeForgotModal()" class="w-full py-2.5 rounded-xl bg-gradient-to-r from-emerald-600 to-emerald-500 hover:from-emerald-500 hover:to-emerald-400 text-white font-bold text-xs border border-amber-400/30 transition duration-200">
                Saya Mengerti, Tutup
            </button>
        </div>
    </div>

    <!-- JAVASCRIPT LOGIC -->
    <script>
        function switchAuthTab(tab) {
            const btnCreds = document.getElementById('tabBtnCredentials');
            const btnDemo = document.getElementById('tabBtnDemo');
            const contentCreds = document.getElementById('tabContentCredentials');
            const contentDemo = document.getElementById('tabContentDemo');

            if (tab === 'credentials') {
                btnCreds.className = 'flex-1 py-2.5 px-4 rounded-2xl text-xs font-bold transition flex items-center justify-center gap-2 bg-gradient-to-r from-emerald-600 to-emerald-700 text-amber-200 shadow-md shadow-emerald-950/50 border border-amber-400/30';
                btnDemo.className = 'flex-1 py-2.5 px-4 rounded-2xl text-xs font-bold transition flex items-center justify-center gap-2 text-slate-400 hover:text-amber-300 hover:bg-slate-800/80';

                contentCreds.classList.remove('hidden');
                contentDemo.classList.add('hidden');
            } else {
                btnDemo.className = 'flex-1 py-2.5 px-4 rounded-2xl text-xs font-bold transition flex items-center justify-center gap-2 bg-gradient-to-r from-amber-500 to-yellow-500 text-slate-950 font-black shadow-md shadow-amber-500/20';
                btnCreds.className = 'flex-1 py-2.5 px-4 rounded-2xl text-xs font-bold transition flex items-center justify-center gap-2 text-slate-400 hover:text-emerald-300 hover:bg-slate-800/80';

                contentCreds.classList.add('hidden');
                contentDemo.classList.remove('hidden');
            }
        }

        function togglePasswordVisibility() {
            const pwd = document.getElementById('password');
            const eyeIcon = document.getElementById('eyeIcon');
            if (pwd.type === 'password') {
                pwd.type = 'text';
                eyeIcon.innerHTML = `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/>`;
            } else {
                pwd.type = 'password';
                eyeIcon.innerHTML = `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>`;
            }
        }

        function updateTuEmail(email) {
            // Optional callback
        }

        function fillSelectedTuToMainForm() {
            const sel = document.getElementById('tuSelect');
            if (!sel.value) {
                alert('Silakan pilih salah satu unit sekolah terlebih dahulu.');
                return;
            }
            document.getElementById('email').value = sel.value;
            document.getElementById('password').value = 'password';
            switchAuthTab('credentials');
        }

        function openForgotModal() {
            const modal = document.getElementById('forgotPasswordModal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeForgotModal() {
            const modal = document.getElementById('forgotPasswordModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        window.addEventListener('DOMContentLoaded', () => {
            const params = new URLSearchParams(window.location.search);
            if (params.get('demo') === '1') {
                switchAuthTab('demo');
            }
        });
    </script>
</body>
</html>

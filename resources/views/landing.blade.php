<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIM-SDM PDM Kota Yogyakarta • Sistem Informasi Manajemen Kepegawaian</title>
    <meta name="description" content="Sistem Informasi Manajemen Sumber Daya Manusia PDM Kota Yogyakarta menaungi 76 Amal Usaha Muhammadiyah (AUM).">

    <!-- Favicon & Touch Icons -->
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">

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
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .hero-pattern {
            background-color: #031c13;
            background-image: radial-gradient(rgba(16, 185, 129, 0.16) 1px, transparent 1px), radial-gradient(rgba(245, 158, 11, 0.10) 1px, #031c13 1px);
            background-size: 32px 32px;
            background-position: 0 0, 16px 16px;
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
<body class="hero-pattern min-h-screen text-slate-100 flex flex-col justify-between relative selection:bg-emerald-600 selection:text-amber-100">

    <!-- Glow background (Muhammadiyah Green & Golden Amber) -->
    <div class="glow-circle w-96 h-96 bg-emerald-600 top-10 left-1/4"></div>
    <div class="glow-circle w-80 h-80 bg-amber-500 bottom-10 right-1/4 opacity-20"></div>
    <div class="glow-circle w-64 h-64 bg-emerald-400 top-1/2 right-10 opacity-15"></div>

    <!-- 1. HEADER RINGKAS -->
    <header class="relative z-10 w-full max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-6 flex items-center justify-between">
        <a href="{{ route('landing') }}" class="flex items-center gap-3 group">
            <img src="{{ asset('images/logo-muhammadiyah.png') }}" alt="Logo Muhammadiyah"
                class="w-10 h-10 rounded-xl object-contain shadow-lg shadow-emerald-950/60 border border-emerald-500/40 p-0.5 bg-emerald-950/80 group-hover:scale-105 transition">
            <div>
                <span class="font-extrabold text-base text-white tracking-tight flex items-center gap-1.5">
                    SIM-SDM
                    <span class="text-[10px] px-1.5 py-0.2 rounded bg-amber-500/20 text-amber-300 border border-amber-500/30 font-semibold">PDM</span>
                </span>
                <span class="text-xs text-emerald-300/80 block -mt-0.5">PDM Kota Yogyakarta</span>
            </div>
        </a>

        <div class="flex items-center gap-3">
            <a href="https://pdmjogja.org" target="_blank" rel="noopener noreferrer" class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium text-emerald-300/90 hover:text-white hover:bg-emerald-950/60 transition border border-emerald-800/40">
                <svg class="w-3.5 h-3.5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/></svg>
                <span>pdmjogja.org</span>
            </a>
            @auth
                <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-gradient-to-r from-emerald-600 to-emerald-700 hover:from-emerald-500 hover:to-emerald-600 text-amber-200 hover:text-white font-bold text-xs shadow-md shadow-emerald-950/50 border border-amber-400/30 transition duration-150">
                    <svg class="w-4 h-4 text-amber-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    <span>Buka Dasbor ({{ strtok(auth()->user()->name, ' ') }})</span>
                </a>
            @else
                <a href="{{ route('login') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-gradient-to-r from-emerald-600 to-emerald-700 hover:from-emerald-500 hover:to-emerald-600 text-white font-bold text-xs shadow-md shadow-emerald-950/50 border border-amber-400/30 transition duration-150 group">
                    <span>Masuk ke Portal</span>
                    <svg class="w-3.5 h-3.5 text-amber-300 group-hover:translate-x-0.5 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </a>
            @endauth
        </div>
    </header>

    <!-- 2. HERO UTAMA: PENJELASAN SINGKAT & TO-THE-POINT -->
    <main class="relative z-10 flex-1 flex flex-col justify-center px-4 py-12 max-w-4xl mx-auto text-center">
        
        <!-- Badge Singkat -->
        <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-emerald-950/80 border border-emerald-500/30 text-xs text-amber-300 font-medium mx-auto mb-6 shadow-sm backdrop-blur-md">
            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
            <span>Majelis Dikdasmen & PNF • PDM Kota Yogyakarta</span>
        </div>

        <!-- Judul Utama -->
        <h1 class="text-3xl sm:text-5xl font-extrabold text-white tracking-tight leading-tight mb-5">
            Data Induk Kepegawaian <br/>
            <span class="text-transparent bg-clip-text bg-gradient-to-r from-emerald-300 via-amber-200 to-amber-400">Majelis Dikdasmen & PNF</span> PDM Kota Yogyakarta
        </h1>

        <!-- Deskripsi Singkat Aplikasi -->
        <p class="text-sm sm:text-base text-slate-300 leading-relaxed max-w-2xl mx-auto mb-8 font-normal">
            Sistem Informasi Manajemen Sumber Daya Manusia (SIM-SDM) <strong class="text-amber-300 font-semibold">AUM</strong> se-Kota Yogyakarta. Menyatukan data pegawai, riwayat karir, dan pembinaan persyarikatan secara akurat dan terpusat.
        </p>

        <!-- Tombol Aksi Utama -->
        <div class="flex items-center justify-center mb-14">
            <a href="{{ route('login') }}" class="w-full sm:w-auto px-8 py-3.5 rounded-xl bg-gradient-to-r from-emerald-600 via-emerald-500 to-emerald-600 hover:from-emerald-500 hover:to-emerald-400 text-white font-bold text-sm shadow-lg shadow-emerald-900/60 border border-amber-400/40 hover:scale-[1.01] active:scale-[0.99] transition duration-150 flex items-center justify-center gap-2 group">
                <span>Masuk ke Portal Kepegawaian</span>
                <svg class="w-4 h-4 text-amber-300 group-hover:translate-x-0.5 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
            </a>
        </div>

        <!-- 3 Poin Inti Aplikasi (Sederhana & Padat) -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-left">
            
            <div class="p-5 rounded-2xl bg-emerald-950/50 border border-emerald-800/60 hover:border-emerald-500/50 transition backdrop-blur-md group">
                <div class="w-9 h-9 rounded-lg bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 flex items-center justify-center mb-3 group-hover:scale-105 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"/></svg>
                </div>
                <h2 class="text-sm font-bold text-white mb-1 group-hover:text-emerald-200 transition">Data Induk & Multi-ID</h2>
                <p class="text-xs text-slate-300 leading-relaxed">
                    Perekaman biodata pegawai dengan verifikasi Bank Data Master NPM terpusat (No. 1 s.d. terkini), NBM, NIK, dan NUPTK.
                </p>
            </div>

            <div class="p-5 rounded-2xl bg-emerald-950/50 border border-emerald-800/60 hover:border-amber-500/50 transition backdrop-blur-md group">
                <div class="w-9 h-9 rounded-lg bg-amber-500/20 text-amber-300 border border-amber-500/30 flex items-center justify-center mb-3 group-hover:scale-105 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                </div>
                <h2 class="text-sm font-bold text-white mb-1 group-hover:text-amber-200 transition">Karir, Kinerja & Mutasi</h2>
                <p class="text-xs text-slate-300 leading-relaxed">
                    Pengelolaan kenaikan pangkat, portofolio prestasi, sertifikasi ideologi SIM (CMI), dan riwayat mutasi antar-unit AUM.
                </p>
            </div>

            <div class="p-5 rounded-2xl bg-emerald-950/50 border border-emerald-800/60 hover:border-yellow-400/50 transition backdrop-blur-md group">
                <div class="w-9 h-9 rounded-lg bg-yellow-400/20 text-yellow-300 border border-yellow-400/30 flex items-center justify-center mb-3 group-hover:scale-105 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                </div>
                <h2 class="text-sm font-bold text-white mb-1 group-hover:text-yellow-200 transition">Pembaruan Data Berkala</h2>
                <p class="text-xs text-slate-300 leading-relaxed">
                    Pemantauan pemutakhiran data per 3 bulan (Triwulanan) oleh operator TU sekolah guna menjamin kebaruan dan keakuratan data persyarikatan.
                </p>
            </div>

        </div>

    </main>

    <!-- 3. FOOTER RINGKAS -->
    <footer class="relative z-10 w-full border-t border-emerald-950/80 py-5 text-center text-xs text-slate-400">
        <div class="max-w-4xl mx-auto px-4 flex flex-col sm:flex-row items-center justify-between gap-2">
            <p>&copy; {{ date('Y') }} Pimpinan Daerah Muhammadiyah Kota Yogyakarta • Majelis Dikdasmen & PNF</p>
            <div class="flex items-center gap-3 text-[11px] text-slate-400">
                <a href="https://pdmjogja.org" target="_blank" rel="noopener noreferrer" class="text-amber-400 hover:text-amber-300 transition underline underline-offset-2">pdmjogja.org</a>
                <span>•</span>
                <a href="mailto:pdmjogja13@gmail.com" class="hover:text-slate-200 transition">pdmjogja13@gmail.com</a>
            </div>
        </div>
    </footer>

</body>
</html>

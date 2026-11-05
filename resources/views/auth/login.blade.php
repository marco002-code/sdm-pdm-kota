<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk ke Sistem • SIM-SDM PDM Kota Yogyakarta</title>
    <meta name="description" content="Portal Masuk Terpadu Sistem Informasi Manajemen Kepegawaian PDM Kota Yogyakarta">

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
                <img src="{{ asset('images/logo-muhammadiyah.png') }}" alt="Logo Muhammadiyah"
                    class="w-20 h-20 sm:w-24 sm:h-24 rounded-2xl object-contain mx-auto mb-3 shadow-2xl shadow-emerald-950/80 border border-emerald-500/40 p-1.5 bg-emerald-950/70 hover:scale-105 transition duration-200">
                <h1 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight flex items-center justify-center gap-2">
                    <span>Sistem Informasi SDM</span>
                </h1>
                <p class="text-emerald-200/90 text-xs sm:text-sm mt-1 max-w-md mx-auto">
                    Pimpinan Daerah Muhammadiyah Kota Yogyakarta • 76 Amal Usaha Muhammadiyah (AUM)
                </p>
            </div>

            <!-- Main Auth Card -->
            <div class="bg-slate-900/95 backdrop-blur-xl rounded-3xl shadow-2xl shadow-emerald-950/80 border border-emerald-900/60 overflow-hidden">
                
                <!-- Card Header -->
                <div class="px-6 py-4 bg-slate-950/80 border-b border-emerald-950 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <div class="w-2 h-2 rounded-full bg-emerald-400"></div>
                        <span class="text-xs font-bold uppercase tracking-wider text-emerald-300">Masuk Akun Resmi Kepegawaian</span>
                    </div>
                    <span class="text-[11px] text-slate-400 font-medium">SIM SDM PDM</span>
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

                <!-- FORM MASUK KREDENSIAL RESMI -->
                <div class="p-6 sm:p-8 pt-4">
                    <form action="{{ route('login.post') }}" method="POST" class="space-y-5" id="loginForm">
                        @csrf
                        
                        <div>
                            <label for="email" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">
                                Email, NPSN, atau Username
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-emerald-500/70">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.206"/></svg>
                                </div>
                                <input type="text" name="email" id="email" value="{{ old('email') }}" required autofocus
                                    class="w-full pl-10 pr-4 py-3 bg-slate-950 border border-emerald-900/70 rounded-xl text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-amber-400 transition text-sm font-medium"
                                    placeholder="Email, NPSN, atau username">
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
                                <input type="password" name="password" id="password" value="" required
                                    class="w-full pl-10 pr-12 py-3 bg-slate-950 border border-emerald-900/70 rounded-xl text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-amber-400 transition text-sm font-medium"
                                    placeholder="Masukkan kata sandi akun Anda">
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
                </div>

                <!-- Card Footer / Security Guarantee & Copyright -->
                <div class="bg-slate-950/90 border-t border-emerald-950 p-4 text-center space-y-1.5">
                    <div class="flex items-center justify-center gap-1.5 text-[11px] text-emerald-400/90 font-medium">
                        <svg class="w-3.5 h-3.5 text-amber-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        <span>Sesi Terenkripsi SSL • Portal Resmi SIM SDM</span>
                    </div>
                    <div class="text-[11px] text-slate-400 flex items-center justify-center flex-wrap gap-x-2 gap-y-1">
                        <span>&copy; {{ date('Y') }} <strong>Majelis Dikdasmen & PNF PDM Kota Yogyakarta</strong></span>
                        <span>•</span>
                        <a href="https://pdmjogja.org" target="_blank" rel="noopener noreferrer" class="text-amber-400 hover:text-amber-300 transition underline underline-offset-2">pdmjogja.org</a>
                        <span>•</span>
                        <a href="mailto:pdmjogja13@gmail.com" class="text-slate-400 hover:text-slate-200 transition">pdmjogja13@gmail.com</a>
                    </div>
                </div>

            </div>

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
                    <span><strong>Untuk Kepala Sekolah & TU:</strong> Hubungi Tim IT / HR Pusat Majelis Dikdasmen & PNF PDM Kota Yogyakarta melalui email resmi: <a href="mailto:pdmjogja13@gmail.com" class="text-amber-300 font-mono bg-emerald-950 px-1.5 py-0.5 rounded border border-emerald-800 hover:underline">pdmjogja13@gmail.com</a> atau portal <a href="https://pdmjogja.org" target="_blank" rel="noopener noreferrer" class="text-amber-300 underline font-medium">pdmjogja.org</a>.</span>
                </li>
            </ul>

            <button type="button" onclick="closeForgotModal()" class="w-full py-2.5 rounded-xl bg-gradient-to-r from-emerald-600 to-emerald-500 hover:from-emerald-500 hover:to-emerald-400 text-white font-bold text-xs border border-amber-400/30 transition duration-200">
                Saya Mengerti, Tutup
            </button>
        </div>
    </div>

    <!-- JAVASCRIPT LOGIC -->
    <script>
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
    </script>
</body>
</html>

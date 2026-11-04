<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Dalam Pemeliharaan • SIM SDM PDM Kota Yogyakarta</title>
    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Inter', sans-serif; }
        @keyframes floatSlow {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-8px) rotate(1deg); }
        }
        .animate-float { animation: floatSlow 4s ease-in-out infinite; }
        @keyframes pulseGlow {
            0%, 100% { opacity: 0.3; transform: scale(1); }
            50% { opacity: 0.6; transform: scale(1.08); }
        }
        .animate-glow { animation: pulseGlow 3s ease-in-out infinite; }
    </style>
</head>
<body class="bg-gradient-to-br from-slate-950 via-slate-900 to-indigo-950 text-slate-100 min-h-screen flex flex-col justify-between antialiased selection:bg-blue-600 selection:text-white">
    <!-- Ambient Background Glow Effect -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none z-0">
        <div class="absolute -top-40 -left-40 w-96 h-96 bg-blue-600/20 rounded-full blur-3xl animate-glow"></div>
        <div class="absolute top-1/2 -right-40 w-96 h-96 bg-indigo-600/15 rounded-full blur-3xl animate-glow" style="animation-delay: 1.5s;"></div>
        <div class="absolute -bottom-40 left-1/3 w-96 h-96 bg-emerald-600/15 rounded-full blur-3xl animate-glow" style="animation-delay: 2s;"></div>
    </div>

    <!-- 1. TOP HEADER BRAND -->
    <header class="relative z-10 w-full max-w-5xl mx-auto px-6 py-6 flex items-center justify-between border-b border-slate-800/60">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center font-black text-lg text-white shadow-lg shadow-blue-500/25 border border-blue-400/30">
                SDM
            </div>
            <div>
                <div class="font-bold text-sm tracking-tight text-white flex items-center gap-2">
                    <span>PDM Kota Yogyakarta</span>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase bg-amber-500/20 text-amber-300 border border-amber-500/30">
                        Maintenance
                    </span>
                </div>
                <div class="text-[11px] text-slate-400 font-medium">Majelis Pendidikan Dasar Menengah & PNF</div>
            </div>
        </div>

        <div class="hidden sm:flex items-center gap-2 text-xs text-slate-400">
            <span class="w-2 h-2 rounded-full bg-amber-400 animate-ping"></span>
            <span>Status: Pemeliharaan Rutin</span>
        </div>
    </header>

    <!-- 2. MAIN NOTICE CARD -->
    <main class="relative z-10 flex-1 flex items-center justify-center px-4 py-12">
        <div class="max-w-2xl w-full mx-auto text-center space-y-6">
            <!-- Animated Maintenance Icon Badge -->
            <div class="relative inline-block animate-float">
                <div class="w-24 h-24 sm:w-28 sm:h-28 rounded-3xl bg-gradient-to-br from-amber-500/20 via-blue-600/20 to-indigo-600/20 border border-slate-700/80 backdrop-blur-xl flex items-center justify-center mx-auto shadow-2xl shadow-indigo-500/10">
                    <svg class="w-12 h-12 sm:w-14 sm:h-14 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <div class="absolute -bottom-2 -right-2 px-2.5 py-1 rounded-full text-[10px] font-black tracking-wide bg-gradient-to-r from-amber-500 to-orange-500 text-slate-950 shadow-md uppercase">
                    HTTP 503
                </div>
            </div>

            <!-- Title & Subtitle -->
            <div class="space-y-2.5">
                <h1 class="text-2xl sm:text-4xl font-extrabold text-white tracking-tight">
                    Sistem Sedang Dalam <span class="bg-gradient-to-r from-amber-400 via-orange-300 to-amber-200 bg-clip-text text-transparent">Pemeliharaan Berkala</span>
                </h1>
                <p class="text-sm sm:text-base text-slate-300 leading-relaxed max-w-xl mx-auto">
                    Mohon maaf atas ketidaknyamanan Anda. Sistem Informasi Manajemen SDM PDM Kota Yogyakarta saat ini sedang melakukan peningkatan performa, pembaruan keamanan, dan sinkronisasi data berkala.
                </p>
            </div>

            <!-- Detail Info Box -->
            <div class="bg-slate-900/80 border border-slate-800 rounded-2xl p-5 backdrop-blur-xl shadow-xl max-w-lg mx-auto text-left space-y-3.5">
                <div class="flex items-center gap-3 pb-3 border-b border-slate-800/80 text-xs">
                    <div class="w-2.5 h-2.5 rounded-full bg-amber-400 animate-ping"></div>
                    <span class="font-bold text-slate-200">Tahap Pekerjaan:</span>
                    <span class="text-amber-300 font-medium ml-auto">Peningkatan & Pencadangan Sistem</span>
                </div>

                <div class="grid grid-cols-2 gap-3 text-xs">
                    <div class="bg-slate-950/60 p-3 rounded-xl border border-slate-800/60">
                        <span class="text-[11px] text-slate-400 block mb-0.5">Layanan Terdampak:</span>
                        <strong class="text-slate-200 text-xs font-semibold">Portal SIM SDM & AUM</strong>
                    </div>
                    <div class="bg-slate-950/60 p-3 rounded-xl border border-slate-800/60">
                        <span class="text-[11px] text-slate-400 block mb-0.5">Integritas Data:</span>
                        <strong class="text-emerald-400 text-xs font-semibold flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Aman & Dicadangkan
                        </strong>
                    </div>
                </div>

                <p class="text-xs text-slate-400 leading-relaxed pt-1">
                    Seluruh data kepegawaian, riwayat nilai, kenaikan pangkat, dan arsip unit sekolah tetap aman. Sistem akan kembali online segera setelah proses selesai.
                </p>
            </div>

            <!-- Action Buttons: Reload & Contact -->
            <div class="flex flex-col sm:flex-row items-center justify-center gap-3 pt-2">
                <button type="button" onclick="window.location.reload()" class="w-full sm:w-auto px-6 py-3 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white text-xs font-bold shadow-lg shadow-blue-500/25 transition-all duration-200 flex items-center justify-center gap-2 group">
                    <svg class="w-4 h-4 group-hover:rotate-180 transition-transform duration-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    <span>Muat Ulang Halaman (Cek Kembali)</span>
                </button>

                <a href="https://wa.me/6281200000000?text=Halo%20IT%20Support%20SIM%20SDM%20PDM%20Kota%20Yogyakarta,%20saya%20ingin%20menanyakan%20status%20pemeliharaan%20sistem." target="_blank" class="w-full sm:w-auto px-5 py-3 rounded-xl bg-slate-800/80 hover:bg-slate-700 text-slate-200 hover:text-white text-xs font-semibold border border-slate-700 transition flex items-center justify-center gap-2">
                    <svg class="w-4 h-4 text-emerald-400" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.711 2.598 2.669-.699c.969.54 1.884.825 2.791.825 3.183 0 5.769-2.587 5.77-5.767 0-3.181-2.587-5.77-5.77-5.77zm0-2.172c4.418 0 8 3.582 8 8 0 4.418-3.582 8-8 8-1.341 0-2.607-.333-3.722-.919l-4.278 1.121 1.139-4.162c-.689-1.189-1.139-2.569-1.139-4.04 0-4.418 3.582-8 8-8z"/>
                    </svg>
                    <span>Hubungi Helpdesk IT Support</span>
                </a>
            </div>

            <!-- Subtle Link for Administrators -->
            <div class="pt-4 text-xs text-slate-500 flex items-center justify-center gap-4">
                <span>Khusus Administrator & IT Support:</span>
                <a href="{{ url('/login') }}" class="text-slate-400 hover:text-blue-400 underline font-medium transition">Masuk Panel Admin</a>
                <span>•</span>
                <a href="{{ url('/maintenance') }}" class="text-slate-400 hover:text-amber-400 underline font-medium transition">Panel Pemeliharaan</a>
            </div>
        </div>
    </main>

    <!-- 3. FOOTER -->
    <footer class="relative z-10 w-full max-w-5xl mx-auto px-6 py-6 border-t border-slate-800/60 text-center text-xs text-slate-500 flex flex-col sm:flex-row items-center justify-between gap-3">
        <div>
            &copy; {{ date('Y') }} <strong>Majelis Dikdasmen & PNF PDM Kota Yogyakarta</strong>. Hak cipta dilindungi undang-undang.
        </div>
        <div class="text-[11px] text-slate-600">
            Gedung Dakwah PDM Kota Yogyakarta • Jl. Sultan Agung No. 14, Gunungketur, Pakualaman
        </div>
    </footer>
</body>
</html>

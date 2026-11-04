@extends('layouts.app')

@section('content')
<div class="space-y-5">
    <!-- Header Halaman -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pb-3 border-b border-slate-200">
        <div>
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-amber-500 text-white flex items-center justify-center shadow-xs">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
                <h1 class="text-xl lg:text-2xl font-bold text-slate-800 tracking-tight">Pemeliharaan Sistem & Status Maintenance</h1>
            </div>
            <p class="text-xs text-slate-500 mt-1">Pusat kendali status operasi aplikasi, pembersihan cache, pencadangan basis data, dan diagnosa server.</p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('maintenance.index') }}" class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-xl transition flex items-center gap-1.5 border border-slate-200">
                <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                <span>Segarkan Status</span>
            </a>
        </div>
    </div>

    <!-- Alert Notifikasi Sukses / Peringatan -->
    @if(session('success'))
        <div class="p-3.5 bg-emerald-50 border border-emerald-200 rounded-xl text-emerald-800 text-xs flex items-center gap-2.5 shadow-xs">
            <svg class="w-5 h-5 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if(session('warning'))
        <div class="p-3.5 bg-amber-50 border border-amber-200 rounded-xl text-amber-900 text-xs flex items-center gap-2.5 shadow-xs">
            <svg class="w-5 h-5 text-amber-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            <span class="break-all">{{ session('warning') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="p-3.5 bg-rose-50 border border-rose-200 rounded-xl text-rose-800 text-xs flex items-center gap-2.5 shadow-xs">
            <svg class="w-5 h-5 text-rose-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <!-- 1. KARTU UTAMA: STATUS MAINTENANCE MODE -->
    <div class="rounded-2xl p-5 border {{ $systemInfo['is_down'] ? 'bg-gradient-to-br from-amber-50 via-white to-orange-50 border-amber-300 shadow-md shadow-amber-500/10' : 'bg-gradient-to-br from-emerald-50/70 via-white to-teal-50/70 border-emerald-200 shadow-xs' }}">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 rounded-2xl flex items-center justify-center text-white shadow-md flex-shrink-0 {{ $systemInfo['is_down'] ? 'bg-amber-600 animate-pulse' : 'bg-emerald-600' }}">
                    @if($systemInfo['is_down'])
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    @else
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    @endif
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Status Operasional Aplikasi:</span>
                        @if($systemInfo['is_down'])
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-black uppercase bg-amber-100 text-amber-800 border border-amber-300 inline-flex items-center gap-1.5 animate-pulse">
                                <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                                Mode Pemeliharaan Aktif (Down)
                            </span>
                        @else
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-black uppercase bg-emerald-100 text-emerald-800 border border-emerald-300 inline-flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                Aplikasi Berjalan Normal (Live)
                            </span>
                        @endif
                    </div>
                    <p class="text-xs text-slate-600 mt-1">
                        @if($systemInfo['is_down'])
                            Aplikasi saat ini ditutup untuk publik (HTTP 503). Pengunjung luar tidak dapat mengakses rute publik kecuali melalui tautan bypass admin atau rute panel pemeliharaan.
                        @else
                            Sistem dalam kondisi daring (live). Seluruh fungsionalitas dan rute dapat diakses normal oleh seluruh pengguna sesuai hak akses perannya.
                        @endif
                    </p>

                    @if($systemInfo['is_down'] && $systemInfo['down_secret'])
                        <div class="mt-3 p-2.5 bg-amber-100/80 rounded-xl border border-amber-200 text-xs text-amber-900 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                            <div>
                                <span class="font-bold">Kunci Rahasia Bypass Admin:</span>
                                <code class="px-1.5 py-0.5 bg-white rounded font-mono font-bold text-amber-800 border border-amber-300 ml-1">{{ $systemInfo['down_secret'] }}</code>
                            </div>
                            <a href="{{ url('/' . $systemInfo['down_secret']) }}" target="_blank" class="inline-flex items-center gap-1 px-3 py-1 bg-amber-700 hover:bg-amber-800 text-white rounded-lg text-xs font-semibold shadow-xs transition">
                                <span>Buka Aplikasi (Bypass Mode)</span>
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Tombol Toggle Aksi -->
            <div class="flex-shrink-0">
                @if($systemInfo['is_down'])
                    <form action="{{ route('maintenance.toggle') }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menonaktifkan Mode Pemeliharaan dan mengaktifkan kembali aplikasi untuk seluruh pengguna?')">
                        @csrf
                        <button type="submit" class="w-full md:w-auto px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold transition flex items-center justify-center gap-2 shadow-sm shadow-emerald-500/30">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <span>Aktifkan Kembali Aplikasi (Matikan Maintenance)</span>
                        </button>
                    </form>
                @else
                    <button type="button" onclick="document.getElementById('modalDown').classList.remove('hidden')" class="w-full md:w-auto px-5 py-2.5 bg-amber-600 hover:bg-amber-700 text-white rounded-xl text-xs font-bold transition flex items-center justify-center gap-2 shadow-sm shadow-amber-500/30">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        <span>Aktifkan Mode Pemeliharaan (Down)</span>
                    </button>
                @endif
            </div>
        </div>
    </div>

    <!-- 2. GRID INFORMASI DIAGNOSTIK SISTEM -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3">
        <!-- 1. Lingkungan & Server -->
        <div class="bg-white rounded-2xl p-4 border border-slate-200 shadow-xs">
            <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                <span class="text-xs font-bold text-slate-700 flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01"/></svg>
                    Lingkungan Aplikasi
                </span>
                <span class="text-[10px] px-2 py-0.5 rounded font-bold uppercase {{ $systemInfo['app_env'] === 'production' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">{{ $systemInfo['app_env'] }}</span>
            </div>
            <div class="mt-2.5 space-y-1.5 text-xs">
                <div class="flex justify-between">
                    <span class="text-slate-500">Versi PHP:</span>
                    <span class="font-semibold text-slate-800 font-mono">{{ $systemInfo['php_version'] }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">Versi Laravel:</span>
                    <span class="font-semibold text-slate-800 font-mono">v{{ $systemInfo['laravel_version'] }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">Batas Memori:</span>
                    <span class="font-semibold text-slate-800 font-mono">{{ $systemInfo['memory_limit'] }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">Debug Mode:</span>
                    <span class="font-semibold {{ $systemInfo['app_debug'] ? 'text-amber-600' : 'text-slate-700' }}">{{ $systemInfo['app_debug'] ? 'Aktif (True)' : 'Nonaktif (False)' }}</span>
                </div>
            </div>
        </div>

        <!-- 2. Basis Data -->
        <div class="bg-white rounded-2xl p-4 border border-slate-200 shadow-xs">
            <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                <span class="text-xs font-bold text-slate-700 flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"/></svg>
                    Basis Data (Database)
                </span>
                <span class="text-[10px] px-2 py-0.5 rounded font-bold uppercase bg-blue-100 text-blue-800">{{ $dbStatus['driver'] }}</span>
            </div>
            <div class="mt-2.5 space-y-1.5 text-xs">
                <div class="flex justify-between">
                    <span class="text-slate-500">Nama DB:</span>
                    <span class="font-semibold text-slate-800 font-mono truncate max-w-[130px]" title="{{ $dbStatus['name'] }}">{{ $dbStatus['name'] }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">Ukuran DB:</span>
                    <span class="font-semibold text-slate-800 font-mono">{{ $dbStatus['size'] }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">Status Koneksi:</span>
                    <span class="font-bold text-emerald-600 flex items-center gap-1">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                        {{ $dbStatus['status'] }}
                    </span>
                </div>
            </div>
        </div>

        <!-- 3. Penyimpanan & Disk -->
        <div class="bg-white rounded-2xl p-4 border border-slate-200 shadow-xs">
            <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                <span class="text-xs font-bold text-slate-700 flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/></svg>
                    Kapasitas Disk Server
                </span>
                <span class="text-[10px] font-bold text-slate-500">{{ $storageInfo['usage_percent'] }}% Digunakan</span>
            </div>
            <div class="mt-2.5 space-y-1.5 text-xs">
                <div class="w-full bg-slate-100 h-2 rounded-full overflow-hidden">
                    <div class="h-full bg-gradient-to-r from-blue-500 to-indigo-600 rounded-full" style="width: {{ min(100, $storageInfo['usage_percent']) }}%"></div>
                </div>
                <div class="flex justify-between text-[11px] pt-1">
                    <span class="text-slate-500">Sisa Bebas: <strong class="text-slate-700">{{ $storageInfo['free_space'] }}</strong></span>
                    <span class="text-slate-500">Total: <strong class="text-slate-700">{{ $storageInfo['total_space'] }}</strong></span>
                </div>
                <div class="flex justify-between pt-0.5">
                    <span class="text-slate-500">Symlink Storage:</span>
                    <span class="font-bold {{ $systemInfo['storage_symlink'] ? 'text-emerald-600' : 'text-rose-600' }}">
                        {{ $systemInfo['storage_symlink'] ? 'Terhubung (OK)' : 'Belum Ada' }}
                    </span>
                </div>
            </div>
        </div>

        <!-- 4. Cadangan & Berkas Log -->
        <div class="bg-white rounded-2xl p-4 border border-slate-200 shadow-xs">
            <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                <span class="text-xs font-bold text-slate-700 flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Cadangan & Log
                </span>
                <span class="text-[10px] px-2 py-0.5 rounded font-bold bg-slate-100 text-slate-700">{{ $storageInfo['backup_count'] }} Berkas</span>
            </div>
            <div class="mt-2.5 space-y-1.5 text-xs">
                <div class="flex justify-between">
                    <span class="text-slate-500">Total Ukuran Backup:</span>
                    <span class="font-semibold text-slate-800 font-mono">{{ $storageInfo['total_backup_size'] }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">Ukuran Log Sistem:</span>
                    <span class="font-semibold text-slate-800 font-mono">{{ $storageInfo['log_size'] }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">Direktori Backup:</span>
                    <span class="font-semibold text-slate-600 font-mono text-[10px]">storage/backups</span>
                </div>
            </div>
        </div>
    </div>

    <!-- 3. PANEL AKSI CEPAT / UTILITY TOOLS -->
    <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-xs">
        <h2 class="text-sm font-bold text-slate-800 flex items-center gap-2 mb-3">
            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
            <span>Alat Pemeliharaan & Operasi Cepat (Maintenance Utilities)</span>
        </h2>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
            <!-- Aksi 1: Clear All Cache -->
            <form action="{{ route('maintenance.clear_cache') }}" method="POST" onsubmit="return confirm('Bersihkan seluruh cache aplikasi, konfigurasi, rute, dan template?')">
                @csrf
                <input type="hidden" name="type" value="all">
                <button type="submit" class="w-full text-left p-3.5 rounded-xl border border-slate-200 hover:border-blue-300 hover:bg-blue-50/40 transition group">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center group-hover:bg-blue-600 group-hover:text-white transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </div>
                        <div>
                            <div class="text-xs font-bold text-slate-800 group-hover:text-blue-600 transition">Bersihkan Semua Cache</div>
                            <div class="text-[10px] text-slate-500">Jalankan optimize:clear</div>
                        </div>
                    </div>
                </button>
            </form>

            <!-- Aksi 2: Optimasi Aplikasi -->
            <form action="{{ route('maintenance.optimize') }}" method="POST" onsubmit="return confirm('Jalankan optimasi cache rute & konfigurasi untuk produksi?')">
                @csrf
                <button type="submit" class="w-full text-left p-3.5 rounded-xl border border-slate-200 hover:border-emerald-300 hover:bg-emerald-50/40 transition group">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-600 flex items-center justify-center group-hover:bg-emerald-600 group-hover:text-white transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        </div>
                        <div>
                            <div class="text-xs font-bold text-slate-800 group-hover:text-emerald-600 transition">Optimalkan Aplikasi</div>
                            <div class="text-[10px] text-slate-500">Cache config & routes (optimize)</div>
                        </div>
                    </div>
                </button>
            </form>

            <!-- Aksi 3: Backup Database Manual -->
            <form action="{{ route('maintenance.backup') }}" method="POST" onsubmit="return confirm('Mulai proses pencadangan basis data instan sekarang?')">
                @csrf
                <button type="submit" class="w-full text-left p-3.5 rounded-xl border border-slate-200 hover:border-indigo-300 hover:bg-indigo-50/40 transition group">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-indigo-100 text-indigo-600 flex items-center justify-center group-hover:bg-indigo-600 group-hover:text-white transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/></svg>
                        </div>
                        <div>
                            <div class="text-xs font-bold text-slate-800 group-hover:text-indigo-600 transition">Cadangkan Database</div>
                            <div class="text-[10px] text-slate-500">Simpan dump terkompresi (.gz)</div>
                        </div>
                    </div>
                </button>
            </form>

            <!-- Aksi 4: Perbaiki Symlink Storage -->
            <form action="{{ route('maintenance.storage_link') }}" method="POST">
                @csrf
                <button type="submit" class="w-full text-left p-3.5 rounded-xl border border-slate-200 hover:border-purple-300 hover:bg-purple-50/40 transition group">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-purple-100 text-purple-600 flex items-center justify-center group-hover:bg-purple-600 group-hover:text-white transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                        </div>
                        <div>
                            <div class="text-xs font-bold text-slate-800 group-hover:text-purple-600 transition">Perbaiki Symlink Storage</div>
                            <div class="text-[10px] text-slate-500">Jalankan storage:link</div>
                        </div>
                    </div>
                </button>
            </form>
        </div>
    </div>

    <!-- 4. PENGELOLA CADANGAN & LOG SISTEM -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <!-- Tabel Berkas Cadangan Terkini -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-xs flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-3">
                    <h3 class="text-xs font-bold text-slate-800 flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                        Berkas Cadangan Tersimpan (storage/backups)
                    </h3>
                    <span class="text-[10px] text-slate-500 font-semibold">{{ count($backups) }} Berkas</span>
                </div>

                @if(count($backups) > 0)
                    <div class="overflow-x-auto max-h-60 overflow-y-auto">
                        <table class="w-full text-left text-xs text-slate-700">
                            <thead class="bg-slate-50 text-[10px] uppercase font-bold text-slate-500 sticky top-0">
                                <tr>
                                    <th class="py-2 px-3">Nama Berkas</th>
                                    <th class="py-2 px-3">Ukuran</th>
                                    <th class="py-2 px-3">Waktu</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach($backups as $b)
                                    <tr class="hover:bg-slate-50/80 transition">
                                        <td class="py-2 px-3 font-mono font-medium text-slate-800 truncate max-w-[220px]" title="{{ $b['name'] }}">{{ $b['name'] }}</td>
                                        <td class="py-2 px-3 font-mono text-slate-600 whitespace-nowrap">{{ $b['size'] }}</td>
                                        <td class="py-2 px-3 text-slate-500 whitespace-nowrap text-[11px]">{{ $b['created_at'] }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="py-8 text-center text-xs text-slate-400">
                        Belum ada berkas cadangan tersimpan. Klik "Cadangkan Database" untuk membuat cadangan pertama.
                    </div>
                @endif
            </div>
            <p class="text-[10px] text-slate-400 mt-3 pt-2 border-t border-slate-100">
                Pencadangan otomatis dilakukan setiap kali eksekusi script deployment <code>deploy.sh</code> di server produksi.
            </p>
        </div>

        <!-- Terminal Log Viewer -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-xs flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-3">
                    <h3 class="text-xs font-bold text-slate-800 flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l3 3-3 3m5 0h3M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        Cuplikan Log Sistem (laravel.log - 50 Baris Terakhir)
                    </h3>
                    <form action="{{ route('maintenance.clear_log') }}" method="POST" onsubmit="return confirm('Kosongkan seluruh isi berkas log laravel.log?')">
                        @csrf
                        <button type="submit" class="px-2 py-1 bg-rose-50 hover:bg-rose-100 text-rose-600 rounded text-[10px] font-bold border border-rose-200 transition">
                            Kosongkan Log
                        </button>
                    </form>
                </div>

                <div class="bg-slate-950 text-emerald-400 p-3.5 rounded-xl font-mono text-[11px] max-h-60 overflow-y-auto whitespace-pre-wrap leading-relaxed shadow-inner">
{{ $recentLogs }}
                </div>
            </div>
            <p class="text-[10px] text-slate-400 mt-3 pt-2 border-t border-slate-100">
                Lokasi berkas log: <code>storage/logs/laravel.log</code> (Ukuran saat ini: {{ $storageInfo['log_size'] }})
            </p>
        </div>
    </div>
</div>

<!-- MODAL KONFIRMASI AKTIVASI MAINTENANCE MODE -->
<div id="modalDown" class="fixed inset-0 z-50 bg-slate-950/60 backdrop-blur-xs hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-md w-full p-5 shadow-2xl border border-slate-200 animate-in fade-in zoom-in-95 duration-200">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <h3 class="text-sm font-bold text-slate-800 flex items-center gap-2">
                <div class="w-6 h-6 rounded-lg bg-amber-500 text-white flex items-center justify-center">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
                <span>Konfirmasi Mode Pemeliharaan</span>
            </h3>
            <button type="button" onclick="document.getElementById('modalDown').classList.add('hidden')" class="p-1 text-slate-400 hover:text-slate-600 rounded-lg">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <form action="{{ route('maintenance.toggle') }}" method="POST" class="mt-4 space-y-3.5 text-xs">
            @csrf
            <div class="p-3 bg-amber-50 rounded-xl border border-amber-200 text-amber-900 leading-relaxed">
                Aplikasi akan menghasilkan respon <strong>HTTP 503</strong> kepada pengunjung umum. Anda dapat mengatur kunci rahasia agar tetap bisa mengakses sistem.
            </div>

            <div>
                <label class="block font-semibold text-slate-700 mb-1">Kunci Rahasia Bypass (Secret Token):</label>
                <input type="text" name="secret" value="pdm-bypass-{{ date('Ymd') }}" class="w-full px-3 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:outline-none font-mono" placeholder="misal: admin-rahasia-2026">
                <span class="text-[10px] text-slate-500 mt-0.5 block">Tautan untuk bypass: <code>{{ url('/') }}/[kunci_rahasia]</code></span>
            </div>

            <div class="grid grid-cols-2 gap-2.5">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Retry After (detik):</label>
                    <input type="number" name="retry" value="60" min="10" max="86400" class="w-full px-3 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:outline-none">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Refresh Otomatis (detik):</label>
                    <input type="number" name="refresh" value="30" min="5" max="3600" class="w-full px-3 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:outline-none">
                </div>
            </div>

            <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
                <button type="button" onclick="document.getElementById('modalDown').classList.add('hidden')" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold rounded-xl transition">
                    Batal
                </button>
                <button type="submit" class="px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white font-bold rounded-xl transition flex items-center gap-1.5 shadow-sm shadow-amber-500/25">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <span>Aktifkan Down Mode</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection


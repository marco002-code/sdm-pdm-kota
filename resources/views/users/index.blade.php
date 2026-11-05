@extends('layouts.app')

@section('title', 'Kelola Akun Pengguna • SIM SDM PDM Kota Yogyakarta')

@section('content')
<div class="space-y-6">

    <!-- 1. HEADER SECTION -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-2 border-b border-slate-200">
        <div>
            <div class="flex items-center gap-2 text-xs font-semibold uppercase tracking-wider text-blue-600 mb-1">
                <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                <span>Otoritas Keamanan Sistem</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-800 tracking-tight">Kelola Akun Pengguna</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">
                Pusat manajemen akun resmi, hak akses peran (RBAC), pengaturan kata sandi, dan kontrol akses kepegawaian.
            </p>
        </div>

        <div class="flex items-center gap-2">
            <button type="button" onclick="openAddModal()"
                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white text-xs sm:text-sm font-bold shadow-md shadow-blue-500/20 active:scale-95 transition duration-150">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                <span>Tambah Akun Baru</span>
            </button>
        </div>
    </div>

    <!-- 2. FLASH & ALERT MESSAGES -->
    @if(session('success'))
        <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs sm:text-sm flex items-center gap-3 shadow-sm">
            <svg class="w-5 h-5 flex-shrink-0 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <div class="flex-1 font-medium">{{ session('success') }}</div>
        </div>
    @endif

    @if(session('error'))
        <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs sm:text-sm flex items-center gap-3 shadow-sm">
            <svg class="w-5 h-5 flex-shrink-0 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <div class="flex-1 font-medium">{{ session('error') }}</div>
        </div>
    @endif

    @if($errors->any())
        <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs sm:text-sm shadow-sm space-y-1">
            <div class="font-bold flex items-center gap-2">
                <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                <span>Terdapat kesalahan pada input formulir:</span>
            </div>
            <ul class="list-disc list-inside text-xs pl-2 space-y-0.5">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- 3. METRIC STATS CARDS -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
        <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200/80 shadow-sm">
            <div class="flex items-center justify-between text-slate-500 mb-2">
                <span class="text-xs font-bold uppercase tracking-wider">Total Akun</span>
                <span class="w-8 h-8 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                </span>
            </div>
            <div class="text-2xl sm:text-3xl font-extrabold text-slate-800">{{ $stats['total'] }}</div>
            <div class="text-[11px] text-slate-400 mt-1">Seluruh akun terdaftar di sistem</div>
        </div>

        <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200/80 shadow-sm">
            <div class="flex items-center justify-between text-slate-500 mb-2">
                <span class="text-xs font-bold uppercase tracking-wider">Admin & Majelis</span>
                <span class="w-8 h-8 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center font-bold">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                </span>
            </div>
            <div class="text-2xl sm:text-3xl font-extrabold text-purple-700">{{ $stats['admin_pusat'] }}</div>
            <div class="text-[11px] text-slate-400 mt-1">Superadmin, SDM & Pimpinan Majelis</div>
        </div>

        <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200/80 shadow-sm">
            <div class="flex items-center justify-between text-slate-500 mb-2">
                <span class="text-xs font-bold uppercase tracking-wider">Operator TU Unit</span>
                <span class="w-8 h-8 rounded-xl bg-teal-50 text-teal-600 flex items-center justify-center font-bold">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                </span>
            </div>
            <div class="text-2xl sm:text-3xl font-extrabold text-teal-700">{{ $stats['tu_unit'] }}</div>
            <div class="text-[11px] text-slate-400 mt-1">Akun operator satuan pendidikan (NPSN)</div>
        </div>

        <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200/80 shadow-sm">
            <div class="flex items-center justify-between text-slate-500 mb-2">
                <span class="text-xs font-bold uppercase tracking-wider">Status Akun Aktif</span>
                <span class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                </span>
            </div>
            <div class="text-2xl sm:text-3xl font-extrabold text-emerald-700">{{ $stats['active'] }} <span class="text-xs font-medium text-slate-400">/ {{ $stats['total'] }}</span></div>
            <div class="text-[11px] text-slate-400 mt-1">{{ $stats['inactive'] }} akun dinonaktifkan</div>
        </div>
    </div>

    <!-- 4. SEARCH & FILTER TOOLBAR -->
    <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200/80 shadow-sm">
        <form method="GET" action="{{ route('users.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3">
            <!-- Keyword Search -->
            <div class="lg:col-span-4">
                <label class="block text-xs font-bold text-slate-600 mb-1">Cari Pengguna</label>
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Nama, username, email, atau NPSN..."
                        class="w-full pl-9 pr-3 py-2 text-xs sm:text-sm bg-slate-50 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:bg-white focus:outline-none transition">
                    <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
            </div>

            <!-- Role Filter -->
            <div class="lg:col-span-3">
                <label class="block text-xs font-bold text-slate-600 mb-1">Peran / Otoritas</label>
                <select name="role" class="w-full px-3 py-2 text-xs sm:text-sm bg-slate-50 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:bg-white focus:outline-none transition">
                    <option value="">-- Semua Peran --</option>
                    <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin Utama (Superadmin)</option>
                    <option value="hr_pusat" {{ request('role') === 'hr_pusat' ? 'selected' : '' }}>Admin Yayasan / SDM Pusat</option>
                    <option value="yayasan" {{ request('role') === 'yayasan' ? 'selected' : '' }}>Ketua Majelis Dikdasmen</option>
                    <option value="kepsek" {{ request('role') === 'kepsek' ? 'selected' : '' }}>Kepala Sekolah</option>
                    <option value="tu" {{ request('role') === 'tu' ? 'selected' : '' }}>Petugas TU Unit (Operator)</option>
                    <option value="pegawai" {{ request('role') === 'pegawai' ? 'selected' : '' }}>Guru / Pegawai (ESS)</option>
                </select>
            </div>

            <!-- Unit Filter -->
            <div class="lg:col-span-3">
                <label class="block text-xs font-bold text-slate-600 mb-1">Unit Kerja / Satuan</label>
                <select name="unit_id" class="w-full px-3 py-2 text-xs sm:text-sm bg-slate-50 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:bg-white focus:outline-none transition">
                    <option value="">-- Semua Satuan Pendidikan --</option>
                    @foreach($units as $unit)
                        <option value="{{ $unit->id }}" {{ request('unit_id') == $unit->id ? 'selected' : '' }}>
                            {{ $unit->name }} {{ $unit->npsn ? "({$unit->npsn})" : '' }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Status & Action Buttons -->
            <div class="lg:col-span-2 flex items-end gap-2">
                <button type="submit"
                    class="flex-1 py-2 px-3 bg-slate-800 hover:bg-slate-900 text-white text-xs sm:text-sm font-semibold rounded-xl transition flex items-center justify-center gap-1.5 shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                    <span>Filter</span>
                </button>
                @if(request()->anyFilled(['search', 'role', 'unit_id', 'status']))
                    <a href="{{ route('users.index') }}"
                        class="py-2 px-3 bg-slate-200 hover:bg-slate-300 text-slate-700 text-xs sm:text-sm font-semibold rounded-xl transition">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- 5. USERS DATA TABLE -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-slate-200 text-[11px] font-bold uppercase tracking-wider text-slate-500">
                        <th class="py-3.5 px-4 text-center w-12">No</th>
                        <th class="py-3.5 px-4">Pengguna & Akun</th>
                        <th class="py-3.5 px-4">Username / ID</th>
                        <th class="py-3.5 px-4">Peran (Role)</th>
                        <th class="py-3.5 px-4">Unit Kerja / Satuan</th>
                        <th class="py-3.5 px-4 text-center">Status</th>
                        <th class="py-3.5 px-4">Login Terakhir</th>
                        <th class="py-3.5 px-4 text-center w-36">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs sm:text-sm">
                    @forelse($users as $idx => $user)
                        <tr class="hover:bg-slate-50/70 transition">
                            <!-- No -->
                            <td class="py-3 px-4 text-center text-slate-400 font-medium">
                                {{ $users->firstItem() + $idx }}
                            </td>

                            <!-- Pengguna & Akun -->
                            <td class="py-3 px-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 text-white font-black text-xs flex items-center justify-center flex-shrink-0 shadow-sm">
                                        {{ substr($user->name, 0, 2) }}
                                    </div>
                                    <div>
                                        <div class="font-bold text-slate-800 flex items-center gap-1.5">
                                            <span>{{ $user->name }}</span>
                                            @if($user->id === auth()->id())
                                                <span class="px-1.5 py-0.2 rounded text-[10px] font-bold bg-blue-100 text-blue-700 border border-blue-200">Anda</span>
                                            @endif
                                        </div>
                                        <div class="text-[11px] text-slate-500 font-mono flex items-center gap-1">
                                            <svg class="w-3 h-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.206"/></svg>
                                            <span>{{ $user->email }}</span>
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <!-- Username / ID -->
                            <td class="py-3 px-4">
                                <span class="px-2.5 py-1 rounded-lg bg-slate-100 font-mono text-xs font-semibold text-slate-800 border border-slate-200">
                                    {{ $user->username }}
                                </span>
                            </td>

                            <!-- Peran (Role) -->
                            <td class="py-3 px-4">
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold border {{ $user->role_badge_color }}">
                                    <span>{{ $user->role_title }}</span>
                                </span>
                            </td>

                            <!-- Unit Kerja -->
                            <td class="py-3 px-4 text-xs">
                                @if($user->unit)
                                    <div class="font-semibold text-slate-700 truncate max-w-[220px]" title="{{ $user->unit->name }}">
                                        {{ $user->unit->name }}
                                    </div>
                                    <div class="text-[11px] text-slate-400 font-mono">
                                        NPSN: {{ $user->unit->npsn ?: '-' }} • {{ $user->unit->code }}
                                    </div>
                                @else
                                    <span class="text-slate-400 italic">Tingkat Pusat / PDM</span>
                                @endif
                            </td>

                            <!-- Status -->
                            <td class="py-3 px-4 text-center">
                                @if($user->is_active)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        <span>Aktif</span>
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-bold bg-slate-100 text-slate-600 border border-slate-300">
                                        <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                                        <span>Nonaktif</span>
                                    </span>
                                @endif
                            </td>

                            <!-- Login Terakhir -->
                            <td class="py-3 px-4 text-xs text-slate-500">
                                @if($user->last_login_at)
                                    <span title="{{ $user->last_login_at->format('d/m/Y H:i:s') }}">
                                        {{ $user->last_login_at->diffForHumans() }}
                                    </span>
                                @else
                                    <span class="text-slate-400 italic">Belum pernah</span>
                                @endif
                            </td>

                            <!-- Aksi -->
                            <td class="py-3 px-4 text-center">
                                <div class="flex items-center justify-center gap-1">
                                    <!-- Reset Password -->
                                    <button type="button"
                                        onclick="openResetModal('{{ $user->id }}', '{{ addslashes($user->name) }}', '{{ addslashes($user->username) }}', '{{ addslashes($user->role_title) }}', '{{ $user->unit?->npsn ?? '' }}')"
                                        class="p-1.5 text-amber-600 hover:text-amber-700 hover:bg-amber-50 rounded-lg transition"
                                        title="Reset Kata Sandi Akun">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                                    </button>

                                    <!-- Toggle Status Aktif/Nonaktif -->
                                    @if($user->id !== auth()->id())
                                        <form method="POST" action="{{ route('users.toggle_status', $user) }}" class="inline">
                                            @csrf
                                            <button type="submit"
                                                class="p-1.5 {{ $user->is_active ? 'text-emerald-600 hover:text-rose-600 hover:bg-rose-50' : 'text-slate-400 hover:text-emerald-600 hover:bg-emerald-50' }} rounded-lg transition"
                                                title="{{ $user->is_active ? 'Nonaktifkan Akun' : 'Aktifkan Akun' }}">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                                            </button>
                                        </form>
                                    @endif

                                    <!-- Delete Button with Double Confirmation -->
                                    @if($user->id !== auth()->id())
                                        <button type="button"
                                            onclick="openDeleteModal('{{ $user->id }}', '{{ addslashes($user->name) }}', '{{ addslashes($user->username) }}', '{{ addslashes($user->role_title) }}')"
                                            class="p-1.5 text-rose-500 hover:text-rose-700 hover:bg-rose-50 rounded-lg transition"
                                            title="Hapus Akun Pengguna (Dobel Konfirmasi)">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    @else
                                        <span class="p-1.5 text-slate-300 cursor-not-allowed" title="Tidak dapat menghapus akun Anda sendiri">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                        </span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-12 text-center text-slate-400">
                                <svg class="w-12 h-12 mx-auto text-slate-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                <div class="font-semibold text-slate-600">Tidak ada pengguna yang cocok dengan kriteria pencarian.</div>
                                <div class="text-xs text-slate-400 mt-1">Coba gunakan kata kunci lain atau bersihkan filter pencarian.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($users->hasPages())
            <div class="px-5 py-4 bg-slate-50 border-t border-slate-200/80">
                {{ $users->links() }}
            </div>
        @endif
    </div>

</div>

<!-- ========================================================================= -->
<!-- MODAL 1: TAMBAH AKUN PENGGUNA BARU (ADD USER)                             -->
<!-- ========================================================================= -->
<div id="addUserModal" class="fixed inset-0 z-50 bg-slate-950/70 backdrop-blur-sm hidden items-center justify-center p-4 overflow-y-auto">
    <div class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-7 shadow-2xl border border-slate-200 relative my-8">
        
        <!-- Header Modal -->
        <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-5">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                </div>
                <div>
                    <h3 class="text-base sm:text-lg font-bold text-slate-800">Tambah Akun Pengguna</h3>
                    <p class="text-xs text-slate-500">Daftarkan akun resmi kepegawaian baru</p>
                </div>
            </div>
            <button type="button" onclick="closeAddModal()" class="text-slate-400 hover:text-slate-600 p-1.5 rounded-lg hover:bg-slate-100 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <form action="{{ route('users.store') }}" method="POST" class="space-y-4">
            @csrf

            <!-- Nama Pengguna -->
            <div>
                <label for="add_name" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                    Nama Lengkap Akun <span class="text-rose-500">*</span>
                </label>
                <input type="text" name="name" id="add_name" required
                    placeholder="Contoh: TU SD MUHAMMADIYAH SAPEN 1 atau Admin Personalia"
                    class="w-full px-3.5 py-2.5 text-xs sm:text-sm bg-slate-50 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:bg-white focus:outline-none transition">
                <p class="text-[11px] text-slate-400 mt-1">Hindari penggunaan nama tokoh bersejarah atau selebritas.</p>
            </div>

            <!-- Role & Unit Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label for="add_role" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Peran (Role) <span class="text-rose-500">*</span>
                    </label>
                    <select name="role" id="add_role" required onchange="handleRoleChange(this.value)"
                        class="w-full px-3 py-2.5 text-xs sm:text-sm bg-slate-50 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:bg-white focus:outline-none transition">
                        <option value="">-- Pilih Peran --</option>
                        <option value="tu">Petugas TU Unit (NPSN)</option>
                        <option value="kepsek">Kepala Sekolah Unit</option>
                        <option value="pegawai">Guru & Karyawan (ESS)</option>
                        <option value="hr_pusat">Admin Yayasan / SDM Pusat</option>
                        <option value="yayasan">Ketua Majelis Dikdasmen</option>
                        <option value="admin">Admin Utama (Super Admin)</option>
                    </select>
                </div>

                <div>
                    <label for="add_unit_id" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Satuan / Unit Kerja
                    </label>
                    <select name="unit_id" id="add_unit_id" onchange="handleUnitChange(this)"
                        class="w-full px-3 py-2.5 text-xs sm:text-sm bg-slate-50 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:bg-white focus:outline-none transition">
                        <option value="">-- Pilih Satuan Kerja --</option>
                        @foreach($units as $u)
                            <option value="{{ $u->id }}" data-npsn="{{ $u->npsn }}" data-name="{{ $u->name }}">
                                {{ $u->name }} {{ $u->npsn ? "({$u->npsn})" : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Username & Email Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label for="add_username" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Username <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="username" id="add_username" required
                        placeholder="Contoh: 20403327 atau admin"
                        class="w-full px-3.5 py-2.5 text-xs sm:text-sm bg-slate-50 border border-slate-300 rounded-xl font-mono focus:ring-2 focus:ring-blue-500 focus:bg-white focus:outline-none transition">
                </div>

                <div>
                    <label for="add_email" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Email Resmi <span class="text-rose-500">*</span>
                    </label>
                    <input type="email" name="email" id="add_email" required
                        placeholder="nama@sekolah.sch.id"
                        class="w-full px-3.5 py-2.5 text-xs sm:text-sm bg-slate-50 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:bg-white focus:outline-none transition">
                </div>
            </div>

            <!-- Kata Sandi -->
            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <label for="add_password" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                        Kata Sandi <span class="text-rose-500">*</span>
                    </label>
                    <button type="button" onclick="generateRandomPassword('add_password')"
                        class="text-[11px] font-bold text-blue-600 hover:text-blue-800 transition">
                        + Acak (4 Huruf + 2 Angka)
                    </button>
                </div>
                <div class="relative">
                    <input type="text" name="password" id="add_password" required minlength="6"
                        placeholder="Masukkan kata sandi (min. 6 karakter)"
                        class="w-full px-3.5 py-2.5 text-xs sm:text-sm bg-slate-50 border border-slate-300 rounded-xl font-mono focus:ring-2 focus:ring-blue-500 focus:bg-white focus:outline-none transition">
                </div>
            </div>

            <!-- Checkbox Aktif -->
            <div class="pt-1">
                <label class="flex items-center gap-2 cursor-pointer text-xs font-semibold text-slate-700">
                    <input type="checkbox" name="is_active" value="1" checked class="w-4 h-4 text-blue-600 rounded border-slate-300 focus:ring-blue-500">
                    <span>Langsung aktifkan status akun pengguna ini</span>
                </label>
            </div>

            <!-- Footer Action -->
            <div class="pt-4 flex items-center justify-end gap-2 border-t border-slate-100">
                <button type="button" onclick="closeAddModal()"
                    class="px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-100 text-xs sm:text-sm font-semibold transition">
                    Batal
                </button>
                <button type="submit"
                    class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white text-xs sm:text-sm font-bold shadow-md shadow-blue-500/20 transition">
                    Simpan Akun
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL 2: RESET KATA SANDI (RESET PASSWORD)                                -->
<!-- ========================================================================= -->
<div id="resetPasswordModal" class="fixed inset-0 z-50 bg-slate-950/70 backdrop-blur-sm hidden items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-md w-full p-6 sm:p-7 shadow-2xl border border-slate-200 relative">
        
        <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center font-bold">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                </div>
                <div>
                    <h3 class="text-base sm:text-lg font-bold text-slate-800">Reset Kata Sandi</h3>
                    <p class="text-xs text-slate-500">Perbarui kredensial masuk pengguna</p>
                </div>
            </div>
            <button type="button" onclick="closeResetModal()" class="text-slate-400 hover:text-slate-600 p-1.5 rounded-lg hover:bg-slate-100 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <!-- Detail Pengguna Target -->
        <div class="bg-slate-50 rounded-2xl p-3.5 border border-slate-200 mb-4 space-y-1 text-xs">
            <div class="text-slate-500 text-[11px] uppercase tracking-wider font-bold">Akun Terpilih:</div>
            <div class="font-bold text-slate-800 text-sm" id="reset_user_name">-</div>
            <div class="text-slate-500 flex items-center gap-2">
                <span>Username: <strong class="font-mono text-slate-700" id="reset_user_username">-</strong></span>
                <span>•</span>
                <span class="text-slate-600 font-medium" id="reset_user_role">-</span>
            </div>
        </div>

        <form id="resetPasswordForm" method="POST" action="" class="space-y-4">
            @csrf

            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <label for="reset_new_password" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                        Kata Sandi Baru <span class="text-rose-500">*</span>
                    </label>
                    <div class="flex items-center gap-2">
                        <button type="button" id="btnResetToNpsn" onclick="resetToNpsn()" class="text-[11px] font-bold text-teal-600 hover:text-teal-800 transition hidden">
                            Gunakan NPSN
                        </button>
                        <button type="button" onclick="generateRandomPassword('reset_new_password')" class="text-[11px] font-bold text-blue-600 hover:text-blue-800 transition">
                            + Acak (4 Huruf + 2 Angka)
                        </button>
                    </div>
                </div>
                <input type="text" name="new_password" id="reset_new_password" required minlength="6"
                    placeholder="Masukkan kata sandi baru (min. 6 karakter)"
                    class="w-full px-3.5 py-2.5 text-xs sm:text-sm bg-slate-50 border border-slate-300 rounded-xl font-mono focus:ring-2 focus:ring-amber-500 focus:bg-white focus:outline-none transition">
                <p class="text-[11px] text-slate-400 mt-1">Pengguna dapat langsung menggunakan kata sandi baru ini saat masuk.</p>
            </div>

            <div class="pt-3 flex items-center justify-end gap-2 border-t border-slate-100">
                <button type="button" onclick="closeResetModal()"
                    class="px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-100 text-xs sm:text-sm font-semibold transition">
                    Batal
                </button>
                <button type="submit"
                    class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 text-white text-xs sm:text-sm font-bold shadow-md shadow-amber-500/20 transition">
                    Simpan Kata Sandi
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL 3: HAPUS AKUN DENGAN DOBEL KONFIRMASI (DOUBLE CONFIRMATION DELETE)  -->
<!-- ========================================================================= -->
<div id="deleteUserModal" class="fixed inset-0 z-50 bg-slate-950/80 backdrop-blur-sm hidden items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-md w-full p-6 sm:p-7 shadow-2xl border border-rose-200 relative">
        
        <!-- Danger Warning Icon -->
        <div class="w-12 h-12 rounded-2xl bg-rose-100 text-rose-600 border border-rose-200 flex items-center justify-center mb-4 mx-auto">
            <svg class="w-6 h-6 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
        </div>

        <div class="text-center space-y-1 mb-5">
            <h3 class="text-lg font-extrabold text-slate-800">Dobel Konfirmasi Penghapusan</h3>
            <p class="text-xs text-rose-600 font-semibold">Tindakan ini permanen dan tidak dapat dibatalkan!</p>
        </div>

        <!-- Info Akun yang Akan Dihapus -->
        <div class="bg-rose-50/70 border border-rose-200/80 rounded-2xl p-3.5 mb-5 text-xs text-left space-y-1.5">
            <div class="text-[11px] font-bold text-rose-800 uppercase tracking-wider">Target Akun:</div>
            <div class="font-extrabold text-slate-800 text-sm" id="delete_target_name">-</div>
            <div class="text-slate-600 flex items-center gap-2">
                <span>Username: <strong class="font-mono text-slate-800" id="delete_target_username">-</strong></span>
                <span>•</span>
                <span class="text-slate-700 font-medium" id="delete_target_role">-</span>
            </div>
            <p class="text-[11px] text-rose-700 leading-relaxed pt-1 border-t border-rose-200/60">
                Seluruh hak akses login dan sesi aktif akun ini akan dicabut. Data kepegawaian (jika ada) akan tetap tersimpan namun akun login dilepas.
            </p>
        </div>

        <form id="deleteUserForm" method="POST" action="" class="space-y-4">
            @csrf
            @method('DELETE')

            <!-- KONFIRMASI 1: CHECKBOX PERSETUJUAN RISIKO -->
            <div class="bg-slate-50 p-3 rounded-xl border border-slate-200 text-left">
                <label class="flex items-start gap-2.5 cursor-pointer text-xs font-semibold text-slate-700 select-none">
                    <input type="checkbox" id="check_agree_delete" onchange="validateDoubleConfirmation()"
                        class="mt-0.5 w-4 h-4 text-rose-600 rounded border-slate-300 focus:ring-rose-500">
                    <span class="leading-snug">
                        <strong>Konfirmasi 1:</strong> Saya memahami risiko dan menyetujui akun ini dihapus secara permanen.
                    </span>
                </label>
            </div>

            <!-- KONFIRMASI 2: KETIK KATA "HAPUS" -->
            <div class="text-left space-y-1.5">
                <label for="input_confirm_delete" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                    <strong>Konfirmasi 2:</strong> Ketik kata <span class="text-rose-600 font-black">HAPUS</span> di bawah:
                </label>
                <input type="text" name="confirm_delete" id="input_confirm_delete" oninput="validateDoubleConfirmation()"
                    autocomplete="off" placeholder="Ketik HAPUS (huruf kapital)"
                    class="w-full px-3.5 py-2.5 text-xs sm:text-sm bg-slate-50 border border-slate-300 rounded-xl font-bold tracking-wider uppercase text-center focus:ring-2 focus:ring-rose-500 focus:bg-white focus:outline-none transition">
            </div>

            <!-- ACTION BUTTONS -->
            <div class="pt-2 flex flex-col sm:flex-row items-center gap-2">
                <button type="button" onclick="closeDeleteModal()"
                    class="w-full sm:w-auto flex-1 py-2.5 rounded-xl border border-slate-200 text-slate-700 hover:bg-slate-100 text-xs sm:text-sm font-semibold transition">
                    Batalkan
                </button>
                <button type="submit" id="btnSubmitDelete" disabled
                    class="w-full sm:w-auto flex-1 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs sm:text-sm font-bold shadow-md shadow-rose-600/30 transition opacity-40 cursor-not-allowed">
                    Hapus Akun Permanen
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ========================================================================= -->
<!-- JAVASCRIPT INTERACTIVE LOGIC                                              -->
<!-- ========================================================================= -->
<script>
    let currentNpsnTarget = '';

    // 1. ADD USER MODAL
    function openAddModal() {
        document.getElementById('addUserModal').classList.remove('hidden');
        document.getElementById('addUserModal').classList.add('flex');
    }

    function closeAddModal() {
        document.getElementById('addUserModal').classList.add('hidden');
        document.getElementById('addUserModal').classList.remove('flex');
    }

    function handleRoleChange(role) {
        const unitSelect = document.getElementById('add_unit_id');
        const usernameInput = document.getElementById('add_username');
        const passwordInput = document.getElementById('add_password');
        const nameInput = document.getElementById('add_name');

        if (role === 'tu') {
            const selectedOpt = unitSelect.options[unitSelect.selectedIndex];
            const npsn = selectedOpt ? selectedOpt.getAttribute('data-npsn') : '';
            const unitName = selectedOpt ? selectedOpt.getAttribute('data-name') : '';

            if (npsn) {
                usernameInput.value = npsn;
                passwordInput.value = npsn;
            }
            if (unitName && !nameInput.value) {
                nameInput.value = 'TU ' + unitName;
            }
        }
    }

    function handleUnitChange(selectEl) {
        const role = document.getElementById('add_role').value;
        const selectedOpt = selectEl.options[selectEl.selectedIndex];
        const npsn = selectedOpt.getAttribute('data-npsn');
        const unitName = selectedOpt.getAttribute('data-name');
        const nameInput = document.getElementById('add_name');
        const usernameInput = document.getElementById('add_username');
        const passwordInput = document.getElementById('add_password');

        if (role === 'tu' && npsn) {
            usernameInput.value = npsn;
            passwordInput.value = npsn;
            if (unitName) {
                nameInput.value = 'TU ' + unitName;
            }
        }
    }

    // 2. RESET PASSWORD MODAL
    function openResetModal(userId, name, username, roleTitle, unitNpsn) {
        currentNpsnTarget = unitNpsn;
        document.getElementById('reset_user_name').innerText = name;
        document.getElementById('reset_user_username').innerText = username;
        document.getElementById('reset_user_role').innerText = roleTitle;
        document.getElementById('reset_new_password').value = '';
        document.getElementById('resetPasswordForm').action = `/users/${userId}/reset-password`;

        const btnNpsn = document.getElementById('btnResetToNpsn');
        if (unitNpsn) {
            btnNpsn.classList.remove('hidden');
            btnNpsn.innerText = `Gunakan NPSN (${unitNpsn})`;
        } else {
            btnNpsn.classList.add('hidden');
        }

        document.getElementById('resetPasswordModal').classList.remove('hidden');
        document.getElementById('resetPasswordModal').classList.add('flex');
    }

    function closeResetModal() {
        document.getElementById('resetPasswordModal').classList.add('hidden');
        document.getElementById('resetPasswordModal').classList.remove('flex');
    }

    function resetToNpsn() {
        if (currentNpsnTarget) {
            document.getElementById('reset_new_password').value = currentNpsnTarget;
        }
    }

    // 3. GENERATE RANDOM PASSWORD (4 Huruf Besar + 2 Angka)
    function generateRandomPassword(targetId) {
        const letters = 'ABCDEFGHJKLMNPQRSTUVWXYZ';
        const numbers = '23456789';
        let res = '';
        for (let i = 0; i < 4; i++) {
            res += letters.charAt(Math.floor(Math.random() * letters.length));
        }
        for (let i = 0; i < 2; i++) {
            res += numbers.charAt(Math.floor(Math.random() * numbers.length));
        }
        document.getElementById(targetId).value = res;
    }

    // 4. DELETE USER WITH DOUBLE CONFIRMATION MODAL
    function openDeleteModal(userId, name, username, roleTitle) {
        document.getElementById('delete_target_name').innerText = name;
        document.getElementById('delete_target_username').innerText = username;
        document.getElementById('delete_target_role').innerText = roleTitle;
        document.getElementById('deleteUserForm').action = `/users/${userId}`;

        // Reset inputs
        document.getElementById('check_agree_delete').checked = false;
        document.getElementById('input_confirm_delete').value = '';
        validateDoubleConfirmation();

        document.getElementById('deleteUserModal').classList.remove('hidden');
        document.getElementById('deleteUserModal').classList.add('flex');
    }

    function closeDeleteModal() {
        document.getElementById('deleteUserModal').classList.add('hidden');
        document.getElementById('deleteUserModal').classList.remove('flex');
    }

    function validateDoubleConfirmation() {
        const isChecked = document.getElementById('check_agree_delete').checked;
        const confirmText = document.getElementById('input_confirm_delete').value.trim();
        const btnSubmit = document.getElementById('btnSubmitDelete');

        if (isChecked && confirmText === 'HAPUS') {
            btnSubmit.disabled = false;
            btnSubmit.classList.remove('opacity-40', 'cursor-not-allowed');
            btnSubmit.classList.add('opacity-100', 'cursor-pointer');
        } else {
            btnSubmit.disabled = true;
            btnSubmit.classList.add('opacity-40', 'cursor-not-allowed');
            btnSubmit.classList.remove('opacity-100', 'cursor-pointer');
        }
    }

    // Close on backdrop click
    window.addEventListener('click', function(e) {
        const addM = document.getElementById('addUserModal');
        const resetM = document.getElementById('resetPasswordModal');
        const deleteM = document.getElementById('deleteUserModal');
        if (e.target === addM) closeAddModal();
        if (e.target === resetM) closeResetModal();
        if (e.target === deleteM) closeDeleteModal();
    });
</script>
@endsection

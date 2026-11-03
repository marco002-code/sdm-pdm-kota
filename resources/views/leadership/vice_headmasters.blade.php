@extends('layouts.app')

@section('content')
<div class="space-y-4">
    <!-- Header Modul Promosi Wakil Kepala Sekolah -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2 py-0.5 rounded-md text-[10px] font-bold uppercase tracking-wider bg-blue-100 text-blue-800">Manajemen Pimpinan Sekolah</span>
                <span class="text-xs text-slate-400">•</span>
                <span class="text-xs text-slate-500 font-medium">Majelis Dikdasmen & PNF PDM Kota Yogyakarta</span>
            </div>
            <h1 class="text-xl lg:text-2xl font-bold text-slate-800 tracking-tight mt-0.5">Modul Promosi Wakil Kepala Sekolah</h1>
            <p class="text-xs text-slate-500 mt-0.5">Pengusulan, verifikasi portofolio, dan penerbitan SK Waka Kurikulum/Ismuba, Kesiswaan, Sarpras, dan Humas</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <button type="button" onclick="document.getElementById('modalNominateWaka').classList.remove('hidden')" class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-semibold shadow-xs transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>Usulkan Wakil Kepala Sekolah</span>
            </button>
            <a href="{{ route('leadership.headmasters') }}" class="inline-flex items-center gap-1.5 px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-800 rounded-xl text-xs font-semibold transition">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                <span>Seleksi Kepala Sekolah</span>
            </a>
        </div>
    </div>

    <!-- Alert Notifikasi -->
    @if(session('success'))
    <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs flex items-center gap-2.5">
        <svg class="w-4 h-4 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
        <span>{{ session('success') }}</span>
    </div>
    @endif

    <!-- Metrik Wakil Kepala Sekolah -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
        <div class="bg-white/90 backdrop-blur-xl p-3.5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500">Total Pengusulan Waka</span>
                <div class="text-xl font-black text-slate-800 mt-0.5">{{ $counts['total'] }} <span class="text-xs font-normal text-slate-400">orang</span></div>
            </div>
            <div class="w-8 h-8 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center font-bold text-xs">
                ALL
            </div>
        </div>

        <div class="bg-white/90 backdrop-blur-xl p-3.5 rounded-2xl border border-amber-200/80 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-amber-700">Dalam Verifikasi Majelis</span>
                <div class="text-xl font-black text-amber-700 mt-0.5">{{ $counts['diajukan'] }} <span class="text-xs font-normal text-amber-500">usulan</span></div>
            </div>
            <div class="w-8 h-8 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center font-bold text-xs">
                REV
            </div>
        </div>

        <div class="bg-white/90 backdrop-blur-xl p-3.5 rounded-2xl border border-emerald-200/80 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-700">Disetujui & Terbit SK PDM</span>
                <div class="text-xl font-black text-emerald-700 mt-0.5">{{ $counts['disetujui'] }} <span class="text-xs font-normal text-emerald-500">waka</span></div>
            </div>
            <div class="w-8 h-8 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold text-xs">
                ACC
            </div>
        </div>
    </div>

    <!-- Filter & Tabel Usulan Wakil Kepala Sekolah -->
    <div class="bg-white/95 backdrop-blur-xl rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="p-4 border-b border-slate-100 flex flex-col md:flex-row md:items-center md:justify-between gap-3">
            <div>
                <h2 class="text-base font-bold text-slate-800">Daftar Usulan & Pengangkatan Wakil Kepala Sekolah</h2>
                <p class="text-xs text-slate-500">Rekomendasi resmi Kepala Sekolah terpilih dan persetujuan PDM Kota Yogyakarta</p>
            </div>

            <!-- Filter Bidang -->
            <form method="GET" action="{{ route('leadership.vice_headmasters') }}" class="flex flex-wrap items-center gap-2">
                <select name="field" class="px-2.5 py-1.5 text-xs rounded-xl border border-slate-200 focus:outline-none focus:border-blue-500">
                    <option value="">Semua Bidang Waka</option>
                    <option value="kurikulum_ismuba" {{ request('field') == 'kurikulum_ismuba' ? 'selected' : '' }}>Kurikulum & ISMUBA</option>
                    <option value="kesiswaan" {{ request('field') == 'kesiswaan' ? 'selected' : '' }}>Kesiswaan</option>
                    <option value="sarpras" {{ request('field') == 'sarpras' ? 'selected' : '' }}>Sarana & Prasarana</option>
                    <option value="humas_kemuhammadiyahan" {{ request('field') == 'humas_kemuhammadiyahan' ? 'selected' : '' }}>Humas & Kemuhammadiyahan</option>
                </select>
                <select name="status" class="px-2.5 py-1.5 text-xs rounded-xl border border-slate-200 focus:outline-none focus:border-blue-500">
                    <option value="">Semua Status</option>
                    <option value="diajukan" {{ request('status') == 'diajukan' ? 'selected' : '' }}>Diajukan</option>
                    <option value="verifikasi_majelis" {{ request('status') == 'verifikasi_majelis' ? 'selected' : '' }}>Verifikasi Majelis</option>
                    <option value="disetujui" {{ request('status') == 'disetujui' ? 'selected' : '' }}>Disetujui</option>
                </select>
                <button type="submit" class="px-3 py-1.5 bg-slate-800 hover:bg-slate-900 text-white rounded-xl text-xs font-semibold transition">
                    Filter
                </button>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50/80 text-slate-600 font-semibold border-b border-slate-200/60 uppercase tracking-wider text-[10px]">
                    <tr>
                        <th class="py-3 px-4">Nama Pegawai & NBM</th>
                        <th class="py-3 px-4">Unit Kerja AUM</th>
                        <th class="py-3 px-3">Bidang Wakil Kepala Sekolah</th>
                        <th class="py-3 px-3">Periode Jabatan</th>
                        <th class="py-3 px-4">Rekomendasi Kepala Sekolah</th>
                        <th class="py-3 px-3 text-center">Status</th>
                        <th class="py-3 px-4">SK Pengangkatan</th>
                        @if(auth()->user()->isYayasanLevel())
                        <th class="py-3 px-3 text-right">Tindakan</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($nominations as $nom)
                        <tr class="hover:bg-slate-50/60 transition">
                            <td class="py-3 px-4">
                                <div class="font-bold text-slate-800 text-sm">{{ $nom->employee ? $nom->employee->full_name : '-' }}</div>
                                <div class="text-[11px] text-slate-500 mt-0.5">
                                    NPM: <span class="font-mono font-medium text-amber-700">{{ $nom->employee->npm ?? '-' }}</span> • 
                                    NBM: <span class="font-mono font-medium">{{ $nom->employee->nbm ?? '-' }}</span>
                                </div>
                            </td>
                            <td class="py-3 px-4 font-semibold text-slate-700">
                                {{ $nom->unit ? $nom->unit->name : '-' }}
                            </td>
                            <td class="py-3 px-3">
                                @php
                                    $fieldBadge = match($nom->field) {
                                        'kurikulum_ismuba'       => 'bg-blue-50 text-blue-700 border-blue-200',
                                        'kesiswaan'              => 'bg-amber-50 text-amber-700 border-amber-200',
                                        'sarpras'                => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                        'humas_kemuhammadiyahan' => 'bg-purple-50 text-purple-700 border-purple-200',
                                        default                  => 'bg-slate-100 text-slate-700 border-slate-200',
                                    };
                                @endphp
                                <span class="px-2 py-0.5 rounded-md text-[10px] font-bold border {{ $fieldBadge }}">
                                    {{ $nom->field_label }}
                                </span>
                            </td>
                            <td class="py-3 px-3 font-semibold text-slate-700">
                                {{ $nom->period_years }}
                            </td>
                            <td class="py-3 px-4 text-slate-600 max-w-xs">
                                <p class="line-clamp-2 text-[11px]">
                                    {{ $nom->recommendation_headmaster ?? '-' }}
                                </p>
                            </td>
                            <td class="py-3 px-3 text-center">
                                @php
                                    $nomBadge = match($nom->status) {
                                        'disetujui'          => 'bg-emerald-100 text-emerald-800 border-emerald-300 font-bold',
                                        'verifikasi_majelis' => 'bg-blue-100 text-blue-800 border-blue-300 font-semibold',
                                        'diajukan'           => 'bg-amber-100 text-amber-800 border-amber-300 font-semibold',
                                        default              => 'bg-slate-100 text-slate-700 border-slate-300',
                                    };
                                @endphp
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold border {{ $nomBadge }}">
                                    {{ $nom->status_label }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-xs font-mono">
                                @if($nom->sk_number)
                                    <div class="font-bold text-emerald-700">{{ $nom->sk_number }}</div>
                                    <div class="text-[10px] text-slate-400">{{ $nom->sk_date ? $nom->sk_date->format('d/m/Y') : '' }}</div>
                                @else
                                    <span class="text-slate-400 italic">Menunggu Sidang</span>
                                @endif
                            </td>
                            @if(auth()->user()->isYayasanLevel())
                            <td class="py-3 px-3 text-right">
                                <button type="button" onclick="openVerifyWakaModal({{ $nom->id }}, '{{ $nom->status }}', '{{ $nom->sk_number }}', '{{ $nom->sk_date ? $nom->sk_date->format('Y-m-d') : '' }}', '{{ addslashes($nom->notes ?? '') }}')" class="px-2.5 py-1 bg-slate-100 hover:bg-blue-50 text-blue-700 border border-slate-200 hover:border-blue-300 rounded-lg text-xs font-semibold transition">
                                    Verifikasi
                                </button>
                            </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ auth()->user()->isYayasanLevel() ? '8' : '7' }}" class="py-8 text-center text-slate-400">Belum ada data usulan wakil kepala sekolah.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($nominations->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $nominations->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Modal Usulkan Wakil Kepala Sekolah -->
<div id="modalNominateWaka" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-sm hidden p-4">
    <div class="bg-white border border-slate-200 rounded-2xl max-w-lg w-full p-6 space-y-4 shadow-xl">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <h3 class="text-base font-bold text-slate-800">
                Usulkan Wakil Kepala Sekolah Baru
            </h3>
            <button type="button" onclick="document.getElementById('modalNominateWaka').classList.add('hidden')" class="p-1 text-slate-400 hover:text-slate-600 rounded-lg">
                ✕
            </button>
        </div>

        <form action="{{ route('leadership.vice_headmasters.store') }}" method="POST" class="space-y-4">
            @csrf

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                    Pilih Calon Wakil Kepala Sekolah <span class="text-rose-500">*</span>
                </label>
                <select name="employee_id" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs text-slate-800 focus:bg-white focus:outline-none focus:border-blue-500 font-medium">
                    <option value="">-- Pilih Pegawai --</option>
                    @foreach($eligibleEmployees as $emp)
                        <option value="{{ $emp->id }}">
                            {{ $emp->full_name }} (NPM: {{ $emp->npm ?? '-' }} | NBM: {{ $emp->nbm ?? '-' }}) - {{ $emp->unit?->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                        Bidang Waka <span class="text-rose-500">*</span>
                    </label>
                    <select name="field" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs text-slate-800 focus:bg-white focus:outline-none focus:border-blue-500">
                        <option value="kurikulum_ismuba">Kurikulum & ISMUBA</option>
                        <option value="kesiswaan">Kesiswaan</option>
                        <option value="sarpras">Sarana & Prasarana</option>
                        <option value="humas_kemuhammadiyahan">Humas & Kemuhammadiyahan</option>
                        <option value="lainnya">Lainnya</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                        Periode Jabatan <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="period_years" value="{{ date('Y') }}-{{ date('Y') + 4 }}" required placeholder="Contoh: 2026-2030" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs text-slate-800 focus:bg-white focus:outline-none focus:border-blue-500">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                    Surat / Catatan Rekomendasi Kepala Sekolah
                </label>
                <textarea name="recommendation_headmaster" rows="2" placeholder="Pertimbangan kompetensi, loyalitas dan rekomendasi Kepala Sekolah terpilih..." class="w-full px-3.5 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs text-slate-800 focus:bg-white focus:outline-none focus:border-blue-500"></textarea>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                    Catatan Tambahan
                </label>
                <input type="text" name="notes" placeholder="Catatan berkas atau SK..." class="w-full px-3.5 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs text-slate-800 focus:bg-white focus:outline-none focus:border-blue-500">
            </div>

            <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100">
                <button type="button" onclick="document.getElementById('modalNominateWaka').classList.add('hidden')" class="px-4 py-2 text-xs font-medium text-slate-500 hover:text-slate-800 transition">Batal</button>
                <button type="submit" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-semibold shadow-xs transition">
                    Kirim Usulan Waka
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Verifikasi Waka (Yayasan / PDM) -->
<div id="modalVerifyWaka" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-sm hidden p-4">
    <div class="bg-white border border-slate-200 rounded-2xl max-w-md w-full p-6 space-y-4 shadow-xl">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <h3 class="text-base font-bold text-slate-800">
                Verifikasi & Terbitkan SK Waka
            </h3>
            <button type="button" onclick="document.getElementById('modalVerifyWaka').classList.add('hidden')" class="p-1 text-slate-400 hover:text-slate-600 rounded-lg">
                ✕
            </button>
        </div>

        <form id="formVerifyWaka" method="POST" action="" class="space-y-3">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Status Usulan</label>
                <select name="status" id="waka_status" class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-300 rounded-xl focus:outline-none focus:border-blue-500">
                    <option value="diajukan">Diajukan</option>
                    <option value="verifikasi_majelis">Verifikasi Majelis Dikdasmen</option>
                    <option value="disetujui">Disetujui & Terbitkan SK PDM</option>
                    <option value="ditolak">Ditolak</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Nomor SK PDM</label>
                <input type="text" name="sk_number" id="waka_sk_number" placeholder="Contoh: 092/KEP/III.0/D/2026" class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-300 rounded-xl focus:outline-none focus:border-blue-500 font-mono">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Tanggal SK</label>
                <input type="date" name="sk_date" id="waka_sk_date" class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-300 rounded-xl focus:outline-none focus:border-blue-500">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Catatan</label>
                <textarea name="notes" id="waka_notes" rows="2" class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-300 rounded-xl focus:outline-none focus:border-blue-500"></textarea>
            </div>

            <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100">
                <button type="button" onclick="document.getElementById('modalVerifyWaka').classList.add('hidden')" class="px-4 py-2 text-xs font-medium text-slate-500 hover:text-slate-800 transition">Batal</button>
                <button type="submit" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-semibold shadow-xs transition">
                    Simpan Keputusan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openVerifyWakaModal(id, status, skNum, skDate, notes) {
    const form = document.getElementById('formVerifyWaka');
    form.action = '/leadership/vice-headmasters/' + id + '/verify';
    document.getElementById('waka_status').value = status || 'diajukan';
    document.getElementById('waka_sk_number').value = skNum || '';
    document.getElementById('waka_sk_date').value = skDate || '';
    document.getElementById('waka_notes').value = notes || '';
    document.getElementById('modalVerifyWaka').classList.remove('hidden');
}
</script>
@endsection

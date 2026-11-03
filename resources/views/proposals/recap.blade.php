@extends('layouts.app')

@section('title', 'Rekapitulasi Usulan Sekolah Terpadu - PDM Kota Yogyakarta')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 bg-white border border-slate-200 p-6 rounded-2xl shadow-xs">
        <div>
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-500 to-blue-600 flex items-center justify-center text-white shadow-md shadow-indigo-500/20 flex-shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
                <div>
                    <h1 class="text-xl font-bold text-slate-800 tracking-tight">Rekapitulasi Usulan Sekolah Terpadu</h1>
                    <p class="text-xs text-slate-500 mt-0.5">Rekapitulasi usulan kenaikan pangkat, alih status (GTT &rarr; GTY, PTT &rarr; PTY), calon Kepala Sekolah, dan tim manajemen (Wakil Kepala Sekolah)</p>
                </div>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('proposals.recap_print', request()->query()) }}" target="_blank" class="inline-flex items-center gap-2 px-3.5 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-900 text-white font-semibold text-xs shadow-xs transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                <span>Cetak Lembar Rekap</span>
            </a>
            <a href="{{ route('promotions.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-xs shadow-xs transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>+ Usulkan Kenaikan Pangkat / Status</span>
            </a>
        </div>
    </div>

    <!-- Metrik Statistik Ringkasan Usulan -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="bg-white border border-slate-200 p-4 rounded-2xl shadow-xs">
            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Total Seluruh Usulan</span>
            <div class="text-2xl font-black text-slate-800 mt-1">{{ $stats['total_all'] }}</div>
            <p class="text-[11px] text-slate-500 mt-0.5">Semua kategori usulan</p>
        </div>

        <div class="bg-white border border-slate-200 p-4 rounded-2xl shadow-xs">
            <span class="text-[10px] font-bold uppercase tracking-wider text-blue-600">Pangkat & Alih Status</span>
            <div class="text-2xl font-black text-blue-700 mt-1">{{ $stats['total_rank'] }}</div>
            <p class="text-[11px] text-blue-600/80 mt-0.5">Alih GTY/PTY: <strong>{{ $stats['total_alih'] }}</strong> orang</p>
        </div>

        <div class="bg-white border border-slate-200 p-4 rounded-2xl shadow-xs">
            <span class="text-[10px] font-bold uppercase tracking-wider text-purple-600">Calon Kepala Sekolah</span>
            <div class="text-2xl font-black text-purple-700 mt-1">{{ $stats['total_ks'] }}</div>
            <p class="text-[11px] text-slate-500 mt-0.5">Bakal calon terdaftar</p>
        </div>

        <div class="bg-white border border-slate-200 p-4 rounded-2xl shadow-xs">
            <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-600">Wakil Kepala Sekolah</span>
            <div class="text-2xl font-black text-emerald-700 mt-1">{{ $stats['total_waka'] }}</div>
            <p class="text-[11px] text-slate-500 mt-0.5">Tim Manajemen AUM</p>
        </div>
    </div>

    <!-- Filter Satuan Pendidikan / Sekolah (Untuk Yayasan & TU) -->
    <div class="bg-white border border-slate-200 p-4 rounded-2xl shadow-xs">
        <form method="GET" action="{{ route('proposals.recap') }}" class="flex flex-wrap items-center gap-3">
            @if(auth()->user()->isYayasanLevel())
            <div class="flex-1 min-w-[220px]">
                <label class="block text-[11px] font-semibold text-slate-600 mb-1">Pilih Sekolah / Unit Kerja</label>
                <select name="unit_id" class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-300 rounded-xl focus:bg-white focus:outline-none focus:border-blue-500">
                    <option value="">-- Semua Satuan Pendidikan (76 Unit AUM) --</option>
                    @foreach($units as $u)
                        <option value="{{ $u->id }}" {{ request('unit_id') == $u->id ? 'selected' : '' }}>{{ $u->name }} ({{ $u->level }})</option>
                    @endforeach
                </select>
            </div>
            @endif

            <div class="w-48">
                <label class="block text-[11px] font-semibold text-slate-600 mb-1">Status Usulan</label>
                <select name="status" class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-300 rounded-xl focus:bg-white focus:outline-none focus:border-blue-500">
                    <option value="">Semua Status</option>
                    <option value="diajukan" {{ request('status') === 'diajukan' ? 'selected' : '' }}>Diajukan</option>
                    <option value="diverifikasi_sekolah" {{ request('status') === 'diverifikasi_sekolah' ? 'selected' : '' }}>Verifikasi Sekolah</option>
                    <option value="sidang_majelis" {{ request('status') === 'sidang_majelis' ? 'selected' : '' }}>Sidang Majelis</option>
                    <option value="disetujui_pdm" {{ request('status') === 'disetujui_pdm' ? 'selected' : '' }}>Disetujui PDM</option>
                    <option value="ditolak" {{ request('status') === 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                </select>
            </div>

            <div class="flex items-end gap-2 pt-5">
                <button type="submit" class="px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white rounded-xl text-xs font-semibold transition">
                    Terapkan Filter
                </button>
                @if(request()->anyFilled(['unit_id', 'status', 'type']))
                <a href="{{ route('proposals.recap') }}" class="px-3 py-2 text-slate-500 hover:text-slate-800 text-xs font-medium transition">
                    Reset
                </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Tab Navigasi Kategori Usulan -->
    <div class="bg-slate-100 p-1.5 rounded-2xl flex max-w-2xl border border-slate-200">
        <button type="button" onclick="switchRecapTab('rank')" id="tab_btn_rank" class="flex-1 py-2 px-3 rounded-xl text-xs font-bold transition bg-white text-slate-800 shadow-xs border border-slate-200/80">
            Kenaikan Pangkat & Alih Status ({{ $rankPromotions->count() }})
        </button>
        <button type="button" onclick="switchRecapTab('ks')" id="tab_btn_ks" class="flex-1 py-2 px-3 rounded-xl text-xs font-bold transition text-slate-500 hover:text-slate-800">
            Calon Kepala Sekolah ({{ $ksCandidates->count() }})
        </button>
        <button type="button" onclick="switchRecapTab('waka')" id="tab_btn_waka" class="flex-1 py-2 px-3 rounded-xl text-xs font-bold transition text-slate-500 hover:text-slate-800">
            Wakil Kepala Sekolah ({{ $viceNominations->count() }})
        </button>
    </div>

    <!-- TAB 1: KENAIKAN PANGKAT & ALIH STATUS PEGAWAI -->
    <div id="tab_content_rank" class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-xs">
        <div class="p-4 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="text-sm font-bold text-slate-800">Rekap Usulan Kenaikan Pangkat & Alih Status Pegawai</h3>
                <p class="text-xs text-slate-500">Perubahan status GTT &rarr; GTY, PTT &rarr; PTY, penyesuaian ijazah, dan kenaikan pangkat berkala</p>
            </div>
            <a href="{{ route('promotions.create') }}" class="text-xs font-semibold text-emerald-700 hover:underline">
                + Tambah Usulan Baru
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-700">
                <thead class="bg-slate-50/75 text-slate-500 font-bold border-b border-slate-200 uppercase tracking-wider text-[10px]">
                    <tr>
                        <th class="px-4 py-3">Nama Pegawai & Unit Sekolah</th>
                        <th class="px-3 py-3">Jenis Usulan</th>
                        <th class="px-3 py-3 text-center">Status Awal &rarr; Diusulkan</th>
                        <th class="px-3 py-3 text-center">Pangkat Saat Ini &rarr; Baru</th>
                        <th class="px-2 py-3 text-center">Masa Kerja</th>
                        <th class="px-2 py-3 text-center">Nilai PKG</th>
                        <th class="px-3 py-3 text-center">Status Verifikasi</th>
                        <th class="px-3 py-3">SK Penetapan PDM</th>
                        <th class="px-3 py-3 text-right">Detail</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($rankPromotions as $r)
                    <tr class="hover:bg-slate-50/70 transition">
                        <td class="px-4 py-3">
                            <div class="font-bold text-slate-800">{{ $r->employee?->full_name }}</div>
                            <div class="text-[11px] text-slate-400 mt-0.5">
                                NPM: <span class="font-mono text-amber-700 font-semibold">{{ $r->employee?->npm ?? '-' }}</span> • 
                                <span class="text-slate-600 font-medium">{{ $r->unit?->name }}</span>
                            </div>
                        </td>
                        <td class="px-3 py-3">
                            <span class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-200">
                                {{ $r->promotion_type_label }}
                            </span>
                        </td>
                        <td class="px-3 py-3 text-center">
                            <span class="font-bold text-slate-600">{{ $r->current_employee_type }}</span>
                            <span class="text-slate-400 mx-1">&rarr;</span>
                            <span class="font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded border border-emerald-200">{{ $r->proposed_employee_type }}</span>
                        </td>
                        <td class="px-3 py-3 text-center">
                            <span class="text-slate-600">{{ $r->current_rank ?? '-' }}</span>
                            <span class="text-slate-400 mx-1">&rarr;</span>
                            <strong class="text-blue-800">{{ $r->proposed_rank }}</strong>
                        </td>
                        <td class="px-2 py-3 text-center font-semibold text-slate-700">
                            {{ $r->years_of_service }} thn
                        </td>
                        <td class="px-2 py-3 text-center font-bold text-emerald-700">
                            {{ $r->performance_score ?? '-' }}
                        </td>
                        <td class="px-3 py-3 text-center">
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-700 border border-slate-200">
                                {{ $r->status_label }}
                            </span>
                        </td>
                        <td class="px-3 py-3 text-xs font-mono">
                            @if($r->sk_number)
                                <div class="font-bold text-emerald-700">{{ $r->sk_number }}</div>
                                <div class="text-[10px] text-slate-400">{{ $r->sk_date ? $r->sk_date->format('d/m/Y') : '' }}</div>
                            @else
                                <span class="text-slate-400 italic">Menunggu Sidang</span>
                            @endif
                        </td>
                        <td class="px-3 py-3 text-right">
                            <a href="{{ route('promotions.show', $r->id) }}" class="text-blue-600 hover:text-blue-700 font-semibold text-xs hover:underline">
                                Buka Berkas
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="px-4 py-8 text-center text-slate-400">
                            Belum ada usulan kenaikan pangkat atau alih status pegawai yang tercatat.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- TAB 2: USULAN CALON KEPALA SEKOLAH -->
    <div id="tab_content_ks" class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-xs hidden">
        <div class="p-4 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="text-sm font-bold text-slate-800">Rekap Usulan Bakal Calon Kepala Sekolah</h3>
                <p class="text-xs text-slate-500">Penjaringan, rekomendasi PCM/Komite, dan hasil uji Fit and Proper Test Majelis Dikdasmen</p>
            </div>
            <a href="{{ route('leadership.headmasters') }}" class="text-xs font-semibold text-purple-700 hover:underline">
                Kelola Agenda Seleksi KS
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-700">
                <thead class="bg-slate-50/75 text-slate-500 font-bold border-b border-slate-200 uppercase tracking-wider text-[10px]">
                    <tr>
                        <th class="px-4 py-3">Nama Calon & NBM</th>
                        <th class="px-4 py-3">Unit Sekolah AUM</th>
                        <th class="px-3 py-3">Periode Jabatan</th>
                        <th class="px-3 py-3">Rekomendasi PCM & Komite</th>
                        <th class="px-3 py-3 text-center">Skor Fit & Proper</th>
                        <th class="px-3 py-3 text-center">Status Pleno</th>
                        <th class="px-4 py-3">SK Penetapan & Pelantikan</th>
                        <th class="px-3 py-3 text-right">Agenda</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($ksCandidates as $c)
                    <tr class="hover:bg-slate-50/70 transition {{ $c->is_elected ? 'bg-emerald-50/40' : '' }}">
                        <td class="px-4 py-3">
                            <div class="font-bold text-slate-800 flex items-center gap-1.5">
                                <span>{{ $c->employee?->full_name }}</span>
                                @if($c->is_elected)
                                    <span class="px-1.5 py-0.5 rounded text-[9px] font-black bg-emerald-600 text-white">TERPILIH</span>
                                @endif
                            </div>
                            <div class="text-[11px] text-slate-400 mt-0.5">
                                NPM: <span class="font-mono text-amber-700 font-semibold">{{ $c->employee?->npm ?? '-' }}</span> • 
                                NBM: <span class="font-mono text-slate-600">{{ $c->nbm ?? '-' }}</span>
                            </div>
                        </td>
                        <td class="px-4 py-3 font-semibold text-slate-800">
                            {{ $c->selection?->unit?->name }}
                        </td>
                        <td class="px-3 py-3 font-medium text-slate-600">
                            {{ $c->selection?->period_years }}
                        </td>
                        <td class="px-3 py-3 text-slate-600">
                            <div class="text-[11px]"><span class="text-slate-400">PCM:</span> {{ $c->recommendation_pcm ?: 'Terlampir' }}</div>
                            <div class="text-[11px]"><span class="text-slate-400">Komite:</span> {{ $c->recommendation_komite ?: 'Disetujui' }}</div>
                        </td>
                        <td class="px-3 py-3 text-center font-mono font-black text-sm {{ $c->is_elected ? 'text-emerald-700' : 'text-blue-700' }}">
                            {{ $c->total_score ?? '-' }}
                        </td>
                        <td class="px-3 py-3 text-center">
                            @if($c->is_elected)
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-300">
                                    DILANTIK
                                </span>
                            @else
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                                    {{ $c->pleno_decision ? ucfirst($c->pleno_decision) : 'Proses Asesmen' }}
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-xs font-mono">
                            @if($c->sk_number)
                                <div class="font-bold text-emerald-700">{{ $c->sk_number }}</div>
                                <div class="text-[10px] text-slate-400">Tgl SK: {{ $c->sk_date ? $c->sk_date->format('d/m/Y') : '-' }}</div>
                            @else
                                <span class="text-slate-400 italic">Belum terbit SK</span>
                            @endif
                        </td>
                        <td class="px-3 py-3 text-right">
                            <a href="{{ route('leadership.headmaster_show', $c->headmaster_selection_id) }}" class="text-purple-600 hover:text-purple-700 font-semibold text-xs hover:underline">
                                Buka Seleksi
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-4 py-8 text-center text-slate-400">
                            Belum ada usulan calon kepala sekolah yang terdaftar.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- TAB 3: USULAN TIM MANAJEMEN (WAKIL KEPALA SEKOLAH) -->
    <div id="tab_content_waka" class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-xs hidden">
        <div class="p-4 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="text-sm font-bold text-slate-800">Rekap Usulan Wakil Kepala Sekolah (Tim Manajemen)</h3>
                <p class="text-xs text-slate-500">Usulan bidang Kurikulum & ISMUBA, Kesiswaan, Sarana Prasarana, dan Humas Kemuhammadiyahan</p>
            </div>
            <a href="{{ route('leadership.vice_headmasters') }}" class="text-xs font-semibold text-blue-700 hover:underline">
                Kelola Modul Waka
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-700">
                <thead class="bg-slate-50/75 text-slate-500 font-bold border-b border-slate-200 uppercase tracking-wider text-[10px]">
                    <tr>
                        <th class="px-4 py-3">Nama Calon Waka & NBM</th>
                        <th class="px-4 py-3">Unit Sekolah AUM</th>
                        <th class="px-3 py-3">Bidang Wakil Kepala Sekolah</th>
                        <th class="px-3 py-3">Periode</th>
                        <th class="px-4 py-3">Rekomendasi Kepala Sekolah</th>
                        <th class="px-3 py-3 text-center">Status Verifikasi</th>
                        <th class="px-4 py-3">SK Pengangkatan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($viceNominations as $v)
                    <tr class="hover:bg-slate-50/70 transition">
                        <td class="px-4 py-3">
                            <div class="font-bold text-slate-800">{{ $v->employee?->full_name }}</div>
                            <div class="text-[11px] text-slate-400 mt-0.5">
                                NPM: <span class="font-mono text-amber-700 font-semibold">{{ $v->employee?->npm ?? '-' }}</span> • 
                                NBM: <span class="font-mono text-slate-600">{{ $v->employee?->nbm ?? '-' }}</span>
                            </div>
                        </td>
                        <td class="px-4 py-3 font-semibold text-slate-800">
                            {{ $v->unit?->name }}
                        </td>
                        <td class="px-3 py-3">
                            <span class="inline-block px-2.5 py-0.5 rounded-md text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-200">
                                {{ $v->field_label }}
                            </span>
                        </td>
                        <td class="px-3 py-3 font-medium text-slate-600">
                            {{ $v->period_years }}
                        </td>
                        <td class="px-4 py-3 text-slate-600 max-w-xs">
                            <p class="line-clamp-2 text-[11px]">{{ $v->recommendation_headmaster ?? '-' }}</p>
                        </td>
                        <td class="px-3 py-3 text-center">
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-700 border border-slate-200">
                                {{ $v->status_label }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-xs font-mono">
                            @if($v->sk_number)
                                <div class="font-bold text-emerald-700">{{ $v->sk_number }}</div>
                                <div class="text-[10px] text-slate-400">{{ $v->sk_date ? $v->sk_date->format('d/m/Y') : '' }}</div>
                            @else
                                <span class="text-slate-400 italic">Menunggu Sidang</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-4 py-8 text-center text-slate-400">
                            Belum ada usulan wakil kepala sekolah yang diajukan.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function switchRecapTab(tab) {
    const tabs = ['rank', 'ks', 'waka'];
    tabs.forEach(t => {
        const btn = document.getElementById('tab_btn_' + t);
        const content = document.getElementById('tab_content_' + t);
        if (t === tab) {
            btn.className = 'flex-1 py-2 px-3 rounded-xl text-xs font-bold transition bg-white text-slate-800 shadow-xs border border-slate-200/80';
            content.classList.remove('hidden');
        } else {
            btn.className = 'flex-1 py-2 px-3 rounded-xl text-xs font-bold transition text-slate-500 hover:text-slate-800';
            content.classList.add('hidden');
        }
    });
}
</script>
@endsection

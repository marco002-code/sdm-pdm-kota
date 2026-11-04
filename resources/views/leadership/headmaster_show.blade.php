@extends('layouts.app')

@section('content')
<div class="space-y-4">
    <!-- Breadcrumb -->
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-2 text-xs text-slate-500">
            <a href="{{ route('leadership.headmasters') }}" class="hover:text-purple-600 transition flex items-center gap-1 font-medium">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                <span>Kembali ke Seleksi Kepala Sekolah</span>
            </a>
            <span>/</span>
            <span class="font-semibold text-slate-700">{{ $selection->unit->name }} (Periode {{ $selection->period_years }})</span>
        </div>
        <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-purple-50 text-purple-700 border border-purple-200">
            {{ $selection->status_label }}
        </span>
    </div>

    <!-- Alert Sukses / Error -->
    @if(session('success'))
    <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs flex items-center gap-2.5">
        <svg class="w-4 h-4 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
        <span>{{ session('success') }}</span>
    </div>
    @endif
    @if(session('error'))
    <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs flex items-center gap-2.5">
        <svg class="w-4 h-4 text-rose-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        <span>{{ session('error') }}</span>
    </div>
    @endif

    <!-- Banner Unit & Seleksi -->
    <div class="bg-white/95 backdrop-blur-xl p-5 rounded-2xl border border-slate-200/80 shadow-xs">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-purple-700 bg-purple-50 border border-purple-200 px-2.5 py-0.5 rounded-md">
                    Seleksi & Promosi Kepala Sekolah
                </span>
                <h1 class="text-xl lg:text-2xl font-black text-slate-800 tracking-tight mt-1">{{ $selection->unit->name }}</h1>
                <p class="text-xs text-slate-500 mt-0.5">
                    NPSN: <span class="font-mono font-semibold text-slate-700">{{ $selection->unit->npsn ?? '-' }}</span> • 
                    Masa Bakti: <strong class="text-purple-800 font-bold">Periode {{ $selection->period_years }}</strong> • 
                    Alamat: {{ $selection->unit->address ?? '-' }}
                </p>
            </div>
            <div class="text-xs text-slate-600 bg-slate-50 p-3 rounded-xl border border-slate-100 space-y-1">
                <div><span class="text-slate-400">Jadwal Penjaringan:</span> <strong>{{ $selection->announcement_date->format('d/m/Y') }} - {{ $selection->closing_date->format('d/m/Y') }}</strong></div>
                <div><span class="text-slate-400">Fit & Proper Test:</span> <strong class="text-purple-700">{{ $selection->fit_proper_date ? $selection->fit_proper_date->format('d F Y') : 'Menyusul' }}</strong></div>
            </div>
        </div>
    </div>

    <!-- Tabel Bakal Calon & Hasil Asesmen Fit & Proper Test -->
    <div class="bg-white/95 backdrop-blur-xl rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="p-4 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h2 class="text-base font-bold text-slate-800">Daftar Bakal Calon & Penilaian Fit and Proper Test</h2>
                <p class="text-xs text-slate-500">Asesmen Kompetensi Manajerial (25%), Supervisi (20%), Kewirausahaan (15%), AIK (20%), dan Pemaparan RPS (20%)</p>
            </div>
            @if(!auth()->user()->isKetuaMajelis() && (auth()->user()->isYayasanLevel() || (auth()->user()->isUnitLevel() && auth()->user()->unit_id === $selection->unit_id)))
            <button type="button" onclick="document.getElementById('modalNominateKS').classList.remove('hidden')" class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-xl text-xs font-semibold shadow-xs transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>Usulkan Bakal Calon KS</span>
            </button>
            @elseif(auth()->user()->isKetuaMajelis())
            <div class="flex items-center gap-2 px-3 py-1.5 bg-amber-50 text-amber-800 border border-amber-200 rounded-xl text-xs font-semibold shadow-2xs">
                <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                <span>Hak Akses: Sidang Pleno & Rekomendasi Majelis</span>
            </div>
            @endif
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50/80 text-slate-600 font-semibold border-b border-slate-200/60 uppercase tracking-wider text-[10px]">
                    <tr>
                        <th class="py-3 px-4">Nama Calon & NBM</th>
                        <th class="py-3 px-3">Rekomendasi PCM & Komite</th>
                        <th class="py-3 px-2 text-center" title="Bobot 25%">Manajerial</th>
                        <th class="py-3 px-2 text-center" title="Bobot 20%">Supervisi</th>
                        <th class="py-3 px-2 text-center" title="Bobot 15%">Wirausaha</th>
                        <th class="py-3 px-2 text-center" title="Bobot 20%">AIK</th>
                        <th class="py-3 px-2 text-center" title="Bobot 20%">Pemaparan RPS</th>
                        <th class="py-3 px-3 text-center">Skor Total</th>
                        <th class="py-3 px-3 text-center">Keputusan Pleno</th>
                        <th class="py-3 px-4">Status SK & Pelantikan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($candidates as $cand)
                        <tr class="hover:bg-slate-50/60 transition {{ $cand->is_elected ? 'bg-emerald-50/30' : '' }}">
                            <td class="py-3 px-4">
                                <div class="font-bold text-slate-800 text-sm flex items-center gap-1.5">
                                    <span>{{ $cand->employee ? $cand->employee->full_name : '-' }}</span>
                                    @if($cand->is_elected)
                                        <svg class="w-4 h-4 text-emerald-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                    @endif
                                </div>
                                <div class="text-[11px] text-slate-500 mt-0.5">
                                    NPM: <span class="font-mono font-medium text-amber-700">{{ $cand->employee?->npm ?? '-' }}</span> • 
                                    NBM: <span class="font-mono font-medium">{{ $cand->nbm ?? '-' }}</span>
                                </div>
                            </td>
                            <td class="py-3 px-3 text-slate-600">
                                <div class="text-[11px]"><span class="text-slate-400">PCM:</span> {{ $cand->recommendation_pcm ?: 'Rekomendasi Terlampir' }}</div>
                                <div class="text-[11px]"><span class="text-slate-400">Komite:</span> {{ $cand->recommendation_komite ?: 'Disetujui' }}</div>
                            </td>
                            <td class="py-3 px-2 text-center font-mono font-semibold text-slate-700">
                                {{ $cand->managerial_score ?? '-' }}
                            </td>
                            <td class="py-3 px-2 text-center font-mono font-semibold text-slate-700">
                                {{ $cand->supervision_score ?? '-' }}
                            </td>
                            <td class="py-3 px-2 text-center font-mono font-semibold text-slate-700">
                                {{ $cand->entrepreneurship_score ?? '-' }}
                            </td>
                            <td class="py-3 px-2 text-center font-mono font-semibold text-slate-700">
                                {{ $cand->aik_score ?? '-' }}
                            </td>
                            <td class="py-3 px-2 text-center font-mono font-semibold text-slate-700">
                                {{ $cand->presentation_score ?? '-' }}
                            </td>
                            <td class="py-3 px-3 text-center font-mono font-black text-sm {{ $cand->is_elected ? 'text-emerald-700' : 'text-blue-700' }}">
                                {{ $cand->total_score ?? '-' }}
                            </td>
                            <td class="py-3 px-3 text-center">
                                @if($cand->is_elected)
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase bg-emerald-100 text-emerald-800 border border-emerald-300">
                                        TERPILIH
                                    </span>
                                @elseif($cand->pleno_decision == 'cadangan')
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase bg-amber-100 text-amber-800 border border-amber-300">
                                        Cadangan
                                    </span>
                                @else
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-slate-100 text-slate-600 border border-slate-200">
                                        {{ $cand->pleno_decision ? ucfirst($cand->pleno_decision) : 'Proses' }}
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-xs">
                                @if($cand->sk_number)
                                    <div class="font-mono font-bold text-slate-800">SK: {{ $cand->sk_number }}</div>
                                    <div class="text-[10px] text-slate-500">Tgl SK: {{ $cand->sk_date ? $cand->sk_date->format('d/m/Y') : '-' }}</div>
                                    @if($cand->inauguration_date)
                                        <div class="text-[10px] text-emerald-700 font-semibold mt-0.5">Dilantik: {{ $cand->inauguration_date->format('d F Y') }}</div>
                                    @endif
                                @else
                                    <span class="text-slate-400 italic">Belum terbit SK</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="py-8 text-center text-slate-400">Belum ada bakal calon yang mendaftar pada seleksi ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Usulkan Bakal Calon KS -->
<div id="modalNominateKS" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-sm hidden p-4">
    <div class="bg-white border border-slate-200 rounded-2xl max-w-lg w-full p-6 space-y-4 shadow-xl">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <h3 class="text-base font-bold text-slate-800">
                Usulkan Bakal Calon Kepala Sekolah
            </h3>
            <button type="button" onclick="document.getElementById('modalNominateKS').classList.add('hidden')" class="p-1 text-slate-400 hover:text-slate-600 rounded-lg">
                ✕
            </button>
        </div>

        <form action="{{ route('leadership.headmasters.nominate', $selection->id) }}" method="POST" class="space-y-4">
            @csrf

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                    Pilih Guru Tetap Yayasan (GTY) <span class="text-rose-500">*</span>
                </label>
                <select name="employee_id" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs text-slate-800 focus:bg-white focus:outline-none focus:border-purple-500 font-medium">
                    <option value="">-- Pilih Guru / Calon --</option>
                    @foreach($eligibleEmployees as $emp)
                        <option value="{{ $emp->id }}">
                            {{ $emp->full_name }} (NPM: {{ $emp->npm ?? '-' }} | NBM: {{ $emp->nbm ?? '-' }}) - {{ $emp->position }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                        Rekomendasi PCM Setempat
                    </label>
                    <input type="text" name="recommendation_pcm" placeholder="Nomor surat / nama PCM..." class="w-full px-3.5 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs text-slate-800 focus:bg-white focus:outline-none focus:border-purple-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                        Rekomendasi Komite Sekolah
                    </label>
                    <input type="text" name="recommendation_komite" placeholder="Surat persetujuan Komite..." class="w-full px-3.5 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs text-slate-800 focus:bg-white focus:outline-none focus:border-purple-500">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                    Ringkasan Visi Misi & RPS (Rencana Pengembangan Sekolah)
                </label>
                <textarea name="vision_mission_rps" rows="3" placeholder="Pokok-pokok program unggulan dan komitmen kemajuan AUM..." class="w-full px-3.5 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs text-slate-800 focus:bg-white focus:outline-none focus:border-purple-500"></textarea>
            </div>

            <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100">
                <button type="button" onclick="document.getElementById('modalNominateKS').classList.add('hidden')" class="px-4 py-2 text-xs font-medium text-slate-500 hover:text-slate-800 transition">Batal</button>
                <button type="submit" class="px-5 py-2.5 bg-purple-600 hover:bg-purple-700 text-white rounded-xl text-xs font-semibold shadow-xs transition">
                    Daftarkan Bakal Calon KS
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

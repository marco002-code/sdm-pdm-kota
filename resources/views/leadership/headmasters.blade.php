@extends('layouts.app')

@section('content')
<div class="space-y-4">
    <!-- Header Modul Promosi Kepala Sekolah -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2 py-0.5 rounded-md text-[10px] font-bold uppercase tracking-wider bg-purple-100 text-purple-800">Regenerasi Kepemimpinan AUM</span>
                <span class="text-xs text-slate-400">•</span>
                <span class="text-xs text-slate-500 font-medium">Ketentuan Majelis Dikdasmen No. 99/KTN/I.4/F/2018</span>
            </div>
            <h1 class="text-xl lg:text-2xl font-bold text-slate-800 tracking-tight mt-0.5">Modul Promosi & Seleksi Kepala Sekolah AUM</h1>
            <p class="text-xs text-slate-500 mt-0.5">Monitoring masa jabatan periodik 4 tahun, penjaringan bakal calon, fit and proper test, dan pelantikan Kepala Sekolah PDM Kota Yogyakarta</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('leadership.vice_headmasters') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-800 hover:bg-slate-900 text-white rounded-xl text-xs font-semibold transition">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                <span>Kelola Wakil Kepala Sekolah</span>
            </a>
        </div>
    </div>

    <!-- Metrik Kepemimpinan -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        <div class="bg-white/90 backdrop-blur-xl p-3.5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500">Total Agenda Seleksi</span>
                <div class="text-xl font-black text-slate-800 mt-0.5">{{ $counts['total'] }} <span class="text-xs font-normal text-slate-400">periode</span></div>
            </div>
            <div class="w-8 h-8 rounded-xl bg-purple-100 text-purple-700 flex items-center justify-center font-bold text-xs">
                ALL
            </div>
        </div>

        <div class="bg-white/90 backdrop-blur-xl p-3.5 rounded-2xl border border-blue-200/80 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-blue-700">Tahap Penjaringan</span>
                <div class="text-xl font-black text-blue-700 mt-0.5">{{ $counts['penjaringan'] }} <span class="text-xs font-normal text-blue-500">unit</span></div>
            </div>
            <div class="w-8 h-8 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center font-bold text-xs">
                OPEN
            </div>
        </div>

        <div class="bg-white/90 backdrop-blur-xl p-3.5 rounded-2xl border border-amber-200/80 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-amber-700">Fit & Proper Test</span>
                <div class="text-xl font-black text-amber-700 mt-0.5">{{ $counts['fit_proper'] }} <span class="text-xs font-normal text-amber-500">unit</span></div>
            </div>
            <div class="w-8 h-8 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center font-bold text-xs">
                TEST
            </div>
        </div>

        <div class="bg-white/90 backdrop-blur-xl p-3.5 rounded-2xl border border-emerald-200/80 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-700">Selesai / Dilantik</span>
                <div class="text-xl font-black text-emerald-700 mt-0.5">{{ $counts['selesai'] }} <span class="text-xs font-normal text-emerald-500">sekolah</span></div>
            </div>
            <div class="w-8 h-8 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold text-xs">
                DONE
            </div>
        </div>
    </div>

    <!-- Tabel Seleksi Kepala Sekolah -->
    <div class="bg-white/95 backdrop-blur-xl rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="p-4 border-b border-slate-100 flex flex-col md:flex-row md:items-center md:justify-between gap-3">
            <div>
                <h2 class="text-base font-bold text-slate-800">Daftar Agenda Seleksi & Promosi Kepala Sekolah</h2>
                <p class="text-xs text-slate-500">Masa jabatan 4 tahunan, pemaparan RPS (Rencana Pengembangan Sekolah), dan SK Pelantikan PDM</p>
            </div>

            <!-- Filter Status -->
            <form method="GET" action="{{ route('leadership.headmasters') }}" class="flex items-center gap-2">
                <select name="status" class="px-2.5 py-1.5 text-xs rounded-xl border border-slate-200 focus:outline-none focus:border-blue-500">
                    <option value="">Semua Status</option>
                    <option value="penjaringan" {{ request('status') == 'penjaringan' ? 'selected' : '' }}>Penjaringan Bakal Calon</option>
                    <option value="fit_and_proper" {{ request('status') == 'fit_and_proper' ? 'selected' : '' }}>Fit & Proper Test</option>
                    <option value="sidang_pleno" {{ request('status') == 'sidang_pleno' ? 'selected' : '' }}>Sidang Pleno PDM</option>
                    <option value="selesai" {{ request('status') == 'selesai' ? 'selected' : '' }}>Selesai / Dilantik</option>
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
                        <th class="py-3 px-4">Unit Kerja AUM</th>
                        <th class="py-3 px-3">Masa Jabatan</th>
                        <th class="py-3 px-3">Jadwal Penjaringan</th>
                        <th class="py-3 px-3">Jadwal Fit & Proper Test</th>
                        <th class="py-3 px-2 text-center">Bakal Calon</th>
                        <th class="py-3 px-3 text-center">Status Tahapan</th>
                        <th class="py-3 px-4">Kepala Sekolah Terpilih</th>
                        <th class="py-3 px-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($selections as $sel)
                        <tr class="hover:bg-slate-50/60 transition">
                            <td class="py-3 px-4">
                                <div class="font-bold text-slate-800 text-sm">{{ $sel->unit ? $sel->unit->name : '-' }}</div>
                                <div class="text-[11px] text-slate-500 mt-0.5">NPSN: {{ $sel->unit->npsn ?? '-' }} • Jenjang {{ $sel->unit->level ?? '-' }}</div>
                            </td>
                            <td class="py-3 px-3">
                                <span class="px-2 py-0.5 rounded-md text-[11px] font-bold bg-purple-50 text-purple-700 border border-purple-200">
                                    Periode {{ $sel->period_years }}
                                </span>
                            </td>
                            <td class="py-3 px-3 text-slate-600">
                                <div>{{ $sel->announcement_date->format('d/m/Y') }} s/d</div>
                                <div class="font-semibold text-slate-800">{{ $sel->closing_date->format('d/m/Y') }}</div>
                            </td>
                            <td class="py-3 px-3 font-medium text-slate-700">
                                {{ $sel->fit_proper_date ? $sel->fit_proper_date->format('d F Y') : 'Menyusul' }}
                            </td>
                            <td class="py-3 px-2 text-center font-bold text-slate-800 text-sm">
                                {{ $sel->candidates->count() }} orang
                            </td>
                            <td class="py-3 px-3 text-center">
                                @php
                                    $selBadge = match($sel->status) {
                                        'selesai'        => 'bg-emerald-100 text-emerald-800 border-emerald-300 font-black',
                                        'penjaringan'    => 'bg-blue-100 text-blue-800 border-blue-300',
                                        'fit_and_proper' => 'bg-amber-100 text-amber-800 border-amber-300',
                                        'sidang_pleno'   => 'bg-purple-100 text-purple-800 border-purple-300',
                                        default          => 'bg-slate-100 text-slate-700 border-slate-300',
                                    };
                                @endphp
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold border {{ $selBadge }}">
                                    {{ $sel->status_label }}
                                </span>
                            </td>
                            <td class="py-3 px-4">
                                @if($sel->electedCandidate && $sel->electedCandidate->employee)
                                    <div class="font-bold text-slate-800 text-xs flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        <span>{{ $sel->electedCandidate->employee->full_name }}</span>
                                    </div>
                                    <div class="text-[10px] text-slate-500 font-mono mt-0.5">
                                        SK: {{ $sel->electedCandidate->sk_number ?? '-' }}
                                        @if($sel->electedCandidate->inauguration_date)
                                            • Dilantik: {{ $sel->electedCandidate->inauguration_date->format('d/m/Y') }}
                                        @endif
                                    </div>
                                @else
                                    <span class="text-slate-400 italic">Dalam Proses Penjaringan</span>
                                @endif
                            </td>
                            <td class="py-3 px-3 text-right">
                                <a href="{{ route('leadership.headmaster_show', $sel->id) }}" class="inline-flex items-center gap-1 px-3 py-1 bg-purple-600 hover:bg-purple-700 text-white rounded-lg text-xs font-semibold shadow-xs transition">
                                    <span>Asesmen</span>
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-8 text-center text-slate-400">Belum ada agenda seleksi kepala sekolah aktif.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($selections->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $selections->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

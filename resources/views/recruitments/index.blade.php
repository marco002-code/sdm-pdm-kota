@extends('layouts.app')

@section('content')
<div class="space-y-4">
    <!-- Header Modul Rekrutmen -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2 py-0.5 rounded-md text-[10px] font-bold uppercase tracking-wider bg-blue-100 text-blue-800">Majelis Dikdasmen & PNF</span>
                <span class="text-xs text-slate-400">•</span>
                <span class="text-xs text-slate-500 font-medium">PDM Kota Yogyakarta</span>
            </div>
            <h1 class="text-xl lg:text-2xl font-bold text-slate-800 tracking-tight mt-0.5">Modul Rekrutmen Guru & Pegawai AUM</h1>
            <p class="text-xs text-slate-500 mt-0.5">Manajemen seleksi terpadu calon GTT, PTT, GTY, dan PTY di lingkungan 76 Unit Kerja AUM</p>
        </div>
        <div class="flex items-center gap-2">
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-50 text-emerald-700 border border-emerald-200 text-xs font-semibold rounded-xl">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                <span>{{ $totalActive }} Gelombang Aktif</span>
            </span>
        </div>
    </div>

    <!-- Metrik Rekrutmen -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
        <div class="bg-white/90 backdrop-blur-xl p-4 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Total Pelamar Terdaftar</span>
                <div class="text-2xl font-black text-slate-800 mt-1">{{ $totalApplicants }} <span class="text-xs font-medium text-slate-400">orang</span></div>
                <p class="text-[11px] text-slate-400 mt-0.5">Seluruh formasi guru & tendik</p>
            </div>
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 text-white flex items-center justify-center shadow-md shadow-blue-500/20">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            </div>
        </div>

        <div class="bg-white/90 backdrop-blur-xl p-4 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-[11px] font-bold uppercase tracking-wider text-emerald-700">Dinyatakan Lulus & Diterima</span>
                <div class="text-2xl font-black text-emerald-700 mt-1">{{ $totalPassed }} <span class="text-xs font-medium text-emerald-500">orang</span></div>
                <p class="text-[11px] text-emerald-600/80 mt-0.5">Telah diterbitkan SK Penempatan</p>
            </div>
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 text-white flex items-center justify-center shadow-md shadow-emerald-500/20">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
        </div>

        <div class="bg-white/90 backdrop-blur-xl p-4 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Gelombang Seleksi</span>
                <div class="text-2xl font-black text-slate-800 mt-1">{{ $recruitments->total() }} <span class="text-xs font-medium text-slate-400">periode</span></div>
                <p class="text-[11px] text-slate-400 mt-0.5">Tahun Anggaran 2026</p>
            </div>
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-purple-500 to-indigo-600 text-white flex items-center justify-center shadow-md shadow-purple-500/20">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            </div>
        </div>
    </div>

    <!-- Daftar Gelombang Seleksi Rekrutmen -->
    <div class="bg-white/95 backdrop-blur-xl rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="p-4 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
            <div>
                <h2 class="text-base font-bold text-slate-800">Daftar Gelombang Seleksi Rekrutmen</h2>
                <p class="text-xs text-slate-500">Daftar agenda penerimaan calon pendidik & tenaga kependidikan PDM Kota Yogyakarta</p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50/80 text-slate-600 font-semibold border-b border-slate-200/60 uppercase tracking-wider text-[10px]">
                    <tr>
                        <th class="py-3 px-4">Gelombang & Judul Rekrutmen</th>
                        <th class="py-3 px-4">Formasi Target</th>
                        <th class="py-3 px-4">Jadwal Pendaftaran</th>
                        <th class="py-3 px-4">Jadwal Ujian Penjajagan</th>
                        <th class="py-3 px-4 text-center">Pelamar</th>
                        <th class="py-3 px-4 text-center">Status</th>
                        <th class="py-3 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($recruitments as $rec)
                        <tr class="hover:bg-slate-50/60 transition">
                            <td class="py-3 px-4">
                                <div class="font-bold text-slate-800 text-sm">{{ $rec->title }}</div>
                                <div class="text-[11px] text-slate-500 font-mono mt-0.5">Kode: <span class="font-semibold text-blue-600">{{ $rec->batch_code }}</span> • Tahun {{ $rec->year }}</div>
                            </td>
                            <td class="py-3 px-4">
                                <span class="px-2 py-0.5 rounded-md text-[11px] font-semibold bg-slate-100 text-slate-700">
                                    {{ $rec->target_employee_type }}
                                </span>
                                @if($rec->positions)
                                    <div class="text-[11px] text-slate-400 mt-1">{{ count($rec->positions) }} Bidang Formasi Dibuka</div>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-slate-600">
                                <div class="font-medium">{{ $rec->start_date->format('d M Y') }} s/d</div>
                                <div class="font-semibold text-slate-800">{{ $rec->end_date->format('d M Y') }}</div>
                            </td>
                            <td class="py-3 px-4">
                                @if($rec->test_date)
                                    <span class="inline-flex items-center gap-1 font-semibold text-slate-700">
                                        <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        <span>{{ $rec->test_date->format('d M Y') }}</span>
                                    </span>
                                    <div class="text-[10px] text-slate-400">Tertulis & Ibadah AIK</div>
                                @else
                                    <span class="text-slate-400">-</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-center">
                                <div class="font-bold text-slate-800 text-sm">{{ $rec->applicants_count }}</div>
                                <div class="text-[10px] text-emerald-600 font-semibold">{{ $rec->passed_count }} Lulus</div>
                            </td>
                            <td class="py-3 px-4 text-center">
                                @php
                                    $statusBadge = match($rec->status) {
                                        'buka'             => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                        'seleksi_berkas'   => 'bg-amber-50 text-amber-700 border-amber-200',
                                        'ujian_penjajagan' => 'bg-blue-50 text-blue-700 border-blue-200',
                                        'wawancara'        => 'bg-purple-50 text-purple-700 border-purple-200',
                                        'selesai'          => 'bg-slate-100 text-slate-700 border-slate-200',
                                        default            => 'bg-slate-50 text-slate-600 border-slate-200',
                                    };
                                @endphp
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold border {{ $statusBadge }}">
                                    {{ $rec->status_label }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-right">
                                <a href="{{ route('recruitments.show', $rec->id) }}" class="inline-flex items-center gap-1 px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-xs font-semibold shadow-xs transition">
                                    <span>Kelola Pelamar</span>
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-slate-400">Belum ada gelombang rekrutmen terdaftar.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($recruitments->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $recruitments->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

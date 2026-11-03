@extends('layouts.app')

@section('content')
<div class="space-y-4">
    <!-- Breadcrumb & Navigasi Balik -->
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-2 text-xs text-slate-500">
            <a href="{{ route('recruitments.index') }}" class="hover:text-blue-600 transition flex items-center gap-1">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                <span>Kembali ke Daftar Rekrutmen</span>
            </a>
            <span>/</span>
            <span class="font-semibold text-slate-700 truncate max-w-xs">{{ $recruitment->batch_code }}</span>
        </div>
        <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-blue-50 text-blue-700 border border-blue-200">
            {{ $recruitment->status_label }}
        </span>
    </div>

    <!-- Banner Informasi Gelombang Rekrutmen -->
    <div class="bg-white/95 backdrop-blur-xl p-5 rounded-2xl border border-slate-200/80 shadow-xs">
        <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">
            <div class="space-y-1.5 max-w-3xl">
                <div class="inline-flex items-center gap-2 text-xs font-bold text-blue-600 bg-blue-50 px-2.5 py-0.5 rounded-md">
                    <span>Batch: {{ $recruitment->batch_code }}</span>
                    <span>•</span>
                    <span>Tahun Anggaran {{ $recruitment->year }}</span>
                </div>
                <h1 class="text-xl lg:text-2xl font-black text-slate-800 tracking-tight">{{ $recruitment->title }}</h1>
                <p class="text-xs text-slate-600 leading-relaxed">{{ $recruitment->description }}</p>
                
                <div class="pt-2 flex flex-wrap gap-4 text-xs text-slate-600">
                    <div><span class="text-slate-400">Pendaftaran:</span> <strong class="text-slate-800">{{ $recruitment->start_date->format('d M Y') }} - {{ $recruitment->end_date->format('d M Y') }}</strong></div>
                    <div><span class="text-slate-400">Tes Penjajagan:</span> <strong class="text-blue-700">{{ $recruitment->test_date ? $recruitment->test_date->format('d M Y') : 'Menyusul' }}</strong></div>
                    <div><span class="text-slate-400">Pengumuman:</span> <strong class="text-emerald-700">{{ $recruitment->announcement_date ? $recruitment->announcement_date->format('d M Y') : 'Menyusul' }}</strong></div>
                </div>
            </div>

            <!-- Formasi Terbuka -->
            <div class="bg-slate-50 p-3.5 rounded-xl border border-slate-200/70 min-w-[260px]">
                <div class="text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-2">Formasi Jabatan & Kuota</div>
                <div class="space-y-1.5 max-h-40 overflow-y-auto custom-scrollbar pr-1 text-xs">
                    @if($recruitment->positions)
                        @foreach($recruitment->positions as $pos)
                            <div class="flex items-center justify-between py-1 px-2 rounded-lg bg-white border border-slate-200/60">
                                <span class="font-medium text-slate-700 truncate mr-2">{{ $pos['name'] }}</span>
                                <span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-blue-100 text-blue-800 flex-shrink-0">{{ $pos['quota'] }} Kuota</span>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Filter & Tabel Pelamar Rekrutmen -->
    <div class="bg-white/95 backdrop-blur-xl rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="p-4 border-b border-slate-100 flex flex-col md:flex-row md:items-center md:justify-between gap-3">
            <div>
                <h2 class="text-base font-bold text-slate-800">Daftar Nilai & Hasil Seleksi Pelamar</h2>
                <p class="text-xs text-slate-500">Hasil penilaian seleksi tertulis, praktik ibadah & AIK, wawancara, dan microteaching</p>
            </div>
            
            <!-- Filter Bar -->
            <form method="GET" action="{{ route('recruitments.show', $recruitment->id) }}" class="flex flex-wrap items-center gap-2">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari Nama / No. Reg..." class="px-3 py-1.5 text-xs rounded-xl border border-slate-200 focus:outline-none focus:border-blue-500 w-44">
                <select name="status" class="px-3 py-1.5 text-xs rounded-xl border border-slate-200 focus:outline-none focus:border-blue-500">
                    <option value="">Semua Status</option>
                    <option value="lulus" {{ request('status') == 'lulus' ? 'selected' : '' }}>Lulus</option>
                    <option value="cadangan" {{ request('status') == 'cadangan' ? 'selected' : '' }}>Cadangan</option>
                    <option value="ujian_penjajagan" {{ request('status') == 'ujian_penjajagan' ? 'selected' : '' }}>Ujian Penjajagan</option>
                    <option value="wawancara_microteaching" {{ request('status') == 'wawancara_microteaching' ? 'selected' : '' }}>Wawancara & Micro</option>
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
                        <th class="py-3 px-3">No. Registrasi</th>
                        <th class="py-3 px-3">Nama Lengkap & NBM</th>
                        <th class="py-3 px-3">Formasi & Pilihan Unit</th>
                        <th class="py-3 px-2 text-center" title="Bobot 20%">Tertulis</th>
                        <th class="py-3 px-2 text-center" title="Bobot 25%">AIK</th>
                        <th class="py-3 px-2 text-center" title="Bobot 25%">Ibadah</th>
                        <th class="py-3 px-2 text-center" title="Bobot 15%">Wawancara</th>
                        <th class="py-3 px-2 text-center" title="Bobot 15%">Micro</th>
                        <th class="py-3 px-3 text-center">Skor Akhir</th>
                        <th class="py-3 px-3 text-center">Status Kelulusan</th>
                        <th class="py-3 px-3">SK & Penempatan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($applicants as $app)
                        <tr class="hover:bg-slate-50/60 transition">
                            <td class="py-3 px-3 font-mono font-bold text-slate-700">
                                {{ $app->registration_number }}
                            </td>
                            <td class="py-3 px-3">
                                <div class="font-bold text-slate-800">{{ $app->full_name }}</div>
                                <div class="text-[11px] text-slate-500 flex items-center gap-1 mt-0.5">
                                    <span>NBM: {{ $app->nbm ?? '-' }}</span>
                                    <span>•</span>
                                    <span>{{ $app->highest_education }} {{ $app->major }}</span>
                                </div>
                            </td>
                            <td class="py-3 px-3">
                                <span class="px-2 py-0.5 rounded text-[11px] font-semibold bg-blue-50 text-blue-700 border border-blue-100">
                                    {{ $app->applied_position }}
                                </span>
                                <div class="text-[10px] text-slate-500 mt-1 truncate max-w-[160px]">
                                    Pilihan: {{ $app->preferredUnit ? $app->preferredUnit->name : 'Fleksibel' }}
                                </div>
                            </td>
                            <td class="py-3 px-2 text-center font-mono font-semibold {{ $app->written_test_score >= 80 ? 'text-slate-800' : 'text-slate-500' }}">
                                {{ $app->written_test_score ?? '-' }}
                            </td>
                            <td class="py-3 px-2 text-center font-mono font-semibold {{ $app->aik_test_score >= 85 ? 'text-emerald-700' : 'text-slate-700' }}">
                                {{ $app->aik_test_score ?? '-' }}
                            </td>
                            <td class="py-3 px-2 text-center font-mono font-semibold {{ $app->worship_test_score >= 85 ? 'text-emerald-700' : 'text-slate-700' }}">
                                {{ $app->worship_test_score ?? '-' }}
                            </td>
                            <td class="py-3 px-2 text-center font-mono font-semibold text-slate-700">
                                {{ $app->interview_score ?? '-' }}
                            </td>
                            <td class="py-3 px-2 text-center font-mono font-semibold text-slate-700">
                                {{ $app->microteaching_score > 0 ? $app->microteaching_score : '-' }}
                            </td>
                            <td class="py-3 px-3 text-center font-mono font-black text-sm text-blue-700">
                                {{ $app->final_score ? number_format($app->final_score, 1) : '-' }}
                            </td>
                            <td class="py-3 px-3 text-center">
                                @php
                                    $appBadge = match($app->selection_status) {
                                        'lulus'                  => 'bg-emerald-100 text-emerald-800 border-emerald-300 font-black',
                                        'cadangan'               => 'bg-amber-100 text-amber-800 border-amber-300',
                                        'wawancara_microteaching'=> 'bg-purple-100 text-purple-800 border-purple-300',
                                        'ujian_penjajagan'       => 'bg-blue-100 text-blue-800 border-blue-300',
                                        'gugur'                  => 'bg-rose-100 text-rose-800 border-rose-300',
                                        default                  => 'bg-slate-100 text-slate-700 border-slate-300',
                                    };
                                @endphp
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase border {{ $appBadge }}">
                                    {{ str_replace('_', ' ', $app->selection_status) }}
                                </span>
                            </td>
                            <td class="py-3 px-3">
                                @if($app->placedUnit)
                                    <div class="font-bold text-slate-800 text-xs">{{ $app->placedUnit->name }}</div>
                                    @if($app->sk_number)
                                        <div class="text-[10px] text-blue-600 font-mono mt-0.5">SK: {{ $app->sk_number }}</div>
                                    @endif
                                @else
                                    <span class="text-[11px] text-slate-400 italic">Belum ditempatkan</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="py-8 text-center text-slate-400">Tidak ada data pelamar dengan filter ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($applicants->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $applicants->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

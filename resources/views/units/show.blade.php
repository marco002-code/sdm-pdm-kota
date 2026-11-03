@extends('layouts.app')

@section('content')
<div class="mb-6 flex items-center justify-between">
    <div class="flex items-center gap-3">
        <a href="{{ route('units.index') }}" class="p-2.5 bg-white border border-slate-200 hover:bg-slate-50 text-slate-600 rounded-xl transition shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-slate-800 tracking-tight">{{ $unit->name }}</h1>
            <p class="text-xs text-slate-500 mt-0.5">Kode: {{ $unit->code }} • NPSN: {{ $unit->npsn ?? '-' }}</p>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <!-- Unit Profile Card: Identitas Unit Kerja (AUM) -->
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
        <div class="flex items-center justify-between pb-3 mb-4 border-b border-slate-100">
            <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                <span>Identitas Unit Kerja (AUM)</span>
            </h3>
            @if($unit->npsn && !auth()->user()->isPegawai())
            <form action="{{ route('units.sync_npsn', $unit->id) }}" method="POST">
                @csrf
                <button type="submit" title="Ambil / perbarui data dari referensi Kemdikbud by NPSN" class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-blue-50 hover:bg-blue-100 text-blue-700 text-xs font-semibold rounded-lg border border-blue-200 transition">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    <span>Get Data by NPSN</span>
                </button>
            </form>
            @endif
        </div>

        <div class="space-y-3 text-sm">
            <div class="flex items-center justify-between py-1 border-b border-slate-50">
                <span class="text-xs font-semibold text-slate-500">Jenis AUM</span>
                <span class="inline-block px-2.5 py-0.5 rounded-full text-xs font-bold bg-blue-100 text-blue-800 border border-blue-200">
                    {{ $unit->level }}
                </span>
            </div>

            <div class="flex items-center justify-between py-1 border-b border-slate-50">
                <span class="text-xs font-semibold text-slate-500">NPSN</span>
                <span class="font-mono font-bold text-slate-800 text-xs">{{ $unit->npsn ?? '-' }}</span>
            </div>

            <div class="py-1 border-b border-slate-50">
                <span class="text-xs font-semibold text-slate-500 block mb-0.5">Alamat</span>
                <span class="font-medium text-slate-800 text-xs block leading-relaxed">{{ $unit->address ?? '-' }}</span>
            </div>

            <div class="flex items-center justify-between py-1 border-b border-slate-50">
                <span class="text-xs font-semibold text-slate-500">Kelurahan</span>
                <span class="font-medium text-slate-800 text-xs">{{ $unit->village ?? '-' }}</span>
            </div>

            <div class="flex items-center justify-between py-1 border-b border-slate-50">
                <span class="text-xs font-semibold text-slate-500">Kecamatan</span>
                <span class="font-medium text-slate-800 text-xs">{{ $unit->district ?? '-' }}</span>
            </div>

            <div class="flex items-center justify-between py-1 border-b border-slate-50">
                <span class="text-xs font-semibold text-slate-500">Akreditasi</span>
                @if($unit->accreditation)
                    <span class="inline-block px-2.5 py-0.5 rounded-md font-bold text-xs {{ $unit->accreditation === 'A' || str_contains($unit->accreditation, 'A') ? 'bg-emerald-100 text-emerald-800 border border-emerald-200' : 'bg-amber-100 text-amber-800 border border-amber-200' }}">
                        Akreditasi {{ $unit->accreditation }}
                    </span>
                @else
                    <span class="text-slate-400 text-xs italic">-</span>
                @endif
            </div>

            @if($unit->website)
            <div class="pt-2">
                <span class="text-xs text-slate-400 block mb-1">Website / Media Sosial</span>
                <a href="{{ $unit->website }}" target="_blank" rel="noopener noreferrer" class="text-blue-600 hover:underline break-all inline-flex items-center gap-1 font-semibold text-xs">
                    <span>{{ $unit->website }}</span>
                    <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                </a>
            </div>
            @endif
        </div>
    </div>

    <!-- Employee Roster -->
    <div class="md:col-span-2 bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
        <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider mb-4 pb-2 border-b border-slate-100 flex items-center justify-between">
            <span>Daftar Pegawai ({{ $unit->employees->count() }} orang)</span>
            @if(!auth()->user()->isPegawai())
                <a href="{{ route('employees.create') }}?unit_id={{ $unit->id }}" class="text-xs text-blue-600 hover:underline font-semibold">+ Tambah Pegawai</a>
            @endif
        </h3>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 font-bold uppercase">
                        <th class="py-2.5 px-3">Nama Pegawai</th>
                        <th class="py-2.5 px-3">NBM</th>
                        <th class="py-2.5 px-3">Kategori</th>
                        <th class="py-2.5 px-3">Jabatan</th>
                        <th class="py-2.5 px-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($unit->employees as $emp)
                    <tr class="hover:bg-slate-50">
                        <td class="py-2.5 px-3 font-semibold text-slate-800">
                            {{ $emp->full_name_with_titles }}
                        </td>
                        <td class="py-2.5 px-3 font-mono font-semibold text-slate-700">
                            {{ $emp->nbm ?? '-' }}
                        </td>
                        <td class="py-2.5 px-3">
                            <span class="inline-block px-2 py-0.5 rounded text-[10px] font-bold uppercase 
                                {{ $emp->employee_type === 'GTY' ? 'bg-blue-100 text-blue-800' : 
                                  ($emp->employee_type === 'GTT' ? 'bg-cyan-100 text-cyan-800' : 
                                  ($emp->employee_type === 'PTY' ? 'bg-emerald-100 text-emerald-800' : 
                                  ($emp->employee_type === 'PTT' ? 'bg-amber-100 text-amber-800' : 
                                  ($emp->employee_type === 'PDM' ? 'bg-slate-200 text-slate-800' : 'bg-violet-100 text-violet-800')))) }}">
                                {{ $emp->category_label ?? $emp->employee_type }}
                            </span>
                        </td>
                        <td class="py-2.5 px-3 text-slate-600">
                            {{ $emp->position }}
                        </td>
                        <td class="py-2.5 px-3 text-center">
                            <a href="{{ route('employees.show', $emp->id) }}" class="text-blue-600 hover:underline font-semibold">Biodata</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-6 text-center text-slate-400">
                            Belum ada data pegawai yang terdaftar pada unit ini.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

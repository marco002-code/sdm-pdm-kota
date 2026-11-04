@extends('layouts.app')

@section('title', ($type === 'pkks' ? 'Input Nilai PKKS (Penilaian Kinerja Kepala Sekolah)' : 'Input Nilai DP3 Pegawai') . ' - PDM Kota Yogyakarta')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 bg-white border border-slate-200 p-6 rounded-2xl shadow-xs">
        <div>
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br {{ $type === 'pkks' ? 'from-indigo-600 to-purple-600 shadow-indigo-500/20' : 'from-blue-600 to-indigo-600 shadow-blue-500/20' }} flex items-center justify-center text-white shadow-md flex-shrink-0">
                    @if($type === 'pkks')
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                    @else
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    @endif
                </div>
                <div>
                    <h1 class="text-xl font-bold text-slate-800 tracking-tight">
                        {{ $type === 'pkks' ? 'Input Nilai PKKS (Penilaian Kinerja Kepala Sekolah)' : 'Input Nilai DP3 (Daftar Penilaian Pelaksanaan Pekerjaan)' }}
                    </h1>
                    <p class="text-xs text-slate-500 mt-0.5">
                        {{ $type === 'pkks' ? 'Instrumen Penilaian Kinerja Kepala Sekolah (PKKS) berkala tingkat unit sekolah' : 'Instrumen Penilaian Prestasi Kerja Pegawai (DP3) berkala bagi seluruh guru dan tenaga kependidikan' }}
                    </p>
                </div>
            </div>
        </div>

        @if(!auth()->user()->isKetuaMajelis())
        <button type="button" onclick="document.getElementById('modalInput').classList.remove('hidden')" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs shadow-xs transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            <span>Input Nilai {{ $type === 'pkks' ? 'PKKS Kepala Sekolah' : 'DP3 Pegawai' }}</span>
        </button>
        @else
        <div class="flex items-center gap-2 px-3 py-1.5 bg-amber-50 text-amber-800 border border-amber-200 rounded-xl text-xs font-semibold shadow-2xs">
            <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
            <span>Mode Peninjauan (Read-Only)</span>
        </div>
        @endif
    </div>

    @if(auth()->user()->isUnitLevel() && auth()->user()->unit)
    <!-- Banner Isolasi Domain Unit Kerja untuk TU / Kepsek -->
    <div class="flex items-center justify-between p-3.5 bg-blue-50/90 border border-blue-200/80 rounded-2xl text-xs text-blue-900 shadow-2xs">
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 rounded-xl bg-blue-600 text-white flex items-center justify-center flex-shrink-0 font-bold text-xs shadow-xs">
                TU
            </div>
            <div>
                <div class="font-bold text-slate-800">Unit Kerja: {{ auth()->user()->unit->name }}</div>
                <div class="text-slate-600 text-[11px]">Sistem hanya menampilkan dan mengelola seluruh pegawai di unit kerja Anda (total {{ $employees->count() }} pegawai aktif).</div>
            </div>
        </div>
        <span class="px-2.5 py-1 bg-white border border-blue-200 text-blue-700 font-bold rounded-lg text-[11px] shadow-2xs">
            {{ auth()->user()->unit->code ?? 'UNIT' }}
        </span>
    </div>
    @endif

    <!-- Tab Selector: DP3 Pegawai vs PKKS Kepala Sekolah -->
    <div class="bg-slate-100 p-1.5 rounded-2xl flex max-w-md border border-slate-200">
        <a href="{{ route('career.performance', ['type' => 'dp3']) }}" class="flex-1 py-2 px-4 rounded-xl text-xs font-bold text-center transition {{ $type !== 'pkks' ? 'bg-white text-blue-700 shadow-xs border border-slate-200/80' : 'text-slate-500 hover:text-slate-800' }}">
            Input Nilai DP3 Pegawai
        </a>
        <a href="{{ route('career.performance', ['type' => 'pkks']) }}" class="flex-1 py-2 px-4 rounded-xl text-xs font-bold text-center transition {{ $type === 'pkks' ? 'bg-white text-blue-700 shadow-xs border border-slate-200/80' : 'text-slate-500 hover:text-slate-800' }}">
            Input Nilai PKKS
        </a>
    </div>

    <!-- Filter & Pencarian -->
    <div class="bg-white border border-slate-200 p-4 rounded-2xl shadow-xs">
        <form method="GET" action="{{ route('career.performance') }}" class="flex flex-wrap items-center gap-3">
            <input type="hidden" name="type" value="{{ $type }}">
            <div class="relative flex-1 min-w-[240px]">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama pegawai, NPM..." class="w-full pl-10 pr-4 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs text-slate-800 placeholder-slate-400 focus:bg-white focus:outline-none focus:border-purple-500 focus:ring-1 focus:ring-purple-500 transition">
                <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>

            @if(auth()->user()->isYayasanLevel())
            <select name="unit_id" class="bg-slate-50 border border-slate-300 text-xs text-slate-700 font-medium rounded-xl px-3 py-2 focus:bg-white focus:outline-none focus:border-purple-500">
                <option value="">-- Semua Unit Sekolah --</option>
                @foreach($units as $u)
                    <option value="{{ $u->id }}" {{ request('unit_id') == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
                @endforeach
            </select>
            @endif

            <button type="submit" class="px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white rounded-xl text-xs font-semibold transition">
                Filter
            </button>
            @if(request()->anyFilled(['search', 'unit_id']))
            <a href="{{ route('career.performance', ['type' => $type]) }}" class="px-3 py-2 text-slate-500 hover:text-slate-800 text-xs font-medium transition">Reset</a>
            @endif
        </form>
    </div>

    <!-- Tabel Nilai Evaluasi -->
    <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-xs">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-700">
                <thead class="bg-slate-50/75 text-slate-500 font-bold border-b border-slate-200 uppercase tracking-wider text-[10px]">
                    <tr>
                        <th class="px-5 py-3.5">Pegawai / Kepala Sekolah</th>
                        <th class="px-5 py-3.5">Unit Kerja AUM</th>
                        <th class="px-5 py-3.5">Periode Penilaian</th>
                        <th class="px-5 py-3.5 text-center">Nilai Angka</th>
                        <th class="px-5 py-3.5 text-center">Predikat</th>
                        <th class="px-5 py-3.5">Catatan Evaluator</th>
                        <th class="px-5 py-3.5 text-right">Tanggal Nilai</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($appraisals as $appraisal)
                    <tr class="hover:bg-slate-50/70 transition">
                        <td class="px-5 py-3.5">
                            <a href="{{ route('employees.show', $appraisal->employee_id) }}" class="font-bold text-slate-800 hover:text-purple-600 transition">
                                {{ $appraisal->employee?->full_name }}
                            </a>
                            <div class="font-mono text-[11px] text-amber-700 font-semibold mt-0.5">NPM: {{ $appraisal->employee?->npm ?? '-' }}</div>
                        </td>
                        <td class="px-5 py-3.5 text-slate-600">
                            {{ $appraisal->unit?->name ?? '-' }}
                            <div class="text-[11px] text-slate-400">{{ $appraisal->employee?->position }}</div>
                        </td>
                        <td class="px-5 py-3.5 font-semibold text-slate-800">
                            {{ $appraisal->period }}
                        </td>
                        <td class="px-5 py-3.5 text-center font-black text-sm text-purple-600">
                            {{ number_format((float)$appraisal->final_score, 2) }}
                        </td>
                        <td class="px-5 py-3.5 text-center">
                            @if(in_array($appraisal->grade, ['A', 'Amat Baik']))
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">Amat Baik</span>
                            @elseif(in_array($appraisal->grade, ['B', 'Baik']))
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-200">Baik</span>
                            @else
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">{{ $appraisal->grade }}</span>
                            @endif
                        </td>
                        <td class="px-5 py-3.5 text-slate-500">
                            {{ $appraisal->notes ?? '-' }}
                        </td>
                        <td class="px-5 py-3.5 text-right whitespace-nowrap text-slate-500">
                            {{ $appraisal->appraisal_date ? $appraisal->appraisal_date->format('d M Y') : '-' }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-5 py-10 text-center text-slate-400">
                            Belum ada rekaman data {{ $type === 'pkks' ? 'Penilaian Kinerja Kepala Sekolah (PKKS)' : 'Nilai DP3 Pegawai' }}. Silakan klik tombol Input di kanan atas.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($appraisals->hasPages())
        <div class="p-4 border-t border-slate-200 bg-slate-50/50">
            {{ $appraisals->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Modal Form Input Nilai Kinerja -->
<div id="modalInput" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-sm hidden p-4">
    <div class="bg-white border border-slate-200 rounded-2xl max-w-lg w-full p-6 space-y-4 shadow-xl">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <h3 class="text-base font-bold text-slate-800">
                Form Input {{ $type === 'pkks' ? 'PKKS Kepala Sekolah' : 'Nilai DP3 Pegawai' }}
            </h3>
            <button type="button" onclick="document.getElementById('modalInput').classList.add('hidden')" class="p-1 text-slate-400 hover:text-slate-600 rounded-lg">
                ✕
            </button>
        </div>

        <form action="{{ route('career.performance_store') }}" method="POST" class="space-y-4">
            @csrf
            <input type="hidden" name="appraisal_type" value="{{ $type }}">

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                    {{ $type === 'pkks' ? 'Pilih Kepala Sekolah / Pegawai' : 'Pilih Pegawai' }} <span class="text-rose-500">*</span>
                    @if(auth()->user()->isUnitLevel() && auth()->user()->unit)
                        <span class="text-[11px] text-blue-600 font-normal">({{ auth()->user()->unit->name }})</span>
                    @endif
                </label>
                <select name="employee_id" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs text-slate-800 focus:bg-white focus:outline-none focus:border-blue-500">
                    <option value="">-- {{ $type === 'pkks' ? 'Pilih Kepala Sekolah / Pegawai' : 'Pilih Pegawai' }} (Total: {{ $employees->count() }} Pegawai) --</option>
                    @foreach($employees as $emp)
                        <option value="{{ $emp->id }}">{{ $emp->full_name }} (NPM: {{ $emp->npm ?? '-' }}) - {{ $emp->position }}</option>
                    @endforeach
                </select>
                <p class="text-[11px] text-slate-500 mt-1">
                    {{ auth()->user()->isUnitLevel() ? 'Hanya menampilkan seluruh pegawai aktif di unit kerja Anda.' : 'Pilih pegawai yang dinilai.' }}
                </p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                        Periode / Tahun <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="period" value="{{ date('Y') }}" required placeholder="Contoh: 2026 atau 2025/2026" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs text-slate-800 focus:bg-white focus:outline-none focus:border-purple-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                        Tanggal Penilaian <span class="text-rose-500">*</span>
                    </label>
                    <input type="date" name="appraisal_date" value="{{ date('Y-m-d') }}" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs text-slate-800 focus:bg-white focus:outline-none focus:border-purple-500">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                        Nilai Angka (Skala 0 - 100) <span class="text-rose-500">*</span>
                    </label>
                    <input type="number" step="0.01" min="0" max="100" name="final_score" required placeholder="Contoh: 87.50" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs text-slate-800 focus:bg-white focus:outline-none focus:border-purple-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                        Predikat Kinerja <span class="text-rose-500">*</span>
                    </label>
                    <select name="grade" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs text-slate-800 focus:bg-white focus:outline-none focus:border-purple-500">
                        <option value="A">Amat Baik (A)</option>
                        <option value="B" selected>Baik (B)</option>
                        <option value="C">Cukup (C)</option>
                        <option value="D">Kurang (D)</option>
                        <option value="E">Sangat Kurang (E)</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                    Catatan Evaluasi / Rekomendasi
                </label>
                <textarea name="notes" rows="2" placeholder="Catatan evaluasi kepala sekolah / tim penilai..." class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs text-slate-800 placeholder-slate-400 focus:bg-white focus:outline-none focus:border-purple-500"></textarea>
            </div>

            <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100">
                <button type="button" onclick="document.getElementById('modalInput').classList.add('hidden')" class="px-4 py-2 text-xs font-medium text-slate-500 hover:text-slate-800 transition">Batal</button>
                <button type="submit" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-semibold shadow-xs transition">
                    Simpan Nilai {{ $type === 'pkks' ? 'PKKS' : 'DP3' }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

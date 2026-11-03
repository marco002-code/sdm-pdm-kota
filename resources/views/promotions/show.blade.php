@extends('layouts.app')

@section('content')
<div class="space-y-4">
    <!-- Breadcrumb -->
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-2 text-xs text-slate-500">
            <a href="{{ route('promotions.index') }}" class="hover:text-blue-600 transition flex items-center gap-1 font-medium">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                <span>Kembali ke Daftar Kenaikan Pangkat</span>
            </a>
            <span>/</span>
            <span class="font-semibold text-slate-700">Berkas Usulan Pegawai</span>
        </div>
        <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-blue-50 text-blue-700 border border-blue-200">
            {{ $promotion->status_label }}
        </span>
    </div>

    <!-- Alert Sukses -->
    @if(session('success'))
    <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs flex items-center gap-2.5">
        <svg class="w-4 h-4 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
        <span>{{ session('success') }}</span>
    </div>
    @endif

    <!-- Rincian Berkas Usulan -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-4">
        <!-- Kolom Kiri: Profil Pegawai & Usulan -->
        <div class="lg:col-span-8 space-y-4">
            <div class="bg-white/95 backdrop-blur-xl p-5 sm:p-6 rounded-2xl border border-slate-200/80 shadow-xs">
                <div class="flex items-start justify-between pb-3 border-b border-slate-100">
                    <div>
                        <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-700 bg-emerald-50 border border-emerald-200 px-2.5 py-0.5 rounded-md">
                            {{ $promotion->promotion_type_label }}
                        </span>
                        <h1 class="text-xl font-black text-slate-800 tracking-tight mt-1.5">{{ $promotion->employee->full_name }}</h1>
                        <p class="text-xs text-slate-500 mt-0.5">
                            NPM: <span class="font-semibold text-slate-700">{{ $promotion->employee->npm ?? '-' }}</span> • 
                            NBM: <span class="font-semibold text-slate-700">{{ $promotion->employee->nbm ?? '-' }}</span> • 
                            Unit Kerja: <span class="font-semibold text-slate-700">{{ $promotion->unit->name }}</span>
                        </p>
                    </div>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 py-4 border-b border-slate-100">
                    <div class="bg-slate-50 p-3 rounded-xl border border-slate-100">
                        <span class="text-[10px] font-bold uppercase text-slate-400">Pangkat Saat Ini</span>
                        <div class="text-xs font-bold text-slate-800 mt-1">{{ $promotion->current_rank ?? '-' }}</div>
                        <span class="text-[10px] text-slate-500 uppercase font-semibold">({{ $promotion->current_employee_type }})</span>
                    </div>

                    <div class="bg-blue-50/70 p-3 rounded-xl border border-blue-100">
                        <span class="text-[10px] font-bold uppercase text-blue-600">Usulan Pangkat Baru</span>
                        <div class="text-xs font-black text-blue-900 mt-1">{{ $promotion->proposed_rank }}</div>
                        <span class="text-[10px] text-blue-700 uppercase font-bold">({{ $promotion->proposed_employee_type }})</span>
                    </div>

                    <div class="bg-slate-50 p-3 rounded-xl border border-slate-100">
                        <span class="text-[10px] font-bold uppercase text-slate-400">Masa Kerja</span>
                        <div class="text-xs font-bold text-slate-800 mt-1">{{ $promotion->years_of_service }} Tahun</div>
                        <span class="text-[10px] text-slate-500">Pengabdian AUM</span>
                    </div>

                    <div class="bg-slate-50 p-3 rounded-xl border border-slate-100">
                        <span class="text-[10px] font-bold uppercase text-slate-400">Nilai PKG / SKP</span>
                        <div class="text-xs font-black text-emerald-700 mt-1">{{ $promotion->performance_score ?? '-' }}</div>
                        <span class="text-[10px] text-slate-500">Kategori Baik</span>
                    </div>
                </div>

                <!-- Keaktifan di Persyarikatan & Ortom Muhammadiyah -->
                <div class="pt-4 space-y-2">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-700">Portofolio Keaktifan Persyarikatan & Ortom Muhammadiyah</h3>
                    <div class="p-3 bg-slate-50 rounded-xl border border-slate-200/70 text-xs text-slate-700 leading-relaxed">
                        {{ $promotion->ortom_involvement ?? 'Belum ada catatan keterlibatan Ortom yang diinput.' }}
                    </div>
                </div>

                <!-- Berkas Dokumen Pendukung -->
                @if(!empty($promotion->supporting_documents) && is_array($promotion->supporting_documents))
                <div class="pt-4 border-t border-slate-100 space-y-2">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-700">Berkas Dokumen Pendukung</h3>
                    <div class="flex flex-wrap gap-2">
                        @foreach($promotion->supporting_documents as $doc)
                            <a href="{{ $doc['path'] ?? '#' }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-blue-50 hover:text-blue-700 text-xs text-slate-700 border border-slate-200 transition">
                                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                <span>{{ $doc['name'] ?? 'Lihat Berkas Pendukung' }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Kolom Kanan: Status & Keputusan Majelis -->
        <div class="lg:col-span-4 space-y-4">
            <div class="bg-white/95 backdrop-blur-xl p-5 rounded-2xl border border-slate-200/80 shadow-xs space-y-4">
                <h3 class="text-sm font-bold text-slate-800 border-b border-slate-100 pb-2">Status & Keputusan Majelis</h3>

                <div class="space-y-3">
                    <div>
                        <span class="text-[10px] font-bold uppercase text-slate-400">Tahapan Proses:</span>
                        <div class="mt-1">
                            <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-800 border border-emerald-300">
                                {{ $promotion->status_label }}
                            </span>
                        </div>
                    </div>

                    @if($promotion->sk_number)
                        <div class="pt-2 border-t border-slate-100">
                            <span class="text-[10px] font-bold uppercase text-slate-400">Nomor SK Penetapan PDM:</span>
                            <div class="text-sm font-mono font-bold text-slate-800 mt-0.5">{{ $promotion->sk_number }}</div>
                            <div class="text-xs text-slate-500 mt-0.5">Tanggal SK: {{ $promotion->sk_date ? $promotion->sk_date->format('d F Y') : '-' }}</div>
                            <div class="text-xs text-emerald-600 font-semibold mt-0.5">TMT Efektif: {{ $promotion->effective_date ? $promotion->effective_date->format('d F Y') : '-' }}</div>
                        </div>
                    @endif

                    @if($promotion->reviewer_notes)
                        <div class="pt-2 border-t border-slate-100">
                            <span class="text-[10px] font-bold uppercase text-slate-400">Catatan Pertimbangan:</span>
                            <p class="text-xs text-slate-600 mt-1 italic bg-slate-50 p-2.5 rounded-lg border border-slate-200/60">
                                "{{ $promotion->reviewer_notes }}"
                            </p>
                        </div>
                    @endif
                </div>

                <!-- Form Verifikasi & Keputusan (Admin Yayasan / PDM / Kepsek) -->
                @if(auth()->user()->isYayasanLevel() || auth()->user()->isKepalaSekolah())
                <div class="pt-4 border-t border-slate-100">
                    <h4 class="text-xs font-bold uppercase tracking-wider text-slate-700 mb-2.5">
                        Tindak Lanjut & Verifikasi SK
                    </h4>
                    <form action="{{ route('promotions.verify', $promotion->id) }}" method="POST" class="space-y-3">
                        @csrf
                        <div>
                            <label class="block text-[11px] font-semibold text-slate-600 mb-1">Perbarui Status</label>
                            <select name="status" id="verify_status" onchange="toggleSkInputs(this.value)" class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-300 rounded-xl focus:outline-none focus:border-blue-500 font-medium">
                                <option value="diajukan" {{ $promotion->status === 'diajukan' ? 'selected' : '' }}>Diajukan</option>
                                <option value="diverifikasi_sekolah" {{ $promotion->status === 'diverifikasi_sekolah' ? 'selected' : '' }}>Diverifikasi Unit / Kepala Sekolah</option>
                                <option value="sidang_majelis" {{ $promotion->status === 'sidang_majelis' ? 'selected' : '' }}>Proses Sidang Tim Penilai Majelis</option>
                                <option value="disetujui_pdm" {{ $promotion->status === 'disetujui_pdm' ? 'selected' : '' }}>Disetujui & Terbitkan SK PDM</option>
                                <option value="ditolak" {{ $promotion->status === 'ditolak' ? 'selected' : '' }}>Ditolak / Belum Memenuhi Syarat</option>
                            </select>
                        </div>

                        <div id="sk_container" class="{{ $promotion->status === 'disetujui_pdm' ? '' : 'hidden' }} space-y-2.5 p-3 rounded-xl bg-emerald-50/50 border border-emerald-200">
                            <div>
                                <label class="block text-[11px] font-semibold text-emerald-900 mb-1">Nomor SK PDM</label>
                                <input type="text" name="sk_number" value="{{ old('sk_number', $promotion->sk_number) }}" placeholder="Contoh: 088/KEP/III.0/D/2026" class="w-full px-3 py-1.5 text-xs bg-white border border-emerald-300 rounded-lg">
                            </div>
                            <div>
                                <label class="block text-[11px] font-semibold text-emerald-900 mb-1">Tanggal SK</label>
                                <input type="date" name="sk_date" value="{{ old('sk_date', $promotion->sk_date ? $promotion->sk_date->format('Y-m-d') : date('Y-m-d')) }}" class="w-full px-3 py-1.5 text-xs bg-white border border-emerald-300 rounded-lg">
                            </div>
                            <div>
                                <label class="block text-[11px] font-semibold text-emerald-900 mb-1">TMT Berlaku</label>
                                <input type="date" name="effective_date" value="{{ old('effective_date', $promotion->effective_date ? $promotion->effective_date->format('Y-m-d') : date('Y-m-d')) }}" class="w-full px-3 py-1.5 text-xs bg-white border border-emerald-300 rounded-lg">
                            </div>
                        </div>

                        <div>
                            <label class="block text-[11px] font-semibold text-slate-600 mb-1">Catatan Tim Penilai</label>
                            <textarea name="reviewer_notes" rows="2" placeholder="Catatan hasil verifikasi atau rekomendasi persetujuan..." class="w-full px-3 py-1.5 text-xs bg-slate-50 border border-slate-300 rounded-xl focus:outline-none focus:border-blue-500">{{ old('reviewer_notes', $promotion->reviewer_notes) }}</textarea>
                        </div>

                        <button type="submit" class="w-full py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-semibold shadow-xs transition">
                            Simpan Keputusan Verifikasi
                        </button>
                    </form>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
function toggleSkInputs(status) {
    const container = document.getElementById('sk_container');
    if (status === 'disetujui_pdm') {
        container.classList.remove('hidden');
    } else {
        container.classList.add('hidden');
    }
}
</script>
@endsection

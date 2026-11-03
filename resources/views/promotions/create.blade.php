@extends('layouts.app')

@section('title', 'Usulan Kenaikan Pangkat & Alih Status - PDM Kota Yogyakarta')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <a href="{{ route('promotions.index') }}" class="inline-flex items-center gap-1.5 text-xs text-slate-500 hover:text-slate-800 mb-2 font-medium transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                <span>Kembali ke Daftar Kenaikan Pangkat</span>
            </a>
            <h1 class="text-xl font-bold text-slate-800 tracking-tight">Formulir Pengusulan Kenaikan Pangkat & Alih Status</h1>
            <p class="text-xs text-slate-500 mt-0.5">Pengajuan alih status guru/karyawan (GTT &rarr; GTY, PTT &rarr; PTY), kenaikan pangkat berkala, dan penyesuaian ijazah</p>
        </div>
    </div>

    <!-- Alert Pedoman untuk TU & Sekolah -->
    <div class="p-4 rounded-2xl bg-blue-50 border border-blue-200 text-blue-900 text-xs flex items-start gap-3">
        <svg class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <div class="leading-relaxed">
            <strong class="font-bold">Pedoman Tata Usaha Sekolah:</strong>
            Setiap usulan alih status (misal: <em>GTT diangkat menjadi GTY</em> atau <em>PTT diangkat menjadi PTY</em>) atau kenaikan pangkat berkala akan melalui proses verifikasi administrasi dan sidang pertimbangan oleh Tim Penilai Majelis Dikdasmen & PNF PDM Kota Yogyakarta sebelum diterbitkan SK PDM resmi.
        </div>
    </div>

    <!-- Form Pengajuan -->
    <div class="bg-white border border-slate-200 rounded-2xl p-6 sm:p-8 shadow-xs">
        <form action="{{ route('promotions.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <!-- 1. IDENTITAS PEGAWAI & UNIT -->
            <div>
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500 mb-3 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                    <span>1. Pegawai yang Diusulkan</span>
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2">
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                            Pilih Guru / Karyawan <span class="text-rose-500">*</span>
                        </label>
                        <select name="employee_id" id="employee_id" required onchange="handleEmployeeChange(this)" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs text-slate-800 focus:bg-white focus:outline-none focus:border-blue-500 font-medium">
                            <option value="">-- Pilih Pegawai --</option>
                            @foreach($employees as $emp)
                                <option value="{{ $emp->id }}" 
                                    data-type="{{ $emp->employee_type ?? 'GTT' }}"
                                    data-rank="{{ $emp->inpassing_grade ?? 'Penata Muda / III.a' }}"
                                    data-unit="{{ $emp->unit?->name ?? 'Sekolah' }}"
                                    data-position="{{ $emp->position }}"
                                    data-nbm="{{ $emp->nbm ?? '-' }}"
                                    data-npm="{{ $emp->npm ?? '-' }}"
                                    {{ (old('employee_id') == $emp->id || $selectedEmployeeId == $emp->id) ? 'selected' : '' }}>
                                    {{ $emp->full_name }} (NPM: {{ $emp->npm ?? '-' }} | NBM: {{ $emp->nbm ?? '-' }}) - {{ $emp->position }} [{{ $emp->employee_type ?? 'GTT' }}]
                                </option>
                            @endforeach
                        </select>
                        @error('employee_id')
                            <p class="text-[11px] text-rose-500 mt-1 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Info Box Pegawai Terpilih -->
                    <div id="emp_info_box" class="md:col-span-2 p-3.5 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-700 hidden">
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                            <div>
                                <span class="text-[10px] text-slate-400 block uppercase font-bold">Unit Kerja:</span>
                                <span id="info_unit" class="font-semibold text-slate-800">-</span>
                            </div>
                            <div>
                                <span class="text-[10px] text-slate-400 block uppercase font-bold">Jabatan:</span>
                                <span id="info_position" class="font-semibold text-slate-800">-</span>
                            </div>
                            <div>
                                <span class="text-[10px] text-slate-400 block uppercase font-bold">Status Saat Ini:</span>
                                <span id="info_status" class="inline-block px-2 py-0.5 rounded font-bold text-[10px] bg-slate-200 text-slate-800">-</span>
                            </div>
                            <div>
                                <span class="text-[10px] text-slate-400 block uppercase font-bold">Pangkat / Inpassing:</span>
                                <span id="info_rank" class="font-semibold text-slate-800">-</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2. JENIS USULAN & SASARAN PANGKAT / STATUS -->
            <div class="pt-4 border-t border-slate-100">
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500 mb-3 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-600"></span>
                    <span>2. Rincian Usulan Kenaikan & Alih Status</span>
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                            Jenis Usulan <span class="text-rose-500">*</span>
                        </label>
                        <select name="promotion_type" id="promotion_type" required onchange="handlePromotionTypeChange(this)" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs text-slate-800 focus:bg-white focus:outline-none focus:border-blue-500 font-medium">
                            <option value="alih_status_gty" {{ old('promotion_type') == 'alih_status_gty' ? 'selected' : '' }}>Alih Status Guru (GTT ke GTY / Guru Tetap Yayasan)</option>
                            <option value="alih_status_pty" {{ old('promotion_type') == 'alih_status_pty' ? 'selected' : '' }}>Alih Status Pegawai (PTT ke PTY / Pegawai Tetap Yayasan)</option>
                            <option value="reguler_berkala" {{ old('promotion_type') == 'reguler_berkala' ? 'selected' : '' }}>Kenaikan Pangkat Reguler / Berkala</option>
                            <option value="penyesuaian_ijazah" {{ old('promotion_type') == 'penyesuaian_ijazah' ? 'selected' : '' }}>Penyesuaian Ijazah Baru (S1/S2/S3)</option>
                            <option value="istimewa" {{ old('promotion_type') == 'istimewa' ? 'selected' : '' }}>Kenaikan Pangkat Istimewa / Prestasi Luar Biasa</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                            Masa Kerja (Tahun) <span class="text-rose-500">*</span>
                        </label>
                        <input type="number" name="years_of_service" id="years_of_service" value="{{ old('years_of_service', 3) }}" min="0" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs text-slate-800 focus:bg-white focus:outline-none focus:border-blue-500">
                    </div>

                    <!-- Status Saat Ini vs Diusulkan -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                            Status Kepegawaian Saat Ini <span class="text-rose-500">*</span>
                        </label>
                        <select name="current_employee_type" id="current_employee_type" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs text-slate-800 focus:bg-white focus:outline-none focus:border-blue-500">
                            <option value="GTT" {{ old('current_employee_type') == 'GTT' ? 'selected' : '' }}>GTT (Guru Tidak Tetap)</option>
                            <option value="PTT" {{ old('current_employee_type') == 'PTT' ? 'selected' : '' }}>PTT (Pegawai Tidak Tetap)</option>
                            <option value="GTY" {{ old('current_employee_type') == 'GTY' ? 'selected' : '' }}>GTY (Guru Tetap Yayasan)</option>
                            <option value="PTY" {{ old('current_employee_type') == 'PTY' ? 'selected' : '' }}>PTY (Pegawai Tetap Yayasan)</option>
                            <option value="LAINNYA" {{ old('current_employee_type') == 'LAINNYA' ? 'selected' : '' }}>Kontrak Mandiri / GPKS</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                            Status yang Diusulkan <span class="text-rose-500">*</span>
                        </label>
                        <select name="proposed_employee_type" id="proposed_employee_type" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-emerald-300 rounded-xl text-xs text-slate-800 focus:bg-white focus:outline-none focus:border-emerald-500 font-bold">
                            <option value="GTY" {{ old('proposed_employee_type', 'GTY') == 'GTY' ? 'selected' : '' }}>GTY (Guru Tetap Yayasan)</option>
                            <option value="PTY" {{ old('proposed_employee_type') == 'PTY' ? 'selected' : '' }}>PTY (Pegawai Tetap Yayasan)</option>
                            <option value="GTT" {{ old('proposed_employee_type') == 'GTT' ? 'selected' : '' }}>GTT (Guru Tidak Tetap)</option>
                            <option value="PTT" {{ old('proposed_employee_type') == 'PTT' ? 'selected' : '' }}>PTT (Pegawai Tidak Tetap)</option>
                        </select>
                    </div>

                    <!-- Pangkat / Golongan Saat Ini vs Diusulkan -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                            Pangkat / Golongan Saat Ini
                        </label>
                        <input type="text" name="current_rank" id="current_rank" value="{{ old('current_rank', 'Pengatur Muda / II.a') }}" placeholder="Contoh: Pengatur Muda / II.a" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs text-slate-800 focus:bg-white focus:outline-none focus:border-blue-500">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                            Pangkat / Golongan yang Diusulkan <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="proposed_rank" id="proposed_rank" value="{{ old('proposed_rank', 'Penata Muda / III.a') }}" required placeholder="Contoh: Penata Muda / III.a" class="w-full px-3.5 py-2.5 bg-slate-50 border border-emerald-300 rounded-xl text-xs text-slate-800 focus:bg-white focus:outline-none focus:border-emerald-500 font-bold">
                    </div>
                </div>
            </div>

            <!-- 3. PARAMETER PENILAIAN & PERSYARATAN -->
            <div class="pt-4 border-t border-slate-100">
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500 mb-3 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-amber-600"></span>
                    <span>3. Rekam Kinerja, Portofolio & Keaktifan AIK</span>
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                            Nilai Kinerja Terakhir (DP3 / PKG)
                        </label>
                        <input type="number" step="0.01" min="0" max="100" name="performance_score" value="{{ old('performance_score', '88.50') }}" placeholder="Contoh: 88.50" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs text-slate-800 focus:bg-white focus:outline-none focus:border-blue-500">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                            Angka Kredit (PAK)
                        </label>
                        <input type="number" step="0.01" min="0" name="credit_points" value="{{ old('credit_points') }}" placeholder="Contoh: 105.20" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs text-slate-800 focus:bg-white focus:outline-none focus:border-blue-500">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                            Gelar Baru (Jika Penyesuaian Ijazah)
                        </label>
                        <input type="text" name="new_education_degree" value="{{ old('new_education_degree') }}" placeholder="Contoh: S.Pd. / M.Pd." class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs text-slate-800 focus:bg-white focus:outline-none focus:border-blue-500">
                    </div>

                    <div class="md:col-span-3">
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                            Keaktifan di Persyarikatan & Ortom Muhammadiyah
                        </label>
                        <input type="text" name="ortom_involvement" value="{{ old('ortom_involvement') }}" placeholder="Contoh: Pengurus Cabang Pemuda Muhammadiyah, Nasyiatul Aisyiyah, Pembina Hizbul Wathan / Tapak Suci..." class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs text-slate-800 focus:bg-white focus:outline-none focus:border-blue-500">
                    </div>

                    <div class="md:col-span-3">
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                            Catatan / Dasar Pertimbangan Usulan dari Sekolah
                        </label>
                        <textarea name="reviewer_notes" rows="2" placeholder="Uraian loyalitas, integritas, dan alasan rekomendasi dari Kepala Sekolah / Tata Usaha..." class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs text-slate-800 focus:bg-white focus:outline-none focus:border-blue-500">{{ old('reviewer_notes') }}</textarea>
                    </div>

                    <div class="md:col-span-3">
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                            Upload Berkas Usulan & Dokumen Pendukung (PDF / Scan Terpadu)
                        </label>
                        <input type="file" name="document_file" accept=".pdf,.jpg,.jpeg,.png" class="w-full px-3.5 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs text-slate-700 focus:bg-white file:mr-4 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                        <p class="text-[11px] text-slate-400 mt-1">Lampirkan Surat Pengantar Kepala Sekolah, Ijazah Terakhir, SK Terakhir, dan Sertifikat Baitul Arqam (Maksimal 10 MB).</p>
                    </div>
                </div>
            </div>

            <!-- Tombol Aksi -->
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                <a href="{{ route('promotions.index') }}" class="px-4 py-2.5 text-xs font-medium text-slate-500 hover:text-slate-800 transition">Batal</a>
                <button type="submit" class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-semibold shadow-xs transition flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>Kirim Usulan ke Majelis Dikdasmen PDM</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function handleEmployeeChange(select) {
    const selected = select.options[select.selectedIndex];
    const infoBox = document.getElementById('emp_info_box');

    if (!selected.value) {
        infoBox.classList.add('hidden');
        return;
    }

    const type = selected.getAttribute('data-type') || 'GTT';
    const rank = selected.getAttribute('data-rank') || 'Penata Muda / III.a';
    const unit = selected.getAttribute('data-unit') || '-';
    const pos  = selected.getAttribute('data-position') || '-';

    document.getElementById('info_unit').innerText = unit;
    document.getElementById('info_position').innerText = pos;
    document.getElementById('info_status').innerText = type;
    document.getElementById('info_rank').innerText = rank;

    infoBox.classList.remove('hidden');

    // Sesuaikan status saat ini
    const curTypeSelect = document.getElementById('current_employee_type');
    if (curTypeSelect) {
        curTypeSelect.value = type;
    }

    const curRankInput = document.getElementById('current_rank');
    if (curRankInput && rank) {
        curRankInput.value = rank;
    }

    // Auto set jenis usulan
    const promoType = document.getElementById('promotion_type');
    const propType = document.getElementById('proposed_employee_type');
    if (type === 'GTT') {
        promoType.value = 'alih_status_gty';
        propType.value = 'GTY';
    } else if (type === 'PTT') {
        promoType.value = 'alih_status_pty';
        propType.value = 'PTY';
    } else {
        promoType.value = 'reguler_berkala';
        propType.value = type;
    }
}

function handlePromotionTypeChange(select) {
    const val = select.value;
    const propType = document.getElementById('proposed_employee_type');
    const curType = document.getElementById('current_employee_type');

    if (val === 'alih_status_gty') {
        propType.value = 'GTY';
        curType.value = 'GTT';
    } else if (val === 'alih_status_pty') {
        propType.value = 'PTY';
        curType.value = 'PTT';
    } else if (val === 'reguler_berkala' || val === 'penyesuaian_ijazah' || val === 'istimewa') {
        propType.value = curType.value;
    }
}

// Inisialisasi jika ada selected employee
document.addEventListener('DOMContentLoaded', function() {
    const empSelect = document.getElementById('employee_id');
    if (empSelect && empSelect.value) {
        handleEmployeeChange(empSelect);
    }
});
</script>
@endsection

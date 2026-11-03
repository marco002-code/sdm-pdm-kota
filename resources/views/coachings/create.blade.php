@extends('layouts.app')

@section('title', 'Catat Pembinaan Pegawai - PDM Kota Yogyakarta')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <!-- Header -->
    <div>
        <a href="{{ route('coachings.index') }}" class="inline-flex items-center gap-1.5 text-xs text-slate-500 hover:text-slate-800 mb-2 font-medium transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            <span>Kembali ke Daftar Pembinaan</span>
        </a>
        <h1 class="text-xl font-bold text-slate-800 tracking-tight">Catat Agenda Pembinaan Pegawai</h1>
        <p class="text-xs text-slate-500 mt-0.5">Perekaman kegiatan pembinaan ideologi, loyalitas, pelatihan kompetensi, atau pembinaan disiplin</p>
    </div>

    <!-- Form -->
    <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-xs">
        <form action="{{ route('coachings.store') }}" method="POST" class="space-y-4">
            @csrf

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                    Pilih Pegawai yang Dibina <span class="text-rose-500">*</span>
                </label>
                <select name="employee_id" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs text-slate-800 focus:bg-white focus:outline-none focus:border-teal-500">
                    <option value="">-- Pilih Pegawai --</option>
                    @foreach($employees as $emp)
                        <option value="{{ $emp->id }}" {{ old('employee_id') == $emp->id ? 'selected' : '' }}>
                            {{ $emp->full_name }} (NPM: {{ $emp->npm ?? '-' }}) - {{ $emp->position }}
                        </option>
                    @endforeach
                </select>
                @error('employee_id')
                    <p class="text-[11px] text-rose-500 mt-1 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                        Kategori Pembinaan <span class="text-rose-500">*</span>
                    </label>
                    <select name="category" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs text-slate-800 focus:bg-white focus:outline-none focus:border-teal-500">
                        @foreach($categories as $key => $lbl)
                            <option value="{{ $key }}" {{ old('category') == $key ? 'selected' : '' }}>{{ $lbl }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                        Tanggal Pembinaan <span class="text-rose-500">*</span>
                    </label>
                    <input type="date" name="coaching_date" value="{{ old('coaching_date', date('Y-m-d')) }}" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs text-slate-800 focus:bg-white focus:outline-none focus:border-teal-500">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                    Judul / Materi Pembinaan <span class="text-rose-500">*</span>
                </label>
                <input type="text" name="title" value="{{ old('title') }}" required placeholder="Contoh: Penguatan Ideologi Kemuhammadiyahan / Pelatihan Kurikulum" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs text-slate-800 placeholder-slate-400 focus:bg-white focus:outline-none focus:border-teal-500">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                        Penyelenggara / Mentor
                    </label>
                    <input type="text" name="organizer" value="{{ old('organizer') }}" placeholder="Contoh: Majelis Dikdasmen PDM / PCM" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs text-slate-800 placeholder-slate-400 focus:bg-white focus:outline-none focus:border-teal-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                        Status <span class="text-rose-500">*</span>
                    </label>
                    <select name="status" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs text-slate-800 focus:bg-white focus:outline-none focus:border-teal-500">
                        <option value="terlaksana" selected>Terlaksana</option>
                        <option value="terjadwal">Terjadwal</option>
                        <option value="evaluasi">Perlu Evaluasi Lanjutan</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                    Catatan Pembinaan / Evaluasi
                </label>
                <textarea name="notes" rows="3" placeholder="Uraian pembinaan, komitmen yang disepakati, atau catatan hasil evaluasi..." class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs text-slate-800 placeholder-slate-400 focus:bg-white focus:outline-none focus:border-teal-500">{{ old('notes') }}</textarea>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                    Rencana Tindak Lanjut
                </label>
                <input type="text" name="follow_up" value="{{ old('follow_up') }}" placeholder="Rekomendasi / jadwal pemantauan ulang..." class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs text-slate-800 placeholder-slate-400 focus:bg-white focus:outline-none focus:border-teal-500">
            </div>

            <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100">
                <a href="{{ route('coachings.index') }}" class="px-4 py-2 text-xs font-medium text-slate-500 hover:text-slate-800 transition">Batal</a>
                <button type="submit" class="px-5 py-2.5 bg-teal-600 hover:bg-teal-700 text-white rounded-xl text-xs font-semibold shadow-xs transition">
                    Simpan Catatan Pembinaan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

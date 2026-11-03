@extends('layouts.app')

@section('title', 'Catat Mutasi Pegawai - PDM Kota Yogyakarta')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <!-- Header -->
    <div>
        <a href="{{ route('mutations.index') }}" class="inline-flex items-center gap-1.5 text-xs text-slate-500 hover:text-slate-800 mb-2 font-medium transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            <span>Kembali ke Riwayat Mutasi</span>
        </a>
        <h1 class="text-xl font-bold text-slate-800 tracking-tight">Catat Mutasi Pegawai Antar-Unit AUM</h1>
        <p class="text-xs text-slate-500 mt-0.5">Penetapan perpindahan penugasan guru atau tenaga kependidikan berdasarkan SK PDM</p>
    </div>

    <!-- Form -->
    <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-xs">
        <form action="{{ route('mutations.store') }}" method="POST" class="space-y-5">
            @csrf

            <!-- Pilih Pegawai -->
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                    Pilih Pegawai yang Dimutasi <span class="text-rose-500">*</span>
                </label>
                <select name="employee_id" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs text-slate-800 focus:bg-white focus:outline-none focus:border-blue-500">
                    <option value="">-- Pilih Pegawai --</option>
                    @foreach($employees as $emp)
                        <option value="{{ $emp->id }}" {{ (old('employee_id') == $emp->id || ($selectedEmployee && $selectedEmployee->id == $emp->id)) ? 'selected' : '' }}>
                            {{ $emp->full_name }} (NPM: {{ $emp->npm ?? '-' }}) - {{ $emp->position }}
                        </option>
                    @endforeach
                </select>
                @error('employee_id')
                    <p class="text-[11px] text-rose-500 mt-1 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <!-- Unit Tujuan -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                        Unit Sekolah / Lembaga Tujuan <span class="text-rose-500">*</span>
                    </label>
                    <select name="destination_unit_id" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs text-slate-800 focus:bg-white focus:outline-none focus:border-blue-500">
                        <option value="">-- Pilih Unit Tujuan --</option>
                        @foreach($units as $u)
                            <option value="{{ $u->id }}" {{ old('destination_unit_id') == $u->id ? 'selected' : '' }}>
                                {{ $u->name }} ({{ $u->level }})
                            </option>
                        @endforeach
                    </select>
                    @error('destination_unit_id')
                        <p class="text-[11px] text-rose-500 mt-1 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Posisi Baru -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                        Jabatan / Posisi di Tempat Baru <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="destination_position" value="{{ old('destination_position') }}" required placeholder="Contoh: Guru Matematika / Staf TU" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs text-slate-800 placeholder-slate-400 focus:bg-white focus:outline-none focus:border-blue-500">
                </div>

                <!-- Tanggal Efektif (TMT) -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                        Tanggal Berlaku (TMT Mutasi) <span class="text-rose-500">*</span>
                    </label>
                    <input type="date" name="effective_date" value="{{ old('effective_date', date('Y-m-d')) }}" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs text-slate-800 focus:bg-white focus:outline-none focus:border-blue-500">
                </div>

                <!-- Nomor SK -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                        Nomor SK Mutasi PDM
                    </label>
                    <input type="text" name="sk_number" value="{{ old('sk_number') }}" placeholder="Contoh: 045/KEP/III.0/D/2026" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs text-slate-800 placeholder-slate-400 focus:bg-white focus:outline-none focus:border-blue-500">
                </div>

                <!-- Tanggal SK -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                        Tanggal Penetapan SK
                    </label>
                    <input type="date" name="sk_date" value="{{ old('sk_date') }}" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs text-slate-800 focus:bg-white focus:outline-none focus:border-blue-500">
                </div>

                <!-- Alasan Mutasi -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                        Alasan / Dasar Pertimbangan Mutasi
                    </label>
                    <input type="text" name="reason" value="{{ old('reason') }}" placeholder="Pemerataan formasi / rotasi / kebutuhan AUM..." class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs text-slate-800 placeholder-slate-400 focus:bg-white focus:outline-none focus:border-blue-500">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                    Catatan Tambahan
                </label>
                <textarea name="notes" rows="2" placeholder="Catatan khusus dari Majelis Dikdasmen PDM..." class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs text-slate-800 placeholder-slate-400 focus:bg-white focus:outline-none focus:border-blue-500">{{ old('notes') }}</textarea>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                <a href="{{ route('mutations.index') }}" class="px-4 py-2 text-xs font-medium text-slate-500 hover:text-slate-800 transition">Batal</a>
                <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-semibold shadow-xs transition">
                    Simpan & Terapkan Mutasi
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

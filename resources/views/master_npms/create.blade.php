@extends('layouts.app')

@section('title', 'Tambah / Generate Master NPM - PDM Kota Yogyakarta')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <a href="{{ route('master-npms.index') }}" class="inline-flex items-center gap-1.5 text-xs text-slate-500 hover:text-slate-800 mb-2 font-medium transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                <span>Kembali ke Bank Data NPM</span>
            </a>
            <h1 class="text-xl font-bold text-slate-800 tracking-tight">Entri & Penambahan Bank Data NPM</h1>
            <p class="text-xs text-slate-500 mt-0.5">Daftarkan nomor resmi dari Buku Induk PDM sejak nomor awal hingga nomor terkini untuk validasi sistem</p>
        </div>
    </div>

    <!-- Toggle Mode Selector -->
    <div class="bg-slate-100 p-1.5 rounded-2xl flex max-w-md border border-slate-200">
        <button type="button" id="tabSingle" onclick="switchMode('single')" class="flex-1 py-2 px-4 rounded-xl text-xs font-bold transition bg-white text-slate-800 shadow-xs border border-slate-200/80">
            Entri Satuan (Single)
        </button>
        <button type="button" id="tabBatch" onclick="switchMode('batch')" class="flex-1 py-2 px-4 rounded-xl text-xs font-bold transition text-slate-500 hover:text-slate-800">
            Generate Range Massal (Batch)
        </button>
    </div>

    <!-- Mode 1: Entri Satuan -->
    <div id="formSingle" class="bg-white border border-slate-200 rounded-2xl p-6 shadow-xs">
        <form action="{{ route('master-npms.store') }}" method="POST" class="space-y-5">
            @csrf
            <input type="hidden" name="mode" value="single">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                        Nomor Pegawai Muhammadiyah (NPM) <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="npm" value="{{ old('npm') }}" required placeholder="Contoh: 0001 atau 12345" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs text-slate-800 placeholder-slate-400 focus:bg-white focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 @error('npm') border-rose-500 @enderror">
                    @error('npm')
                        <p class="text-[11px] text-rose-500 mt-1 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                        Nama Terdaftar di Buku Induk PDM
                    </label>
                    <input type="text" name="registered_name" value="{{ old('registered_name') }}" placeholder="Nama guru/pegawai sesuai arsip buku induk..." class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs text-slate-800 placeholder-slate-400 focus:bg-white focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                        NBM Terkait (Jika Ada)
                    </label>
                    <input type="text" name="nbm" value="{{ old('nbm') }}" placeholder="Nomor Baku Muhammadiyah..." class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs text-slate-800 placeholder-slate-400 focus:bg-white focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                        Alokasi Unit / Sekolah AUM
                    </label>
                    <select name="allocated_unit_id" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs text-slate-800 focus:bg-white focus:outline-none focus:border-blue-500">
                        <option value="">-- Tidak Dibatasi / Umum --</option>
                        @foreach($units as $u)
                            <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->level }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                        Catatan / Riwayat SK
                    </label>
                    <textarea name="notes" rows="2" placeholder="Catatan arsip manual atau referensi SK..." class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs text-slate-800 placeholder-slate-400 focus:bg-white focus:outline-none focus:border-blue-500">{{ old('notes') }}</textarea>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                <a href="{{ route('master-npms.index') }}" class="px-4 py-2 text-xs font-medium text-slate-500 hover:text-slate-800 transition">Batal</a>
                <button type="submit" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-semibold shadow-xs transition">
                    Simpan ke Bank Data
                </button>
            </div>
        </form>
    </div>

    <!-- Mode 2: Generate Range Massal -->
    <div id="formBatch" class="bg-white border border-slate-200 rounded-2xl p-6 shadow-xs hidden">
        <div class="mb-5 p-4 rounded-xl bg-amber-50 border border-amber-200 text-xs text-amber-800 flex items-start gap-3">
            <svg class="w-5 h-5 flex-shrink-0 text-amber-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <div class="leading-relaxed">
                <span class="font-bold">Fasilitas Generate Rentang Nomor NPM:</span>
                Gunakan fitur ini untuk membuat kuota nomor urut sekaligus dari nomor awal sampai nomor terkini (misal nomor 1 s.d. 500). Sistem akan secara otomatis mengabaikan nomor yang sudah ada agar tidak duplikat.
            </div>
        </div>

        <form action="{{ route('master-npms.store') }}" method="POST" class="space-y-5">
            @csrf
            <input type="hidden" name="mode" value="batch">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                        Nomor Awal (Start Number) <span class="text-rose-500">*</span>
                    </label>
                    <input type="number" name="start_number" value="{{ old('start_number', 1) }}" min="1" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs text-slate-800 focus:bg-white focus:outline-none focus:border-blue-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                        Nomor Terkini / Akhir (End Number) <span class="text-rose-500">*</span>
                    </label>
                    <input type="number" name="end_number" value="{{ old('end_number', 100) }}" min="1" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs text-slate-800 focus:bg-white focus:outline-none focus:border-blue-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                        Digit Format Nol di Depan (Padding Digit)
                    </label>
                    <select name="pad_length" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs text-slate-800 focus:bg-white focus:outline-none focus:border-blue-500">
                        <option value="1">Tanpa padding nol (contoh: 1, 2, ..., 100)</option>
                        <option value="4">4 digit (contoh: 0001, 0002, ...)</option>
                        <option value="5">5 digit (contoh: 00001, 00002, ...)</option>
                        <option value="6" selected>6 digit (contoh: 000001, 000002, ...)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                        Prefix Awalan (Opsional)
                    </label>
                    <input type="text" name="prefix" value="{{ old('prefix') }}" placeholder="Contoh: NPM- (bisa dikosongkan)" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs text-slate-800 placeholder-slate-400 focus:bg-white focus:outline-none focus:border-blue-500">
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                <a href="{{ route('master-npms.index') }}" class="px-4 py-2 text-xs font-medium text-slate-500 hover:text-slate-800 transition">Batal</a>
                <button type="submit" class="px-5 py-2.5 bg-amber-600 hover:bg-amber-700 text-white rounded-xl text-xs font-semibold shadow-xs transition">
                    Generate Batch Range NPM
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function switchMode(mode) {
    const formSingle = document.getElementById('formSingle');
    const formBatch = document.getElementById('formBatch');
    const tabSingle = document.getElementById('tabSingle');
    const tabBatch = document.getElementById('tabBatch');

    if (mode === 'single') {
        formSingle.classList.remove('hidden');
        formBatch.classList.add('hidden');
        tabSingle.className = 'flex-1 py-2 px-4 rounded-xl text-xs font-bold transition bg-white text-slate-800 shadow-xs border border-slate-200/80';
        tabBatch.className = 'flex-1 py-2 px-4 rounded-xl text-xs font-bold transition text-slate-500 hover:text-slate-800';
    } else {
        formSingle.classList.add('hidden');
        formBatch.classList.remove('hidden');
        tabSingle.className = 'flex-1 py-2 px-4 rounded-xl text-xs font-bold transition text-slate-500 hover:text-slate-800';
        tabBatch.className = 'flex-1 py-2 px-4 rounded-xl text-xs font-bold transition bg-white text-slate-800 shadow-xs border border-slate-200/80';
    }
}
</script>
@endsection

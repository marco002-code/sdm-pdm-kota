@extends('layouts.app')

@section('title', 'Pembaruan Data Berkala - PDM Kota Yogyakarta')

@section('content')
<div class="space-y-6">

    <!-- Header Bersih & Ramah Pengguna -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 bg-white border border-slate-200 p-6 rounded-2xl shadow-xs">
        <div class="flex items-center gap-3.5">
            <div class="w-11 h-11 rounded-2xl bg-gradient-to-tr from-blue-600 to-indigo-600 flex items-center justify-center text-white shadow-sm flex-shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
            </div>
            <div>
                <h1 class="text-xl sm:text-2xl font-bold text-slate-800 tracking-tight">Pembaruan Data Berkala</h1>
                <p class="text-xs text-slate-500 mt-0.5">Monitoring dan konfirmasi pemutakhiran data kepegawaian setiap 3 bulan (Triwulanan) oleh AUM se-Kota Yogyakarta</p>
            </div>
        </div>

        <!-- Filter Periode Triwulan -->
        <form method="GET" action="{{ route('audits.quarterly') }}" class="flex flex-wrap items-center gap-2">
            <select name="quarter" class="bg-slate-50 border border-slate-300 text-xs text-slate-800 font-semibold rounded-xl px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                <option value="1" {{ $quarter == 1 ? 'selected' : '' }}>Triwulan I (Jan - Mar)</option>
                <option value="2" {{ $quarter == 2 ? 'selected' : '' }}>Triwulan II (Apr - Jun)</option>
                <option value="3" {{ $quarter == 3 ? 'selected' : '' }}>Triwulan III (Jul - Sep)</option>
                <option value="4" {{ $quarter == 4 ? 'selected' : '' }}>Triwulan IV (Okt - Des)</option>
            </select>

            <select name="year" class="bg-slate-50 border border-slate-300 text-xs text-slate-800 font-semibold rounded-xl px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                @for($y = date('Y'); $y >= date('Y') - 2; $y--)
                    <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>Tahun {{ $y }}</option>
                @endfor
            </select>

            <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold transition shadow-xs">
                Pilih Periode
            </button>
        </form>
    </div>

    <!-- Banner Panduan Sekolah / TU -->
    @if(auth()->user()->isUnitLevel())
    <div class="p-4 rounded-2xl bg-blue-50 border border-blue-200 text-blue-900 text-xs flex items-start gap-3">
        <svg class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <div class="leading-relaxed">
            <strong class="font-bold">Panduan Pembaruan untuk Satuan Pendidikan:</strong>
            Pastikan seluruh data guru dan tenaga kependidikan di unit sekolah Anda telah mutakhir pada menu <a href="{{ route('employees.index') }}" class="font-bold underline text-blue-700">Data Induk Pegawai</a>. Setelah selesai, klik tombol <strong>"Konfirmasi Selesai Pembaruan"</strong> pada baris unit Anda di bawah ini untuk melaporkan status pembaruan Triwulan {{ $quarter }} ke PDM.
        </div>
    </div>
    @endif

    <!-- 4 Kartu Metrik Ringkas & Jelas -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="bg-white border border-slate-200 p-4 rounded-2xl shadow-xs">
            <div class="text-xs text-slate-500 font-medium">Total Satuan Pendidikan</div>
            <div class="text-2xl font-black text-slate-800 mt-1">{{ $totalUnits }}</div>
            <div class="text-[11px] text-slate-400 mt-0.5">TPA, TK, SD, SMP, SMA, SMK</div>
        </div>

        <div class="bg-white border border-slate-200 p-4 rounded-2xl shadow-xs">
            <div class="text-xs text-emerald-600 font-medium">Sudah Pembaruan Data</div>
            <div class="text-2xl font-black text-emerald-600 mt-1">{{ $updatedUnits }}</div>
            <div class="text-[11px] text-slate-500 mt-0.5">
                {{ $totalUnits > 0 ? round(($updatedUnits / $totalUnits) * 100) : 0 }}% dari total unit
            </div>
        </div>

        <div class="bg-white border border-slate-200 p-4 rounded-2xl shadow-xs">
            <div class="text-xs text-amber-600 font-medium">Belum Pembaruan</div>
            <div class="text-2xl font-black text-amber-600 mt-1">{{ $pendingUnits }}</div>
            <div class="text-[11px] text-slate-400 mt-0.5">Menunggu konfirmasi TU</div>
        </div>

        <div class="bg-white border border-slate-200 p-4 rounded-2xl shadow-xs">
            <div class="text-xs text-blue-600 font-medium">Terverifikasi PDM</div>
            <div class="text-2xl font-black text-blue-600 mt-1">{{ $verifiedUnits }}</div>
            <div class="text-[11px] text-slate-400 mt-0.5">Disahkan Majelis Dikdasmen</div>
        </div>
    </div>

    <!-- Tabel Monitoring Sekolah -->
    <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-xs">
        <div class="p-4 sm:p-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h2 class="text-sm font-bold text-slate-800">
                    Daftar Pembaruan Data Unit AUM — Triwulan {{ $quarter }} Tahun {{ $year }}
                </h2>
                <p class="text-xs text-slate-500 mt-0.5">Status pelaporan berkala dari masing-masing satuan pendidikan</p>
            </div>

            <!-- Instant Search Input -->
            <div class="w-full sm:w-64">
                <input type="text" id="auditSearchInput" onkeyup="filterAuditTable()" placeholder="Cari sekolah atau jenjang..."
                    class="w-full px-3 py-1.5 text-xs bg-slate-50 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:outline-none">
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-700" id="auditTable">
                <thead class="bg-slate-50 text-slate-500 font-bold border-b border-slate-200 uppercase tracking-wider text-[11px]">
                    <tr>
                        <th class="px-5 py-3.5">Nama Satuan Pendidikan</th>
                        <th class="px-5 py-3.5">Jenjang</th>
                        <th class="px-5 py-3.5 text-center">Pegawai Aktif</th>
                        <th class="px-5 py-3.5 text-center">Status Pembaruan</th>
                        <th class="px-5 py-3.5">Waktu Pembaruan</th>
                        <th class="px-5 py-3.5">Petugas TU</th>
                        <th class="px-5 py-3.5 text-right">Tindakan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($units as $u)
                        @php
                            $audit = $audits->get($u->id);
                            $isUpdated = $audit && in_array($audit->status, ['sudah_update', 'terverifikasi']);
                            $isVerified = $audit && $audit->status === 'terverifikasi';
                            $isMyUnit = auth()->user()->unit_id === $u->id;
                        @endphp
                    <tr class="audit-row hover:bg-slate-50/70 transition {{ $isMyUnit ? 'bg-blue-50/30' : '' }}"
                        data-name="{{ strtolower($u->name) }}"
                        data-level="{{ strtolower($u->level) }}">
                        <td class="px-5 py-3.5">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('units.show', $u->id) }}" class="font-bold text-slate-900 hover:text-blue-600 transition">
                                    {{ $u->name }}
                                </a>
                                @if($isMyUnit)
                                    <span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-blue-100 text-blue-800">Unit Anda</span>
                                @endif
                            </div>
                            <div class="text-[11px] text-slate-400 font-mono">NPSN: {{ $u->npsn ?? '-' }}</div>
                        </td>
                        <td class="px-5 py-3.5">
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-700 border border-slate-200">
                                {{ $u->level }}
                            </span>
                        </td>
                        <td class="px-5 py-3.5 text-center font-bold text-slate-800">
                            {{ $u->employees()->where('status', 'aktif')->count() }} orang
                        </td>
                        <td class="px-5 py-3.5 text-center">
                            @if($isVerified)
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-blue-600"></span>
                                    Terverifikasi PDM
                                </span>
                            @elseif($isUpdated)
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                    Sudah Diperbarui
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                    Belum Diperbarui
                                </span>
                            @endif
                        </td>
                        <td class="px-5 py-3.5 whitespace-nowrap text-slate-500 text-[11px]">
                            {{ $audit && $audit->last_updated_at ? $audit->last_updated_at->format('d M Y, H:i') : '-' }}
                        </td>
                        <td class="px-5 py-3.5 text-slate-600 text-[11px]">
                            {{ $audit?->updater?->name ?? '-' }}
                        </td>
                        <td class="px-5 py-3.5 text-right">
                            <form action="{{ route('audits.update_status', $u->id) }}" method="POST" class="inline-flex items-center gap-1.5">
                                @csrf
                                <input type="hidden" name="year" value="{{ $year }}">
                                <input type="hidden" name="quarter" value="{{ $quarter }}">

                                @if(auth()->user()->isYayasanLevel())
                                    @if(!$isVerified)
                                        <input type="hidden" name="status" value="terverifikasi">
                                        <button type="submit" class="px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-xs font-semibold transition shadow-xs">
                                            Verifikasi
                                        </button>
                                    @else
                                        <span class="text-xs text-emerald-600 font-bold inline-flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                            Selesai
                                        </span>
                                    @endif
                                @elseif($isMyUnit)
                                    <input type="hidden" name="status" value="sudah_update">
                                    <button type="submit" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-bold transition shadow-xs flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        <span>Konfirmasi Selesai</span>
                                    </button>
                                @else
                                    <span class="text-[11px] text-slate-400 italic">-</span>
                                @endif
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-5 py-10 text-center text-slate-400">
                            Tidak ada data satuan pendidikan yang terdaftar.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    function filterAuditTable() {
        const query = document.getElementById('auditSearchInput').value.toLowerCase().trim();
        const rows = document.querySelectorAll('.audit-row');

        rows.forEach(row => {
            const name = row.getAttribute('data-name') || '';
            const level = row.getAttribute('data-level') || '';
            if (!query || name.includes(query) || level.includes(query)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }
</script>
@endsection

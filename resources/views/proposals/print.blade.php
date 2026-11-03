<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lembar Rekapitulasi Usulan Kepegawaian & Kepemimpinan - PDM Kota Yogyakarta</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 15mm 12mm 15mm 12mm;
        }
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11px;
            color: #1e293b;
            background: #fff;
            margin: 0;
            padding: 10px;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #0f172a;
            padding-bottom: 8px;
            margin-bottom: 15px;
        }
        .header h1 {
            margin: 0;
            font-size: 15px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .header h2 {
            margin: 2px 0;
            font-size: 13px;
            font-weight: 700;
        }
        .header p {
            margin: 0;
            font-size: 10px;
            color: #475569;
        }
        .section-title {
            font-size: 12px;
            font-weight: 700;
            margin-top: 15px;
            margin-bottom: 6px;
            padding-bottom: 3px;
            border-bottom: 1px solid #cbd5e1;
            color: #1e3a8a;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            font-size: 10px;
        }
        th, td {
            border: 1px solid #cbd5e1;
            padding: 5px 6px;
            text-align: left;
        }
        th {
            background-color: #f1f5f9;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 9px;
            color: #334155;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-mono { font-family: monospace; }
        .badge {
            display: inline-block;
            padding: 1px 4px;
            border-radius: 3px;
            font-size: 9px;
            font-weight: bold;
        }
        .footer {
            margin-top: 25px;
            display: flex;
            justify-content: space-between;
            font-size: 11px;
        }
        .signature-box {
            width: 200px;
            text-align: center;
        }
        @media print {
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="no-print" style="margin-bottom: 15px; background: #e0f2fe; padding: 10px 15px; border-radius: 8px; display: flex; justify-content: space-between; align-items: center;">
        <span style="font-weight: bold; color: #0369a1;">Lembar Rekapitulasi Usulan Resmi Majelis Dikdasmen & PNF PDM Kota Yogyakarta</span>
        <button onclick="window.print()" style="background: #0284c7; color: white; border: none; padding: 6px 14px; border-radius: 6px; font-weight: bold; cursor: pointer;">
            🖨️ Cetak Dokumen (Print)
        </button>
    </div>

    <div class="header">
        <h1>Pimpinan Daerah Muhammadiyah Kota Yogyakarta</h1>
        <h2>Majelis Pendidikan Dasar Menengah dan Pendidikan Nonformal</h2>
        <p>Jl. Sultan Agung No. 14, Gunungketur, Pakualaman, Kota Yogyakarta, D.I. Yogyakarta</p>
        <p style="margin-top: 4px; font-weight: bold; color: #0f172a;">
            DAFTAR REKAPITULASI USULAN KENAIKAN PANGKAT, ALIH STATUS, DAN PROMOSI KEPEMIMPINAN AUM
            @if($selectedUnit) - {{ $selectedUnit->name }} @endif
        </p>
    </div>

    <!-- 1. Kenaikan Pangkat & Alih Status -->
    <div class="section-title">I. USULAN KENAIKAN PANGKAT & ALIH STATUS KEPEGAWAIAN (GTT &rarr; GTY, PTT &rarr; PTY)</div>
    <table>
        <thead>
            <tr>
                <th style="width: 25px;" class="text-center">No</th>
                <th>Nama Pegawai</th>
                <th>NPM / NBM</th>
                <th>Satuan Pendidikan</th>
                <th>Jenis Usulan</th>
                <th class="text-center">Status Awal &rarr; Usulan</th>
                <th class="text-center">Masa Kerja</th>
                <th class="text-center">Nilai PKG</th>
                <th class="text-center">Status Sidang</th>
                <th>No. & Tgl SK PDM</th>
            </tr>
        </thead>
        <tbody>
            @forelse($rankPromotions as $i => $r)
            <tr>
                <td class="text-center">{{ $i + 1 }}</td>
                <td><strong>{{ $r->employee?->full_name }}</strong></td>
                <td class="font-mono">{{ $r->employee?->npm ?? '-' }} / {{ $r->employee?->nbm ?? '-' }}</td>
                <td>{{ $r->unit?->name }}</td>
                <td>{{ $r->promotion_type_label }}</td>
                <td class="text-center">{{ $r->current_employee_type }} &rarr; <strong>{{ $r->proposed_employee_type }}</strong></td>
                <td class="text-center">{{ $r->years_of_service }} Th</td>
                <td class="text-center">{{ $r->performance_score ?? '-' }}</td>
                <td class="text-center">{{ $r->status_label }}</td>
                <td class="font-mono">{{ $r->sk_number ? $r->sk_number . ' (' . ($r->sk_date ? $r->sk_date->format('d/m/y') : '') . ')' : '-' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="10" class="text-center" style="padding: 15px; color: #64748b;">Tidak ada data usulan kenaikan pangkat atau alih status.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <!-- 2. Calon Kepala Sekolah -->
    <div class="section-title">II. USULAN BAKAL CALON KEPALA SEKOLAH</div>
    <table>
        <thead>
            <tr>
                <th style="width: 25px;" class="text-center">No</th>
                <th>Nama Bakal Calon</th>
                <th>NPM / NBM</th>
                <th>Sekolah AUM</th>
                <th>Periode Jabatan</th>
                <th>Rekomendasi PCM & Komite</th>
                <th class="text-center">Skor Fit & Proper</th>
                <th class="text-center">Keputusan Pleno</th>
                <th>SK Penetapan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($ksCandidates as $i => $c)
            <tr>
                <td class="text-center">{{ $i + 1 }}</td>
                <td><strong>{{ $c->employee?->full_name }}</strong></td>
                <td class="font-mono">{{ $c->employee?->npm ?? '-' }} / {{ $c->nbm ?? '-' }}</td>
                <td>{{ $c->selection?->unit?->name }}</td>
                <td>{{ $c->selection?->period_years }}</td>
                <td>PCM: {{ $c->recommendation_pcm ?: '-' }}, Komite: {{ $c->recommendation_komite ?: '-' }}</td>
                <td class="text-center font-mono">{{ $c->total_score ?? '-' }}</td>
                <td class="text-center"><strong>{{ $c->is_elected ? 'TERPILIH' : ($c->pleno_decision ?: 'Proses') }}</strong></td>
                <td>{{ $c->sk_number ? $c->sk_number : '-' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="9" class="text-center" style="padding: 15px; color: #64748b;">Tidak ada data usulan calon kepala sekolah.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <!-- 3. Tim Manajemen / Wakil Kepala Sekolah -->
    <div class="section-title">III. USULAN PROMOSI TIM MANAJEMEN (WAKIL KEPALA SEKOLAH)</div>
    <table>
        <thead>
            <tr>
                <th style="width: 25px;" class="text-center">No</th>
                <th>Nama Calon Waka</th>
                <th>NPM / NBM</th>
                <th>Sekolah AUM</th>
                <th>Bidang Waka</th>
                <th>Periode Jabatan</th>
                <th>Rekomendasi Kepala Sekolah</th>
                <th class="text-center">Status</th>
                <th>SK Pengangkatan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($viceNominations as $i => $v)
            <tr>
                <td class="text-center">{{ $i + 1 }}</td>
                <td><strong>{{ $v->employee?->full_name }}</strong></td>
                <td class="font-mono">{{ $v->employee?->npm ?? '-' }} / {{ $v->employee?->nbm ?? '-' }}</td>
                <td>{{ $v->unit?->name }}</td>
                <td><strong>{{ $v->field_label }}</strong></td>
                <td>{{ $v->period_years }}</td>
                <td>{{ $v->recommendation_headmaster ?: '-' }}</td>
                <td class="text-center">{{ $v->status_label }}</td>
                <td>{{ $v->sk_number ?: '-' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="9" class="text-center" style="padding: 15px; color: #64748b;">Tidak ada data usulan wakil kepala sekolah.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer" style="margin-top: 30px; display: table; width: 100%;">
        <div style="display: table-row;">
            <div style="display: table-cell; width: 50%; vertical-align: top;">
                Mengetahui,<br>
                <strong>Ketua Majelis Dikdasmen & PNF</strong><br>
                PDM Kota Yogyakarta<br><br><br><br>
                ( .................................................... )<br>
                NBM. ...............................................
            </div>
            <div style="display: table-cell; width: 50%; vertical-align: top; text-align: right;">
                Yogyakarta, {{ date('d F Y') }}<br>
                <strong>Koordinator Bidang Kepegawaian & SDM</strong><br>
                Majelis Dikdasmen & PNF<br><br><br><br>
                ( .................................................... )<br>
                NBM. ...............................................
            </div>
        </div>
    </div>
</body>
</html>

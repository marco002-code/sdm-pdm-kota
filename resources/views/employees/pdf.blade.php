<!DOCTYPE html>
<html lang="id">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Dokumen Formulir Data Induk - {{ $employee->full_name }}</title>
    <style>
        @page {
            size: a4 portrait;
            margin: 15mm 18mm 15mm 18mm;
        }

        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 10pt;
            line-height: 1.35;
            color: #1a202c;
            margin: 0;
            padding: 0;
        }

        /* Kop Surat Resmi */
        .kop-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 2px;
        }

        .kop-table td {
            vertical-align: middle;
            text-align: center;
        }

        .kop-title-instansi {
            font-size: 9pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #4a5568;
            margin: 0;
        }

        .kop-title-majelis {
            font-size: 11pt;
            font-weight: bold;
            text-transform: uppercase;
            color: #1a202c;
            margin: 2px 0;
        }

        .kop-title-unit {
            font-size: 12pt;
            font-weight: bold;
            text-transform: uppercase;
            color: #1e3a8a;
            margin: 2px 0;
        }

        .kop-address {
            font-size: 8pt;
            color: #4a5568;
            margin: 2px 0 0 0;
        }

        .kop-divider-thick {
            border-top: 2.5px solid #1a202c;
            margin-top: 5px;
            margin-bottom: 1.5px;
        }

        .kop-divider-thin {
            border-top: 1px solid #1a202c;
            margin-bottom: 14px;
        }

        /* Judul Dokumen */
        .doc-title-container {
            text-align: center;
            margin-bottom: 14px;
        }

        .doc-title {
            font-size: 13pt;
            font-weight: bold;
            text-transform: uppercase;
            text-decoration: underline;
            letter-spacing: 0.8px;
            color: #0f172a;
            margin: 0;
        }

        .doc-subtitle {
            font-size: 8.5pt;
            color: #475569;
            margin-top: 3px;
        }

        /* Section Styling */
        .section-header {
            font-size: 9.5pt;
            font-weight: bold;
            text-transform: uppercase;
            background-color: #f1f5f9;
            border-left: 3px solid #1e3a8a;
            padding: 3px 6px;
            margin-top: 10px;
            margin-bottom: 6px;
            letter-spacing: 0.3px;
        }

        /* Table Layout for Fields */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 4px;
        }

        .data-table td {
            padding: 2.5px 4px;
            vertical-align: top;
            font-size: 9pt;
        }

        .label-col {
            width: 28%;
            font-weight: 500;
            color: #334155;
        }

        .colon-col {
            width: 2%;
            text-align: center;
            color: #64748b;
        }

        .val-col {
            width: 70%;
            color: #0f172a;
        }

        .dotted-line {
            border-bottom: 1px dotted #94a3b8;
            display: inline-block;
            width: 100%;
            min-height: 12px;
        }

        /* Sub-list table (Pendidikan, Pengalaman, dll) */
        .list-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 4px;
            margin-left: 6px;
        }

        .list-table td {
            padding: 2px 4px;
            font-size: 9pt;
            vertical-align: top;
        }

        .badge {
            display: inline-block;
            padding: 1px 6px;
            font-size: 8pt;
            font-weight: bold;
            background-color: #e2e8f0;
            color: #334155;
            border-radius: 3px;
        }

        /* Finansial & Teknis Box */
        .box-info {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 4px;
            padding: 6px 8px;
            margin-top: 6px;
            margin-bottom: 8px;
        }

        .box-table {
            width: 100%;
            border-collapse: collapse;
        }

        .box-table td {
            font-size: 8.5pt;
            padding: 2px 4px;
            vertical-align: top;
        }

        /* Signature Table */
        .signature-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            page-break-inside: avoid;
        }

        .signature-table td {
            width: 50%;
            text-align: center;
            vertical-align: top;
            font-size: 9pt;
        }

        .signature-role {
            font-weight: bold;
            color: #1e293b;
            margin-bottom: 2px;
        }

        .signature-space {
            height: 55px;
        }

        .signature-name {
            font-weight: bold;
            text-decoration: underline;
            color: #0f172a;
        }

        .signature-id {
            font-size: 8pt;
            color: #64748b;
            margin-top: 2px;
        }

        /* Document Footer */
        .doc-footer {
            margin-top: 15px;
            border-top: 1px solid #cbd5e1;
            padding-top: 4px;
            font-size: 7.5pt;
            color: #94a3b8;
            text-align: justify;
        }

        .doc-footer table {
            width: 100%;
        }

        .doc-footer td {
            padding: 0;
            font-size: 7.5pt;
            color: #94a3b8;
        }
    </style>
</head>
<body>

    <!-- KOP SURAT RESMI -->
    <table class="kop-table">
        <tr>
            <td style="width: 75px; text-align: left; vertical-align: middle;">
                <img src="{{ public_path('images/logo-muhammadiyah.png') }}" style="width: 65px; height: 65px; object-fit: contain;">
            </td>
            <td>
                <div class="kop-title-instansi">Pimpinan Daerah Muhammadiyah Kota Yogyakarta</div>
                <div class="kop-title-majelis">Majelis Pendidikan Dasar, Menengah dan Pendidikan Nonformal</div>
                <div class="kop-title-unit">{{ $employee->unit->name ?? 'Unit Kerja PDM Kota Yogyakarta' }}</div>
                <div class="kop-address">
                    {{ $employee->unit->address ?? 'Alamat Unit Kerja PDM Kota Yogyakarta' }}
                    @if(!empty($employee->unit->phone))
                        • Telp: {{ $employee->unit->phone }}
                    @endif
                    @if(!empty($employee->unit->npsn))
                        • NPSN: {{ $employee->unit->npsn }}
                    @endif
                </div>
            </td>
            <td style="width: 75px;"></td>
        </tr>
    </table>

    <div class="kop-divider-thick"></div>
    <div class="kop-divider-thin"></div>

    <!-- JUDUL DOKUMEN -->
    <div class="doc-title-container">
        <h1 class="doc-title">DOKUMEN FORMULIR DATA INDUK PEGAWAI</h1>
        <div class="doc-subtitle">
            NPM: <strong>{{ $employee->npm ?? '-' }}</strong> &nbsp;|&nbsp; 
            NBM: <strong>{{ $employee->nbm ?? '-' }}</strong> &nbsp;|&nbsp;
            NIK: <strong>{{ $employee->nik ?? '-' }}</strong>
        </div>
    </div>

    <!-- 1. IDENTITAS DIRI -->
    <div class="section-header">I. Identitas Pribadi</div>
    <table class="data-table">
        <tr>
            <td class="label-col">Nama Lengkap & Gelar</td>
            <td class="colon-col">:</td>
            <td class="val-col"><strong>{{ $employee->full_name_with_titles }}</strong></td>
        </tr>
        <tr>
            <td class="label-col">Nomor Baku Muhammadiyah (NBM)</td>
            <td class="colon-col">:</td>
            <td class="val-col">{{ $employee->nbm ?: '-' }}</td>
        </tr>
        <tr>
            <td class="label-col">Nomor Induk Kependudukan (NIK)</td>
            <td class="colon-col">:</td>
            <td class="val-col">{{ $employee->nik ?: '-' }}</td>
        </tr>
        <tr>
            <td class="label-col">Tempat, Tanggal Lahir</td>
            <td class="colon-col">:</td>
            <td class="val-col">
                {{ $employee->birth_place ?? '-' }}, 
                {{ $employee->birth_date ? $employee->birth_date->translatedFormat('d F Y') : '-' }}
            </td>
        </tr>
        <tr>
            <td class="label-col">Jenis Kelamin / Agama</td>
            <td class="colon-col">:</td>
            <td class="val-col">
                {{ $employee->gender === 'L' ? 'Laki-laki' : ($employee->gender === 'P' ? 'Perempuan' : '-') }} / 
                {{ $employee->religion ?? 'Islam' }}
            </td>
        </tr>
        <tr>
            <td class="label-col">Status Pernikahan / Gol. Darah</td>
            <td class="colon-col">:</td>
            <td class="val-col">
                {{ ucfirst($employee->marital_status ?? '-') }} / 
                Golongan {{ $employee->blood_type ?? '-' }}
            </td>
        </tr>
        <tr>
            <td class="label-col">Alamat Tempat Tinggal</td>
            <td class="colon-col">:</td>
            <td class="val-col">{{ $employee->address ?: '-' }}</td>
        </tr>
        <tr>
            <td class="label-col">Nomor HP / WhatsApp</td>
            <td class="colon-col">:</td>
            <td class="val-col">{{ $employee->phone_number ?: '-' }}</td>
        </tr>
        <tr>
            <td class="label-col">Alamat Email</td>
            <td class="colon-col">:</td>
            <td class="val-col">{{ $employee->email ?: '-' }}</td>
        </tr>
        <tr>
            <td class="label-col">Kontak Darurat</td>
            <td class="colon-col">:</td>
            <td class="val-col">
                {{ $employee->emergency_contact_name ?: '-' }} 
                @if($employee->emergency_contact_phone)
                    ({{ $employee->emergency_contact_phone }})
                @endif
            </td>
        </tr>
    </table>

    <!-- 2. STATUS & PENUGASAN KEPEGAWAIAN -->
    <div class="section-header">II. Status & Penugasan Kepegawaian</div>
    <table class="data-table">
        <tr>
            <td class="label-col">Unit Kerja (AUM)</td>
            <td class="colon-col">:</td>
            <td class="val-col"><strong>{{ $employee->unit->name ?? '-' }}</strong> ({{ $employee->unit->code ?? '-' }})</td>
        </tr>
        <tr>
            <td class="label-col">Jabatan / Amanah</td>
            <td class="colon-col">:</td>
            <td class="val-col"><strong>{{ $employee->position ?? '-' }}</strong></td>
        </tr>
        <tr>
            <td class="label-col">Status Kepegawaian</td>
            <td class="colon-col">:</td>
            <td class="val-col">
                {{ $employee->category_label }} 
                ({{ $employee->status_label }})
            </td>
        </tr>
        <tr>
            <td class="label-col">Tanggal Mulai Bekerja (TMT)</td>
            <td class="colon-col">:</td>
            <td class="val-col">
                {{ $employee->join_date ? $employee->join_date->translatedFormat('d F Y') : '-' }}
                @if($employee->end_date)
                    s/d {{ $employee->end_date->translatedFormat('d F Y') }}
                @endif
            </td>
        </tr>
        <tr>
            <td class="label-col">Tahun Pensiun (BUP)</td>
            <td class="colon-col">:</td>
            <td class="val-col">
                {{ $employee->retirement_year ?? $employee->calculateRetirementYear() ?? '-' }}
                ({{ $employee->isTeacher() ? 'Batas Usia 60 Tahun' : 'Batas Usia 58 Tahun' }})
            </td>
        </tr>
        @if($employee->isTeacher())
        <tr>
            <td class="label-col">Sertifikasi Pendidik</td>
            <td class="colon-col">:</td>
            <td class="val-col">
                {{ $employee->is_certified_educator ? 'Sudah Tersertifikasi' . ($employee->certification_year ? ' (Th. ' . $employee->certification_year . ')' : '') : 'Belum Tersertifikasi' }}
            </td>
        </tr>
        <tr>
            <td class="label-col">Mata Pelajaran Diampu</td>
            <td class="colon-col">:</td>
            <td class="val-col">{{ $employee->teaching_subject ?: '-' }}</td>
        </tr>
        <tr>
            <td class="label-col">Status Inpassing</td>
            <td class="colon-col">:</td>
            <td class="val-col">{{ $employee->inpassing_status ? 'Inpassing (Gol. ' . ($employee->inpassing_grade ?? '-') . ')' : 'Non-Inpassing' }}</td>
        </tr>
        @endif
        @if($employee->isTendik() || $employee->isStaff())
        <tr>
            <td class="label-col">Divisi / Shift Kerja</td>
            <td class="colon-col">:</td>
            <td class="val-col">{{ $employee->work_unit_division ?: '-' }} / {{ $employee->work_shift ?: 'Reguler' }}</td>
        </tr>
        @endif
    </table>

    <!-- 3. RIWAYAT PENDIDIKAN -->
    <div class="section-header">III. Riwayat Pendidikan Formal</div>
    @php
        $edu = $employee->education_history ?? [];
        $s3 = $edu['s3'] ?? ['school' => null, 'major' => null];
        $s2 = $edu['s2'] ?? ['school' => null, 'major' => null];
        $s1 = $edu['s1'] ?? ['school' => null, 'major' => null];
        $d3 = $edu['d3'] ?? ['school' => null, 'major' => null];
    @endphp
    <table class="list-table">
        <tr>
            <td style="width: 15%; font-weight: 500;">- Jenjang S3</td>
            <td style="width: 2%;">:</td>
            <td style="width: 48%;">{{ $s3['school'] ?: '..............................................................' }}</td>
            <td style="width: 10%; font-weight: 500;">Bidang:</td>
            <td style="width: 25%;">{{ $s3['major'] ?: '...................................' }}</td>
        </tr>
        <tr>
            <td style="width: 15%; font-weight: 500;">- Jenjang S2</td>
            <td style="width: 2%;">:</td>
            <td style="width: 48%;">{{ $s2['school'] ?: '..............................................................' }}</td>
            <td style="width: 10%; font-weight: 500;">Bidang:</td>
            <td style="width: 25%;">{{ $s2['major'] ?: '...................................' }}</td>
        </tr>
        <tr>
            <td style="width: 15%; font-weight: 500;">- Jenjang S1</td>
            <td style="width: 2%;">:</td>
            <td style="width: 48%;">{{ $s1['school'] ?: ($employee->university ?: '..............................................................') }}</td>
            <td style="width: 10%; font-weight: 500;">Bidang:</td>
            <td style="width: 25%;">{{ $s1['major'] ?: ($employee->major ?: '...................................') }}</td>
        </tr>
        <tr>
            <td style="width: 15%; font-weight: 500;">- Jenjang D3</td>
            <td style="width: 2%;">:</td>
            <td style="width: 48%;">{{ $d3['school'] ?: '..............................................................' }}</td>
            <td style="width: 10%; font-weight: 500;">Bidang:</td>
            <td style="width: 25%;">{{ $d3['major'] ?: '...................................' }}</td>
        </tr>
        <tr>
            <td style="width: 15%; font-weight: 500;">- SMA/SMK/MA</td>
            <td style="width: 2%;">:</td>
            <td colspan="3">{{ $edu['sma'] ?? '................................................................................................................' }}</td>
        </tr>
        <tr>
            <td style="width: 15%; font-weight: 500;">- SMP/MTs</td>
            <td style="width: 2%;">:</td>
            <td colspan="3">{{ $edu['smp'] ?? '................................................................................................................' }}</td>
        </tr>
        <tr>
            <td style="width: 15%; font-weight: 500;">- SD/MI</td>
            <td style="width: 2%;">:</td>
            <td colspan="3">{{ $edu['sd'] ?? '................................................................................................................' }}</td>
        </tr>
    </table>

    <!-- 4. PENGALAMAN PEKERJAAN -->
    <div class="section-header">IV. Pengalaman Pekerjaan</div>
    @php
        $work = $employee->work_experiences ?? [];
    @endphp
    <table class="list-table">
        @if(!empty($work) && count($work) > 0)
            @foreach($work as $idx => $exp)
                <tr>
                    <td style="width: 4%;">{{ $idx + 1 }}.</td>
                    <td style="width: 96%;">{{ $exp }}</td>
                </tr>
            @endforeach
        @else
            <tr>
                <td style="width: 4%;">1.</td>
                <td style="width: 96%;">..............................................................................................................................................</td>
            </tr>
            <tr>
                <td style="width: 4%;">2.</td>
                <td style="width: 96%;">..............................................................................................................................................</td>
            </tr>
            <tr>
                <td style="width: 4%;">3.</td>
                <td style="width: 96%;">..............................................................................................................................................</td>
            </tr>
        @endif
    </table>

    <!-- 5. PENGALAMAN JABATAN / ORGANISASI PERSYARIKATAN -->
    <div class="section-header">V. Pengalaman Jabatan & Organisasi</div>
    @php
        $org = $employee->organizational_experiences ?? [];
    @endphp
    <table class="list-table">
        @if(!empty($org) && count($org) > 0)
            @foreach($org as $idx => $item)
                <tr>
                    <td style="width: 4%;">{{ $idx + 1 }}.</td>
                    <td style="width: 96%;">{{ $item }}</td>
                </tr>
            @endforeach
        @else
            <tr>
                <td style="width: 4%;">1.</td>
                <td style="width: 96%;">..............................................................................................................................................</td>
            </tr>
            <tr>
                <td style="width: 4%;">2.</td>
                <td style="width: 96%;">..............................................................................................................................................</td>
            </tr>
            <tr>
                <td style="width: 4%;">3.</td>
                <td style="width: 96%;">..............................................................................................................................................</td>
            </tr>
        @endif
    </table>

    <!-- 6. INFORMASI FINANSIAL & ADMINISTRASI -->
    <div class="section-header">VI. Informasi Finansial & Administrasi</div>
    <table class="data-table">
        <tr>
            <td class="label-col">Gaji Pokok Terdaftar</td>
            <td class="colon-col">:</td>
            <td class="val-col"><strong>Rp {{ number_format((float)$employee->basic_salary, 0, ',', '.') }}</strong></td>
        </tr>
        <tr>
            <td class="label-col">Rekening Bank</td>
            <td class="colon-col">:</td>
            <td class="val-col">
                {{ $employee->bank_name ?: '-' }} - {{ $employee->bank_account_number ?: '-' }} 
                (a.n. {{ $employee->bank_account_holder ?: $employee->full_name }})
            </td>
        </tr>
        <tr>
            <td class="label-col">Nomor Pokok Wajib Pajak (NPWP)</td>
            <td class="colon-col">:</td>
            <td class="val-col">{{ $employee->npwp ?: '-' }}</td>
        </tr>
        <tr>
            <td class="label-col">BPJS Ketenagakerjaan / Kesehatan</td>
            <td class="colon-col">:</td>
            <td class="val-col">{{ $employee->bpjs_ketenagakerjaan ?: '-' }} / {{ $employee->bpjs_kesehatan ?: '-' }}</td>
        </tr>
        <tr>
            <td class="label-col">Hak Cuti Tahunan / Sisa Cuti</td>
            <td class="colon-col">:</td>
            <td class="val-col">{{ $employee->annual_leave_quota }} hari / <strong>{{ $employee->remaining_leave_quota }} hari</strong></td>
        </tr>
    </table>

    <!-- PENGESAHAN & TANDA TANGAN -->
    <table class="signature-table">
        <tr>
            <td>
                <div style="color: #64748b; font-size: 8pt;">Mengetahui,</div>
                <div class="signature-role">Kepala Sekolah / Pimpinan Unit</div>
                <div class="signature-space"></div>
                <div class="signature-name">( ............................................................ )</div>
                <div class="signature-id">NBM : ............................................</div>
            </td>
            <td>
                <div style="color: #64748b; font-size: 8pt;">Yogyakarta, {{ now()->translatedFormat('d F Y') }}</div>
                <div class="signature-role">Pegawai yang Bersangkutan,</div>
                <div class="signature-space"></div>
                <div class="signature-name">{{ $employee->full_name_with_titles }}</div>
                <div class="signature-id">NBM : {{ $employee->nbm ?: '............................................' }}</div>
            </td>
        </tr>
    </table>

    <!-- FOOTER RESMI -->
    <div class="doc-footer">
        <table>
            <tr>
                <td style="text-align: left;">
                    Dokumen Formulir Data Induk Pegawai PDM Kota Yogyakarta • Dicetak otomatis pada: {{ now()->translatedFormat('d F Y H:i:s') }} WIB
                </td>
                <td style="text-align: right;">
                    Halaman 1 / Resmi
                </td>
            </tr>
        </table>
    </div>

</body>
</html>

# Rancangan Sistem Informasi Manajemen SDM (SIM-SDM)
## Pimpinan Daerah Muhammadiyah (PDM) Kota Yogyakarta

Sistem Informasi Manajemen Sumber Daya Manusia (SIM-SDM) PDM Kota Yogyakarta berfungsi sebagai **Pusat Bank Data Induk Kepegawaian (Single Source of Truth)** dan pusat pelaporan, pembinaan, serta monitoring berkala di lingkungan persyarikatan. Sistem menaungi seluruh satuan pendidikan Amal Usaha Muhammadiyah (AUM) di Kota Yogyakarta (TPA, TK/ABA, SD/MI, SMP, **MTs**, SMA, SMK, dan **PKBM**), dengan tetap menghormati otonomi operasional masing-masing unit sekolah/madrasah.

---

## 1. Prioritas Modul & Skala Urgensi

Berdasarkan arahan pembaruan dan regulasi kepegawaian terkini:

| Prioritas | Nama Modul | Status Implementasi | Deskripsi Singkat |
|:---|:---|:---|:---|
| **URGENT (Utama)** | **Modul Pencatatan Pegawai (Core HR) & Multi-ID** | **Aktif / Prioritas Utama** | Perekaman biodata pegawai dengan ID multi-sistem, menempatkan **NPM (Nomor Pegawai Muhammadiyah)** sebagai ID primer dengan validasi database Master NPM (nomor 1 s.d. terkini) saat diinput TU sekolah. |
| **URGENT** | **Sertifikasi SIM (Sekolah Ideologi Muhammadiyah)** | **Aktif** | Pencatatan sertifikat CMI (*Certified in Muhammadiyah Ideology*), input nomor sertifikat, nilai kelulusan, dan arsip PDF sertifikat. |
| **TINGGI** | **Modul Karir, Prestasi & Penilaian Kinerja** | **Aktif** | Integrasi Kenaikan Pangkat/Golongan, Promosi Kepala Sekolah (PKKS), Promosi Tim Manajemen (Waka/Koordinator), Portofolio Prestasi Pegawai (Lokal, Nasional, Internasional), dan Penilaian DP3. |
| **TINGGI** | **Modul Mutasi Pegawai (Riwayat Mutasi)** | **Aktif** | Pencatatan riwayat mutasi tugas pegawai antar-unit AUM (sekolah asal, tujuan, TMT, SK, alasan). |
| **SEDANG** | **Audit Kepatuhan Update Triwulanan (Per 3 Bulan)** | **Aktif** | Rekapitulasi kepatuhan pembaruan data tiap sekolah/madrasah secara berkala per 3 bulan oleh PDM. |
| **SEDANG** | **Modul Pembinaan & Penegakan Disiplin** | **Aktif** | Pencatatan riwayat pembinaan loyalitas, ideologi Al-Islam & Kemuhammadiyahan, serta rekam jejak sanksi (SP1, SP2, SP3). |
| **RENDAH / PENDING**| **Modul Rekrutmen Pegawai Baru** | **Pending (BETA)** | Modul pembukaan gelombang formasi & tes pelamar baru ditangguhkan sementara (status beta/non-urgent) hingga proses penataan data pegawai selesai. |

---

## 2. Struktur Data Induk Pegawai & Sistem Identitas Multi-ID

Setiap pendidik dan tenaga kependidikan di lingkungan Muhammadiyah Kota Yogyakarta memiliki beberapa nomor identitas resmi:

### A. Hierarki Nomor Identitas (Multi-ID)
1. **NPM (Nomor Pegawai Muhammadiyah) — [ID UTAMA]**:
   * Diinput oleh staf Tata Usaha (TU) sekolah/madrasah saat pendaftaran/pemutakhiran pegawai.
   * Sistem melakukan validasi silang (*cross-validation*) dengan tabel **Bank Data Master NPM** (terdaftar sejak nomor 1 hingga nomor terkini) untuk mencegah duplikasi, nomor ganda, atau nomor liar yang tidak diakui PDM.
2. **NBM (Nomor Baku Muhammadiyah)**: Kartu identitas resmi anggota Persyarikatan Muhammadiyah.
3. **NIK (Nomor Induk Kependudukan)**: 16 digit KTP resmi Republik Indonesia.
4. **NIP / NIPY (Nomor Induk Pegawai Yayasan/Unit)**: Nomor register internal unit sekolah.
5. **NUPTK**: Nomor Unik Pendidik dan Tenaga Kependidikan (Kementerian Pendidikan / Kemendikbudristek).
6. **NRG**: Nomor Registrasi Guru bagi yang telah lulus sertifikasi pendidik.
7. **SIMPATIKA ID / PegID**: Nomor registrasi tenaga pendidik madrasah di lingkungan Kementerian Agama (Kemenag) untuk MTs/MI/MA.

### B. Status & Kategori Pegawai Sesuai Regulasi
* **GTY**: Guru Tetap Yayasan / Persyarikatan (PTP).
* **GTT**: Guru Tidak Tetap (Kontrak 11 bulan PDM, batas waktu maksimal 10 tahun).
* **PTY**: Pegawai / Tenaga Kependidikan Tetap Yayasan / Persyarikatan (PTP).
* **PTT**: Pegawai / Tenaga Kependidikan Tidak Tetap (Kontrak 11 bulan PDM).
* **GPKS/M & TKPKS/M**: Guru & Tenaga Kependidikan Perjanjian Kerja Sekolah/Madrasah (Non-Dapodik, kontrak mandiri sekolah maksimal 1 tahun).

---

## 3. Modul Sertifikasi Ideologi (SIM) & Pembinaan

Mengacu pada berkas regulasi dan penguatan Al-Islam Kemuhammadiyahan:
* **Sekolah Ideologi Muhammadiyah (SIM)**:
  * Field: `sim_certificate_number` (Nomor Sertifikat resmi CMI dari PWM/PDM).
  * Field: `sim_grade` / `sim_score` (Predikat Nilai kelulusan ideologi).
  * Field: `sim_date` (Tanggal penetapan sertifikat).
  * Field: `sim_certificate_file` (Upload scan berkas PDF sertifikat).
* **Syarat Suksesi**: Kelulusan SIM menjadi salah satu prasyarat wajib dalam usulan promosi Kepala Sekolah dan Tim Manajemen Sekolah (Wakil Kepala Sekolah/Koordinator Bidang).

---

## 4. Modul Karir & Rekam Jejak Prestasi

Menu induk **Manajemen Karir** mencakup lima sub-fitur:
1. **Kenaikan Pangkat & Golongan**:
   * Kenaikan berkala (reguler), penyesuaian ijazah (S1 -> III/a, S2 -> III/b), dan pengangkatan tetap (TMT awal 2 tahun 0 bulan).
2. **Promosi Kepala Sekolah**:
   * Tahapan seleksi penjaringan, verifikasi berkas, penilaian visi-misi RPS, fit & proper test, rekomendasi PCM & Komite, serta sidang pleno PDM.
3. **Promosi Tim Manajemen Sekolah**:
   * Pengajuan Wakil Kepala Sekolah (SMP, MTs, SMA, SMK) dan Koordinator Bidang (SD/MI) dengan SK pengesahan dari Majelis Dikdasmen PDM.
4. **Prestasi Pegawai**:
   * Perekaman rekam jejak prestasi akademik/non-akademik guru dan tendik berjenjang: **Tingkat Lokal/Kota**, **Tingkat Nasional**, dan **Tingkat Internasional**, dilengkapi tahun, deskripsi penghargaan, dan dokumen pendukung.
5. **Penilaian Kinerja (DP3 & PKKS)**:
   * Input nilai Daftar Penilaian Pelaksanaan Pekerjaan (DP3) tahunan pegawai.
   * Instrumen Penilaian Kinerja Kepala Sekolah (PKKS) berbasis kompetensi manajerial, supervisi, kewirausahaan, dan kepemimpinan AUM.

---

## 5. Modul Mutasi Pegawai (Riwayat Mutasi Antar-Unit)

* Mencatat perpindahan tugas pegawai di lingkungan PDM Kota Yogyakarta.
* Meliputi:
  * Unit Kerja Asal dan Posisi Asal.
  * Unit Kerja Tujuan dan Posisi Tujuan.
  * Tanggal Berlaku (TMT Mutasi).
  * Nomor SK Mutasi & Tanggal SK dari Majelis/PDM.
  * Alasan perpindahan (pemerataan beban kerja, rotasi berkala, permohonan pribadi).
  * Log riwayat lengkap yang dapat dilihat di profil pegawai.

---

## 6. Monitoring & Audit Kepatuhan Data (Update Triwulanan)

* Fitur kontrol bagi PDM Kota Yogyakarta untuk memastikan akurasi data induk.
* **Siklus 3 Bulanan**:
  * Triwulan I: Januari – Maret
  * Triwulan II: April – Juni
  * Triwulan III: Juli – September
  * Triwulan IV: Oktober – Desember
* **Dasbor Kepatuhan**:
  * Daftar seluruh unit sekolah/madrasah beserta status (`Sudah Update` / `Belum Update`).
  * Tanggal pembaruan terakhir dan staf TU yang melakukan pembaharuan.
  * Fasilitas ekspor rekapitulasi kepatuhan untuk laporan pimpinan majelis.

---

## 7. Pemetaan Hak Akses (Role-Based Access Control)

### Tingkat Pusat (PDM / Majelis Dikdasmen & PNF)
1. **Admin (IT Support)**: Manajemen server, akun pengguna, sinkronisasi Bank Data Master NPM, audit trail.
2. **Pimpinan Daerah Muhammadiyah (Ketua/BPH)**: Dasbor eksekutif, persetujuan SK Pengangkatan Tetap, SK Mutasi, dan SK Kepala Sekolah.
3. **SDM / Majelis Dikdasmen PDM**: Verifikasi data pegawai seluruh unit, rekapitulasi kepatuhan per 3 bulan, sidang kenaikan pangkat, pengelolaan seleksi kepala sekolah & waka.

### Tingkat Unit Kerja (Sekolah / Madrasah / PKBM)
4. **Kepala Sekolah / Kepala PKBM**: Monitoring seluruh pendidik & tendik di unitnya, input penilaian kinerja (DP3, PKG), rekomendasi promosi/mutasi.
5. **Operator TU (Tata Usaha)**: Operator harian data induk unit. Menginput pegawai baru beserta **NPM** (tervalidasi sistem), mengunggah berkas sertifikat SIM, mengupdate prestasi, dan melaporkan pembaruan berkala tiap triwulan.

### Tingkat Individu (End-User)
6. **Guru & Pegawai**: Akses mandiri (ESS) untuk melihat profil pribadi, riwayat karir, data gaji pokok, dan unduh berkas biodata PDF.

---

## 8. Satuan Pendidikan Unit Kerja AUM

Unit kerja di bawah binaan Majelis Dikdasmen & PNF PDM Kota Yogyakarta meliputi jenjang:
1. **TPA & KB/TK/ABA** (Taman Kanak-kanak / Aisyiyah Bustanul Athfal)
2. **SD / MI** (Sekolah Dasar / Madrasah Ibtidaiyah Muhammadiyah)
3. **SMP** (Sekolah Menengah Pertama Muhammadiyah)
4. **MTs** (Madrasah Tsanawiyah Muhammadiyah — *Penambahan Baru*)
5. **SMA** (Sekolah Menengah Atas Muhammadiyah)
6. **SMK** (Sekolah Menengah Kejuruan Muhammadiyah)
7. **PKBM** (Pusat Kegiatan Belajar Masyarakat Muhammadiyah — *Penambahan Baru*)
8. **Kantor PDM / Majelis** (Sekretariat Persyarikatan)

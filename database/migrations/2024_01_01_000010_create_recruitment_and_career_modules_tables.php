<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. MODUL REKRUTMEN PEGAWAI (GTT, PTT, GTY, PTY)
        Schema::create('recruitments', function (Blueprint $table) {
            $table->id();
            $table->string('title', 200)->comment('Nama/Judul Periode Rekrutmen');
            $table->string('batch_code', 50)->unique()->comment('Kode Gelombang Seleksi');
            $table->unsignedSmallInteger('year')->default(2026);
            $table->date('start_date')->comment('Tanggal Pembukaan Pendaftaran');
            $table->date('end_date')->comment('Batas Akhir Pendaftaran');
            $table->date('test_date')->nullable()->comment('Jadwal Tes Tertulis & Praktik Ibadah');
            $table->date('announcement_date')->nullable()->comment('Pengumuman Hasil Akhir');
            $table->string('target_employee_type', 50)->default('SEMUA')->comment('Formasi: GTT, PTT, GTY, PTY, atau SEMUA');
            $table->json('positions')->nullable()->comment('Daftar Formasi Jabatan & Kuota Unit');
            $table->text('requirements')->nullable()->comment('Persyaratan Administratif & Khusus');
            $table->text('description')->nullable();
            $table->enum('status', ['draft', 'buka', 'seleksi_berkas', 'ujian_penjajagan', 'wawancara', 'selesai', 'ditutup'])->default('buka');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('recruitment_applicants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('recruitment_id')->constrained('recruitments')->cascadeOnDelete();
            $table->string('registration_number', 50)->unique()->comment('Nomor Registrasi Pelamar');
            $table->string('nik', 20);
            $table->string('nbm', 50)->nullable()->comment('Nomor Baku Muhammadiyah jika ada');
            $table->string('full_name', 150);
            $table->enum('gender', ['L', 'P']);
            $table->string('birth_place', 100)->nullable();
            $table->date('birth_date')->nullable();
            $table->string('email', 100);
            $table->string('phone_number', 30);
            $table->string('highest_education', 50)->nullable();
            $table->string('major', 100)->nullable();
            $table->string('university', 150)->nullable();
            $table->decimal('gpa', 3, 2)->nullable()->comment('IPK');
            $table->string('applied_position', 100)->comment('Formasi Jabatan yang Dilamar');
            $table->foreignId('preferred_unit_id')->nullable()->constrained('units')->nullOnDelete()->comment('Pilihan Unit Kerja');
            $table->foreignId('placed_unit_id')->nullable()->constrained('units')->nullOnDelete()->comment('Unit Penempatan Akhir');
            $table->string('employee_type', 50)->default('GTT')->comment('Kategori: GTT, PTT, GTY, PTY');
            $table->json('documents')->nullable()->comment('Daftar Berkas Terunggah');
            
            // Tahapan Penilaian & Seleksi PDM Kota Yogyakarta
            $table->enum('administration_status', ['pending', 'lolos', 'tidak_lolos'])->default('pending');
            $table->text('administration_notes')->nullable();
            
            $table->decimal('written_test_score', 5, 2)->nullable()->comment('Tes Tertulis (Pengetahuan Umum & Keilmuan)');
            $table->decimal('aik_test_score', 5, 2)->nullable()->comment('Al-Islam & Kemuhammadiyahan');
            $table->decimal('worship_test_score', 5, 2)->nullable()->comment('Praktik Ibadah & Membaca Al-Quran');
            $table->decimal('interview_score', 5, 2)->nullable()->comment('Wawancara Komitmen AUM');
            $table->decimal('microteaching_score', 5, 2)->nullable()->comment('Uji Mengajar (Khusus Guru)');
            $table->decimal('final_score', 5, 2)->nullable();
            
            $table->enum('selection_status', ['terdaftar', 'seleksi_administrasi', 'ujian_penjajagan', 'wawancara_microteaching', 'lulus', 'cadangan', 'gugur'])->default('terdaftar');
            $table->string('sk_number', 100)->nullable();
            $table->date('sk_date')->nullable();
            $table->foreignId('created_employee_id')->nullable()->constrained('employees')->nullOnDelete();
            $table->timestamps();

            $table->index(['recruitment_id', 'selection_status']);
            $table->index(['nik', 'email']);
        });

        // 2. MODUL KENAIKAN PANGKAT & ALIH STATUS KEPEGAWAIAN
        Schema::create('rank_promotions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->foreignId('unit_id')->constrained('units')->cascadeOnDelete();
            $table->enum('promotion_type', ['reguler_berkala', 'penyesuaian_ijazah', 'alih_status_gty', 'alih_status_pty', 'istimewa'])->comment('Jenis Pengajuan');
            $table->string('current_rank', 100)->nullable()->comment('Pangkat/Golongan Sekarang');
            $table->string('proposed_rank', 100)->comment('Pangkat/Golongan yang Diusulkan');
            $table->string('current_employee_type', 50)->comment('Kategori Sekarang (GTT/PTT/GTY/PTY)');
            $table->string('proposed_employee_type', 50)->comment('Kategori Diusulkan');
            $table->unsignedSmallInteger('years_of_service')->default(0)->comment('Masa Kerja Pegawai (Tahun)');
            $table->decimal('performance_score', 5, 2)->nullable()->comment('Nilai PKG / SKP Terakhir');
            $table->decimal('credit_points', 6, 2)->nullable()->comment('Angka Kredit / PAK');
            $table->string('new_education_degree', 100)->nullable()->comment('Gelar / Ijazah Baru untuk Penyesuaian');
            $table->text('ortom_involvement')->nullable()->comment('Riwayat Keaktifan di Persyarikatan & Ortom Muhammadiyah');
            $table->json('supporting_documents')->nullable()->comment('Berkas Pendukung (SK Terakhir, Sertifikat Baitul Arqam, dll)');
            $table->enum('status', ['diajukan', 'diverifikasi_sekolah', 'sidang_majelis', 'disetujui_pdm', 'ditolak'])->default('diajukan');
            $table->text('reviewer_notes')->nullable();
            $table->string('sk_number', 100)->nullable();
            $table->date('sk_date')->nullable();
            $table->date('effective_date')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['employee_id', 'status']);
            $table->index(['unit_id', 'promotion_type']);
        });

        // 3. MODUL PROMOSI KEPALA SEKOLAH (Ketentuan Dikdasmen Muhammadiyah)
        Schema::create('headmaster_selections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('unit_id')->constrained('units')->cascadeOnDelete();
            $table->string('period_years', 50)->comment('Periode Jabatan, contoh: 2026-2030');
            $table->date('announcement_date');
            $table->date('closing_date');
            $table->date('fit_proper_date')->nullable();
            $table->date('pleno_date')->nullable();
            $table->enum('status', ['penjaringan', 'verifikasi_berkas', 'fit_and_proper', 'sidang_pleno', 'penetapan_sk', 'selesai'])->default('penjaringan');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['unit_id', 'status']);
        });

        Schema::create('headmaster_candidates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('headmaster_selection_id')->constrained('headmaster_selections')->cascadeOnDelete();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->string('nbm', 50)->nullable();
            $table->text('vision_mission_rps')->nullable()->comment('Pemaparan Visi, Misi & Rencana Pengembangan Sekolah');
            $table->string('recommendation_pcm', 150)->nullable()->comment('Rekomendasi PCM');
            $table->string('recommendation_komite', 150)->nullable()->comment('Rekomendasi Komite Sekolah');
            
            // Asesmen Fit and Proper Test Majelis Dikdasmen
            $table->decimal('managerial_score', 5, 2)->nullable()->comment('Kompetensi Manajerial');
            $table->decimal('supervision_score', 5, 2)->nullable()->comment('Kompetensi Supervisi & Akademik');
            $table->decimal('entrepreneurship_score', 5, 2)->nullable()->comment('Kompetensi Kewirausahaan');
            $table->decimal('aik_score', 5, 2)->nullable()->comment('Al-Islam & Kemuhammadiyahan');
            $table->decimal('presentation_score', 5, 2)->nullable()->comment('Presentasi Program Kerja');
            $table->decimal('total_score', 5, 2)->nullable();
            
            $table->enum('pleno_decision', ['direkomendasikan', 'cadangan', 'tidak_lolos'])->nullable();
            $table->boolean('is_elected')->default(false)->comment('Kepala Sekolah Terpilih');
            $table->string('sk_number', 100)->nullable();
            $table->date('sk_date')->nullable();
            $table->date('inauguration_date')->nullable()->comment('Tanggal Pelantikan');
            $table->timestamps();

            $table->index(['headmaster_selection_id', 'is_elected']);
        });

        // 4. MODUL PROMOSI WAKIL KEPALA SEKOLAH
        Schema::create('vice_headmaster_nominations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('unit_id')->constrained('units')->cascadeOnDelete();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->enum('field', ['kurikulum_ismuba', 'kesiswaan', 'sarpras', 'humas_kemuhammadiyahan', 'lainnya'])->comment('Bidang Wakil Kepala Sekolah');
            $table->string('period_years', 50)->comment('Periode Jabatan');
            $table->text('recommendation_headmaster')->nullable()->comment('Surat Rekomendasi/Usulan Kepala Sekolah');
            $table->enum('status', ['diajukan', 'verifikasi_majelis', 'disetujui', 'ditolak'])->default('diajukan');
            $table->string('sk_number', 100)->nullable();
            $table->date('sk_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['unit_id', 'field', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vice_headmaster_nominations');
        Schema::dropIfExists('headmaster_candidates');
        Schema::dropIfExists('headmaster_selections');
        Schema::dropIfExists('rank_promotions');
        Schema::dropIfExists('recruitment_applicants');
        Schema::dropIfExists('recruitments');
    }
};

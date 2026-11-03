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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();

            // Relasi Akun & Unit
            $table->foreignId('user_id')->nullable()->unique()->constrained('users')->nullOnDelete()
                ->comment('Relasi akun login pegawai (1-to-1)');
            $table->foreignId('unit_id')->constrained('units')->cascadeOnDelete()
                ->comment('Unit kerja induk (TK, SD, SMP, SMA, Yayasan)');

            // 1. Data Umum (Semua Pegawai)
            $table->string('nik', 20)->unique()->comment('Nomor Induk Kependudukan (16 digit)');
            $table->string('nip', 50)->nullable()->unique()->comment('Nomor Induk Pegawai Yayasan/Unit');
            $table->string('nbm', 50)->nullable()->unique()->comment('Nomor Baku Muhammadiyah (NBM)');
            $table->string('full_name', 150)->comment('Nama Lengkap Sesuai KTP');
            $table->string('title_prefix', 30)->nullable()->comment('Gelar Depan (Dr., Drs., dll)');
            $table->string('title_suffix', 30)->nullable()->comment('Gelar Belakang (S.Pd., M.Pd., dll)');
            $table->enum('gender', ['L', 'P'])->comment('L = Laki-laki, P = Perempuan');
            $table->string('birth_place', 100)->nullable();
            $table->date('birth_date')->nullable();
            $table->string('religion', 30)->nullable();
            $table->enum('marital_status', ['lajang', 'menikah', 'cerai_hidup', 'cerai_mati'])->nullable();
            $table->string('blood_type', 5)->nullable();

            // Kontak & Domisili
            $table->text('address')->nullable()->comment('Alamat KTP / Domisili');
            $table->string('phone_number', 30)->nullable();
            $table->string('email', 100)->nullable();
            $table->string('emergency_contact_name', 100)->nullable();
            $table->string('emergency_contact_phone', 30)->nullable();

            // Data Finansial & Rekening Bank (Data Umum)
            $table->string('bank_name', 50)->nullable()->comment('Nama Bank Pembayaran Gaji');
            $table->string('bank_account_number', 50)->nullable();
            $table->string('bank_account_holder', 150)->nullable();
            $table->string('npwp', 30)->nullable();
            $table->string('bpjs_ketenagakerjaan', 50)->nullable();
            $table->string('bpjs_kesehatan', 50)->nullable();

            // Status & Jabatan Kepegawaian
            $table->string('employee_type', 50)->default('GTY')->comment('Kategori Pegawai: PDM, GTY, GTT, PTY, PTT, LAINNYA');
            $table->enum('employment_status', ['tetap', 'kontrak', 'gty', 'gtt', 'honor', 'magang'])->default('tetap');
            $table->string('position', 100)->comment('Jabatan/Posisi (e.g. Guru Matematika, Staf TU, Satpam)');
            $table->date('join_date')->comment('Tanggal Masuk Kerja');
            $table->date('end_date')->nullable()->comment('Tanggal Berakhir Kontrak/Keluar');
            $table->enum('status', ['aktif', 'cuti', 'nonaktif', 'mutasi', 'pensiun'])->default('aktif');

            // Data Gaji Pokok (Decentralized Payroll reference untuk pelaporan yayasan)
            $table->decimal('basic_salary', 15, 2)->default(0)->comment('Nominal Gaji Pokok Existing (Rekapitulasi Yayasan)');

            // 2. Khusus Guru (Teacher Specific Attributes)
            $table->string('nuptk', 30)->nullable()->comment('Nomor Unik Pendidik dan Tenaga Kependidikan');
            $table->string('nrg', 30)->nullable()->comment('Nomor Registrasi Guru');
            $table->string('teaching_subject', 100)->nullable()->comment('Mata Pelajaran Utama');
            $table->boolean('is_certified_educator')->default(false)->comment('Status Sertifikasi Pendidik');
            $table->unsignedSmallInteger('certification_year')->nullable();
            $table->string('inpassing_status', 50)->nullable()->comment('Status SK Inpassing');
            $table->string('inpassing_grade', 20)->nullable()->comment('Golongan Inpassing (misal: III/a)');
            $table->json('teaching_history')->nullable()->comment('Riwayat Mengajar / Beban Mengajar');

            // 3. Khusus Tenaga Kependidikan & Karyawan (Staff/Tendik Specific Attributes)
            $table->string('work_unit_division', 100)->nullable()->comment('Unit Kerja/Bidang: Sarpras, Keamanan, TU, Kebersihan, IT, dll.');
            $table->enum('work_shift', ['pagi', 'siang', 'malam', 'normal', 'fleksibel'])->nullable()->comment('Shift Kerja');
            $table->json('technical_certifications')->nullable()->comment('Sertifikasi Teknis (misal K3, Driver, dll)');

            // Pendidikan Terakhir
            $table->enum('highest_education', ['SD', 'SMP', 'SMA/SMK', 'D1', 'D2', 'D3', 'D4', 'S1', 'S2', 'S3', 'Lainnya'])->nullable();
            $table->string('major', 100)->nullable()->comment('Jurusan');
            $table->string('university', 150)->nullable()->comment('Nama Sekolah/Universitas');
            $table->unsignedSmallInteger('graduation_year')->nullable();
            $table->json('education_history')->nullable()->comment('Detail Riwayat Pendidikan S3, S2, S1, D3, SMA, SMP, SD');
            $table->json('work_experiences')->nullable()->comment('Daftar Pengalaman Pekerjaan');
            $table->json('organizational_experiences')->nullable()->comment('Daftar Pengalaman Jabatan / Organisasi');

            // Manajemen Cuti
            $table->unsignedSmallInteger('annual_leave_quota')->default(12)->comment('Kuota cuti tahunan');
            $table->unsignedSmallInteger('used_leave_quota')->default(0)->comment('Kuota cuti terpakai tahun berjalan');

            $table->text('notes')->nullable();
            $table->softDeletes();
            $table->timestamps();

            // Indexes
            $table->index(['unit_id', 'employee_type', 'status']);
            $table->index(['employee_type', 'is_certified_educator']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};

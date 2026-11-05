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
        // 1. BANK DATA MASTER NPM (Nomor Pegawai Muhammadiyah 1 s.d. Terkini)
        Schema::create('master_npms', function (Blueprint $table) {
            $table->id();
            $table->string('npm', 50)->unique()->comment('Nomor Pegawai Muhammadiyah Resmi');
            $table->string('registered_name', 150)->nullable()->comment('Nama Terdaftar di Buku Induk PDM');
            $table->string('nbm', 50)->nullable()->comment('NBM terkait jika ada');
            $table->foreignId('allocated_unit_id')->nullable()->constrained('units')->nullOnDelete()->comment('Unit Alokasi Awal');
            $table->boolean('is_used')->default(false)->comment('Status Apakah Sudah Terhubung ke Pegawai Aktif');
            $table->foreignId('used_by_employee_id')->nullable()->constrained('employees')->nullOnDelete()->comment('Pegawai Pemegang NPM');
            $table->timestamp('used_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['npm', 'is_used']);
        });

        // 2. MODUL MUTASI PEGAWAI (Riwayat Mutasi Antar-Unit AUM)
        Schema::create('employee_mutations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->foreignId('origin_unit_id')->constrained('units')->cascadeOnDelete()->comment('Unit Sekolah Asal');
            $table->foreignId('destination_unit_id')->constrained('units')->cascadeOnDelete()->comment('Unit Sekolah Tujuan');
            $table->string('origin_position', 100)->comment('Jabatan/Posisi Lama');
            $table->string('destination_position', 100)->comment('Jabatan/Posisi Baru');
            $table->date('effective_date')->comment('TMT Berlaku Mutasi');
            $table->string('sk_number', 100)->nullable()->comment('Nomor SK Mutasi PDM');
            $table->date('sk_date')->nullable();
            $table->text('reason')->nullable()->comment('Alasan Mutasi');
            $table->enum('status', ['diajukan', 'disetujui', 'dibatalkan'])->default('disetujui');
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['employee_id', 'effective_date']);
            $table->index(['origin_unit_id', 'destination_unit_id']);
        });

        // 3. MONITORING AUDIT KEPATUHAN UPDATE TRIWULANAN (Per 3 Bulan)
        Schema::create('unit_update_audits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('unit_id')->constrained('units')->cascadeOnDelete();
            $table->unsignedSmallInteger('year')->comment('Tahun Berjalan, misal 2026');
            $table->unsignedTinyInteger('quarter')->comment('Triwulan: 1, 2, 3, 4');
            $table->enum('status', ['belum_update', 'sudah_update', 'terverifikasi'])->default('belum_update');
            $table->timestamp('last_updated_at')->nullable();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete()->comment('Operator TU yang update');
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete()->comment('Verifikator PDM');
            $table->unsignedInteger('total_employees')->default(0)->comment('Jumlah Total Pegawai Terdata Saat Audit');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['unit_id', 'year', 'quarter']);
            $table->index(['year', 'quarter', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('unit_update_audits');
        Schema::dropIfExists('employee_mutations');
        Schema::dropIfExists('master_npms');
    }
};

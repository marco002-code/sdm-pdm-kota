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
        // 1. MODUL PEMBINAAN PEGAWAI (Ideologi AIK, Loyalitas, Pelatihan & Disiplin)
        Schema::create('employee_coachings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->foreignId('unit_id')->constrained('units')->cascadeOnDelete();
            $table->date('coaching_date')->comment('Tanggal Pembinaan / Pelatihan');
            $table->enum('category', ['ideologi_aik', 'komitmen_loyalitas', 'kompetensi_pelatihan', 'disiplin_sanksi', 'lainnya'])->default('ideologi_aik');
            $table->string('title', 200)->comment('Nama Kegiatan Pembinaan / Materi');
            $table->string('organizer', 150)->nullable()->comment('Penyelenggara: PDM / Majelis Dikdasmen / PCM / Sekolah');
            $table->text('notes')->nullable()->comment('Catatan Pembinaan / Resume Evaluasi');
            $table->text('follow_up')->nullable()->comment('Rencana Tindak Lanjut / Rekomendasi');
            $table->enum('status', ['terjadwal', 'terlaksana', 'evaluasi'])->default('terlaksana');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['employee_id', 'category']);
            $table->index(['unit_id', 'coaching_date']);
        });

        // 2. Tambah tipe penilaian pada tabel performance_appraisals (DP3 & PKKS)
        Schema::table('performance_appraisals', function (Blueprint $table) {
            if (!Schema::hasColumn('performance_appraisals', 'appraisal_type')) {
                $table->enum('appraisal_type', ['dp3', 'pkks', 'reguler'])->default('reguler')->after('unit_id')->comment('Jenis: DP3 Pegawai, PKKS (Kepala Sekolah), atau Reguler');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('performance_appraisals', function (Blueprint $table) {
            if (Schema::hasColumn('performance_appraisals', 'appraisal_type')) {
                $table->dropColumn('appraisal_type');
            }
        });
        Schema::dropIfExists('employee_coachings');
    }
};

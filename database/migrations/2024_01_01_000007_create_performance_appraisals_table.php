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
        Schema::create('performance_appraisals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->foreignId('evaluator_id')->constrained('users')->cascadeOnDelete()
                ->comment('Penilai: Kepala Sekolah / Tim Penilai / HR');
            $table->foreignId('unit_id')->constrained('units')->cascadeOnDelete();

            $table->string('period', 50)->comment('Periode Penilaian (contoh: 2024/2025 Semester 1)');
            $table->date('appraisal_date');

            // Komponen Penilaian (0 - 100)
            $table->decimal('pedagogic_score', 5, 2)->nullable()->comment('Kompetensi Pedagogik / Mengajar (Khusus Guru)');
            $table->decimal('discipline_score', 5, 2)->default(0)->comment('Kedisiplinan & Kehadiran');
            $table->decimal('technical_competency_score', 5, 2)->default(0)->comment('Kompetensi Teknis / Tugas Pokok');
            $table->decimal('attitude_score', 5, 2)->default(0)->comment('Perilaku & Kerjasama');
            $table->decimal('final_score', 5, 2)->default(0)->comment('Nilai Akhir Terbobot');

            $table->enum('grade', ['A', 'B', 'C', 'D', 'E'])->nullable()->comment('Predikat Kinerja (A = Amat Baik, B = Baik, dll)');
            $table->text('strengths')->nullable()->comment('Kelebihan / Kekuatan Pegawai');
            $table->text('improvements_needed')->nullable()->comment('Area Pengembangan');
            $table->text('notes')->nullable();
            $table->json('kpi_details')->nullable()->comment('Rincian butir-butir KPI');

            $table->enum('status', ['draft', 'submitted', 'reviewed_by_hr', 'finalized'])->default('draft');
            $table->timestamps();

            $table->index(['employee_id', 'period']);
            $table->index(['unit_id', 'period']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('performance_appraisals');
    }
};

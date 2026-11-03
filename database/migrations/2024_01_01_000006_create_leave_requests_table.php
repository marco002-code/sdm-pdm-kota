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
        Schema::create('leave_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->foreignId('unit_id')->constrained('units')->cascadeOnDelete();

            $table->enum('leave_type', [
                'cuti_tahunan',
                'cuti_sakit',
                'cuti_melahirkan',
                'cuti_alasan_penting',
                'izin',
                'lainnya'
            ])->default('cuti_tahunan');

            $table->date('start_date');
            $table->date('end_date');
            $table->unsignedSmallInteger('total_days')->default(1)->comment('Jumlah hari kerja cuti/izin');
            $table->text('reason')->comment('Alasan Pengajuan Cuti/Izin');
            $table->string('attachment_path')->nullable()->comment('Bukti Pendukung (Surat Dokter, dsb)');

            // Status Hierarkis:
            // pending -> approved_by_principal (validasi unit) -> approved_by_hr (disetujui yayasan)
            // atau ditolak (rejected) / dibatalkan (cancelled)
            $table->enum('status', [
                'pending',
                'approved_by_principal',
                'approved_by_hr',
                'rejected',
                'cancelled'
            ])->default('pending');

            // Approval Tingkat 1: Kepala Sekolah / Pimpinan Unit
            $table->foreignId('principal_approver_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('principal_approved_at')->nullable();
            $table->text('principal_notes')->nullable();

            // Approval Tingkat 2 / Pusat: SDM Yayasan (HR Pusat)
            $table->foreignId('hr_approver_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('hr_approved_at')->nullable();
            $table->text('hr_notes')->nullable();

            // Rejection info
            $table->foreignId('rejected_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('rejected_at')->nullable();
            $table->text('rejection_reason')->nullable();

            $table->boolean('deducts_leave_quota')->default(true)->comment('Apakah memotong kuota cuti tahunan');
            $table->timestamps();

            $table->index(['employee_id', 'status']);
            $table->index(['unit_id', 'status', 'start_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leave_requests');
    }
};

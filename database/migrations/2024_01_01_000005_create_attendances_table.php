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
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->foreignId('unit_id')->constrained('units')->cascadeOnDelete();
            $table->date('date')->comment('Tanggal Presensi');
            $table->time('time_in')->nullable()->comment('Jam Masuk');
            $table->time('time_out')->nullable()->comment('Jam Keluar');
            $table->enum('status', [
                'hadir',
                'terlambat',
                'izin',
                'sakit',
                'cuti',
                'dinas_luar',
                'alpa'
            ])->default('hadir');
            $table->decimal('overtime_hours', 4, 2)->default(0)->comment('Durasi Lembur dalam Jam');
            $table->decimal('check_in_latitude', 10, 7)->nullable();
            $table->decimal('check_in_longitude', 10, 7)->nullable();
            $table->string('check_in_photo')->nullable()->comment('Foto Bukti Presensi');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['employee_id', 'date']);
            $table->index(['unit_id', 'date', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};

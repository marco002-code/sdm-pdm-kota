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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('username', 50)->nullable()->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->enum('role', [
                'admin',           // IT Support Yayasan
                'yayasan',         // Alias Eksekutif Yayasan
                'ketua_yayasan',   // Eksekutif Yayasan
                'hr_pusat',        // SDM Yayasan (HR Pusat)
                'kepsek',          // Alias Kepala Sekolah
                'kepala_sekolah',  // Kepala Sekolah Unit (TK/SD/SMP/SMA)
                'tu',              // Alias Tata Usaha
                'tu_sekolah',      // Tata Usaha Sekolah
                'pegawai'          // Guru & Karyawan (ESS)
            ])->default('pegawai');
            $table->foreignId('unit_id')->nullable()->constrained('units')->nullOnDelete()
                ->comment('Unit kerja pengguna. Null untuk level yayasan (admin, yayasan, hr_pusat)');
            $table->string('phone', 30)->nullable();
            $table->string('avatar')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_login_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};

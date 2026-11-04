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
        Schema::table('users', function (Blueprint $table) {
            // Ubah tipe kolom role menjadi string 50 agar fleksibel mendukung
            // role baru (admin_utama, admin_yayasan, it_support, ketua_majelis, tu_unit)
            // beserta seluruh alias yang sudah ada.
            $table->string('role', 50)->default('pegawai')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role', 50)->default('pegawai')->change();
        });
    }
};

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
        Schema::create('units', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique()->comment('Kode Unit unik (e.g. SD-SAPEN1, SMP-1, dsb)');
            $table->string('npsn', 20)->nullable()->unique()->comment('Nomor Pokok Sekolah Nasional (NPSN)');
            $table->string('name', 150)->comment('Nama Lengkap Unit Sekolah / Kantor');
            $table->enum('level', ['YAYASAN', 'TPA', 'TK', 'SD', 'SMP', 'SMA', 'SMK', 'MA', 'LAINNYA'])->default('SD');
            $table->string('district', 100)->nullable()->comment('Kecamatan / Kemantren di Kota Yogyakarta');
            $table->string('village', 100)->nullable()->comment('Kelurahan di Kota Yogyakarta');
            $table->string('website', 255)->nullable()->comment('Website Resmi / Media Sosial');
            $table->text('address')->nullable();
            $table->string('accreditation', 20)->nullable()->comment('Akreditasi Unit / Sekolah');
            $table->string('phone', 30)->nullable();
            $table->string('email', 100)->nullable();
            $table->string('principal_name', 150)->nullable()->comment('Nama Kepala Sekolah / Pimpinan Unit');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('units');
    }
};

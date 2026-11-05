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
        Schema::create('employee_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->enum('document_type', [
                'ktp',
                'kartu_keluarga',
                'ijazah',
                'transkrip',
                'sk_pengangkatan',
                'sertifikat_pendidik',
                'sertifikat_k3',
                'sertifikat_pelatihan',
                'cv',
                'foto_profil',
                'dokumen_lain'
            ])->default('dokumen_lain');
            $table->string('document_name', 150)->comment('Judul/Deskripsi Dokumen');
            $table->string('document_number', 100)->nullable()->comment('Nomor Dokumen/SK');
            $table->string('file_path')->comment('Lokasi penyimpanan file digital');
            $table->unsignedBigInteger('file_size')->nullable()->comment('Ukuran file dalam bytes');
            $table->string('mime_type', 50)->nullable();
            $table->date('issue_date')->nullable()->comment('Tanggal Terbit Dokumen');
            $table->date('expiry_date')->nullable()->comment('Tanggal Berakhir / Masa Berlaku');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['employee_id', 'document_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_documents');
    }
};

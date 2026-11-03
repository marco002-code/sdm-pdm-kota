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
        Schema::table('employees', function (Blueprint $table) {
            // Nomor Identitas Utama Pegawai Muhammadiyah
            if (!Schema::hasColumn('employees', 'npm')) {
                $table->string('npm', 50)->nullable()->unique()->after('id')->comment('Nomor Pegawai Muhammadiyah (ID Utama)');
            }

            // Nomor ID Kemenag / Simpatika (khusus MTs / Madrasah)
            if (!Schema::hasColumn('employees', 'simpatika_id')) {
                $table->string('simpatika_id', 50)->nullable()->after('nrg')->comment('PegID / SIMPATIKA ID Kemenag');
            }

            // SIM: Sekolah Ideologi Muhammadiyah
            if (!Schema::hasColumn('employees', 'sim_certificate_number')) {
                $table->string('sim_certificate_number', 100)->nullable()->after('technical_certifications')->comment('Nomor Sertifikat SIM CMI');
            }
            if (!Schema::hasColumn('employees', 'sim_grade')) {
                $table->string('sim_grade', 30)->nullable()->after('sim_certificate_number')->comment('Nilai/Predikat Kelulusan SIM');
            }
            if (!Schema::hasColumn('employees', 'sim_certificate_file')) {
                $table->string('sim_certificate_file', 255)->nullable()->after('sim_grade')->comment('Path Berkas PDF/Scan Sertifikat SIM');
            }
            if (!Schema::hasColumn('employees', 'sim_date')) {
                $table->date('sim_date')->nullable()->after('sim_certificate_file')->comment('Tanggal Penerbitan Sertifikat SIM');
            }

            // Rekam Jejak Prestasi Pegawai (Lokal, Nasional, Internasional)
            if (!Schema::hasColumn('employees', 'achievements')) {
                $table->json('achievements')->nullable()->after('organizational_experiences')->comment('Riwayat Prestasi: Tingkat Lokal, Nasional, Internasional');
            }

            // Penilaian Kinerja Tambahan (DP3 & PKKS)
            if (!Schema::hasColumn('employees', 'dp3_scores')) {
                $table->json('dp3_scores')->nullable()->after('achievements')->comment('Riwayat Input Nilai DP3 Tahunan');
            }
            if (!Schema::hasColumn('employees', 'pkks_scores')) {
                $table->json('pkks_scores')->nullable()->after('dp3_scores')->comment('Riwayat Penilaian Kinerja Kepala Sekolah (PKKS)');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $columns = [
                'npm',
                'simpatika_id',
                'sim_certificate_number',
                'sim_grade',
                'sim_certificate_file',
                'sim_date',
                'achievements',
                'dp3_scores',
                'pkks_scores',
            ];
            foreach ($columns as $column) {
                if (Schema::hasColumn('employees', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};

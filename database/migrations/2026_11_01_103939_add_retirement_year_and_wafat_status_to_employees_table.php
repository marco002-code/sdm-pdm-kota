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
            if (!Schema::hasColumn('employees', 'retirement_year')) {
                $table->integer('retirement_year')->nullable()->after('end_date')
                    ->comment('Tahun Batas Usia Pensiun (BUP) Pegawai');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            if (Schema::hasColumn('employees', 'retirement_year')) {
                $table->dropColumn('retirement_year');
            }
        });
    }
};

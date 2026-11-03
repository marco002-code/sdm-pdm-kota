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
        Schema::table('units', function (Blueprint $table) {
            if (!Schema::hasColumn('units', 'village')) {
                $table->string('village', 100)->nullable()->after('district')->comment('Kelurahan');
            }
            if (!Schema::hasColumn('units', 'accreditation')) {
                $table->string('accreditation', 20)->nullable()->after('address')->comment('Akreditasi');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('units', function (Blueprint $table) {
            if (Schema::hasColumn('units', 'village')) {
                $table->dropColumn('village');
            }
            if (Schema::hasColumn('units', 'accreditation')) {
                $table->dropColumn('accreditation');
            }
        });
    }
};

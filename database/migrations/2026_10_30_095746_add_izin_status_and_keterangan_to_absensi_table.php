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
        Schema::table('absensi', function (Blueprint $table) {
            $table->string('status', 30)->default('tepat_waktu')->change();
            $table->time('jam_masuk')->nullable()->change();
            $table->string('foto')->nullable()->change();
            $table->string('keterangan')->nullable()->after('telat_menit');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('absensi', function (Blueprint $table) {
            $table->dropColumn('keterangan');
            $table->string('status', 30)->change();
        });
    }
};

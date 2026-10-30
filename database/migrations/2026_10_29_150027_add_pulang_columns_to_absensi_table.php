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
        Schema::table('absensi', function (Blueprint $table): void {
            $table->time('jam_pulang')->nullable()->after('jam_masuk');
            $table->string('foto_pulang')->nullable()->after('foto');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('absensi', function (Blueprint $table): void {
            $table->dropColumn(['jam_pulang', 'foto_pulang']);
        });
    }
};

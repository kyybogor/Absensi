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
            $table->string('pose_masuk', 50)->nullable()->after('foto');
            $table->string('pose_pulang', 50)->nullable()->after('foto_pulang');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('absensi', function (Blueprint $table): void {
            $table->dropColumn(['pose_masuk', 'pose_pulang']);
        });
    }
};

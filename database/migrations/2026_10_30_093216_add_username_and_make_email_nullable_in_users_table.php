<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('username')->nullable()->unique()->after('id');
            $table->string('email')->nullable()->change();
        });

        DB::table('users')->whereNull('username')->get()->each(function ($user) {
            $username = match ($user->email) {
                'admin@absensi.test' => 'admin',
                'karyawan@absensi.test' => 'karyawan',
                default => Str::slug($user->name, '_') ?: 'user_'.$user->id,
            };

            $base = $username;
            $count = 1;
            while (DB::table('users')->where('username', $username)->where('id', '!=', $user->id)->exists()) {
                $username = $base.'_'.$count++;
            }

            DB::table('users')->where('id', $user->id)->update(['username' => $username]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('username');
            $table->string('email')->nullable(false)->change();
        });
    }
};

<?php

namespace Database\Seeders;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seeder akun admin awal
        User::updateOrCreate(
            ['username' => 'admin'],
            [
                'name' => 'Administrator',
                'email' => 'admin@absensi.test',
                'password' => Hash::make('password123'),
                'role' => Role::Admin,
            ],
        );

        // Seeder akun karyawan contoh untuk memudahkan pengujian
        User::updateOrCreate(
            ['username' => 'karyawan'],
            [
                'name' => 'Budi Santoso',
                'email' => 'karyawan@absensi.test',
                'password' => Hash::make('password123'),
                'role' => Role::Karyawan,
            ],
        );

        // Seeder jam_absen (hanya 1 baris, default 08:00:00)
        DB::table('jam_absen')->truncate();
        DB::table('jam_absen')->insert([
            'jam_absen' => '08:00:00',
        ]);
    }
}

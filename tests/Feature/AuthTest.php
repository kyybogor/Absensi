<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_halaman_login_dapat_dibuka_oleh_tamu(): void
    {
        $response = $this->get(route('login'));

        $response->assertStatus(200);
        $response->assertSee('Masuk ke Sistem');
    }

    public function test_login_berhasil_memakai_password_bcrypt_dan_masuk_ke_halaman_absen(): void
    {
        $user = User::factory()->create([
            'username' => 'karyawan_test',
            'email' => 'karyawan@test.com',
            'password' => Hash::make('rahasia123'),
        ]);

        $response = $this->post(route('login.store'), [
            'login' => 'karyawan@test.com',
            'password' => 'rahasia123',
        ]);

        $response->assertRedirect(route('absensi.create'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_login_berhasil_memakai_username_tanpa_email(): void
    {
        $user = User::factory()->create([
            'username' => 'karyawan_no_email',
            'email' => null,
            'password' => Hash::make('rahasia123'),
        ]);

        $response = $this->post(route('login.store'), [
            'login' => 'karyawan_no_email',
            'password' => 'rahasia123',
        ]);

        $response->assertRedirect(route('absensi.create'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_login_berhasil_secara_case_insensitive(): void
    {
        $user = User::factory()->create([
            'username' => 'karyawan_unik',
            'email' => 'unik@test.com',
            'password' => Hash::make('rahasia123'),
        ]);

        $response = $this->post(route('login.store'), [
            'login' => 'KARYAWAN_UNIK',
            'password' => 'rahasia123',
        ]);

        $response->assertRedirect(route('absensi.create'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_login_gagal_jika_password_salah(): void
    {
        User::factory()->create([
            'email' => 'karyawan@test.com',
            'password' => Hash::make('rahasia123'),
        ]);

        $response = $this->from(route('login'))->post(route('login.store'), [
            'email' => 'karyawan@test.com',
            'password' => 'password_salah',
        ]);

        $response->assertRedirect(route('login'));
        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_rate_limiting_membatasi_maksimal_lima_kali_percobaan(): void
    {
        User::factory()->create([
            'email' => 'rate@test.com',
            'password' => Hash::make('password123'),
        ]);

        for ($i = 0; $i < 5; $i++) {
            $this->post(route('login.store'), [
                'email' => 'rate@test.com',
                'password' => 'salah',
            ]);
        }

        // Percobaan ke-6 harus ditolak oleh rate limiter
        $response = $this->post(route('login.store'), [
            'email' => 'rate@test.com',
            'password' => 'salah',
        ]);

        $response->assertSessionHasErrors('email');
        $errors = session('errors')->get('email');
        $this->assertStringContainsString('Terlalu banyak percobaan', $errors[0]);
    }

    public function test_pengguna_dapat_logout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('logout'));

        $response->assertRedirect(route('login'));
        $this->assertGuest();
    }
}

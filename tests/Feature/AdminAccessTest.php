<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Enums\StatusAbsensi;
use App\Models\Absensi;
use App\Models\JamAbsen;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminAccessTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        JamAbsen::setBatas('08:00:00');
    }

    public function test_karyawan_dilarang_mengakses_halaman_tambah_akun(): void
    {
        $karyawan = User::factory()->karyawan()->create();

        $response = $this->actingAs($karyawan)->get(route('users.index'));

        $response->assertStatus(403);
    }

    public function test_karyawan_dilarang_mengakses_halaman_atur_jam_absen(): void
    {
        $karyawan = User::factory()->karyawan()->create();

        $response = $this->actingAs($karyawan)->get(route('jam-absen.edit'));

        $response->assertStatus(403);
    }

    public function test_karyawan_dilarang_mengubah_jam_absen(): void
    {
        $karyawan = User::factory()->karyawan()->create();

        $response = $this->actingAs($karyawan)->put(route('jam-absen.update'), [
            'jam_absen' => '09:00',
        ]);

        $response->assertStatus(403);
        $this->assertSame('08:00:00', JamAbsen::getBatas());
    }

    public function test_admin_dapat_mengakses_halaman_tambah_akun_dan_atur_jam(): void
    {
        $admin = User::factory()->admin()->create();

        $responseUsers = $this->actingAs($admin)->get(route('users.index'));
        $responseUsers->assertStatus(200);
        $responseUsers->assertSee('Tambah Akun');

        $responseJam = $this->actingAs($admin)->get(route('jam-absen.edit'));
        $responseJam->assertStatus(200);
        $responseJam->assertSee('Atur Batas Jam Masuk');
    }

    public function test_admin_dapat_mengubah_batas_jam_absen(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->put(route('jam-absen.update'), [
            'jam_absen' => '08:30',
        ]);

        $response->assertRedirect(route('jam-absen.edit'));
        $response->assertSessionHas('status_sukses');

        $this->assertSame('08:30:00', JamAbsen::getBatas());
    }

    public function test_admin_dapat_mendaftarkan_akun_baru_dengan_password_bcrypt(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->post(route('users.store'), [
            'name' => 'Karyawan Baru',
            'email' => 'baru@absensi.test',
            'role' => Role::Karyawan->value,
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect(route('users.index'));
        $response->assertSessionHas('status_sukses');

        $userBaru = User::where('email', 'baru@absensi.test')->first();
        $this->assertNotNull($userBaru);
        $this->assertSame(Role::Karyawan, $userBaru->role);
        $this->assertTrue(Hash::check('password123', $userBaru->password));
    }

    public function test_admin_dapat_mendaftarkan_akun_baru_dengan_username_dan_tanpa_email(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->post(route('users.store'), [
            'username' => 'karyawan_opsional_email',
            'email' => '',
            'role' => Role::Karyawan->value,
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect(route('users.index'));
        $response->assertSessionHas('status_sukses');

        $userBaru = User::where('username', 'karyawan_opsional_email')->first();
        $this->assertNotNull($userBaru);
        $this->assertNull($userBaru->email);
        $this->assertSame('karyawan_opsional_email', $userBaru->username);
        $this->assertSame(Role::Karyawan, $userBaru->role);
    }

    public function test_admin_gagal_mendaftarkan_akun_jika_username_duplikat(): void
    {
        $admin = User::factory()->admin()->create();
        User::factory()->create(['username' => 'user_kembar']);

        $response = $this->actingAs($admin)->post(route('users.store'), [
            'username' => 'user_kembar',
            'role' => Role::Karyawan->value,
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors('username');
    }

    public function test_admin_dapat_mengubah_status_absensi_menjadi_izin(): void
    {
        $admin = User::factory()->admin()->create();
        $karyawan = User::factory()->karyawan()->create();
        $absensi = Absensi::factory()->telat(30)->create([
            'user_id' => $karyawan->id,
            'tanggal' => '2026-10-30',
        ]);

        $response = $this->actingAs($admin)->patch(route('admin.absensi.update-status', $absensi), [
            'status' => StatusAbsensi::Izin->value,
            'keterangan' => 'Izin sakit disetujui',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('status_sukses');

        $absensi->refresh();
        $this->assertSame(StatusAbsensi::Izin, $absensi->status);
        $this->assertSame(0, $absensi->telat_menit);
        $this->assertSame('Izin sakit disetujui', $absensi->keterangan);
    }

    public function test_admin_dapat_mencatat_izin_karyawan_secara_langsung(): void
    {
        $admin = User::factory()->admin()->create();
        $karyawan = User::factory()->karyawan()->create();

        $response = $this->actingAs($admin)->post(route('admin.absensi.store-izin'), [
            'user_id' => $karyawan->id,
            'tanggal' => '2026-10-31',
            'keterangan' => 'Keperluan dinas luar kantor',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('status_sukses');

        $absensi = Absensi::where('user_id', $karyawan->id)->whereDate('tanggal', '2026-10-31')->first();
        $this->assertNotNull($absensi);
        $this->assertSame(StatusAbsensi::Izin, $absensi->status);
        $this->assertSame(0, $absensi->telat_menit);
        $this->assertSame('Keperluan dinas luar kantor', $absensi->keterangan);
    }

    public function test_karyawan_dilarang_mengubah_status_absensi_atau_mencatat_izin(): void
    {
        $karyawan = User::factory()->karyawan()->create();
        $absensi = Absensi::factory()->create(['user_id' => $karyawan->id]);

        $responseUpdate = $this->actingAs($karyawan)->patch(route('admin.absensi.update-status', $absensi), [
            'status' => StatusAbsensi::Izin->value,
        ]);
        $responseUpdate->assertStatus(403);

        $responseIzin = $this->actingAs($karyawan)->post(route('admin.absensi.store-izin'), [
            'user_id' => $karyawan->id,
            'tanggal' => '2026-11-01',
        ]);
        $responseIzin->assertStatus(403);
    }
}

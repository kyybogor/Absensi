<?php

namespace Tests\Feature;

use App\Enums\StatusAbsensi;
use App\Models\Absensi;
use App\Models\JamAbsen;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AbsensiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        JamAbsen::setBatas('08:00:00');
        Storage::fake('public');
    }

    public function test_absen_berhasil_dengan_foto(): void
    {
        $user = User::factory()->karyawan()->create();

        // Set waktu sebelum batas 08:00 (tepat waktu)
        Carbon::setTestNow(Carbon::parse('2026-10-29 07:45:00', 'Asia/Jakarta'));

        $foto = UploadedFile::fake()->image('wajah.jpg', 640, 480);

        $response = $this->actingAs($user)->post(route('absensi.store'), [
            'foto' => $foto,
        ]);

        $response->assertRedirect(route('absensi.create'));
        $response->assertSessionHas('status_sukses');

        $this->assertDatabaseHas('absensi', [
            'user_id' => $user->id,
            'jam_masuk' => '07:45:00',
            'status' => 'tepat_waktu',
            'telat_menit' => 0,
        ]);

        $absensi = Absensi::where('user_id', $user->id)->first();
        $this->assertNotNull($absensi);
        $this->assertSame('2026-10-29', $absensi->tanggal->toDateString());
        Storage::disk('public')->assertExists($absensi->foto);
    }

    public function test_absen_telat_mencatat_selisih_menit_secara_tepat(): void
    {
        $user = User::factory()->karyawan()->create();

        // Set waktu lewat batas 08:00 WIB (08:25:00 = telat 25 menit)
        Carbon::setTestNow(Carbon::parse('2026-10-29 08:25:00', 'Asia/Jakarta'));

        $foto = UploadedFile::fake()->image('wajah.jpg', 640, 480);

        $response = $this->actingAs($user)->post(route('absensi.store'), [
            'foto' => $foto,
        ]);

        $response->assertRedirect(route('absensi.create'));

        $this->assertDatabaseHas('absensi', [
            'user_id' => $user->id,
            'status' => 'telat',
            'telat_menit' => 25,
        ]);
    }

    public function test_absen_tanpa_foto_ditolak_oleh_validasi(): void
    {
        $user = User::factory()->karyawan()->create();

        $response = $this->actingAs($user)->post(route('absensi.store'), []);

        $response->assertSessionHasErrors('foto');
        $this->assertDatabaseEmpty('absensi');
    }

    public function test_larangan_absen_dua_kali_pada_hari_yang_sama(): void
    {
        $user = User::factory()->karyawan()->create();

        Carbon::setTestNow(Carbon::parse('2026-10-29 07:50:00', 'Asia/Jakarta'));

        $foto1 = UploadedFile::fake()->image('wajah1.jpg', 640, 480);
        $this->actingAs($user)->post(route('absensi.store'), ['foto' => $foto1]);

        $this->assertDatabaseCount('absensi', 1);

        // Percobaan absen kedua kali di hari yang sama
        $foto2 = UploadedFile::fake()->image('wajah2.jpg', 640, 480);
        $response = $this->actingAs($user)->post(route('absensi.store'), ['foto' => $foto2]);

        $response->assertSessionHasErrors('absen');
        // Jumlah record tetap 1
        $this->assertDatabaseCount('absensi', 1);
    }

    public function test_karyawan_yang_sudah_masuk_bisa_melihat_form_absen_pulang(): void
    {
        $user = User::factory()->karyawan()->create();

        Carbon::setTestNow(Carbon::parse('2026-10-29 07:50:00', 'Asia/Jakarta'));

        Absensi::factory()->create([
            'user_id' => $user->id,
            'tanggal' => '2026-10-29',
            'jam_masuk' => '07:50:00',
            'status' => StatusAbsensi::TepatWaktu,
            'jam_pulang' => null,
        ]);

        $response = $this->actingAs($user)->get(route('absensi.create'));

        $response->assertStatus(200);
        $response->assertSee('Ringkasan Kehadiran Hari Ini');
        $response->assertSee('Sudah Masuk');
        $response->assertSee('Absen Pulang');
        $response->assertSee('Simpan Absen Pulang');
    }

    public function test_karyawan_berhasil_absen_pulang_dengan_foto(): void
    {
        $user = User::factory()->karyawan()->create();

        Carbon::setTestNow(Carbon::parse('2026-10-29 07:45:00', 'Asia/Jakarta'));

        $absensi = Absensi::factory()->create([
            'user_id' => $user->id,
            'tanggal' => '2026-10-29',
            'jam_masuk' => '07:45:00',
            'status' => StatusAbsensi::TepatWaktu,
            'jam_pulang' => null,
            'foto_pulang' => null,
        ]);

        // Karyawan pulang pukul 17:05:00 WIB
        Carbon::setTestNow(Carbon::parse('2026-10-29 17:05:00', 'Asia/Jakarta'));

        $fotoPulang = UploadedFile::fake()->image('wajah_pulang.jpg', 640, 480);

        $response = $this->actingAs($user)->post(route('absensi.pulang'), [
            'foto' => $fotoPulang,
        ]);

        $response->assertRedirect(route('absensi.create'));
        $response->assertSessionHas('status_sukses');

        $absensi->refresh();
        $this->assertSame('17:05:00', $absensi->jam_pulang);
        $this->assertNotNull($absensi->foto_pulang);
        Storage::disk('public')->assertExists($absensi->foto_pulang);
    }

    public function test_absen_pulang_ditolak_jika_belum_absen_masuk(): void
    {
        $user = User::factory()->karyawan()->create();

        Carbon::setTestNow(Carbon::parse('2026-10-29 17:00:00', 'Asia/Jakarta'));

        $foto = UploadedFile::fake()->image('pulang.jpg', 640, 480);
        $response = $this->actingAs($user)->post(route('absensi.pulang'), [
            'foto' => $foto,
        ]);

        $response->assertSessionHasErrors('absen');
    }

    public function test_absen_pulang_tidak_dapat_dilakukan_dua_kali_di_hari_yang_sama(): void
    {
        $user = User::factory()->karyawan()->create();

        Carbon::setTestNow(Carbon::parse('2026-10-29 07:50:00', 'Asia/Jakarta'));

        Absensi::factory()->create([
            'user_id' => $user->id,
            'tanggal' => '2026-10-29',
            'jam_masuk' => '07:50:00',
            'jam_pulang' => '17:00:00',
            'foto_pulang' => 'absensi/pulang.jpg',
        ]);

        Carbon::setTestNow(Carbon::parse('2026-10-29 17:30:00', 'Asia/Jakarta'));

        $foto = UploadedFile::fake()->image('pulang2.jpg', 640, 480);
        $response = $this->actingAs($user)->post(route('absensi.pulang'), [
            'foto' => $foto,
        ]);

        $response->assertSessionHasErrors('absen');
    }

    public function test_karyawan_yang_sudah_masuk_dan_pulang_melihat_ringkasan_lengkap_dan_sembunyikan_kamera(): void
    {
        $user = User::factory()->karyawan()->create();

        Carbon::setTestNow(Carbon::parse('2026-10-29 17:10:00', 'Asia/Jakarta'));

        Absensi::factory()->create([
            'user_id' => $user->id,
            'tanggal' => '2026-10-29',
            'jam_masuk' => '07:50:00',
            'jam_pulang' => '17:05:00',
            'foto_pulang' => 'absensi/pulang.jpg',
            'status' => StatusAbsensi::TepatWaktu,
        ]);

        $response = $this->actingAs($user)->get(route('absensi.create'));

        $response->assertStatus(200);
        $response->assertSee('Kehadiran Hari Ini Lengkap');
        $response->assertSee('Absen Masuk dan Pulang Lengkap');
        $response->assertDontSee('Ambil Foto');
    }
}

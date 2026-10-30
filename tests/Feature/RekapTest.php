<?php

namespace Tests\Feature;

use App\Enums\StatusAbsensi;
use App\Models\Absensi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RekapTest extends TestCase
{
    use RefreshDatabase;

    public function test_karyawan_hanya_melihat_rekap_miliknya_sendiri(): void
    {
        $karyawan1 = User::factory()->karyawan()->create(['name' => 'Budi']);
        $karyawan2 = User::factory()->karyawan()->create(['name' => 'Siti']);

        Absensi::factory()->create([
            'user_id' => $karyawan1->id,
            'tanggal' => '2026-10-28',
            'jam_masuk' => '07:50:00',
            'status' => StatusAbsensi::TepatWaktu,
        ]);

        Absensi::factory()->create([
            'user_id' => $karyawan2->id,
            'tanggal' => '2026-10-28',
            'jam_masuk' => '08:15:00',
            'status' => StatusAbsensi::Telat,
            'telat_menit' => 15,
        ]);

        $response = $this->actingAs($karyawan1)->get(route('rekap.index'));

        $response->assertStatus(200);
        $response->assertSee('Budi');
        $response->assertDontSee('Siti');
        $response->assertDontSee('Pilih Karyawan');
    }

    public function test_admin_melihat_rekap_semua_karyawan_dan_dapat_memfilter(): void
    {
        $admin = User::factory()->admin()->create();
        $karyawan1 = User::factory()->karyawan()->create(['name' => 'Budi']);
        $karyawan2 = User::factory()->karyawan()->create(['name' => 'Siti']);

        Absensi::factory()->create([
            'user_id' => $karyawan1->id,
            'tanggal' => '2026-10-28',
            'jam_masuk' => '07:50:00',
        ]);

        Absensi::factory()->create([
            'user_id' => $karyawan2->id,
            'tanggal' => '2026-10-28',
            'jam_masuk' => '08:15:00',
        ]);

        $response = $this->actingAs($admin)->get(route('rekap.index'));

        $response->assertStatus(200);
        $response->assertSee('Budi');
        $response->assertSee('Siti');
        $response->assertSee('Pilih Karyawan');

        // Filter khusus Siti
        $responseFilter = $this->actingAs($admin)->get(route('rekap.index', ['user_id' => $karyawan2->id]));
        $responseFilter->assertStatus(200);
        $responseFilter->assertViewHas('absensiList', function ($list) use ($karyawan2) {
            return $list->total() === 1 && $list->first()->user_id === $karyawan2->id;
        });
    }

    public function test_rekap_menghitung_ringkasan_izin_dan_menampilkannya(): void
    {
        $admin = User::factory()->admin()->create();
        $karyawan = User::factory()->karyawan()->create();

        Absensi::factory()->create([
            'user_id' => $karyawan->id,
            'tanggal' => '2026-10-27',
            'status' => StatusAbsensi::TepatWaktu,
        ]);
        Absensi::factory()->telat(20)->create([
            'user_id' => $karyawan->id,
            'tanggal' => '2026-10-28',
        ]);
        Absensi::factory()->izin('Sakit Flu')->create([
            'user_id' => $karyawan->id,
            'tanggal' => '2026-10-29',
        ]);

        $response = $this->actingAs($admin)->get(route('rekap.index'));

        $response->assertStatus(200);
        $response->assertViewHas('ringkasan', function ($ringkasan) {
            return $ringkasan['total_hadir'] === 2
                && $ringkasan['total_izin'] === 1
                && $ringkasan['total_telat'] === 1;
        });
        $response->assertSee('Sakit Flu');
        $response->assertSee('Izin');
    }
}

<?php

namespace Tests\Feature;

use App\Actions\HitungKeterlambatan;
use App\Enums\StatusAbsensi;
use App\Models\Absensi;
use Carbon\Carbon;
use Tests\TestCase;

class KeterlambatanTest extends TestCase
{
    private HitungKeterlambatan $action;

    protected function setUp(): void
    {
        parent::setUp();
        $this->action = new HitungKeterlambatan;
    }

    public function test_tepat_waktu_jika_jam_absen_kurang_dari_atau_sama_dengan_batas(): void
    {
        $batas = '08:00:00';

        // Tepat pukul 07:55:00
        $waktuSebelum = Carbon::parse('2026-10-29 07:55:00', 'Asia/Jakarta');
        $this->assertSame(0, $this->action->execute($waktuSebelum, $batas));

        // Tepat pukul 08:00:00 (batas)
        $waktuPas = Carbon::parse('2026-10-29 08:00:00', 'Asia/Jakarta');
        $this->assertSame(0, $this->action->execute($waktuPas, $batas));
    }

    public function test_telat_jika_jam_absen_melebihi_batas(): void
    {
        $batas = '08:00:00';

        // Telat 20 menit (08:20:00)
        $waktu20 = Carbon::parse('2026-10-29 08:20:00', 'Asia/Jakarta');
        $this->assertSame(20, $this->action->execute($waktu20, $batas));

        // Telat 75 menit (09:15:00)
        $waktu75 = Carbon::parse('2026-10-29 09:15:00', 'Asia/Jakarta');
        $this->assertSame(75, $this->action->execute($waktu75, $batas));
    }

    public function test_format_keterlambatan_menit_ke_jam_dan_menit(): void
    {
        $this->assertSame('20 menit', Absensi::formatMenit(20));
        $this->assertSame('1 jam 15 menit', Absensi::formatMenit(75));
        $this->assertSame('2 jam', Absensi::formatMenit(120));
        $this->assertSame('2 jam 5 menit', Absensi::formatMenit(125));
        $this->assertSame('0 menit', Absensi::formatMenit(0));
    }

    public function test_accessor_keterlambatan_format_pada_model_absensi(): void
    {
        $absensiTepat = new Absensi([
            'status' => StatusAbsensi::TepatWaktu,
            'telat_menit' => 0,
        ]);
        $this->assertSame('Tepat waktu', $absensiTepat->keterlambatan_format);

        $absensiTelat = new Absensi([
            'status' => StatusAbsensi::Telat,
            'telat_menit' => 75,
        ]);
        $this->assertSame('1 jam 15 menit', $absensiTelat->keterlambatan_format);
    }
}

<?php

namespace Database\Factories;

use App\Enums\StatusAbsensi;
use App\Models\Absensi;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Absensi>
 */
class AbsensiFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'tanggal' => now()->toDateString(),
            'jam_masuk' => '07:55:00',
            'foto' => 'absensi/sample.jpg',
            'status' => StatusAbsensi::TepatWaktu,
            'telat_menit' => 0,
        ];
    }

    /**
     * Set status telat.
     */
    public function telat(int $menit = 15): static
    {
        return $this->state(fn (array $attributes) => [
            'jam_masuk' => '08:'.str_pad((string) $menit, 2, '0', STR_PAD_LEFT).':00',
            'status' => StatusAbsensi::Telat,
            'telat_menit' => $menit,
        ]);
    }

    /**
     * Set status izin.
     */
    public function izin(?string $keterangan = 'Izin'): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => StatusAbsensi::Izin,
            'telat_menit' => 0,
            'keterangan' => $keterangan,
        ]);
    }
}

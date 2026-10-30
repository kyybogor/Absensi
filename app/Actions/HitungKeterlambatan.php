<?php

namespace App\Actions;

use Carbon\Carbon;
use Carbon\CarbonInterface;

class HitungKeterlambatan
{
    /**
     * Hitung selisih menit keterlambatan antara waktu absen dan jam batas.
     * Mengembalikan 0 jika tepat waktu (waktu absen <= jam batas).
     */
    public function execute(CarbonInterface|string $waktuAbsen, string $jamBatas): int
    {
        $waktu = $waktuAbsen instanceof CarbonInterface
            ? $waktuAbsen->copy()->setTimezone('Asia/Jakarta')
            : Carbon::parse($waktuAbsen, 'Asia/Jakarta');

        $batas = $waktu->copy()->setTimeFromTimeString($jamBatas);

        if ($waktu->lessThanOrEqualTo($batas)) {
            return 0;
        }

        $selisihDetik = $batas->diffInSeconds($waktu);

        return (int) ceil($selisihDetik / 60.0);
    }
}

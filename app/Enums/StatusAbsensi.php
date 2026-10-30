<?php

namespace App\Enums;

enum StatusAbsensi: string
{
    case TepatWaktu = 'tepat_waktu';
    case Telat = 'telat';
    case Izin = 'izin';

    public function label(): string
    {
        return match ($this) {
            self::TepatWaktu => 'Tepat Waktu',
            self::Telat => 'Telat',
            self::Izin => 'Izin',
        };
    }

    public function isTelat(): bool
    {
        return $this === self::Telat;
    }

    public function isTepatWaktu(): bool
    {
        return $this === self::TepatWaktu;
    }

    public function isIzin(): bool
    {
        return $this === self::Izin;
    }
}

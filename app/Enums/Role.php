<?php

namespace App\Enums;

enum Role: string
{
    case Admin = 'admin';
    case Karyawan = 'karyawan';

    public function label(): string
    {
        return match ($this) {
            self::Admin => 'Admin',
            self::Karyawan => 'Karyawan',
        };
    }

    public function isAdmin(): bool
    {
        return $this === self::Admin;
    }

    public function isKaryawan(): bool
    {
        return $this === self::Karyawan;
    }
}

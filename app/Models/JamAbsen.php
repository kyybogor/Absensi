<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JamAbsen extends Model
{
    /**
     * Nama tabel di database.
     *
     * @var string
     */
    protected $table = 'jam_absen';

    /**
     * Tabel tidak menggunakan primary key default.
     *
     * @var null|string
     */
    protected $primaryKey = null;

    /**
     * Tidak auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * Tidak ada kolom timestamps.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Atribut yang dapat diisi secara massal.
     *
     * @var list<string>
     */
    protected $fillable = [
        'jam_absen',
    ];

    /**
     * Ambil nilai batas jam absen aktif (format HH:mm atau HH:mm:ss).
     */
    public static function getBatas(): string
    {
        $val = static::query()->value('jam_absen');

        return $val ?? '08:00:00';
    }

    /**
     * Format jam batas untuk tampilan (contoh: 08:00 WIB).
     */
    public static function getBatasFormat(): string
    {
        $batas = static::getBatas();

        return substr($batas, 0, 5).' WIB';
    }

    /**
     * Perbarui nilai batas jam absen tunggal.
     */
    public static function setBatas(string $jam): void
    {
        if (static::query()->exists()) {
            static::query()->update(['jam_absen' => $jam]);
        } else {
            static::query()->insert(['jam_absen' => $jam]);
        }
    }
}

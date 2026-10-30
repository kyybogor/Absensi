<?php

namespace App\Models;

use App\Enums\StatusAbsensi;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Absensi extends Model
{
    use HasFactory;

    /**
     * Nama tabel di database.
     *
     * @var string
     */
    protected $table = 'absensi';

    /**
     * Atribut yang dapat diisi secara massal.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'tanggal',
        'jam_masuk',
        'jam_pulang',
        'foto',
        'foto_pulang',
        'status',
        'telat_menit',
        'keterangan',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
            'status' => StatusAbsensi::class,
            'telat_menit' => 'integer',
        ];
    }

    /**
     * Relasi ke user.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * URL publik untuk file foto absensi masuk.
     */
    protected function fotoUrl(): Attribute
    {
        return Attribute::make(
            get: fn (): string => $this->foto ? Storage::disk('public')->url($this->foto) : '',
        );
    }

    /**
     * URL publik untuk file foto absensi pulang.
     */
    protected function fotoPulangUrl(): Attribute
    {
        return Attribute::make(
            get: fn (): ?string => $this->foto_pulang ? Storage::disk('public')->url($this->foto_pulang) : null,
        );
    }

    /**
     * Cek apakah sudah absen pulang.
     */
    public function sudahAbsenPulang(): bool
    {
        return ! is_null($this->jam_pulang);
    }

    /**
     * Cek apakah siap absen pulang (sudah masuk tapi belum pulang).
     */
    public function bisaAbsenPulang(): bool
    {
        return ! is_null($this->jam_masuk) && is_null($this->jam_pulang);
    }

    /**
     * Format keterlambatan dalam format "X jam Y menit".
     */
    protected function keterlambatanFormat(): Attribute
    {
        return Attribute::make(
            get: function (): string {
                if ($this->status === StatusAbsensi::Izin) {
                    return 'Izin';
                }

                if ($this->status !== StatusAbsensi::Telat || $this->telat_menit <= 0) {
                    return 'Tepat waktu';
                }

                return static::formatMenit($this->telat_menit);
            },
        );
    }

    /**
     * Helper konversi menit ke representasi "X jam Y menit".
     */
    public static function formatMenit(int $menit): string
    {
        if ($menit <= 0) {
            return '0 menit';
        }

        $jam = intdiv($menit, 60);
        $sisaMenit = $menit % 60;

        if ($jam > 0 && $sisaMenit > 0) {
            return "{$jam} jam {$sisaMenit} menit";
        }

        if ($jam > 0) {
            return "{$jam} jam";
        }

        return "{$sisaMenit} menit";
    }

    /**
     * Scope absensi hari ini (atau tanggal tertentu).
     */
    public function scopeHariIni(Builder $query, ?string $tanggal = null): Builder
    {
        return $query->whereDate('tanggal', $tanggal ?? now()->toDateString());
    }

    /**
     * Scope filter rentang tanggal.
     */
    public function scopeRentangTanggal(Builder $query, ?string $dari = null, ?string $sampai = null): Builder
    {
        if ($dari) {
            $query->whereDate('tanggal', '>=', $dari);
        }

        if ($sampai) {
            $query->whereDate('tanggal', '<=', $sampai);
        }

        return $query;
    }

    /**
     * Scope filter milik user tertentu.
     */
    public function scopeMilikUser(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }
}

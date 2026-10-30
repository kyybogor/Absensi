<?php

namespace App\Actions;

use App\Enums\StatusAbsensi;
use App\Models\Absensi;
use Illuminate\Database\Eloquent\Builder;

class HitungRingkasanAbsensi
{
    /**
     * Hitung total hadir, total izin, total telat, dan total menit keterlambatan dari query.
     *
     * @param  Builder<Absensi>  $query
     * @return array{total_hadir: int, total_izin: int, total_telat: int, total_menit_telat: int, format_keterlambatan: string}
     */
    public function execute(Builder $query): array
    {
        $totalHadir = (clone $query)->whereIn('status', [StatusAbsensi::TepatWaktu, StatusAbsensi::Telat])->count();
        $totalIzin = (clone $query)->where('status', StatusAbsensi::Izin)->count();
        $totalTelat = (clone $query)->where('status', StatusAbsensi::Telat)->count();
        $totalMenitTelat = (int) (clone $query)->where('status', StatusAbsensi::Telat)->sum('telat_menit');

        return [
            'total_hadir' => $totalHadir,
            'total_izin' => $totalIzin,
            'total_telat' => $totalTelat,
            'total_menit_telat' => $totalMenitTelat,
            'format_keterlambatan' => Absensi::formatMenit($totalMenitTelat),
        ];
    }
}

<?php

namespace App\Services;

use App\Models\Absensi;
use App\Models\User;
use Carbon\Carbon;
use DomainException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;

class CatatAbsenPulangService
{
    /**
     * Catat absensi pulang untuk user pada hari ini dengan verifikasi foto kamera.
     *
     * @throws DomainException
     * @throws Throwable
     */
    public function execute(User $user, UploadedFile $file): Absensi
    {
        $sekarang = Carbon::now('Asia/Jakarta');
        $tanggalHariIni = $sekarang->toDateString();

        // Pastikan pengguna sudah absen masuk hari ini
        $absensi = Absensi::query()
            ->milikUser($user->id)
            ->hariIni($tanggalHariIni)
            ->first();

        if (! $absensi) {
            throw new DomainException('Anda belum melakukan absensi masuk hari ini.');
        }

        // Pastikan belum absen pulang
        if ($absensi->sudahAbsenPulang()) {
            throw new DomainException('Anda sudah melakukan absensi pulang hari ini.');
        }

        // Tentukan ekstensi aman
        $ekstensi = strtolower($file->getClientOriginalExtension());
        if (! in_array($ekstensi, ['jpg', 'jpeg', 'png'], true)) {
            $ekstensi = 'jpg';
        }

        // Simpan file foto pulang dengan nama unik
        $namaFile = sprintf('pulang_%d_%s_%s.%s', $user->id, $sekarang->format('Ymd_His'), Str::random(10), $ekstensi);
        $pathTersimpan = $file->storeAs('absensi', $namaFile, 'public');

        try {
            return DB::transaction(function () use ($absensi, $sekarang, $pathTersimpan): Absensi {
                $absensi->update([
                    'jam_pulang' => $sekarang->format('H:i:s'),
                    'foto_pulang' => $pathTersimpan,
                ]);

                return $absensi->fresh();
            });
        } catch (Throwable $e) {
            // Rollback file foto jika database gagal menyimpan
            if (Storage::disk('public')->exists($pathTersimpan)) {
                Storage::disk('public')->delete($pathTersimpan);
            }

            throw $e;
        }
    }
}

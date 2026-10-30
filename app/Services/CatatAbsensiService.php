<?php

namespace App\Services;

use App\Actions\HitungKeterlambatan;
use App\Enums\StatusAbsensi;
use App\Models\Absensi;
use App\Models\JamAbsen;
use App\Models\User;
use Carbon\Carbon;
use DomainException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;

class CatatAbsensiService
{
    public function __construct(
        private readonly HitungKeterlambatan $hitungKeterlambatan,
    ) {}

    /**
     * Catat absensi user dengan foto, validasi satu kali per hari,
     * hitung keterlambatan, dan tangani transaksi database serta rollback file.
     *
     * @throws DomainException
     * @throws Throwable
     */
    public function execute(User $user, UploadedFile $file): Absensi
    {
        $sekarang = Carbon::now('Asia/Jakarta');
        $tanggalHariIni = $sekarang->toDateString();

        // Cek duplikasi absensi hari ini untuk user
        $sudahAbsen = Absensi::query()
            ->milikUser($user->id)
            ->hariIni($tanggalHariIni)
            ->exists();

        if ($sudahAbsen) {
            throw new DomainException('Anda sudah melakukan absensi untuk hari ini.');
        }

        // Ambil batas jam absen aktif
        $jamBatas = JamAbsen::getBatas();
        $telatMenit = $this->hitungKeterlambatan->execute($sekarang, $jamBatas);
        $status = $telatMenit > 0 ? StatusAbsensi::Telat : StatusAbsensi::TepatWaktu;

        // Tentukan ekstensi aman
        $ekstensi = strtolower($file->getClientOriginalExtension());
        if (! in_array($ekstensi, ['jpg', 'jpeg', 'png'], true)) {
            $ekstensi = 'jpg';
        }

        // Simpan file foto dengan nama unik
        $namaFile = sprintf('absensi_%d_%s_%s.%s', $user->id, $sekarang->format('Ymd_His'), Str::random(10), $ekstensi);
        $pathRelatif = 'absensi/'.$namaFile;

        $pathTersimpan = $file->storeAs('absensi', $namaFile, 'public');

        try {
            return DB::transaction(function () use ($user, $tanggalHariIni, $sekarang, $pathTersimpan, $status, $telatMenit): Absensi {
                return Absensi::create([
                    'user_id' => $user->id,
                    'tanggal' => $tanggalHariIni,
                    'jam_masuk' => $sekarang->format('H:i:s'),
                    'foto' => $pathTersimpan,
                    'status' => $status,
                    'telat_menit' => $telatMenit,
                ]);
            });
        } catch (Throwable $e) {
            // Rollback: hapus file foto jika database gagal menyimpan
            if (Storage::disk('public')->exists($pathTersimpan)) {
                Storage::disk('public')->delete($pathTersimpan);
            }

            throw $e;
        }
    }
}

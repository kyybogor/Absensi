<?php

namespace App\Http\Controllers;

use App\Http\Requests\SimpanAbsensiRequest;
use App\Models\Absensi;
use App\Models\JamAbsen;
use App\Services\CatatAbsenPulangService;
use App\Services\CatatAbsensiService;
use Carbon\Carbon;
use DomainException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Throwable;

class AbsensiController extends Controller
{
    /**
     * Tampilkan halaman "Absen Sekarang" (Absen Masuk atau Absen Pulang).
     */
    public function create(Request $request): View
    {
        $user = $request->user();
        $sekarang = Carbon::now('Asia/Jakarta');
        $tanggalHariIni = $sekarang->toDateString();

        // Cek apakah user sudah absen masuk hari ini
        $absensiHariIni = Absensi::query()
            ->milikUser($user->id)
            ->hariIni($tanggalHariIni)
            ->first();

        $batasJam = JamAbsen::getBatas();
        $batasJamFormat = JamAbsen::getBatasFormat();

        return view('absensi.create', [
            'absensiHariIni' => $absensiHariIni,
            'batasJam' => $batasJam,
            'batasJamFormat' => $batasJamFormat,
            'tanggalLabel' => $sekarang->translatedFormat('l, d F Y'),
        ]);
    }

    /**
     * Simpan data absensi masuk dan foto pengguna.
     */
    public function store(SimpanAbsensiRequest $request, CatatAbsensiService $service): RedirectResponse
    {
        try {
            $absensi = $service->execute($request->user(), $request->file('foto'));

            $pesan = $absensi->status->isTelat()
                ? "Absensi masuk tercatat. Anda telat {$absensi->keterlambatan_format}."
                : 'Absensi masuk berhasil dicatat tepat waktu. Terima kasih!';

            return redirect()->route('absensi.create')->with('status_sukses', $pesan);
        } catch (DomainException $e) {
            return redirect()->route('absensi.create')->withErrors(['absen' => $e->getMessage()]);
        } catch (Throwable $e) {
            report($e);

            return redirect()->route('absensi.create')->withErrors(['absen' => 'Terjadi kesalahan sistem saat menyimpan absensi masuk. Silakan coba kembali.']);
        }
    }

    /**
     * Simpan data absensi pulang dan foto pengguna.
     */
    public function storePulang(SimpanAbsensiRequest $request, CatatAbsenPulangService $service): RedirectResponse
    {
        try {
            $absensi = $service->execute($request->user(), $request->file('foto'));

            $jamPulangFormat = substr($absensi->jam_pulang, 0, 5);
            $pesan = "Absensi pulang berhasil dicatat pada pukul {$jamPulangFormat} WIB. Selamat beristirahat!";

            return redirect()->route('absensi.create')->with('status_sukses', $pesan);
        } catch (DomainException $e) {
            return redirect()->route('absensi.create')->withErrors(['absen' => $e->getMessage()]);
        } catch (Throwable $e) {
            report($e);

            return redirect()->route('absensi.create')->withErrors(['absen' => 'Terjadi kesalahan sistem saat menyimpan absensi pulang. Silakan coba kembali.']);
        }
    }
}

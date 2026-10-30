<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateJamAbsenRequest;
use App\Models\JamAbsen;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class JamAbsenController extends Controller
{
    /**
     * Tampilkan halaman formulir pengaturan jam batas absensi.
     */
    public function edit(): View
    {
        $batasAktif = JamAbsen::getBatas();
        $jamMenit = substr($batasAktif, 0, 5);

        return view('jam-absen.edit', [
            'jamMenit' => $jamMenit,
            'batasFormat' => JamAbsen::getBatasFormat(),
        ]);
    }

    /**
     * Perbarui nilai batas jam absensi tunggal.
     */
    public function update(UpdateJamAbsenRequest $request): RedirectResponse
    {
        $jamInput = $request->validated('jam_absen');

        // Pastikan format detik ada untuk konsistensi database (HH:mm:ss)
        if (strlen($jamInput) === 5) {
            $jamInput .= ':00';
        }

        JamAbsen::setBatas($jamInput);

        return redirect()->route('jam-absen.edit')
            ->with('status_sukses', "Batas jam masuk berhasil diperbarui menjadi {$jamInput} WIB. Berlaku untuk absensi berikutnya.");
    }
}

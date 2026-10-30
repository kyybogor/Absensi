<?php

namespace App\Http\Controllers;

use App\Actions\HitungRingkasanAbsensi;
use App\Enums\StatusAbsensi;
use App\Models\Absensi;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class RekapAbsensiController extends Controller
{
    /**
     * Tampilkan rekap data absensi dengan filter dan ringkasan.
     */
    public function index(Request $request, HitungRingkasanAbsensi $hitungRingkasan): View
    {
        $user = $request->user();
        $isAdmin = $user->isAdmin();

        $query = Absensi::query()
            ->with('user')
            ->latest('tanggal')
            ->latest('jam_masuk');

        // Jika bukan admin, hanya bisa melihat absensi miliknya sendiri
        if (! $isAdmin) {
            $query->milikUser($user->id);
        } else {
            // Admin dapat memfilter berdasarkan karyawan tertentu
            if ($request->filled('user_id')) {
                $query->milikUser((int) $request->input('user_id'));
            }
        }

        // Filter rentang tanggal
        $dariTanggal = $request->input('dari');
        $sampaiTanggal = $request->input('sampai');
        $query->rentangTanggal($dariTanggal, $sampaiTanggal);

        // Hitung ringkasan statistik dari query terfilter
        $ringkasan = $hitungRingkasan->execute($query);

        // Ambil data dengan pagination
        $absensiList = $query->paginate(15)->withQueryString();

        // Daftar karyawan untuk pilihan filter admin
        $karyawanList = $isAdmin
            ? User::orderBy('name')->get(['id', 'name', 'username', 'email'])
            : collect();

        return view('rekap.index', [
            'absensiList' => $absensiList,
            'ringkasan' => $ringkasan,
            'karyawanList' => $karyawanList,
            'isAdmin' => $isAdmin,
            'filterUserId' => $request->input('user_id'),
            'filterDari' => $dariTanggal,
            'filterSampai' => $sampaiTanggal,
        ]);
    }

    /**
     * Ubah status catatan absensi (misalnya ditandai sebagai izin oleh admin).
     */
    public function updateStatus(Request $request, Absensi $absensi): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', Rule::enum(StatusAbsensi::class)],
            'keterangan' => ['nullable', 'string', 'max:255'],
            'telat_menit' => ['nullable', 'integer', 'min:0'],
        ]);

        $status = $validated['status'] instanceof StatusAbsensi
            ? $validated['status']
            : StatusAbsensi::from($validated['status']);

        $updateData = [
            'status' => $status,
            'keterangan' => $validated['keterangan'] ?? $absensi->keterangan,
        ];

        // Jika diubah menjadi Izin atau Tepat Waktu, keterlambatan direset ke 0
        if ($status === StatusAbsensi::Izin || $status === StatusAbsensi::TepatWaktu) {
            $updateData['telat_menit'] = 0;
        } elseif ($status === StatusAbsensi::Telat && isset($validated['telat_menit'])) {
            $updateData['telat_menit'] = (int) $validated['telat_menit'];
        }

        $absensi->update($updateData);

        $tanggalFormat = $absensi->tanggal->translatedFormat('d/m/Y');

        return back()->with('status_sukses', "Status absensi {$absensi->user->name} tanggal {$tanggalFormat} berhasil diubah menjadi {$status->label()}.");
    }

    /**
     * Catat izin karyawan langsung oleh admin untuk tanggal tertentu.
     */
    public function storeIzin(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'tanggal' => ['required', 'date'],
            'keterangan' => ['nullable', 'string', 'max:255'],
        ], [
            'user_id.required' => 'Pilih karyawan terlebih dahulu.',
            'user_id.exists' => 'Karyawan yang dipilih tidak valid.',
            'tanggal.required' => 'Tanggal izin wajib diisi.',
            'tanggal.date' => 'Format tanggal tidak valid.',
        ]);

        $user = User::findOrFail($validated['user_id']);

        $absensi = Absensi::updateOrCreate(
            [
                'user_id' => $user->id,
                'tanggal' => $validated['tanggal'],
            ],
            [
                'status' => StatusAbsensi::Izin,
                'telat_menit' => 0,
                'keterangan' => $validated['keterangan'] ?: 'Izin disetujui Admin',
            ],
        );

        $tanggalFormat = $absensi->tanggal->translatedFormat('d/m/Y');

        return back()->with('status_sukses', "Izin untuk {$user->name} pada tanggal {$tanggalFormat} berhasil dicatat.");
    }
}

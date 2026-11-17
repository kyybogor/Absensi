<?php

namespace App\Services;

class PoseTantanganService
{
    /**
     * Daftar seluruh opsi pose tantangan foto absensi dengan foto orang asli.
     *
     * @return array<string, array{code: string, nama: string, deskripsi: string, gambar: string, icon: string}>
     */
    public static function all(): array
    {
        return [
            'dua_jari' => [
                'code' => 'dua_jari',
                'nama' => 'Salam Dua Jari (Peace)',
                'deskripsi' => 'Tunjukkan 2 jari (telunjuk & tengah) di samping wajah.',
                'gambar' => 'images/pose_2_jari.jpg',
                'icon' => '✌️',
            ],
            'jempol' => [
                'code' => 'jempol',
                'nama' => 'Jempol ke Atas (Thumbs Up)',
                'deskripsi' => 'Acungkan jempol tangan ke atas di depan dada atau pipi.',
                'gambar' => 'images/pose_jempol.jpg',
                'icon' => '👍',
            ],
            'tiga_jari' => [
                'code' => 'tiga_jari',
                'nama' => 'Tiga Jari Terbuka',
                'deskripsi' => 'Angkat 3 jari (telunjuk, tengah, manis) di samping wajah.',
                'gambar' => 'images/pose_3_jari.jpg',
                'icon' => '3️⃣',
            ],
            'kepal_tangan' => [
                'code' => 'kepal_tangan',
                'nama' => 'Kepalkan Tangan (Fist)',
                'deskripsi' => 'Kepalkan satu tangan di samping bahu atau wajah.',
                'gambar' => 'images/pose_kepal_tangan.jpg',
                'icon' => '✊',
            ],
            'tangan_dagu' => [
                'code' => 'tangan_dagu',
                'nama' => 'Tangan di Dagu (Pose Berpikir)',
                'deskripsi' => 'Letakkan tangan atau ibu jari dan telunjuk di bawah dagu.',
                'gambar' => 'images/pose_tangan_dagu.jpg',
                'icon' => '🤔',
            ],
            'salam_hormat' => [
                'code' => 'salam_hormat',
                'nama' => 'Telapak Tangan di Dada (Salam Hormat)',
                'deskripsi' => 'Tempelkan kedua telapak tangan di depan dada tanda salam.',
                'gambar' => 'images/pose_salam_hormat.jpg',
                'icon' => '🙏',
            ],
            'ok_sign' => [
                'code' => 'ok_sign',
                'nama' => 'Simbol OK (Lingkaran Jari)',
                'deskripsi' => 'Bentuk lingkaran dengan jempol dan telunjuk di depan dada atau wajah.',
                'gambar' => 'images/pose_ok.jpg',
                'icon' => '👌',
            ],
            'finger_heart' => [
                'code' => 'finger_heart',
                'nama' => 'Finger Heart (Saranghae)',
                'deskripsi' => 'Silangkan ibu jari dan telunjuk membentuk hati mini ala Korea.',
                'gambar' => 'images/pose_finger_heart.jpg',
                'icon' => '🫰',
            ],
        ];
    }

    /**
     * Ambil satu pose acak untuk sesi absensi saat ini.
     *
     * @return array{code: string, nama: string, deskripsi: string, gambar: string, icon: string}
     */
    public static function random(): array
    {
        $all = self::all();
        $keys = array_keys($all);
        $randomKey = $keys[array_rand($keys)];

        return $all[$randomKey];
    }

    /**
     * Cari detail pose berdasarkan kode uniknya.
     *
     * @return array{code: string, nama: string, deskripsi: string, gambar: string, icon: string}|null
     */
    public static function find(?string $code): ?array
    {
        if (! $code) {
            return null;
        }

        $all = self::all();

        return $all[$code] ?? null;
    }
}

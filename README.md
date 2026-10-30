# 🕒 Sistem Absensi Karyawan Berbasis Web

Aplikasi presensi dan absensi karyawan modern berbasis web yang dibangun menggunakan **Laravel 11/12**, dirancang dengan fokus pada efisiensi operasional, keamanan data, kemudahan penggunaan (mobile-friendly), dan fleksibilitas deployment.

Aplikasi ini dilengkapi dengan fitur presensi berbasis foto kamera real-time (selfie), kalkulasi keterlambatan otomatis, pengelolaan izin karyawan, dashboard rekapitulasi, dan manajemen akun multi-peran (Admin & Karyawan).

---

## 🚀 Fitur Utama

### 👤 Modul Karyawan
- **Presensi Masuk & Pulang via Kamera (Webcam / HP)**:
  - Pengambilan foto bukti kehadiran langsung melalui kamera perangkat secara real-time.
  - Validasi foto wajib dan anti-pemalsuan jam masuk/pulang.
- **Deteksi Keterlambatan Otomatis**:
  - Sistem otomatis membandingkan jam absen masuk dengan jam toleransi kerja yang ditentukan Admin.
  - Penghitungan selisih keterlambatan secara presisi (dalam format jam dan menit).
- **Riwayat Rekap Presensi Pribadi**:
  - Melihat riwayat presensi harian, jam masuk, jam pulang, durasi keterlambatan, dan status kehadiran.
  - Filter riwayat berdasarkan rentang tanggal.
  - Modal pratinjau (preview) foto bukti absensi.

### 🛡️ Modul Administrator
- **Dashboard Ringkasan & Statistik**:
  - Metrik statistik harian dan periode: **Total Hadir**, **Tepat Waktu**, **Terlambat**, dan **Total Izin**.
- **Rekapitulasi Global & Filter Lengkap**:
  - Melihat dan memantau absensi seluruh karyawan.
  - Filter pencarian berdasarkan nama karyawan dan rentang tanggal (dari - sampai).
- **Manajemen Status & Catat Izin**:
  - Admin dapat mencatat izin karyawan secara langsung pada tanggal tertentu beserta catatan keterangan.
  - Fleksibilitas mengubah status absensi (Tepat Waktu / Telat / Izin) sewaktu-waktu jika terdapat penyesuaian khusus.
- **Pengaturan Jam Masuk Fleksibel**:
  - Form konfigurasi jam batas masuk kantor secara dinamis tanpa perlu mengubah baris kode program.
- **Manajemen Akun Karyawan**:
  - Tambah karyawan baru dengan **Username unik** (nama pengguna).
  - Alamat email bersifat opsional (fleksibel untuk karyawan lapangan yang belum memiliki email perusahaan).

### 🔒 Keamanan & Otentikasi
- **Dual-Login Identifier**: Karyawan dan admin dapat login menggunakan **Username** ataupun **Email**.
- **Proteksi Brute-Force**: Dilengkapi *rate limiting* untuk mencegah serangan tebakan kata sandi.
- **Role-Based Access Control (RBAC)**: Pembatasan akses halaman admin melalui middleware `EnsureUserIsAdmin`.
- **Database Transaction & File Rollback**: Menjamin konsistensi database dan integritas file penyimpanan saat proses upload foto absensi.

---

## 🛠️ Tech Stack & Arsitektur

- **Backend**: [PHP 8.3+](https://www.php.net/), [Laravel Framework](https://laravel.com/)
- **Database**: MySQL / MariaDB
- **Frontend**: Blade Templating, [Tailwind CSS](https://tailwindcss.com/), Vanilla JS (Webcam API)
- **Kode & Standar**:
  - **Laravel Pint**: Kode terformat rapi sesuai PSR-12 / Laravel standards.
  - **PHPUnit**: Dilengkapi unit & feature test komprehensif (**100% Passed: 36 Tests, 132 Assertions**).
  - **Clean Architecture Pattern**:
    - **Enums**: `Role` (`admin`, `karyawan`), `StatusAbsensi` (`tepat_waktu`, `telat`, `izin`).
    - **Actions**: `HitungKeterlambatan`, `HitungRingkasanAbsensi`, `SimpanUserAction`.
    - **Services**: `CatatAbsensiService`, `CatatAbsenPulangService`.
    - **Form Requests**: Validasi request terisolasi (`LoginRequest`, `SimpanAbsensiRequest`, `SimpanUserRequest`, `UpdateJamAbsenRequest`).

---

## 📋 Prasyarat Sistem

Sebelum menjalankan aplikasi, pastikan komputer/server Anda telah terpasang:
- PHP >= 8.3 dengan ekstensi: `bcmath`, `ctype`, `curl`, `dom`, `fileinfo`, `json`, `mbstring`, `openssl`, `pcre`, `pdo_mysql`, `tokenizer`, `xml`.
- [Composer](https://getcomposer.org/)
- [Node.js & NPM](https://nodejs.org/)
- Server Database MySQL / MariaDB

---

## ⚙️ Panduan Instalasi Lokal

1. **Clone Repository**
   ```bash
   git clone https://github.com/kyybogor/Absensi.git
   cd Absensi
   ```

2. **Install Dependensi PHP & JavaScript**
   ```bash
   composer install
   npm install
   ```

3. **Konfigurasi Environment (`.env`)**
   Salin file template `.env.example` menjadi `.env`:
   ```bash
   cp .env.example .env
   ```
   Buka file `.env` dan sesuaikan pengaturan database Anda:
   ```env
   APP_NAME="Absensi Karyawan"
   APP_ENV=local
   APP_KEY=
   APP_DEBUG=true
   APP_URL=http://localhost:8000

   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=absensi
   DB_USERNAME=root
   DB_PASSWORD=
   ```

4. **Generate Application Key**
   ```bash
   php artisan key:generate
   ```

5. **Migrasi Database & Seeder**
   Jalankan migrasi untuk membuat tabel database beserta akun bawaan:
   ```bash
   php artisan migrate --seed
   ```

6. **Buat Storage Link**
   ```bash
   php artisan storage:link
   ```

7. **Compile Asset Frontend**
   ```bash
   npm run build
   # Atau untuk mode pengembangan (hot reload):
   # npm run dev
   ```

8. **Jalankan Web Server Lokal**
   ```bash
   php artisan serve
   ```
   Aplikasi siap diakses di browser melalui: `http://localhost:8000`

---

## 🔑 Akun Default (Seeder)

Setelah menjalankan `php artisan migrate --seed`, akun bawaan berikut siap digunakan:

| Peran | Username | Email | Password | Hak Akses |
|---|---|---|---|---|
| **Administrator** | `admin` | `admin@example.com` | `password` | Mengelola rekap, mengatur jam, mencatat izin, kelola akun |
| **Karyawan** | `karyawan` | `karyawan@example.com` | `password` | Absensi masuk/pulang, rekap pribadi |

---

## 🧪 Menjalankan Pengujian (Testing)

Proyek ini telah dilengkapi dengan rangkaian automated feature tests untuk menguji fungsionalitas kritis:
```bash
# Menjalankan PHPUnit test
php vendor/bin/phpunit

# Memeriksa dan memformat standarisasi kode
vendor/bin/pint --format agent
```

---

## 🌐 Panduan Deployment di Shared Hosting (cPanel / Hostinger LiteSpeed)

Aplikasi ini sudah dipersiapkan secara khusus untuk environment shared hosting yang document root-nya mengarah ke root project:

1. **Bridge Index & Rewrite**:
   - File [index.php](file:///d:/absensi/index.php) dan [.htaccess](file:///d:/absensi/.htaccess) di root folder sudah dikonfigurasi untuk meneruskan request ke folder `public/` dengan proteksi ketat pada file sensitif (`.env`, `storage/app/private`, `storage/logs`).
2. **Penyimpanan Foto pada Server Tanpa Dukungan `symlink()`**:
   - Jika hosting menonaktifkan fungsi PHP `symlink()` dan `exec()`, konfigurasi disk `public` di `config/filesystems.php` telah disesuaikan agar mengarah ke `public_path('storage')`. Cukup buat folder `storage/absensi` di dalam folder `public/`.

---

## 📄 Lisensi

Aplikasi ini bersifat open-source dan berada di bawah lisensi [MIT License](LICENSE).
Dikembangkan oleh **[kyybogor](https://github.com/kyybogor)**.

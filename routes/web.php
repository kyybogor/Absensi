<?php

use App\Http\Controllers\AbsensiController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\JamAbsenController;
use App\Http\Controllers\RekapAbsensiController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Redirect root ke halaman absen jika sudah login, atau ke halaman login jika guest
Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('absensi.create')
        : redirect()->route('login');
});

// Autentikasi Guest
Route::middleware('guest')->group(function (): void {
    Route::get('/login', [AuthController::class, 'create'])->name('login');
    Route::post('/login', [AuthController::class, 'store'])->name('login.store');
});

// Autentikasi Pengguna Terdaftar
Route::middleware('auth')->group(function (): void {
    Route::post('/logout', [AuthController::class, 'destroy'])->name('logout');

    // Halaman Absensi Karyawan
    Route::get('/absen', [AbsensiController::class, 'create'])->name('absensi.create');
    Route::post('/absen', [AbsensiController::class, 'store'])->name('absensi.store');
    Route::post('/absen/pulang', [AbsensiController::class, 'storePulang'])->name('absensi.pulang');

    // Rekap Absensi (karyawan miliknya sendiri, admin semua karyawan)
    Route::get('/rekap', [RekapAbsensiController::class, 'index'])->name('rekap.index');

    // Menu Khusus Admin
    Route::middleware('admin')->prefix('admin')->group(function (): void {
        // Tambah dan Daftar Akun
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');

        // Atur Jam Absen
        Route::get('/jam-absen', [JamAbsenController::class, 'edit'])->name('jam-absen.edit');
        Route::put('/jam-absen', [JamAbsenController::class, 'update'])->name('jam-absen.update');

        // Kelola Status Absensi & Izin Karyawan
        Route::patch('/absensi/{absensi}/status', [RekapAbsensiController::class, 'updateStatus'])->name('admin.absensi.update-status');
        Route::post('/absensi/izin', [RekapAbsensiController::class, 'storeIzin'])->name('admin.absensi.store-izin');
    });
});

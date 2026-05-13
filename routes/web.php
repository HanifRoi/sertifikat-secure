<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SertifikatController;
use App\Http\Controllers\KriptografiController;
use App\Http\Controllers\VerifikasiController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AuthController;

// =========================================
// JALUR TAMU (Belum Login)
// =========================================
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.process');
});

// =========================================
// JALUR PUBLIK (Tanpa Login)
// =========================================
Route::get('/verify/{token}', [VerifikasiController::class, 'index'])->name('verify.index');
Route::post('/verify/process', [VerifikasiController::class, 'process'])->name('verify.process');

// UBAH BARIS INI: Menjadi gerbang utama Scanner
Route::view('/scan', 'verifikasi.scan')->name('verify.scan');


// =========================================
// JALUR ADMIN (Wajib Login!)
// =========================================
Route::middleware('auth')->group(function () {
    
    // 1. Dashboard Utama
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // 2. Proses Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // 3. Modul Kriptografi (Manajemen Kunci Master)
    Route::get('/kriptografi', [KriptografiController::class, 'index'])->name('kriptografi.index');
    Route::post('/kriptografi/generate', [KriptografiController::class, 'generate'])->name('kriptografi.generate');

    // 4. Modul Sertifikat (Riwayat, Pembuatan, Pencabutan, Unduh)
    Route::get('/riwayat-sertifikat', [SertifikatController::class, 'index'])->name('sertifikat.index');
    Route::get('/sertifikat/create', [SertifikatController::class, 'create'])->name('sertifikat.create');
    Route::post('/sertifikat/store', [SertifikatController::class, 'store'])->name('sertifikat.store');
    Route::post('/sertifikat/{id}/revoke', [SertifikatController::class, 'revoke'])->name('sertifikat.revoke');
    Route::get('/download-sertifikat/{filename}', [SertifikatController::class, 'downloadFile'])->name('sertifikat.download');

});
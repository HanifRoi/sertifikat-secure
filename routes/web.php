<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SertifikatController;
use App\Http\Controllers\KriptografiController;
use App\Http\Controllers\VerifikasiController;

Route::get('/', function () {
    return view('dashboard');
});
Route::get('/tambah-sertifikat', [SertifikatController::class, 'create']);
// Yang kemarin sudah ada (untuk nampilin form)
Route::get('/tambah-sertifikat', [SertifikatController::class, 'create']);

// TAMBAHKAN BARIS INI (untuk nerima data form saat disubmit)
Route::post('/tambah-sertifikat', [SertifikatController::class, 'store']);
// Menampilkan halaman manajemen kunci
Route::get('/kriptografi', [KriptografiController::class, 'index']);

// Memproses pembuatan kunci master baru
Route::post('/kriptografi/generate', [KriptografiController::class, 'generate']);
// Route untuk download sertifikat final yang aman
Route::get('/download-sertifikat/{filename}', [App\Http\Controllers\SertifikatController::class, 'downloadFile'])->name('sertifikat.download');
Route::get('/verify/{token}', [VerifikasiController::class, 'index'])->name('verify.index');
// Jalur untuk memproses uji kriptografi dokumen
Route::post('/verify/process', [VerifikasiController::class, 'process'])->name('verify.process');
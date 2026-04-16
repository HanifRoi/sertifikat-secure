<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SertifikatController;

Route::get('/', function () {
    return view('dashboard');
});
Route::get('/tambah-sertifikat', [SertifikatController::class, 'create']);
// Yang kemarin sudah ada (untuk nampilin form)
Route::get('/tambah-sertifikat', [SertifikatController::class, 'create']);

// TAMBAHKAN BARIS INI (untuk nerima data form saat disubmit)
Route::post('/tambah-sertifikat', [SertifikatController::class, 'store']);
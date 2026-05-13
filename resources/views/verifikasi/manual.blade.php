@extends('layouts.app')

@section('title', 'Simulator Verifikasi QC')

@section('content')
<div class="max-w-3xl mx-auto mt-16 text-center">
    
    <div class="mb-10">
        <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-indigo-100 text-indigo-600 mb-6 text-4xl shadow-inner">
            <i class="fa-solid fa-qrcode"></i>
        </div>
        <h1 class="text-3xl font-extrabold text-slate-800 mb-3">
            Gerbang Verifikasi Manual
        </h1>
        <p class="text-gray-500 leading-relaxed">
            Tidak memiliki alat pemindai QR Code? <br>
            Silakan masukkan <strong class="text-indigo-600">15 Karakter Token</strong> Kriptografi yang tertera pada URL sertifikat untuk masuk ke Laboratorium Verifikasi.
        </p>
    </div>

    <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 max-w-lg mx-auto">
        <form onsubmit="event.preventDefault(); window.location.href='/verify/' + document.getElementById('token').value;" class="flex flex-col gap-4">
            
            <div class="text-left">
                <label for="token" class="block text-sm font-semibold text-gray-700 mb-2">Token Kriptografi (ID Laci)</label>
                <input type="text" id="token" name="token" placeholder="Contoh: a1b2c3d4e5f6g7h" required autocomplete="off" minlength="15" maxlength="15"
                    class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition font-mono text-lg text-center tracking-widest placeholder:tracking-normal placeholder:text-base">
                <p class="text-xs text-gray-400 mt-2 text-center"><i class="fa-solid fa-circle-info"></i> Token terdiri dari 15 karakter acak.</p>
            </div>

            <button type="submit" class="w-full bg-slate-900 hover:bg-slate-800 text-white font-bold py-3 px-4 rounded-xl shadow transition-transform transform hover:-translate-y-0.5 mt-2">
                <i class="fa-solid fa-magnifying-glass mr-2"></i> Cari & Verifikasi
            </button>
            
        </form>
    </div>

</div>
@endsection
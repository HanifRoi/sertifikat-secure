@extends('layouts.app')

@section('title', 'Sertifikat Berhasil Diamankan')

@section('content')
<div class="max-w-3xl mx-auto bg-white p-8 rounded-xl shadow-sm border border-gray-100 text-center">
    <div class="w-16 h-16 bg-green-100 text-green-600 rounded-full flex items-center justify-center mx-auto mb-4 text-2xl">
        <i class="fa-solid fa-check"></i>
    </div>
    <h2 class="text-2xl font-bold text-gray-800 mb-2">Dokumen Berhasil Ditandatangani!</h2>
    <p class="text-gray-500 mb-8">Digital Signature berbasis RSA dan SHA3-512 telah berhasil di-generate menjadi QR Code.</p>

    <div class="bg-gray-50 p-6 rounded-lg inline-block border border-gray-200 mb-8">
        <div class="p-4 bg-white rounded-lg shadow-sm">
            {!! $qrCode !!}
        </div>
        <p class="text-sm font-bold text-gray-700 mt-4">Scan untuk Verifikasi</p>
    </div>

    <div class="text-left bg-slate-900 rounded-lg p-4 overflow-x-auto">
        <p class="text-xs text-slate-400 mb-1">Nilai Hash Asli (SHA3-512):</p>
        <code class="text-emerald-400 text-xs">{{ $hash }}</code>
        
        <p class="text-xs text-slate-400 mt-4 mb-1">Digital Signature (Base64):</p>
        <code class="text-indigo-300 text-xs">{{ $signature }}</code>
    </div>
   <div class="mt-6 mb-8 text-center">
        <a href="{{ $downloadLink }}" 
           class="inline-flex items-center gap-2 bg-emerald-500 hover:bg-emerald-600 text-white font-bold py-3 px-8 rounded-full shadow-lg transition-transform transform hover:scale-105">
            <i class="fa-solid fa-download"></i> Download Sertifikat (PDF)
        </a>
    </div>

    <div class="mt-8">
        <a href="/tambah-sertifikat" class="text-indigo-600 font-bold hover:underline">
            <i class="fa-solid fa-arrow-left mr-2"></i> Kembali Tambah Sertifikat
        </a>
    </div>
</div>
@endsection
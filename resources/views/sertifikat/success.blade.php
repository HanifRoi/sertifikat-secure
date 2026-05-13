@extends('layouts.app')

@section('title', 'Penerbitan Sukses')

@section('content')
<div class="max-w-4xl mx-auto mt-8 text-center">
    
    <div class="mb-6">
        <div class="inline-flex items-center justify-center w-24 h-24 rounded-full bg-emerald-100 text-emerald-500 mb-4 shadow-inner">
            <i class="fa-solid fa-check text-5xl"></i>
        </div>
        <h1 class="text-3xl font-extrabold text-slate-800">Sertifikat Berhasil Diamankan!</h1>
        <p class="text-gray-500 mt-2">Dokumen atas nama <strong class="text-indigo-600">{{ $peserta }}</strong> telah disisipi Kriptografi RSA & SHA3-512.</p>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden text-left mb-6">
        <div class="grid grid-cols-1 md:grid-cols-2">
            
            <div class="bg-slate-50 p-8 flex flex-col items-center justify-center border-b md:border-b-0 md:border-r border-gray-100">
                <p class="text-sm font-bold text-slate-500 uppercase tracking-wider mb-4">Stempel QR Code</p>
                <div class="bg-white p-3 rounded-xl shadow-sm border border-gray-200">
                    {!! $qrCode !!}
                </div>
                <p class="text-xs text-gray-400 mt-4 text-center">QR ini telah otomatis tertempel<br>pada file PDF.</p>
            </div>

            <div class="p-8 flex flex-col justify-center">
                
                <div class="mb-6">
                    <p class="text-sm font-bold text-slate-800 mb-2">Token Verifikasi Manual</p>
                    <div class="flex items-center">
                        <input type="text" id="tokenField" value="{{ $token }}" readonly 
                            class="bg-indigo-50 border border-indigo-100 text-indigo-700 font-mono font-bold text-lg rounded-l-lg py-2 px-4 w-full focus:outline-none cursor-text tracking-widest">
                        <button onclick="copyToken()" class="bg-indigo-600 hover:bg-indigo-700 text-white py-2 px-4 rounded-r-lg transition border border-indigo-600 h-full">
                            <i class="fa-regular fa-copy" id="copyIcon"></i>
                        </button>
                    </div>
                    <p class="text-xs text-indigo-400 mt-1" id="copyText">Gunakan token ini untuk tes di Simulator Verifikasi.</p>
                </div>

                <div class="space-y-4">
                    <div>
                        <p class="text-xs font-bold text-gray-400 uppercase">Dokumen Hash (SHA3-512)</p>
                        <p class="text-xs font-mono text-gray-600 bg-gray-50 p-2 rounded border border-gray-100 truncate" title="{{ $hash }}">{{ substr($hash, 0, 30) }}...</p>
                    </div>

                    <div>
                        <p class="text-xs font-bold text-gray-400 uppercase">Digital Signature (RSA Base64)</p>
                        <p class="text-xs font-mono text-gray-600 bg-gray-50 p-2 rounded border border-gray-100 truncate" title="{{ $signature }}">{{ substr($signature, 0, 30) }}...</p>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden text-left mb-8">
        <div class="bg-purple-50 border-b border-purple-100 p-4">
            <h2 class="text-sm font-bold text-purple-900 uppercase tracking-wider">
                <i class="fa-solid fa-stopwatch mr-2"></i> Data Pengujian Kriptografi 
            </h2>
        </div>
        <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
            
            <div class="space-y-5">
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase mb-1">Kecepatan Hashing (SHA3-512)</p>
                    <p class="font-mono text-emerald-600 font-extrabold text-2xl">{{ $waktuHashing }} <span class="text-sm text-gray-500 font-sans font-normal">detik</span></p>
                </div>
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase mb-1">Kecepatan Signing (RSA-2048)</p>
                    <p class="font-mono text-blue-600 font-extrabold text-2xl">{{ $waktuRsa }} <span class="text-sm text-gray-500 font-sans font-normal">detik</span></p>
                </div>
            </div>

            <div class="flex flex-col h-full">
                <p class="text-xs font-bold text-gray-400 uppercase mb-2">Hash Biner 512-bit (Uji Avalanche Effect)</p>
                <textarea id="binerField" readonly class="w-full flex-grow text-xs font-mono text-gray-600 bg-gray-50 p-3 rounded-lg border border-gray-200 focus:outline-none focus:ring-2 focus:ring-purple-200 resize-none" onclick="this.select()">{{ $hashBiner }}</textarea>
                <p class="text-[10px] text-gray-400 mt-2 text-right"><i class="fa-solid fa-circle-info mr-1"></i> Klik area teks untuk menyeleksi seluruh bit.</p>
            </div>

        </div>
    </div>

    <div class="flex justify-center gap-4">
        <a href="{{ route('sertifikat.create') }}" class="bg-white hover:bg-gray-50 text-slate-700 font-bold py-3 px-6 rounded-xl border border-gray-200 shadow-sm transition">
            <i class="fa-solid fa-arrow-left mr-2"></i> Kembali
        </a>
        <a href="{{ $downloadLink }}" class="bg-slate-900 hover:bg-slate-800 text-white font-bold py-3 px-6 rounded-xl shadow transition">
            <i class="fa-solid fa-file-pdf mr-2"></i> Download PDF Tersertifikasi
        </a>
    </div>

</div>

<script>
    function copyToken() {
        var copyText = document.getElementById("tokenField");
        copyText.select();
        copyText.setSelectionRange(0, 99999); /* Untuk mobile */
        navigator.clipboard.writeText(copyText.value);
        
        // Ubah ikon dan teks sementara
        var icon = document.getElementById("copyIcon");
        var text = document.getElementById("copyText");
        
        icon.classList.remove("fa-copy", "fa-regular");
        icon.classList.add("fa-check", "fa-solid");
        text.innerText = "Token berhasil disalin!";
        text.classList.remove("text-indigo-400");
        text.classList.add("text-emerald-500");

        setTimeout(function() {
            icon.classList.remove("fa-check", "fa-solid");
            icon.classList.add("fa-copy", "fa-regular");
            text.innerText = "Gunakan token ini untuk tes di Simulator Verifikasi.";
            text.classList.remove("text-emerald-500");
            text.classList.add("text-indigo-400");
        }, 3000);
    }
</script>
@endsection
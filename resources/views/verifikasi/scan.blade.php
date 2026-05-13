@extends('layouts.app')

@section('title', 'Portal Verifikasi Kriptografi')

@section('content')
<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>

<div class="max-w-4xl mx-auto mt-8 text-center">
    
    <div class="mb-8">
        <h1 class="text-3xl font-extrabold text-slate-800 mb-3">
            <i class="fa-solid fa-qrcode text-indigo-600 mr-2"></i> Verifikasi Dokumen
        </h1>
        <p class="text-gray-500">Menguji keaslian dokumen.</p>
    </div>

    <div class="grid grid-cols-1 {{ Auth::check() && Auth::user()->role == 'admin' ? 'md:grid-cols-2' : 'max-w-md mx-auto' }} gap-8">
        
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex flex-col items-center justify-center">
            <h2 class="text-lg font-bold text-slate-800 mb-4"><i class="fa-solid fa-camera mr-2"></i> Scan QR Code</h2>
            
            <div id="reader" class="w-full max-w-sm rounded-lg overflow-hidden border-2 border-indigo-100 shadow-inner"></div>
            
            <p class="text-xs text-gray-400 mt-4 text-center">Arahkan kamera ke QR Code atau gunakan tombol upload di atas area kamera.</p>
        </div>

        @if(Auth::check() && Auth::user()->role == 'admin')
        <div class="bg-slate-900 p-8 rounded-2xl shadow-sm border border-slate-800 flex flex-col justify-center text-left relative overflow-hidden">
            <div class="absolute -right-10 -bottom-10 w-40 h-40 bg-indigo-500 rounded-full opacity-20 blur-2xl"></div>

            <h2 class="text-lg font-bold text-white mb-2 relative z-10"><i class="fa-solid fa-keyboard mr-2"></i> QC: Input Manual</h2>
            <p class="text-sm text-gray-400 mb-6 relative z-10">Fitur khusus Admin untuk pengujian sistem tanpa scan fisik.</p>

            <form onsubmit="event.preventDefault(); prosesManual();" class="relative z-10 flex flex-col gap-3">
                <div>
                    <label for="tokenManual" class="block text-xs font-semibold text-gray-400 mb-1 uppercase tracking-wider">Token Kriptografi</label>
                    <input type="text" id="tokenManual" placeholder="Masukkan 15 karakter..." required autocomplete="off" minlength="15" maxlength="15"
                        class="w-full px-4 py-3 rounded-xl bg-slate-800 border border-slate-700 text-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition font-mono tracking-widest text-center">
                </div>
                <button type="submit" class="w-full bg-purple-700 hover:bg-purple-500 text-white font-bold py-3 px-4 rounded-xl shadow transition mt-2">
                    Cek Token <i class="fa-solid fa-arrow-right ml-1"></i>
                </button>
            </form>
        </div>
        @endif

    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Fungsi jika QR berhasil terbaca
        function onScanSuccess(decodedText, decodedResult) {
            // Hentikan kamera agar tidak scan berkali-kali
            html5QrcodeScanner.clear(); 
            
            // Tampilkan efek loading sederhana
            document.getElementById('reader').innerHTML = '<div class="p-8 text-center"><i class="fa-solid fa-circle-notch fa-spin text-4xl text-indigo-600 mb-4"></i><p class="text-sm font-bold text-gray-700">QR Terdeteksi! Mengalihkan...</p></div>';
            
            // DecodedText berisi URL lengkap, langsung arahkan browser ke sana.
            window.location.href = decodedText;
        }

        // Inisialisasi Mesin Scanner
        let html5QrcodeScanner = new Html5QrcodeScanner(
            "reader", 
            { 
                fps: 10, 
                qrbox: {width: 250, height: 250},
                rememberLastUsedCamera: true
            }, 
            /* verbose= */ false
        );
        
        // Jalankan Scanner
        html5QrcodeScanner.render(onScanSuccess);
    });

    // Fungsi untuk form input manual (Dilindungi agar tidak error jika elemen tidak ada)
    function prosesManual() {
        let tokenInput = document.getElementById('tokenManual');
        if(tokenInput && tokenInput.value) {
            window.location.href = '/verify/' + tokenInput.value;
        }
    }
</script>

<style>
    #reader button {
        background-color: #8424D6; color: white; border: none; padding: 8px 16px; border-radius: 8px; font-weight: bold; cursor: pointer; transition: 0.3s; margin: 4px; font-size: 14px;
    }
    #reader button:hover { background-color: #9c57d8; }
    #reader a { color: #4f46e5; text-decoration: none; font-size: 14px; }
    #reader select { padding: 8px; border-radius: 8px; border: 1px solid #e5e7eb; margin-bottom: 10px; width: 80%; }
</style>
@endsection
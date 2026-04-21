@extends('layouts.app')

@section('title', 'Laboratorium Verifikasi Sertifikat')

@section('content')
<div class="max-w-5xl mx-auto mt-8">
    
    <div class="text-center mb-10">
        <h1 class="text-3xl font-extrabold text-slate-800">
            <i class="fa-solid fa-shield-halved text-indigo-600 mr-2"></i> Verifikasi Keaslian Sertifikat
        </h1>
        <p class="text-gray-500 mt-2">Sistem Pengecekan Kriptografi SHA3-512 & Asimetris RSA</p>
    </div>

    @if (session('error'))
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded mb-6 shadow-sm flex items-center">
            <i class="fa-solid fa-circle-xmark text-2xl mr-3"></i>
            <div>
                <p class="font-bold">Verifikasi Gagal!</p>
                <p class="text-sm">{{ session('error') }}</p>
            </div>
        </div>
    @endif

    @if (session('success'))
        <div class="bg-emerald-100 border-l-4 border-emerald-500 text-emerald-800 p-4 rounded mb-6 shadow-sm flex items-center">
            <i class="fa-solid fa-circle-check text-2xl mr-3"></i>
            <div>
                <p class="font-bold">Sertifikat Valid!</p>
                <p class="text-sm">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="bg-indigo-50 border-b border-indigo-100 p-5">
                <h2 class="text-lg font-bold text-indigo-900">
                    <i class="fa-solid fa-database mr-2"></i> Data Rekam Jejak Sistem
                </h2>
            </div>
            <div class="p-6 space-y-5">
                <div>
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Nama Peserta</p>
                    <p class="font-bold text-gray-800 text-xl">{{ $certificate->participant->name }}</p>
                </div>
                <div>
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Nomor Identitas (NIM/NIS)</p>
                    <p class="font-medium text-gray-700">{{ $certificate->participant->identifier_number }}</p>
                </div>
                <div>
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Program / Kegiatan</p>
                    <p class="font-medium text-gray-700">{{ $certificate->program }}</p>
                </div>
                <div>
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Instansi Asal</p>
                    <p class="font-medium text-gray-700">{{ $certificate->participant->institution }}</p>
                </div>
                <div class="pt-4 border-t border-gray-100">
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Tanggal Diterbitkan</p>
                    <p class="font-medium text-slate-800">{{ \Carbon\Carbon::parse($certificate->publish_date)->translatedFormat('d F Y') }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="bg-slate-50 border-b border-slate-200 p-5">
                <h2 class="text-lg font-bold text-slate-800">
                    <i class="fa-solid fa-microscope mr-2"></i> Uji Klinis Dokumen
                </h2>
            </div>
            <div class="p-6 flex flex-col justify-between h-[calc(100%-4rem)]">
                <div>
                    <p class="text-sm text-gray-600 mb-6 leading-relaxed">
                        Data di sebelah kiri hanya membuktikan sertifikat pernah diterbitkan. Untuk membuktikan bahwa file PDF yang Anda pegang adalah <strong>100% Asli</strong> dan isinya belum dimodifikasi oknum, silakan upload dokumen Anda.
                    </p>

                    <form action="/verify/process" method="POST" enctype="multipart/form-data" id="form-verifikasi">
                        @csrf
                        <input type="hidden" name="token" value="{{ $token }}">
                        
                        <div class="border-2 border-dashed border-indigo-200 rounded-xl p-8 text-center hover:bg-indigo-50 transition cursor-pointer mb-6" onclick="document.getElementById('file_pdf').click()">
                            <i class="fa-regular fa-file-pdf text-5xl text-red-400 mb-4"></i>
                            <p class="text-sm font-semibold text-indigo-700 mb-1">Klik untuk memilih file PDF</p>
                            <p class="text-xs text-gray-400">Atau seret dan lepas file di sini</p>
                            <input type="file" id="file_pdf" name="file_pdf" accept="application/pdf" required class="hidden">
                        </div>
                </div>

                        <button type="submit" class="w-full bg-slate-900 hover:bg-slate-800 text-white font-bold py-4 px-4 rounded-xl shadow-lg transition-transform transform hover:-translate-y-0.5 flex justify-center items-center gap-2">
                            <i class="fa-solid fa-fingerprint text-xl"></i> Mulai Verifikasi Keaslian
                        </button>
                    </form>
            </div>
        </div>

    </div>
</div>

<script>
    // Script kecil untuk mengubah teks saat file PDF sudah dipilih
    document.getElementById('file_pdf').addEventListener('change', function(e) {
        if(e.target.files.length > 0) {
            const fileName = e.target.files[0].name;
            const dropzoneText = this.previousElementSibling.previousElementSibling;
            dropzoneText.innerHTML = `File terpilih:<br><span class="text-slate-800 font-bold">${fileName}</span>`;
            dropzoneText.classList.remove('text-indigo-700');
            dropzoneText.classList.add('text-emerald-600');
        }
    });
</script>
@endsection
@extends('layouts.app')

@section('title', 'Verifikasi Sertifikat')

@section('content')
<div class="max-w-5xl mx-auto mt-8">
    
    <div class="text-center mb-10">
        <h1 class="text-3xl font-extrabold text-slate-800">
            <i class="fa-solid fa-shield-halved text-indigo-600 mr-2"></i> Verifikasi Keaslian Sertifikat
        </h1>
        <p class="text-gray-500 mt-2">Sistem Pengecekan Keaslian Sertifikat</p>
    </div>

    @if (session('error'))
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded mb-6 shadow-sm flex items-center">
            <i class="fa-solid fa-circle-xmark text-2xl mr-3"></i>
            <div>
                <p class="font-bold">Verifikasi Gagal!</p>
                <p class="text-sm">{{ session('error') }}</p>
            </div>
        </div>
    @elseif (session('success'))
        <div class="bg-emerald-100 border-l-4 border-emerald-500 text-emerald-800 p-4 rounded mb-6 shadow-sm flex items-center">
            <i class="fa-solid fa-shield-check text-2xl mr-3"></i>
            <div>
                <p class="font-bold">Dokumen 100% Valid & Asli!</p>
                <p class="text-sm">{{ session('success') }}</p>
            </div>
        </div>
    @else
        <div class="bg-blue-100 border-l-4 border-blue-500 text-blue-800 p-4 rounded mb-6 shadow-sm flex items-center">
            <i class="fa-solid fa-circle-info text-2xl mr-3 animate-pulse"></i>
            <div>
                <p class="font-bold">Data Ditemukan di Database!</p>
                <p class="text-sm">Riwayat sertifikat terdaftar. Silakan lakukan <strong>Uji Dokumen</strong> di bawah untuk membuktikan keaslian file PDF.</p>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="bg-indigo-50 border-b border-indigo-100 p-5">
                <h2 class="text-lg font-bold text-purple-900">
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
    <p class="font-medium text-gray-700">{{ $certificate->participant?->program ?? '-' }}</p>
</div>
                <div>
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Instansi Asal</p>
                    <p class="font-medium text-gray-700">{{ $certificate->participant->institution }}</p>
                </div>
                <div class="pt-4 border-t border-gray-100">
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Tanggal Diterbitkan</p>
                    <p class="font-medium text-slate-800">{{ \Carbon\Carbon::parse($certificate->publish_date)->translatedFormat('d F Y') }}</p>
                </div>
                
                @php
                    // Decode JSON grades dari database menjadi array
                    $grades = is_string($certificate->grades) ? json_decode($certificate->grades, true) : $certificate->grades;
                    $rataRata = ($grades && count($grades) > 0) ? array_sum($grades) / count($grades) : 0;
                    
                    $predikat = '';
                    if ($rataRata > 90) { $predikat = 'Amat Baik'; }
                    elseif ($rataRata >= 75) { $predikat = 'Baik'; }
                    elseif ($rataRata >= 65) { $predikat = 'Cukup'; }
                    else { $predikat = 'Kurang'; }
                @endphp
                
                <div class="pt-4 border-t border-gray-100">
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Hasil Penilaian Akhir</p>
                    <p class="font-medium text-slate-800 mt-1">
                        <span class="text-2xl font-black text-indigo-600">{{ number_format($rataRata, 1) }}</span> 
                        <span class="text-sm font-bold text-gray-500 bg-gray-100 px-2 py-1 rounded ml-1">{{ $predikat }}</span>
                    </p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="bg-slate-50 border-b border-slate-200 p-5">
                <h2 class="text-lg font-bold text-green-600">
                    <i class="fa-solid fa-microscope mr-2"></i> Validasi Keutuhan Dokumen
                </h2>
            </div>
            <div class="p-6 flex flex-col justify-between h-[calc(100%-4rem)]">
                
                <form action="/verify/process" method="POST" enctype="multipart/form-data" id="form-verifikasi" class="flex flex-col h-full justify-between">
                    @csrf
                    <input type="hidden" name="token" value="{{ $token }}">
                    
                    <div>
                        <div class="bg-blue-50 border border-blue-100 text-blue-800 p-4 rounded-xl mb-6 text-sm leading-relaxed">
                            <p class="mb-2">
                                Data riwayat di samping mengonfirmasi bahwa Cazh Academy <strong>telah menerbitkan</strong> sertifikat tersebut.
                            </p>
                            <p>
                                Sebagai standar keamanan tambahan, kami merekomendasikan Anda untuk memastikan bahwa file PDF yang Anda terima <strong>tidak mengalami perubahan isi atau manipulasi nilai</strong>. Silakan unggah dokumen tersebut di bawah ini untuk pengujian sidik jari digital (Kriptografi).
                            </p>
                        </div>

                        <div id="drop-area" class="border-2 border-dashed border-indigo-200 rounded-xl p-8 text-center hover:bg-indigo-50 transition cursor-pointer mb-6 group" onclick="document.getElementById('file_pdf').click()">
                            <i class="fa-regular fa-file-pdf text-5xl text-indigo-300 group-hover:text-indigo-500 transition mb-4"></i>
                            <p class="text-sm font-semibold text-slate-700 mb-1">Klik di sini untuk memilih file PDF</p>
                            <p class="text-xs text-gray-400">Atau seret dan lepas file ke area ini</p>
                            <input type="file" id="file_pdf" name="file_pdf" accept="application/pdf" required class="hidden">
                        </div>
                    </div>

                    <button type="submit" id="btnSubmitVerify" disabled class="w-full mt-auto bg-slate-900 text-white font-bold py-4 px-4 rounded-xl shadow-md transition-all duration-300 flex justify-center items-center gap-2 opacity-50 cursor-not-allowed">
                        <span id="btnTextVerify" class="flex items-center gap-2">
                            <i class="fa-solid fa-shield-check text-xl text-emerald-400"></i> Jalankan Uji Keaslian Dokumen
                        </span>
                    </button>
                </form>
                
            </div>
        </div>

    </div>
</div>

<script>
    const fileInput = document.getElementById('file_pdf');
    const dropArea = document.getElementById('drop-area');
    const formVerifikasi = document.getElementById('form-verifikasi');
    const btnSubmitVerify = document.getElementById('btnSubmitVerify');
    const btnTextVerify = document.getElementById('btnTextVerify');

    // 1. Script untuk mengubah teks saat file PDF sudah dipilih (klik atau drop)
    fileInput.addEventListener('change', function(e) {
        if(e.target.files.length > 0) {
            const fileName = e.target.files[0].name;
            const dropzoneText = this.previousElementSibling.previousElementSibling;
            dropzoneText.innerHTML = `File terpilih:<br><span class="text-slate-800 font-bold">${fileName}</span>`;
            dropzoneText.classList.remove('text-indigo-300', 'group-hover:text-indigo-500');
            dropzoneText.classList.add('text-emerald-500');

            // PERUBAHAN DI SINI: Menghidupkan tombol saat file sudah terpilih
            btnSubmitVerify.disabled = false;
            btnSubmitVerify.classList.remove('opacity-50', 'cursor-not-allowed');
            btnSubmitVerify.classList.add('hover:bg-slate-800', 'hover:-translate-y-0.5', 'cursor-pointer');
        } else {
            // Jika user batal memilih file (klik cancel), matikan lagi tombolnya
            btnSubmitVerify.disabled = true;
            btnSubmitVerify.classList.add('opacity-50', 'cursor-not-allowed');
            btnSubmitVerify.classList.remove('hover:bg-slate-800', 'hover:-translate-y-0.5', 'cursor-pointer');
        }
    });

    // 2. Mencegah browser membuka PDF di tab baru
    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        dropArea.addEventListener(eventName, preventDefaults, false);
    });

    function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }

    // 3. Efek visual saat file diseret ke atas kotak
    ['dragenter', 'dragover'].forEach(eventName => {
        dropArea.addEventListener(eventName, () => {
            dropArea.classList.add('bg-indigo-50');
            dropArea.classList.add('border-indigo-400');
        }, false);
    });

    ['dragleave', 'drop'].forEach(eventName => {
        dropArea.addEventListener(eventName, () => {
            dropArea.classList.remove('bg-indigo-50');
            dropArea.classList.remove('border-indigo-400');
        }, false);
    });

    // 4. Menangkap file yang dijatuhkan (drop)
    dropArea.addEventListener('drop', (e) => {
        let dt = e.dataTransfer;
        let files = dt.files;

        if (files.length > 0) {
            if (files[0].type !== "application/pdf") {
                alert("Mohon maaf, hanya file PDF yang diizinkan.");
                return;
            }

            // Masukkan file ke tag input tersembunyi
            fileInput.files = files;

            // PENTING: Picu event 'change' agar teks nama file ikut berubah dan tombol menyala
            fileInput.dispatchEvent(new Event('change'));
        }
    }, false);

    // 5. Efek Loading saat disubmit
    formVerifikasi.addEventListener('submit', function(e) {
        // Pastikan file sudah dipilih
        if(fileInput.files.length === 0) return;

        // Ubah tombol jadi loading
        btnSubmitVerify.classList.remove('hover:bg-slate-800', 'hover:-translate-y-0.5');
        btnSubmitVerify.classList.add('opacity-75', 'cursor-wait');
        btnTextVerify.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin text-xl text-emerald-400"></i> MEMPROSES HASHING SHA-3...';
        
        // Matikan tombol
        btnSubmitVerify.disabled = true;

        // Lanjutkan pengiriman form
        this.submit();
    });
</script>
@endsection
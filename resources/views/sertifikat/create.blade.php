@extends('layouts.app')

@section('title', 'Amankan Sertifikat Baru')

@section('content')

    @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6">
            <strong class="font-bold">Oops! Ada kesalahan:</strong>
            <ul class="list-disc pl-5 mt-2 text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    @if (session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6 shadow-sm">
            <strong class="font-bold"><i class="fa-solid fa-triangle-exclamation"></i> Gagal!</strong>
            <span class="block sm:inline mt-1">{{ session('error') }}</span>
        </div>
    @endif

<div class="flex flex-col lg:flex-row gap-6 h-[calc(100vh-12rem)]">

    <div class="w-full lg:w-1/3 bg-white p-6 rounded-xl shadow-sm border border-gray-100 overflow-y-auto">
        <h3 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2">Data Sertifikat</h3>
        
        <form action="/tambah-sertifikat" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            
            <div class="bg-indigo-50 p-4 rounded-lg border border-indigo-100 mb-6">
                <label class="block text-sm font-semibold text-indigo-800 mb-2">1. Upload File Sertifikat (PDF)</label>
                <input type="file" id="file_pdf" name="file_pdf" accept="application/pdf" required
                       class="block w-full text-sm text-slate-500
                              file:mr-4 file:py-2 file:px-4
                              file:rounded-md file:border-0
                              file:text-sm file:font-semibold
                              file:bg-indigo-600 file:text-white
                              hover:file:bg-indigo-700
                              cursor-pointer">
                <p class="text-xs text-indigo-600 mt-2">*Pilih file untuk memunculkan preview di layar kanan</p>
            </div>

           <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">2. Nomor Identifier (NIM/NIS)</label>
                <input type="text" name="identifier_number" placeholder="Contoh: 220202669" required
                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 border p-2">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Peserta</label>
                <input type="text" name="name" placeholder="Sesuai yang tertera di sertifikat" required
                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 border p-2">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Asal Instansi/Kampus</label>
                <input type="text" name="institution" placeholder="Contoh: Universitas Amikom" required
                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 border p-2">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Program Studi / Jurusan</label>
                <input type="text" name="study_program" placeholder="Contoh: Teknik Informatika" required
                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 border p-2">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Kegiatan (Program)</label>
                <select name="program" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 border p-2 bg-white">
                    <option value="" disabled selected>Pilih jenis kegiatan...</option>
                    <option value="Praktik Kerja Lapangan (PKL)">Praktik Kerja Lapangan (PKL)</option>
                    <option value="Magang Industri">Magang Industri</option>
                </select>
            </div>

            <hr class="my-4">

            <button type="submit" class="w-full bg-slate-900 text-white font-bold py-3 px-4 rounded-lg hover:bg-slate-800 transition shadow-lg flex justify-center items-center gap-2">
                <i class="fa-solid fa-lock"></i> Proses Kriptografi & Amankan
            </button>
        </form>
    </div>

    <div class="w-full lg:w-2/3 bg-slate-100 rounded-xl border border-gray-300 shadow-inner flex flex-col overflow-hidden relative">
        <div class="bg-slate-200 p-2 border-b border-gray-300 flex justify-between items-center px-4">
            <span class="text-sm font-semibold text-gray-600"><i class="fa-regular fa-eye"></i> Preview Dokumen Asli</span>
            <span id="file-name-display" class="text-xs text-gray-500 italic">Belum ada file dipilih</span>
        </div>
        
        <div id="preview-container" class="flex-1 flex items-center justify-center p-4">
            <div class="text-center text-gray-400">
                <i class="fa-regular fa-file-pdf text-6xl mb-3"></i>
                <p>Silakan upload file PDF di sebelah kiri untuk melihat preview.</p>
            </div>
        </div>
    </div>

</div>

<script>
    document.getElementById('file_pdf').addEventListener('change', function(event) {
        const file = event.target.files[0];
        const container = document.getElementById('preview-container');
        const fileNameDisplay = document.getElementById('file-name-display');

        if (file && file.type === 'application/pdf') {
            // Tampilkan nama file
            fileNameDisplay.textContent = file.name;

            // Buat URL sementara untuk PDF
            const fileURL = URL.createObjectURL(file);
            
            // Masukkan iframe ke dalam container
            container.innerHTML = `<iframe src="${fileURL}" class="w-full h-full rounded shadow-sm border-0"></iframe>`;
        } else {
            container.innerHTML = `<div class="text-center text-red-400">
                <i class="fa-solid fa-triangle-exclamation text-4xl mb-2"></i>
                <p>Format file tidak didukung. Harap upload PDF.</p>
            </div>`;
        }
    });
</script>
@endsection
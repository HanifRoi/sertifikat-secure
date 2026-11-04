@extends('layouts.app')

@section('title', 'Generate Sertifikat Baru')

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

<form action="{{ route('sertifikat.store') }}" method="POST" id="formSertifikat">
    @csrf
    <div class="flex flex-col lg:flex-row gap-6">

        <div class="w-full lg:w-1/3 bg-white p-6 rounded-xl shadow-sm border border-gray-100 h-fit">
            <h3 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2">
                <i class="fa-solid fa-user-graduate mr-2 text-indigo-600"></i>Data Sertifikat
            </h3>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">1. Pilih Peserta Magang</label>
                <select name="participant_id" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 border p-2 bg-white text-sm">
                    <option value="" disabled selected>-- Pilih Mahasiswa / Siswa SMK --</option>
                    @foreach($participants as $p)
                        <option value="{{ $p->id }}">{{ $p->identifier_number }} - {{ $p->name }} ({{ $p->institution }})</option>
                    @endforeach
                </select>
            </div>

            
        </div>

        <div class="w-full lg:w-2/3 bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <div class="flex justify-between items-center mb-4 border-b pb-2">
                <h3 class="text-lg font-bold text-gray-800">
                    <i class="fa-solid fa-list-check mr-2 text-purple-600"></i>Input Nilai
                </h3>
                <span class="text-xs font-semibold bg-purple-100 text-purple-700 px-2 py-1 rounded">Skala 10 - 100</span>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                <div class="bg-slate-50 p-5 rounded-xl border border-slate-200 shadow-inner">
                    <h4 class="font-bold text-indigo-800 mb-4 flex items-center gap-2">
                        <span class="w-2 h-6 bg-indigo-600 rounded"></span> Kategori Soft Skill
                    </h4>
                    
                    <div class="space-y-4">
                        @foreach(['inisiatif' => 'Inisiatif', 'kedisiplinan' => 'Kedisiplinan', 'kejujuran' => 'Kejujuran', 'ketelitian' => 'Ketelitian', 'kerja_sama' => 'Kerja Sama'] as $key => $label)
                        <div class="flex justify-between items-center bg-white p-2 rounded border border-gray-100">
                            <label class="text-sm font-medium text-gray-700">{{ $label }}</label>
                            <input type="number" name="grades[{{ $key }}]" min="10" max="100" placeholder="0" required 
                                   class="w-20 rounded border-gray-300 p-1 text-center font-bold text-indigo-600 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        @endforeach
                    </div>
                </div>

                <div class="bg-slate-50 p-5 rounded-xl border border-slate-200 shadow-inner">
                    <h4 class="font-bold text-purple-800 mb-4 flex items-center gap-2">
                        <span class="w-2 h-6 bg-purple-600 rounded"></span> Kategori Hard Skill
                    </h4>
                    
                    <div class="space-y-4">
                        @foreach(['pemahaman_tugas' => 'Pemahaman Tugas', 'komunikasi' => 'Komunikasi', 'perencanaan_projek' => 'Perencanaan Projek', 'pengerjaan_projek' => 'Pengerjaan Projek', 'presentasi_projek' => 'Presentasi Projek'] as $key => $label)
                        <div class="flex justify-between items-center bg-white p-2 rounded border border-gray-100">
                            <label class="text-sm font-medium text-gray-700">{{ $label }}</label>
                            <input type="number" name="grades[{{ $key }}]" min="10" max="100" placeholder="0" required 
                                   class="w-20 rounded border-gray-300 p-1 text-center font-bold text-purple-600 focus:ring-purple-500 focus:border-purple-500">
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="space-y-3">
                <button type="submit" id="btnSubmit" class="w-full bg-green-600 text-white font-black py-4 px-6 rounded-xl hover:bg-green-800 transition-all duration-300 transform hover:scale-[1.02] shadow-xl flex justify-center items-center gap-3">
                    <span id="btnText" class="flex items-center gap-3">
                        <i class="fa-solid fa-wand-magic-sparkles text-xl"></i> 
                        GENERATE & AMANKAN SERTIFIKAT SEKARANG
                    </span>
                </button>
                
                <button type="reset" id="btnCancel" onclick="return confirm('PERINGATAN: Apakah Anda yakin ingin membatalkan dan menghapus semua inputan nilai di form ini?');" class="w-full bg-red-50 text-red-600 font-bold py-3 px-6 rounded-xl border border-red-200 hover:bg-red-100 transition-all duration-300 flex justify-center items-center gap-2">
                    <i class="fa-solid fa-rotate-left"></i> 
                    Batal & Bersihkan Form
                </button>
            </div>
            
            
        </div>

    </div>
</form>

<script>
    document.getElementById('formSertifikat').addEventListener('submit', function(e) {
        const btnSubmit = document.getElementById('btnSubmit');
        const btnText = document.getElementById('btnText');
        const btnCancel = document.getElementById('btnCancel');

        // Ubah tampilan tombol submit menjadi loading
        btnSubmit.classList.remove('hover:bg-green-800', 'hover:scale-[1.02]');
        btnSubmit.classList.add('opacity-75', 'cursor-wait');
        btnText.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin text-xl"></i> MEMPROSES KRIPTOGRAFI, HARAP TUNGGU...';
        
        // Matikan tombol agar tidak bisa diklik dua kali
        btnSubmit.disabled = true;
        btnCancel.disabled = true;
        btnCancel.classList.add('opacity-50', 'cursor-not-allowed');

        // Lanjutkan pengiriman form
        this.submit();
    });
</script>

@endsection
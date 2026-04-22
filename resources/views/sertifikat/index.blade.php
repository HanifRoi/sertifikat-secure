@extends('layouts.app')

@section('title', 'Riwayat E-Sertifikat')

@section('content')
<div class="max-w-6xl mx-auto mt-8">
    
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-extrabold text-slate-800">
                <i class="fa-solid fa-folder-open text-indigo-600 mr-2"></i> Riwayat Sertifikat
            </h1>
            <p class="text-gray-500 mt-1">Daftar seluruh dokumen yang telah diamankan dengan Kriptografi</p>
        </div>
        <div>
            <a href="{{ route('sertifikat.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-lg shadow transition">
                <i class="fa-solid fa-plus mr-1"></i> Buat Baru
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="bg-emerald-100 border-l-4 border-emerald-500 text-emerald-800 p-4 rounded mb-6 shadow-sm flex items-center">
            <i class="fa-solid fa-circle-check text-2xl mr-3"></i>
            <p class="font-bold">{{ session('success') }}</p>
        </div>
    @endif

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 text-slate-600 text-sm uppercase tracking-wider border-b border-slate-200">
                        <th class="p-4 font-semibold">No</th>
                        <th class="p-4 font-semibold">Identitas Peserta</th>
                        <th class="p-4 font-semibold">Program</th>
                        <th class="p-4 font-semibold">Tanggal Terbit</th>
                        <th class="p-4 font-semibold text-center">Status</th>
                        <th class="p-4 font-semibold text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($certificates as $index => $cert)
                    <tr class="hover:bg-slate-50 transition">
                        <td class="p-4 text-gray-500">{{ $index + 1 }}</td>
                        <td class="p-4">
                            <p class="font-bold text-slate-800">{{ $cert->participant->name }}</p>
                            <p class="text-xs text-gray-500">{{ $cert->participant->identifier_number }} • {{ $cert->participant->institution }}</p>
                        </td>
                        <td class="p-4 text-gray-700 font-medium">{{ $cert->program }}</td>
                        <td class="p-4 text-gray-600">{{ \Carbon\Carbon::parse($cert->publish_date)->translatedFormat('d F Y') }}</td>
                        
                        <td class="p-4 text-center">
                            @if($cert->status === 'secured')
                                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 shadow-sm">
                                    <i class="fa-solid fa-shield-check"></i> AKTIF
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold bg-red-100 text-red-800 shadow-sm">
                                    <i class="fa-solid fa-ban"></i> DICABUT
                                </span>
                            @endif
                        </td>
                        
                        <td class="p-4 text-center space-x-2 whitespace-nowrap">
                            @php
                                $filename = str_replace('public/sertifikat_final/', '', $cert->file_path);
                            @endphp
                            <a href="{{ route('sertifikat.download', ['filename' => $filename]) }}" class="inline-block text-indigo-600 hover:text-indigo-900 bg-indigo-50 hover:bg-indigo-100 p-2 rounded-lg transition" title="Download Sertifikat">
                                <i class="fa-solid fa-download"></i>
                            </a>

                            @if($cert->status === 'secured')
                            <form action="{{ route('sertifikat.revoke', $cert->id) }}" method="POST" class="inline-block" onsubmit="return confirm('PERINGATAN: Apakah Anda yakin ingin mencabut sertifikat ini? Sertifikat yang dicabut akan ditolak oleh sistem verifikasi secara permanen!');">
                                @csrf
                                <button type="submit" class="text-red-600 hover:text-red-900 bg-red-50 hover:bg-red-100 p-2 rounded-lg transition" title="Cabut Sertifikat">
                                    <i class="fa-solid fa-trash-can"></i>
                                </button>
                            </form>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="p-8 text-center text-gray-400">
                            <i class="fa-solid fa-box-open text-4xl mb-3"></i>
                            <p>Belum ada sertifikat yang diterbitkan.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
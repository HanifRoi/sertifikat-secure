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
            <a href="{{ route('sertifikat.create') }}" class="bg-green-500 hover:bg-green-300 text-white font-bold py-2 px-4 rounded-lg shadow transition">
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

    <form id="form-filter" action="{{ route('sertifikat.index') }}" method="GET"></form>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-6">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-800 text-white text-xs uppercase tracking-wider border-b border-slate-700">
                        <th class="p-4 font-semibold text-center w-12">No</th>
                        <th class="p-4 font-semibold">Identitas Peserta</th>
                        <th class="p-4 font-semibold">Instansi</th>
                        <th class="p-4 font-semibold">Program</th>
                        <th class="p-4 font-semibold">Tanggal Terbit</th>
                        <th class="p-4 font-semibold text-center">Status</th>
                        <th class="p-4 font-semibold text-center w-28">Aksi</th>
                    </tr>
                    
                    <tr class="bg-slate-100 border-b border-slate-300">
                        <td class="p-2 text-center text-gray-400">
                            <i class="fa-solid fa-filter"></i>
                        </td>
                        
                        <td class="p-2">
                            <input type="text" name="search" form="form-filter" value="{{ request('search') }}" placeholder="Cari Nama/NIM..." 
                                   class="w-full px-2 py-1.5 text-xs rounded border border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                        </td>
                        
                        <td class="p-2">
                            <select name="institution" form="form-filter" class="w-full px-2 py-1.5 text-xs rounded border border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 bg-white">
                                <option value="">Semua Instansi</option>
                                @isset($institutions)
                                    @foreach($institutions as $inst)
                                        <option value="{{ $inst }}" {{ request('institution') == $inst ? 'selected' : '' }}>{{ Str::limit($inst, 20) }}</option>
                                    @endforeach
                                @endisset
                            </select>
                        </td>

                        <td class="p-2">
                            <select name="program" form="form-filter" class="w-full px-2 py-1.5 text-xs rounded border border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 bg-white">
                                <option value="">Semua</option>
                                <option value="Praktik Kerja Lapangan (PKL)" {{ request('program') == 'Praktik Kerja Lapangan (PKL)' ? 'selected' : '' }}>PKL</option>
                                <option value="Magang Industri" {{ request('program') == 'Magang Industri' ? 'selected' : '' }}>Magang</option>
                            </select>
                        </td>
                        
                        <td class="p-2 space-y-1">
                            <input type="date" name="start_date" form="form-filter" value="{{ request('start_date') }}" title="Dari Tanggal"
                                   class="w-full px-2 py-1 text-xs rounded border border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                            <input type="date" name="end_date" form="form-filter" value="{{ request('end_date') }}" title="Sampai Tanggal"
                                   class="w-full px-2 py-1 text-xs rounded border border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                        </td>

                        <td class="p-2">
                            <select name="status" form="form-filter" class="w-full px-2 py-1.5 text-xs rounded border border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 bg-white">
                                <option value="">Semua</option>
                                <option value="secured" {{ request('status') == 'secured' ? 'selected' : '' }}>Aktif</option>
                                <option value="dicabut" {{ request('status') == 'dicabut' ? 'selected' : '' }}>Dicabut</option>
                            </select>
                        </td>

                        <td class="p-2 text-center space-x-1">
                            <button type="submit" form="form-filter" class="bg-indigo-600 hover:bg-indigo-800 text-white p-1.5 rounded transition" title="Terapkan Filter">
                                <i class="fa-solid fa-magnifying-glass"></i>
                            </button>
                            <a href="{{ route('sertifikat.index') }}" class="inline-block bg-gray-300 hover:bg-gray-400 text-gray-700 p-1.5 rounded transition" title="Reset Filter">
                                <i class="fa-solid fa-xmark"></i>
                            </a>
                        </td>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100">
                    @forelse ($certificates as $index => $cert)
                    <tr class="hover:bg-slate-50 transition">
                        <td class="p-4 text-center text-gray-500">
                            {{ ($certificates->currentPage() - 1) * $certificates->perPage() + $loop->iteration }}
                        </td>
                        
                        <td class="p-4">
                            <p class="font-bold text-slate-800">{{ $cert->participant->name }}</p>
                            <p class="text-xs text-gray-500">{{ $cert->participant->identifier_number }}</p>
                            <p class="text-[10px] font-mono text-indigo-500 mt-1" title="Nomor Sertifikat">{{ $cert->certificate_number }}</p>
                        </td>

                        <td class="p-4">
                            <span class="text-xs font-semibold text-slate-600 bg-slate-100 px-2 py-1 rounded">{{ $cert->participant->institution }}</span>
                        </td>
                        
                        <td class="p-4 text-sm text-gray-700 font-medium">{{ $cert->participant->program }}</td>
                        
                        <td class="p-4">
                            <p class="text-sm text-gray-700 font-medium">{{ \Carbon\Carbon::parse($cert->publish_date)->translatedFormat('d M Y') }}</p>
                            <p class="text-[11px] text-gray-400 mt-1" title="Admin yang menerbitkan">
                                <i class="fa-solid fa-user-pen mr-1"></i> {{ $cert->user->name ?? 'Sistem' }}
                            </p>
                        </td>
                        
                        <td class="p-4 text-center">
                            @if($cert->status === 'secured')
                                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 shadow-sm">
                                    <i class="fa-solid fa-shield-check"></i> AKTIF
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-[10px] font-bold bg-red-100 text-red-800 shadow-sm">
                                    <i class="fa-solid fa-ban"></i> DICABUT
                                </span>
                            @endif
                        </td>
                        
                        <td class="p-4 text-center space-x-1 whitespace-nowrap">
                            @php
                                $filename = str_replace('public/sertifikat_final/', '', $cert->file_path);
                            @endphp
                            
                            <a href="{{ route('sertifikat.preview', ['filename' => $filename]) }}" target="_blank" class="inline-block text-sky-600 hover:text-sky-900 bg-sky-50 hover:bg-sky-100 p-2 rounded-lg transition" title="Lihat PDF">
                                <i class="fa-solid fa-eye"></i>
                            </a>

                            <a href="{{ route('sertifikat.download', ['filename' => $filename]) }}" class="inline-block text-indigo-600 hover:text-indigo-900 bg-indigo-50 hover:bg-indigo-100 p-2 rounded-lg transition" title="Download">
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
                        <td colspan="7" class="p-8 text-center text-gray-400">
                            <i class="fa-solid fa-box-open text-4xl mb-3"></i>
                            <p>Data sertifikat tidak ditemukan.</p>
                            @if(request()->anyFilled(['search', 'status', 'program', 'institution', 'start_date', 'end_date']))
                                <a href="{{ route('sertifikat.index') }}" class="text-indigo-500 hover:underline mt-2 inline-block text-sm">Hapus Filter</a>
                            @endif
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($certificates->hasPages())
        <div class="p-4 border-t border-gray-100 bg-gray-50">
            {{ $certificates->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
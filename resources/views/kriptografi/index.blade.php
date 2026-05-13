@extends('layouts.app')

@section('title', 'Manajemen Kunci Kriptografi')

@section('content')
<div class="space-y-6">
    
    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex justify-between items-center">
        <div>
            <h3 class="text-xl font-bold text-gray-800">Key Generator</h3>
            <p class="text-sm text-gray-500">Bangkitkan sepasang kunci RSA 2048-bit untuk pengamanan seluruh sertifikat.</p>
        </div>
        <form action="/kriptografi/generate" method="POST">
            @csrf
            <button type="submit" onclick="return confirm('Bangkitkan kunci baru? Kunci lama akan dinonaktifkan.')" 
                    class="bg-purple-700 text-white px-6 py-3 rounded-lg font-bold hover:bg-purple-500 transition flex items-center gap-2">
                <i class="fa-solid fa-wand-magic-sparkles"></i> Generate Key
            </button>
        </form>
    </div>

    @if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
        <strong class="font-bold">Berhasil!</strong> {{ session('success') }}
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <div class="flex items-center gap-2 mb-4 text-emerald-600">
                <i class="fa-solid fa-unlock-keyhole text-xl"></i>
                <h4 class="font-bold uppercase tracking-wider text-sm">Public Key (Aktif)</h4>
            </div>
            <textarea readonly class="w-full h-48 p-4 bg-slate-50 border border-gray-200 rounded-lg font-mono text-xs text-slate-600 focus:outline-none">{{ $activeKey->public_key ?? 'Belum ada kunci aktif' }}</textarea>
            <p class="text-[10px] text-gray-400 mt-2">*Kunci ini akan digunakan oleh sistem verifikator (QC) untuk memvalidasi hash.</p>
        </div>

        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <div class="flex items-center gap-2 mb-4 text-rose-600">
                <i class="fa-solid fa-key text-xl"></i>
                <h4 class="font-bold uppercase tracking-wider text-sm">Private Key (Aktif)</h4>
            </div>
            <textarea readonly class="w-full h-48 p-4 bg-slate-900 border border-gray-800 rounded-lg font-mono text-xs text-indigo-300 focus:outline-none">{{ $activeKey->private_key ?? 'Belum ada kunci aktif' }}</textarea>
            <p class="text-[10px] text-gray-400 mt-2 text-rose-500 font-semibold">*RAHASIA: Kunci ini hanya digunakan oleh internal sistem untuk enkripsi.</p>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-100">
            <h4 class="font-bold text-gray-800">Riwayat Pembangkitan Kunci</h4>
        </div>
        <table class="w-full text-left border-collapse">
           <thead class="bg-gray-50 text-gray-500 uppercase text-xs font-bold">
                <tr>
                    <th class="px-6 py-4">Waktu Generate</th>
                    <th class="px-6 py-4">Dibuat Oleh</th> <th class="px-6 py-4">Deskripsi</th>
                    <th class="px-6 py-4">Status</th>
                </tr>
            </thead>
           <tbody class="divide-y divide-gray-100">
                @foreach($historyKeys as $key)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $key->created_at->format('d M Y, H:i') }}</td>
                    
                    <td class="px-6 py-4">
                        <span class="inline-flex items-center text-sm font-semibold text-purple-600">
                            <i class="fa-solid fa-user-shield mr-2"></i> {{ $key->user->name ?? 'Sistem Lama' }}
                        </span>
                    </td>
                    
                    <td class="px-6 py-4 text-sm text-gray-500">{{ $key->description }}</td>
                    <td class="px-6 py-4">
                        @if($key->is_active)
                            <span class="px-2 py-1 bg-green-100 text-green-700 rounded text-xs font-bold shadow-sm"><i class="fa-solid fa-check mr-1"></i> AKTIF</span>
                        @else
                            <span class="px-2 py-1 bg-gray-100 text-gray-400 rounded text-xs"><i class="fa-solid fa-power-off mr-1"></i> NON-AKTIF</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
@extends('layouts.app')

@section('title', 'Dashboard Kriptografi')

@section('content')

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

@if (session('success'))
        <div id="toast-wrapper" class="fixed top-5 left-0 right-0 flex justify-center z-50 pointer-events-none transition-opacity duration-500 transform -translate-y-full opacity-0">
            <div id="toast-success" class="pointer-events-auto bg-white border-l-4 border-emerald-500 shadow-2xl rounded-xl p-4 flex items-start max-w-sm mx-5">
                <div class="bg-emerald-100 text-emerald-600 rounded-full w-8 h-8 flex items-center justify-center mr-3 shrink-0">
                    <i class="fa-solid fa-check text-sm"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-slate-800">Berhasil!</h3>
                    <p class="text-xs text-gray-500 mt-1">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif
<div class="max-w-7xl mx-auto mt-6">
    
    <div class="mb-8">
        <h1 class="text-3xl font-extrabold text-slate-800">
            <i class="fa-solid fa-chart-pie text-indigo-600 mr-2"></i> Dashboard
        </h1>
        <p class="text-gray-500 mt-1">Statistik e-sertifikat Cazh Academy</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex items-center hover:shadow-md transition">
            <div class="bg-blue-100 text-blue-600 p-4 rounded-xl mr-4 text-2xl">
                <i class="fa-solid fa-file-contract"></i>
            </div>
            <div>
                <p class="text-sm font-semibold text-gray-500 uppercase">Total Diterbitkan</p>
                <p class="text-3xl font-bold text-slate-800">{{ $totalSertifikat }}</p>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex items-center hover:shadow-md transition">
            <div class="bg-emerald-100 text-emerald-600 p-4 rounded-xl mr-4 text-2xl">
                <i class="fa-solid fa-shield-check"></i>
            </div>
            <div>
                <p class="text-sm font-semibold text-gray-500 uppercase">Sertifikat Aktif</p>
                <p class="text-3xl font-bold text-slate-800">{{ $sertifikatAktif }}</p>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex items-center hover:shadow-md transition">
            <div class="bg-red-100 text-red-600 p-4 rounded-xl mr-4 text-2xl">
                <i class="fa-solid fa-ban"></i>
            </div>
            <div>
                <p class="text-sm font-semibold text-gray-500 uppercase">Sertifikat Dicabut</p>
                <p class="text-3xl font-bold text-slate-800">{{ $sertifikatDicabut }}</p>
            </div>
        </div>

        <div class="bg-indigo-700 rounded-2xl shadow-sm border border-slate-800 p-6 flex items-center hover:shadow-md transition relative overflow-hidden">
            <div class="absolute -right-6 -top-6 w-24 h-24 bg-indigo-500 rounded-full opacity-20 blur-xl"></div>
            
            <div class="bg-indigo-700 text-white p-4 rounded-xl mr-4 text-2xl relative z-10">
                <i class="fa-solid fa-key"></i>
            </div>
            <div class="relative z-10">
                <p class="text-sm font-semibold text-indigo-300 uppercase">Status RSA & SHA3-512</p>
                @if($rsaKey)
                    <p class="text-xl font-bold text-white flex items-center mt-1">
                        <span class="w-3 h-3 bg-emerald-400 rounded-full mr-2 animate-pulse"></span> AMAN / AKTIF
                    </p>
                @else
                    <p class="text-xl font-bold text-red-400 mt-1">TIDAK ADA KUNCI</p>
                @endif
            </div>
        </div>

    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-lg font-bold text-slate-800 mb-4">Statistik Penerbitan Tahun {{ date('Y') }}</h2>
            <div class="relative h-72 w-full">
                <canvas id="sertifikatChart"></canvas>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-bold text-slate-800">5 Aktivitas Terakhir</h2>
                <a href="{{ route('sertifikat.index') }}" class="text-sm text-indigo-600 hover:text-indigo-800 font-semibold">Lihat Semua</a>
            </div>
            
            <div class="space-y-4">
                @forelse($recentCertificates as $cert)
                <div class="flex items-start border-b border-gray-50 pb-3 last:border-0 last:pb-0">
                    <div class="mt-1 mr-3">
                        @if($cert->status === 'secured')
                            <div class="w-8 h-8 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center text-xs">
                                <i class="fa-solid fa-check"></i>
                            </div>
                        @else
                            <div class="w-8 h-8 rounded-full bg-red-100 text-red-600 flex items-center justify-center text-xs">
                                <i class="fa-solid fa-xmark"></i>
                            </div>
                        @endif
                    </div>
                    <div>
                        <p class="text-sm font-bold text-slate-800">{{ $cert->participant->name }}</p>
                        <p class="text-xs text-gray-500 truncate max-w-[200px]">{{ $cert->program }}</p>
                        <p class="text-[10px] text-gray-400 mt-1"><i class="fa-regular fa-clock"></i> {{ \Carbon\Carbon::parse($cert->publish_date)->diffForHumans() }}</p>
                    </div>
                </div>
                @empty
                <div class="text-center text-gray-400 py-6">
                    <p class="text-sm">Belum ada aktivitas terekam.</p>
                </div>
                @endforelse
            </div>
        </div>

    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        
        // --- 1. Logika Notifikasi Pop-up (Toast) Tengah Atas ---
        // Sekarang kita targetkan wrapper-nya buat animasi
        const toastWrapper = document.getElementById('toast-wrapper');
        if (toastWrapper) {
            // Animasi masuk (muncul & TURUN dari atas ke bawah)
            setTimeout(() => {
                // Hilangkan class sembunyi (-translate-y-full & opacity-0)
                toastWrapper.classList.remove('-translate-y-full', 'opacity-0');
                // Tambahkan class muncul (translate-y-0 & opacity-100)
                toastWrapper.classList.add('translate-y-0', 'opacity-100');
            }, 100);

            // Set timer 2.5 detik untuk mulai menghilang
            setTimeout(() => {
                // Animasi keluar (geser NAIK kembali ke atas & transparan)
                toastWrapper.classList.remove('translate-y-0', 'opacity-100');
                toastWrapper.classList.add('-translate-y-full', 'opacity-0');
                
                // Tunggu transisi CSS selesai (500ms), lalu hilangkan elemen dari DOM biar gak ngebruk
                setTimeout(() => {
                    toastWrapper.style.display = 'none';
                }, 500); 
            }, 2500);
        }

        // --- 2. Logika Chart.js ---
        const canvas = document.getElementById('sertifikatChart');
        if (canvas) {
            const ctx = canvas.getContext('2d');
            const labels = @json($months);
            const dataPoint = @json($monthlyCounts);

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Jumlah Sertifikat Diterbitkan',
                        data: dataPoint,
                        backgroundColor: 'rgba(79, 70, 229, 0.2)', 
                        borderColor: 'rgba(79, 70, 229, 1)',
                        borderWidth: 2,
                        borderRadius: 6, 
                        hoverBackgroundColor: 'rgba(79, 70, 229, 0.4)'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1 
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false 
                        }
                    }
                }
            });
        }
    });
</script>
@endsection
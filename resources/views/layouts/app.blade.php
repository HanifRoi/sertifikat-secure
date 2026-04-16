<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sertifikat-Secure | PT Cazh</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-100 font-sans">

    <div class="min-h-screen flex">
        <aside class="w-64 bg-slate-900 text-white flex-shrink-0 hidden md:flex flex-col">
            <div class="p-6">
                <h1 class="text-2xl font-bold tracking-wider text-indigo-400">CAZH<span class="text-white text-sm block font-normal">SECURE SYSTEM</span></h1>
            </div>
            
        <nav class="flex-1 px-4 space-y-2 mt-4">
    {{-- SEMUA MENU DITAMPILKAN SEMENTARA UNTUK DEVELOPMENT --}}
    <a href="#" class="flex items-center p-3 text-gray-300 hover:bg-slate-800 hover:text-white rounded-lg transition">
        <i class="fa-solid fa-chart-pie w-6"></i> <span>Dashboard</span>
    </a>
    <a href="#" class="flex items-center p-3 text-gray-300 hover:bg-slate-800 hover:text-white rounded-lg transition">
        <i class="fa-solid fa-circle-plus w-6"></i> <span>Tambah Sertifikat</span>
    </a>
    <a href="#" class="flex items-center p-3 text-gray-300 hover:bg-slate-800 hover:text-white rounded-lg transition">
        <i class="fa-solid fa-clipboard-list w-6"></i> <span>Riwayat Sertifikat</span>
    </a>
    <a href="#" class="flex items-center p-3 text-gray-300 hover:bg-slate-800 hover:text-white rounded-lg transition">
        <i class="fa-solid fa-key w-6"></i> <span>Kriptografi</span>
    </a>
    
    {{-- MENU VERIFIKASI (QC) GABUNG DI SINI SEMENTARA --}}
    <a href="#" class="flex items-center p-3 text-indigo-400 hover:bg-slate-800 hover:text-indigo-300 rounded-lg transition">
        <i class="fa-solid fa-check-double w-6"></i> <span>Verifikasi (QC)</span>
    </a>
</nav>

{{-- TOMBOL LOGOUT --}}
<div class="p-4 border-t border-slate-800 mt-auto">
    <a href="#" class="flex items-center p-3 text-red-400 hover:bg-red-900/20 rounded-lg transition">
        <i class="fa-solid fa-right-from-bracket w-6"></i> <span>Logout</span>
    </a>
</div>


            <div class="p-4 border-t border-slate-800">
                <a href="#" class="flex items-center p-3 text-red-400 hover:bg-red-900/20 rounded-lg transition">
                    <i class="fa-solid fa-right-from-bracket w-6"></i> <span>Logout</span>
                </a>
            </div>
        </aside>

        <main class="flex-1 flex flex-col min-w-0 overflow-hidden">
            <header class="bg-white shadow-sm h-16 flex items-center justify-between px-8">
                <h2 class="text-xl font-semibold text-gray-800">@yield('title')</h2>
                <div class="flex items-center space-x-4">
                    <span class="text-sm text-gray-500">Admin: <strong class="text-gray-700">Hanif Roihan</strong></span>
                    <div class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 font-bold">
                        HR
                    </div>
                </div>
            </header>

            <div class="p-8 overflow-y-auto">
                @yield('content')
            </div>
        </main>
    </div>

</body>
</html>
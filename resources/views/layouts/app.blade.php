<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sertifikat-Secure | Cazh Academy</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-100 font-sans">

    <div class="min-h-screen flex">
        
        @auth
        <aside class="w-64 bg-slate-800 text-white shrink-0 hidden md:flex flex-col shadow-xl z-20">
            <div class="p-6">
                <h1 class="text-2xl font-bold tracking-wider text-indigo-300">CAZHSECURE<span class="text-white text-sm block font-normal">SYSTEM</span></h1>
            </div>
            
            <nav class="flex-1 px-4 space-y-3 mt-4">
                <a href="{{ route('dashboard') }}" 
                   class="flex items-center p-3 rounded-lg transition-all duration-200 border border-l-4 {{ request()->routeIs('dashboard') ? 'bg-slate-700 border-slate-600 border-l-sky-400 text-white shadow-md' : 'bg-slate-800/40 border-slate-700 border-l-transparent text-slate-400 hover:bg-slate-700 hover:border-slate-500 hover:text-white hover:shadow-sm' }}">
                    <i class="fa-solid fa-chart-pie w-6"></i> 
                    <span class="ml-2 font-medium">Dashboard</span>
                </a>

                <a href="{{ route('sertifikat.create') }}" 
                   class="flex items-center p-3 rounded-lg transition-all duration-200 border border-l-4 {{ request()->routeIs('sertifikat.create') ? 'bg-slate-700 border-slate-600 border-l-sky-400 text-white shadow-md' : 'bg-slate-800/40 border-slate-700 border-l-transparent text-slate-400 hover:bg-slate-700 hover:border-slate-500 hover:text-white hover:shadow-sm' }}">
                    <i class="fa-solid fa-circle-plus w-6"></i> 
                    <span class="ml-2 font-medium">Tambah Sertifikat</span>
                </a>

                <a href="{{ route('sertifikat.index') }}" 
                   class="flex items-center p-3 rounded-lg transition-all duration-200 border border-l-4 {{ request()->routeIs('sertifikat.index') ? 'bg-slate-700 border-slate-600 border-l-sky-400 text-white shadow-md' : 'bg-slate-800/40 border-slate-700 border-l-transparent text-slate-400 hover:bg-slate-700 hover:border-slate-500 hover:text-white hover:shadow-sm' }}">
                    <i class="fa-solid fa-clipboard-list w-6"></i> 
                    <span class="ml-2 font-medium">Riwayat Sertifikat</span>
                </a>

                <a href="{{ route('kriptografi.index') }}" 
                   class="flex items-center p-3 rounded-lg transition-all duration-200 border border-l-4 {{ request()->routeIs('kriptografi.index') ? 'bg-slate-700 border-slate-600 border-l-sky-400 text-white shadow-md' : 'bg-slate-800/40 border-slate-700 border-l-transparent text-slate-400 hover:bg-slate-700 hover:border-slate-500 hover:text-white hover:shadow-sm' }}">
                    <i class="fa-solid fa-key w-6"></i> 
                    <span class="ml-2 font-medium">Kriptografi</span>
                </a>

                <a href="{{ route('verify.scan') }}" 
                   class="flex items-center p-3 rounded-lg transition-all duration-200 border border-l-4 {{ request()->routeIs('verify.scan') ? 'bg-slate-700 border-slate-600 border-l-emerald-400 text-white shadow-md' : 'bg-slate-800/40 border-slate-700 border-l-transparent text-slate-400 hover:bg-slate-700 hover:border-slate-500 hover:text-white hover:shadow-sm' }}">
                    <i class="fa-solid fa-qrcode w-6"></i> 
                    <span class="ml-2 font-medium">Verifikasi</span>
                </a>

            </nav>
        </aside>
        @endauth

        <main class="flex-1 flex flex-col min-w-0 overflow-hidden">
            <header class="bg-white shadow-sm h-16 flex items-center justify-between px-8 z-10">
                <h2 class="text-xl font-semibold text-gray-800">@yield('title')</h2>
                
                <div class="flex items-center space-x-4">
                    @auth
                        @php
                            $name = Auth::user()->name;
                            $words = explode(' ', $name);
                            $initials = strtoupper(substr($words[0], 0, 1));
                            if (count($words) > 1) {
                                $initials .= strtoupper(substr($words[1], 0, 1));
                            }
                        @endphp
                        
                        <div class="flex items-center gap-4">
                            
                            <div class="flex items-center space-x-3">
                                <span class="text-sm text-gray-500 hidden sm:block">Admin: <strong class="text-gray-700">{{ $name }}</strong></span>
                                <div class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 font-bold shadow-inner border border-indigo-200" title="{{ $name }}">
                                    {{ $initials }}
                                </div>
                            </div>

                            <div class="h-6 w-px bg-gray-200 hidden sm:block"></div>

                            <form action="{{ route('logout') }}" method="POST" class="m-0">
                                @csrf
                                <button type="submit" 
                                        class="w-10 h-10 rounded-full bg-red-50 hover:bg-red-500 flex items-center justify-center text-red-500 hover:text-white transition-all duration-300 shadow-sm border border-red-100" 
                                        title="Keluar dari Sistem"
                                        onclick="return confirm('Apakah Anda yakin ingin keluar dari sistem?');">
                                    <i class="fa-solid fa-right-from-bracket"></i>
                                </button>
                            </form>
                            
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="text-sm font-semibold text-indigo-600 hover:text-indigo-800 bg-indigo-50 px-4 py-2 rounded-lg border border-indigo-100 transition shadow-sm">
                            <i class="fa-solid fa-shield-halved mr-1"></i> Login Admin
                        </a>
                    @endauth
                </div>
            </header>

            <div class="p-8 overflow-y-auto">
                @yield('content')
            </div>
        </main>
        
    </div>

</body>
</html>
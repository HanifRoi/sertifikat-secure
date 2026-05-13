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
        <aside class="w-64 bg-purple-700 text-white shrink-0 hidden md:flex flex-col">
            <div class="p-6">
                <h1 class="text-2xl font-bold tracking-wider text-indigo-300">CAZH ACADEMY<span class="text-white text-sm block font-normal">SECURE SYSTEM</span></h1>
            </div>
            
            <nav class="flex-1 px-4 space-y-2 mt-4">
                <a href="{{ route('dashboard') }}" class="flex items-center p-3 rounded-lg transition {{ request()->routeIs('dashboard') ? 'bg-purple-500 text-white' : 'text-gray-300 hover:bg-purple-500 hover:text-white' }}">
                    <i class="fa-solid fa-chart-pie w-6"></i> <span class="ml-2">Dashboard</span>
                </a>
                <a href="{{ route('sertifikat.create') }}" class="flex items-center p-3 rounded-lg transition {{ request()->routeIs('sertifikat.create') ? 'bg-purple-500 text-white' : 'text-gray-300 hover:bg-purple-500 hover:text-white' }}">
                    <i class="fa-solid fa-circle-plus w-6"></i> <span class="ml-2">Tambah Sertifikat</span>
                </a>
                <a href="{{ route('sertifikat.index') }}" class="flex items-center p-3 rounded-lg transition {{ request()->routeIs('sertifikat.index') ? 'bg-purple-500 text-white' : 'text-gray-300 hover:bg-purple-500 hover:text-white' }}">
                    <i class="fa-solid fa-clipboard-list w-6"></i> <span class="ml-2">Riwayat Sertifikat</span>
                </a>
                <a href="{{ route('kriptografi.index') }}" class="flex items-center p-3 rounded-lg transition {{ request()->routeIs('kriptografi.index') ? 'bg-purple-500 text-white' : 'text-gray-300 hover:bg-purple-500 hover:text-white' }}">
                    <i class="fa-solid fa-key w-6"></i> <span class="ml-2">Kriptografi</span>
                </a>
                <a href="{{ route('verify.scan') }}" class="flex items-center p-3 rounded-lg transition {{ request()->routeIs('verify.scan') ? 'bg-purple-500 text-white' : 'text-gray-300 hover:bg-purple-500 hover:text-white' }} mt-4">
                    <i class="fa-solid fa-qrcode w-6"></i> <span class="ml-2">Verifikasi</span>
                </a>
            </nav>

            <div class="p-4 border-t border-slate-800 mt-auto">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full flex items-center p-3 text-red-400 hover:bg-red-700/20 rounded-lg transition">
                        <i class="fa-solid fa-right-from-bracket w-6"></i> <span class="ml-2">Logout</span>
                    </button>
                </form>
            </div>
        </aside>
        @endauth

        <main class="flex-1 flex flex-col min-w-0 overflow-hidden">
            <header class="bg-white shadow-sm h-16 flex items-center justify-between px-8">
                <h2 class="text-xl font-semibold text-gray-800">@yield('title')</h2>
                
                <div class="flex items-center space-x-4">
                    @auth
                        <span class="text-sm text-gray-500">Admin: <strong class="text-gray-700">{{ Auth::user()->name }}</strong></span>
                        @php
                            $name = Auth::user()->name;
                            $words = explode(' ', $name);
                            $initials = strtoupper(substr($words[0], 0, 1));
                            if (count($words) > 1) {
                                $initials .= strtoupper(substr($words[1], 0, 1));
                            }
                        @endphp
                        <div class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 font-bold shadow-inner">
                            {{ $initials }}
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
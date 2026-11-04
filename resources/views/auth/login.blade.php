<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | CAZH Secure System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-slate-50 font-sans flex items-center justify-center min-h-screen relative overflow-hidden">

    <div class="absolute -top-32 -right-32 w-96 h-96 bg-indigo-500 rounded-full mix-blend-multiply filter blur-3xl opacity-20"></div>
    <div class="absolute -bottom-32 -left-32 w-96 h-96 bg-emerald-500 rounded-full mix-blend-multiply filter blur-3xl opacity-20"></div>

    <div class="w-full max-w-md bg-white rounded-3xl shadow-xl border border-gray-100 p-10 relative z-10">
        
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-slate-900 text-indigo-400 mb-4 shadow-lg">
                <i class="fa-solid fa-shield-halved text-3xl"></i>
            </div>
            <h1 class="text-2xl font-bold tracking-wider text-slate-800">CAZH<span class="text-purple-600">SECURE</span></h1>
            <p class="text-sm text-gray-400 mt-1">Sistem Verifikasi E-Sertifikat</p>
        </div>

        @if ($errors->has('login_error'))
            <div class="bg-red-100 border-l-4 border-red-500 text-red-800 p-4 rounded-xl mb-6 shadow-sm animate-pulse">
                <div class="flex items-center">
                    <i class="fa-solid fa-shield-xmark text-2xl mr-3"></i>
                    <div>
                        <p class="font-bold">Akses Dibatasi Sistem!</p>
                        <p class="text-sm">{{ $errors->first('login_error') }}</p>
                    </div>
                </div>
            </div>
        @endif

        @if ($errors->any() && !$errors->has('login_error'))
            <div class="bg-red-50 text-red-600 p-3 rounded-lg text-sm mb-6 border border-red-100 flex items-start">
                <i class="fa-solid fa-circle-exclamation mt-1 mr-2"></i>
                <span>{{ $errors->first() }}</span>
            </div>
        @endif

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

        <form action="{{ route('login.process') }}" method="POST" class="space-y-5" novalidate>
            @csrf
            
            <div>
                <label for="email" class="block text-sm font-semibold text-gray-700 mb-1">Email</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fa-solid fa-envelope text-gray-400"></i>
                    </div>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus
                        class="w-full pl-10 pr-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition text-sm">
                </div>
            </div>

            <div>
                <label for="password" class="block text-sm font-semibold text-gray-700 mb-1">Password</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fa-solid fa-lock text-gray-400"></i>
                    </div>
                    <input type="password" name="password" id="password" required
                        class="w-full pl-10 pr-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition text-sm">
                </div>
            </div>

            <button type="submit" class="w-full bg-slate-900 hover:bg-slate-800 text-white font-bold py-3 px-4 rounded-xl shadow-lg transition-transform transform hover:-translate-y-0.5 mt-4">
                Masuk ke Dashboard <i class="fa-solid fa-arrow-right ml-2"></i>
            </button>
            <div class="text-right mt-3">
    <a href="{{ route('password.request') }}" class="text-sm font-semibold text-indigo-600 hover:text-indigo-500 transition">
        Lupa Password?
    </a>
</div>
        </form>

    </div>
</body>
<script>
        document.addEventListener('DOMContentLoaded', function() {
            // --- Logika Notifikasi Pop-up (Toast) Tengah Atas ---
        
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
        });
    </script>
</html>
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

        @if ($errors->any())
            <div class="bg-red-50 text-red-600 p-3 rounded-lg text-sm mb-6 border border-red-100 flex items-start">
                <i class="fa-solid fa-circle-exclamation mt-1 mr-2"></i>
                <span>{{ $errors->first() }}</span>
            </div>
        @endif

        @if (session('success'))
            <div class="bg-emerald-50 text-emerald-600 p-3 rounded-lg text-sm mb-6 border border-emerald-100 flex items-start">
                <i class="fa-solid fa-circle-check mt-1 mr-2"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <form action="{{ route('login.process') }}" method="POST" class="space-y-5">
            @csrf
            
            <div>
                <label for="email" class="block text-sm font-semibold text-gray-700 mb-1">Email Admin</label>
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
        </form>

    </div>
</body>
</html>
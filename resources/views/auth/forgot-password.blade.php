<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password | Cazh Academy</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
    <div class="bg-white p-8 rounded-xl shadow-md w-full max-w-md border border-gray-100">
        <h2 class="text-2xl font-bold text-slate-800 text-center mb-2">Lupa Password?</h2>
        <p class="text-sm text-gray-500 text-center mb-6">Masukkan email Anda untuk menerima link pemulihan akun.</p>

        @if (session('success'))
            <div class="bg-green-100 text-green-700 p-3 rounded mb-4 text-sm font-semibold text-center">{{ session('success') }}</div>
        @endif

        <form action="{{ route('password.email') }}" method="POST" novalidate>
            @csrf
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Alamat Email</label>
                <input type="email" name="email" required class="w-full px-4 py-3 rounded-lg bg-gray-50 border border-gray-200 focus:outline-none focus:border-indigo-500 focus:bg-white transition" placeholder="">
                @error('email') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
            <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-4 rounded-lg transition-all">Kirim Link Reset</button>
        </form>
        <div class="mt-6 text-center">
            <a href="/login" class="text-indigo-600 hover:text-indigo-800 text-sm font-semibold transition">Kembali ke Halaman Login</a>
        </div>
    </div>
</body>
</html>
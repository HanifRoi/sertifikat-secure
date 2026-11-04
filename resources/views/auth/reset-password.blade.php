<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buat Password Baru | Cazh Academy</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
    <div class="bg-white p-8 rounded-xl shadow-md w-full max-w-md border border-gray-100">
        <h2 class="text-2xl font-bold text-slate-800 text-center mb-6">Buat Password Baru</h2>

        @if (session('error'))
            <div class="bg-red-100 text-red-700 p-3 rounded mb-4 text-sm font-semibold">{{ session('error') }}</div>
        @endif

        <form action="{{ route('password.update') }}" method="POST" novalidate>
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">
            
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Email</label>
                <input type="email" name="email" value="{{ $email }}" readonly class="w-full px-4 py-3 rounded-lg bg-gray-200 border border-gray-300 text-gray-500 focus:outline-none cursor-not-allowed">
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Password Baru</label>
                <input type="password" name="password" required class="w-full px-4 py-3 rounded-lg bg-gray-50 border border-gray-200 focus:outline-none focus:border-indigo-500 focus:bg-white transition">
                @error('password') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-bold mb-2">Konfirmasi Password Baru</label>
                <input type="password" name="password_confirmation" required class="w-full px-4 py-3 rounded-lg bg-gray-50 border border-gray-200 focus:outline-none focus:border-indigo-500 focus:bg-white transition">
            </div>
            <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3 px-4 rounded-lg transition-all">Simpan Password Baru</button>
        </form>
    </div>
</body>
</html>
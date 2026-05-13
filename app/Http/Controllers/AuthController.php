<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    // 1. Menampilkan halaman form login
    public function showLoginForm()
    {
        return view('auth.login');
    }

    // 2. Memproses data yang diketik saat login
    public function login(Request $request)
    {
        // Validasi inputan tidak boleh kosong
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required'
        ]);

        // Coba cocokkan dengan database
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            
            // Jika berhasil, arahkan ke Dashboard
            return redirect()->intended('/');
        }

        // Jika gagal, kembalikan ke halaman login bawa pesan error
        return back()->withErrors([
            'email' => 'Maaf, Email atau Password Anda salah.',
        ])->onlyInput('email');
    }

    // 3. Memproses Logout
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect('/login')->with('success', 'Anda berhasil keluar dari sistem.');
    }
}
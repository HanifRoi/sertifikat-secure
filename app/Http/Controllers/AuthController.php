<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use App\Models\User;
class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required'
        ], [
            'email.required' => 'Harap isi kolom Email Pembimbing.',
            'email.email'    => 'Format email yang dimasukkan tidak valid.',
            'password.required' => 'Harap isi kolom Password.'
        ]);
        $throttleKey = Str::lower($request->input('email')) . '|' . $request->ip();
        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            return back()->withErrors([
                'login_error' => "Terlalu banyak percobaan gagal. Silakan coba lagi dalam {$seconds} detik."
            ])->onlyInput('email');
        }
        $user = User::where('email', $request->email)->first();
        if (!$user) {
            RateLimiter::hit($throttleKey, 15);
            return back()->withErrors([
                'login_error' => 'Email tidak terdaftar di sistem.'
            ])->onlyInput('email');
        }
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            RateLimiter::clear($throttleKey);
            $request->session()->regenerate();
            return redirect()->intended('/')->with('success', 'Berhasil masuk ke Dashboard CAZHSECURE.');
        }
        RateLimiter::hit($throttleKey, 15);

        $retriesLeft = RateLimiter::retriesLeft($throttleKey, 5);
        $pesanError = 'Password yang Anda masukkan salah. ';
        $pesanError .= $retriesLeft > 0 ? "(Sisa percobaan: {$retriesLeft}x)" : "Akun diblokir sementara.";
        return back()->withErrors([
            'login_error' => $pesanError
        ])->onlyInput('email');
    }
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect('/login')->with('success', 'Anda berhasil keluar dari sistem.');
    }
}
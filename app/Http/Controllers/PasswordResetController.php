<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;
use Carbon\Carbon;
class PasswordResetController extends Controller
{
    public function showForgotForm() {
        return view('auth.forgot-password');
    }
    public function sendResetLink(Request $request) {
        $request->validate([
            'email' => 'required|email|exists:users,email'
        ], [
            'email.required' => 'Harap masukkan alamat email Anda.',
            'email.email'    => 'Format penulisan email tidak valid.',
            'email.exists'   => 'Maaf, alamat email ini tidak terdaftar di sistem.'
        ]);
        
        $token = Str::random(64);
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $request->email],
            ['token' => $token, 'created_at' => Carbon::now()]
        );
        $action_link = route('password.reset', ['token' => $token, 'email' => $request->email]);
        
        $body = "<h3>Reset Password - Cazh Academy</h3>
                 <p>Anda menerima email ini karena ada permintaan reset password untuk akun Anda.</p>
                 <p>Silakan klik link di bawah ini untuk membuat password baru:</p>
                 <a href='".$action_link."' style='background-color:#4F46E5; color:white; padding:10px 20px; text-decoration:none; border-radius:5px; display:inline-block; font-weight:bold;'>Reset Password Saya</a>
                 <br><br><p>Jika Anda tidak merasa meminta reset password, abaikan email ini.</p>";
        Mail::html($body, function($message) use ($request) {
            $message->to($request->email)->subject('Reset Password - Cazh Academy Secure System');
        });

        return back()->with('success', 'Link reset password telah dikirim ke email Anda! Silakan cek kotak masuk atau folder Spam.');
    }
    public function showResetForm(Request $request, $token) {
        return view('auth.reset-password', ['token' => $token, 'email' => $request->email]);
    }
    public function resetPassword(Request $request) {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'password' => 'required|min:6|confirmed',
            'token' => 'required'
        ], [
            'email.required'       => 'Harap masukkan alamat email Anda.',
            'email.exists'         => 'Maaf, alamat email ini tidak terdaftar di sistem.',
            'password.required'    => 'Harap isi kolom password baru.',
            'password.min'         => 'Password minimal harus 6 karakter.',
            'password.confirmed'   => 'Konfirmasi password tidak cocok.'
        ]);

        $check_token = DB::table('password_reset_tokens')->where([
            'email' => $request->email,
            'token' => $request->token
        ])->first();

        if(!$check_token){
            return back()->with('error', 'Token tidak valid atau sudah kedaluwarsa!');
        }

        User::where('email', $request->email)->update([
            'password' => Hash::make($request->password)
        ]);

        DB::table('password_reset_tokens')->where(['email'=> $request->email])->delete();

        return redirect('/login')->with('success', 'Password berhasil diubah! Silakan login dengan sandi baru.');
    }
}
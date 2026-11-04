<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Certificate;
use App\Models\RsaKey;        
use phpseclib3\Crypt\RSA;      
class VerifikasiController extends Controller

{
  public function index($token)
    {
        $certificate = Certificate::with('participant')
            ->where('qr_code_path', $token)
            ->first();

        if (!$certificate) {
            return response('
                <div style="text-align:center; margin-top:10vh; font-family:sans-serif;">
                    <h1 style="font-size: 50px; color: #333;">404 | Not Found</h1>
                    <h3 style="color: #d9534f; background: #fdf7f7; padding: 20px; display: inline-block; border-radius: 8px; border: 1px solid #d9534f;">
                        DITOLAK: Sertifikat tidak ditemukan atau token tidak valid!
                    </h3>
                </div>
            ', 404);
        }
        if ($certificate->status === 'dicabut') {
 
            abort(403, 'SERTIFIKAT DIBATALKAN: Dokumen ini telah dicabut secara resmi dan tidak lagi berlaku.');
        }
        return view('verifikasi.index', [
            'certificate' => $certificate,
            'token'       => $token
        ]);
    }

    public function process(Request $request)
    {
        $request->validate([
            'token'    => 'required',
            'file_pdf' => 'required|mimes:pdf|max:5120',
        ]);

        $file = $request->file('file_pdf');
        $uploadedHash = hash_file('sha3-512', $file->getRealPath());
        $certificate = Certificate::where('qr_code_path', $request->token)->firstOrFail();
        $rsaKey = RsaKey::findOrFail($certificate->key_id);

        try {
            $publicKey = RSA::loadPublicKey($rsaKey->public_key);
            $signatureBiner = base64_decode($certificate->digital_signature);
        
            $isValid = $publicKey->verify($uploadedHash, $signatureBiner);

            if ($isValid) {
                return back()->with('success', 'Kriptografi Cocok! Dokumen ini terbukti 100% ASLI dan belum pernah dimodifikasi.');
            } else {
                return back()->with('error', 'DITOLAK! Dokumen ini telah dimodifikasi (palsu) atau tidak sesuai dengan data asli.');
            }

        } catch (\Exception $e) {
           
            return back()->with('error', 'Gagal memproses algoritma kriptografi: ' . $e->getMessage());
        }
    }

}
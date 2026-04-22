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
            return abort(404, 'AWAS: Sertifikat Tidak Ditemukan atau Berpotensi Palsu!');
        }

        // --- TAMBAHKAN BLOK INI ---
        // Jika sertifikat sudah dicabut, tolak akses dan beri tahu alasannya
        if ($certificate->status === 'dicabut') {
            return abort(403, 'SERTIFIKAT DIBATALKAN: Dokumen ini telah dicabut secara resmi oleh pihak PT Cazh dan tidak lagi berlaku.');
        }
        // --------------------------

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

        // 2. HASING PDF YANG DI-UPLOAD PENGGUNA (YANG ADA STEMPELNYA)
        $uploadedHash = hash_file('sha3-512', $file->getRealPath());

        $certificate = Certificate::where('qr_code_path', $request->token)->first();

        // --- TAMBAHKAN BLOK INI JUGA ---
        if ($certificate && $certificate->status === 'dicabut') {
            return back()->with('error', 'DITOLAK! Sertifikat ini telah DICABUT/DIBATALKAN secara resmi oleh PT Cazh.');
        }

        if (!$certificate) {
            return back()->with('error', 'Akses Ditolak: Token tidak valid.');
        }

        $rsaKey = RsaKey::find($certificate->key_id);

        if (!$rsaKey) {
            return back()->with('error', 'Sistem Error: Kunci Publik tidak ditemukan.');
        }

        try {
            $publicKey = RSA::loadPublicKey($rsaKey->public_key);
            $signatureBiner = base64_decode($certificate->digital_signature);

            // 4. BUKTIKAN! (Apakah Hash PDF Berstempel = Signature Asli)
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
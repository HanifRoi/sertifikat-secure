<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Participant;
use phpseclib3\Crypt\RSA;

class SertifikatController extends Controller
{
    // Fungsi untuk menampilkan form (yang kemarin)
    public function create()
    {
        $participants = Participant::all(); 
        return view('sertifikat.create', compact('participants'));
    }

    // FUNGSI BARU UNTUK MENERIMA DATA
    public function store(Request $request)
    {
        // 1. VALIDASI DATA
        $request->validate([
            'identifier_number' => 'required',
            'name'              => 'required',
            'institution'       => 'required',
            'study_program'     => 'required', 
            'program'           => 'required', 
            'file_pdf'          => 'required|mimes:pdf|max:5120', 
        ]);

        if ($request->hasFile('file_pdf')) {
            $file = $request->file('file_pdf');
            
            // 2. HASING SHA3-512 (ALGORITMA SEMPRO-MU TETAP AMAN!)
            $fileHash = hash_file('sha3-512', $file->getRealPath());

            // 3. FINAL BOSS: RSA CRYPTOGRAPHY & DIGITAL SIGNATURE
            // Kita buat kunci RSA berkekuatan 2048-bit (Kosongkan saja withHash-nya)
            $privateKey = RSA::createKey(2048);
            $publicKey  = $privateKey->getPublicKey();

            // TRIK SKRIPSI: 
            // Kita suruh RSA "menandatangani" nilai Hash SHA3-512 yang sudah kita dapatkan,
            // bukan menandatangani file PDF-nya secara langsung!
            $signatureBiner = $privateKey->sign($fileHash);
            
            $digitalSignature = base64_encode($signatureBiner);

            // 4. SIMPAN FILE
            $fileName = time() . '_' . str_replace(' ', '_', $file->getClientOriginalName());
            $path = $file->storeAs('public/sertifikat_mentah', $fileName);

            // TAMPILKAN HASILNYA
            dd([
                'STATUS' => 'SUKSES! SKRIPSI AMAN!',
                'NILAI HASH SHA3-512' => $fileHash,
                'KUNCI PUBLIK' => $publicKey->toString('PKCS8'),
                'KUNCI PRIVAT' => $privateKey->toString('PKCS8'),
                '====== DIGITAL SIGNATURE ======' => 'Deretan teks di bawah ini adalah enkripsi RSA dari nilai Hash SHA3-512',
                'HASIL DIGITAL SIGNATURE' => $digitalSignature,
            ]);
        }

        return back()->with('error', 'Gagal mengupload file.');
    }
}
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RsaKey;
use phpseclib3\Crypt\RSA; // Panggil mesin RSA

class KriptografiController extends Controller
{
    // Menampilkan Halaman Kriptografi
    public function index()
    {
        // Ambil kunci yang sedang aktif saat ini untuk ditampilkan di layar
        $activeKey = RsaKey::where('is_active', true)->first();
        
        // Ambil riwayat kunci sebelumnya (opsional untuk ditampilkan)
        $historyKeys = RsaKey::orderBy('created_at', 'desc')->get();

        return view('kriptografi.index', compact('activeKey', 'historyKeys'));
    }

    // Fungsi untuk men-generate Kunci Master Baru
    public function generate(Request $request)
    {
        try {
            // 1. Keamanan Data: Matikan status aktif semua kunci lama 
            // (Agar sistem tidak bingung kunci mana yang dipakai untuk sertifikat baru)
            RsaKey::where('is_active', true)->update(['is_active' => false]);

            // 2. Bangkitkan Sepasang Kunci Master Baru (2048-bit)
            $privateKey = RSA::createKey(2048);
            $publicKey  = $privateKey->getPublicKey();

            // 3. Simpan ke Database
            RsaKey::create([
                'private_key' => $privateKey->toString('PKCS8'),
                'public_key'  => $publicKey->toString('PKCS8'),
                'is_active'   => true,
                'description' => 'Master Key Generator (Admin) - ' . now()->format('d M Y H:i:s')
            ]);

            // 4. Kembali ke halaman dengan pesan sukses
            return back()->with('success', 'BUM! Kunci Master RSA 2048-bit berhasil dibangkitkan dan diamankan ke database!');
            
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan saat membangkitkan kunci: ' . $e->getMessage());
        }
    }
}
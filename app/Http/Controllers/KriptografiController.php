<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\RsaKey;
use phpseclib3\Crypt\RSA;

class KriptografiController extends Controller
{
    public function index()
    {
        $activeKey = RsaKey::where('is_active', true)->first();
        $historyKeys = RsaKey::with('user')->orderBy('created_at', 'desc')->get();

        return view('kriptografi.index', compact('activeKey', 'historyKeys'));
    }
    public function generate(Request $request)
    {
        try {
            RsaKey::where('is_active', true)->update(['is_active' => false]);

            $privateKey = RSA::createKey(2048);
            $publicKey  = $privateKey->getPublicKey();

            RsaKey::create([
                'user_id'     => auth()->id(), 
                'private_key' => $privateKey->toString('PKCS8'),
                'public_key'  => $publicKey->toString('PKCS8'),
                'is_active'   => true,
                'description' => 'Master Key Generated'
            ]);

            return back()->with('success', 'Kunci RSA 2048-bit berhasil dibangkitkan dan diamankan ke database!');
            
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan saat membangkitkan kunci: ' . $e->getMessage());
        }
    }
}
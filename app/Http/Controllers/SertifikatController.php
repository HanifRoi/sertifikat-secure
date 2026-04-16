<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Participant;

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
            
            // TRIK JITU: Ambil nilai hash dari file .tmp sebelum dipindah
            // getRealPath() akan mengambil path absolut yang sangat aman dari error slash
            $fileHash = hash_file('sha3-512', $file->getRealPath());

            // Setelah sukses di-hash, baru kita simpan secara permanen
            $fileName = time() . '_' . str_replace(' ', '_', $file->getClientOriginalName());
            $path = $file->storeAs('public/sertifikat_mentah', $fileName);

            // Tampilkan hasilnya
            dd([
                'Pesan' => 'BUM! File berhasil diamankan dan di-hash tanpa error path!',
                'NILAI HASH SHA-3' => $fileHash,
                'Lokasi Simpan' => $path,
                'Data Peserta' => $request->only(['name', 'identifier_number', 'institution', 'study_program']),
                'Data Kegiatan' => $request->only(['program']),
            ]);
        }

        return back()->with('error', 'Gagal mengupload file.');
    }
}
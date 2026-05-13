<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Participant;
use App\Models\Certificate;
use App\Models\RsaKey;
use phpseclib3\Crypt\RSA;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use setasign\Fpdi\Tcpdf\Fpdi; 
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SertifikatController extends Controller
{
    /**
     * Misi C: Menampilkan Daftar Riwayat Sertifikat
     */
    public function index()
    {
        $certificates = Certificate::with('participant')->latest()->get();
        return view('sertifikat.index', compact('certificates'));
    }

    /**
     * Menampilkan Form Tambah Sertifikat
     */
    public function create()
    {
        return view('sertifikat.create');
    }

    /**
     * Misi A: Proses Pembuatan Sertifikat dengan Kriptografi
     */
    /**
     * Misi A: Proses Pembuatan Sertifikat dengan Kriptografi
     */
    public function store(Request $request)
    {
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

            // 1. Cek Kunci RSA yang sedang Aktif
            $activeKey = RsaKey::where('is_active', true)->first();
            if (!$activeKey) {
                return back()->with('error', 'Kunci Master RSA belum aktif. Silakan aktifkan kunci di menu Kriptografi.');
            }

            // 2. Generate Token Unik (15 Karakter) untuk URL Verifikasi
            $token = Str::random(15);
            $verificationUrl = url('/verify/' . $token); 

            // 3. Penyiapan Path & Nama File
            $fileName = time() . '_' . str_replace(' ', '_', $file->getClientOriginalName());
            $mentahPathRelative = $file->storeAs('public/sertifikat_mentah', $fileName);
            $mentahAbsolutePath = Storage::path($mentahPathRelative);
            
            $finalFileName = 'SECURED_' . $fileName;
            $finalPathRelative = 'public/sertifikat_final/' . $finalFileName;
            
            if (!Storage::exists('public/sertifikat_final')) {
                Storage::makeDirectory('public/sertifikat_final');
            }
            $finalAbsolutePath = Storage::path($finalPathRelative);

            // 4. STEMPEL QR CODE KE PDF
            $pdf = new Fpdi();
            $pdf->setPrintHeader(false);
            $pdf->setPrintFooter(false);
            $pdf->SetAutoPageBreak(false); 

            $pageCount = $pdf->setSourceFile($mentahAbsolutePath);
            
            for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
                $templateId = $pdf->importPage($pageNo);
                $size = $pdf->getTemplateSize($templateId);

                $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
                $pdf->useTemplate($templateId);

                if ($pageNo == 1) {
                    $style = array(
                        'border' => false,
                        'padding' => 0,
                        'fgcolor' => array(0, 0, 0), 
                        'bgcolor' => array(255, 255, 255) 
                    );
                    $pdf->write2DBarcode($verificationUrl, 'QRCODE,M', 218, 178, 30, 30, $style, 'N');
                    
                    $pdf->SetFont('helvetica', 'B', 8);
                    $pdf->SetXY(218, 209); 
                    $pdf->Cell(30, 4, 'TERVERIFIKASI', 0, 0, 'C');
                }
            }

            $pdf->Output($finalAbsolutePath, 'F');

            // --- 5. PROSES KRIPTOGRAFI & PENGUKURAN PERFORMA ---
            
            // A. Ukur Kecepatan Hashing SHA3-512
            $startHashTime = microtime(true);
            $fileHashHex = hash_file('sha3-512', $finalAbsolutePath);
            $endHashTime = microtime(true);
            $waktuHashing = $endHashTime - $startHashTime; // Hasil dalam detik

            // B. Konversi ke Biner Full (512-bit) untuk Uji Avalanche Effect
            $fileHashBinerFull = '';
            foreach (str_split($fileHashHex) as $char) {
                // Ubah setiap 1 karakter hex menjadi 4 digit biner (0000 s/d 1111)
                $fileHashBinerFull .= str_pad(base_convert($char, 16, 2), 4, '0', STR_PAD_LEFT);
            }

            // C. Ukur Kecepatan Enkripsi (Signing) RSA
            $startRsaTime = microtime(true);
            $privateKey = RSA::loadPrivateKey($activeKey->private_key);
            $signatureBiner = $privateKey->sign($fileHashHex); // Yang di-sign tetap Hex-nya
            $digitalSignature = base64_encode($signatureBiner);
            $endRsaTime = microtime(true);
            $waktuRsa = $endRsaTime - $startRsaTime; // Hasil dalam detik


            // 6. SIMPAN DATA KE DATABASE
            $participant = Participant::firstOrCreate(
                ['identifier_number' => $request->identifier_number], 
                [
                    'name'          => $request->name,
                    'institution'   => $request->institution,
                    'study_program' => $request->study_program,
                ]
            );

            $certificate = Certificate::create([
                'participant_id'    => $participant->id,
                'user_id'           => auth()->id() ?? 1,
                'key_id'            => $activeKey->id,      
                'program'           => $request->program,
                'file_path'         => $finalPathRelative,  
                'document_hash'     => $fileHashHex, // Simpan Hex di DB agar efisien
                'digital_signature' => $digitalSignature,
                'qr_code_path'      => $token,              
                'publish_date'      => now()->toDateString(),
                'status'            => 'secured'            
            ]);

            Storage::delete($mentahPathRelative);

            $qrCode = QrCode::size(200)->generate($verificationUrl);
            $downloadLink = route('sertifikat.download', ['filename' => $finalFileName]);

            // Tambahkan data performa dan biner ke View
            return view('sertifikat.success', [
                'qrCode'        => $qrCode,
                'hash'          => $fileHashHex,
                'hashBiner'     => $fileHashBinerFull, // Lempar Biner 512 bit ke View
                'waktuHashing'  => number_format($waktuHashing, 5), // Format 5 angka di belakang koma
                'waktuRsa'      => number_format($waktuRsa, 5), 
                'signature'     => $digitalSignature,
                'peserta'       => $participant->name,
                'downloadLink'  => $downloadLink,
                'token'         => $token
            ]);
        }

        return back()->with('error', 'Gagal memproses file PDF.');
    }

    /**
     * Misi C: Fitur Pencabutan Sertifikat
     */
    public function revoke($id)
    {
        $certificate = Certificate::findOrFail($id);
        
        $certificate->update([
            'status' => 'dicabut'
        ]);

        return back()->with('success', 'Sertifikat atas nama ' . $certificate->participant->name . ' telah berhasil dicabut.');
    }

    /**
     * Fungsi untuk Mengunduh PDF yang Sudah Tersertifikasi
     */
    public function downloadFile($filename)
    {
        $path = 'public/sertifikat_final/' . $filename;
        
        if (Storage::exists($path)) {
            return Storage::download($path);
        }

        return abort(404, 'File sertifikat tidak ditemukan di server.');
    }
}
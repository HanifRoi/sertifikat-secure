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
use Illuminate\Support\Str; // TAMBAHAN UNTUK GENERATE TOKEN

class SertifikatController extends Controller
{
    public function create()
    {
        return view('sertifikat.create');
    }

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

            // 1. CEK KUNCI MASTER DULU
            $activeKey = RsaKey::where('is_active', true)->first();
            if (!$activeKey) {
                return back()->with('error', 'Kunci Master RSA belum aktif.');
            }

            // 2. GENERATE TOKEN ACAK UNTUK QR CODE (Pengganti potongan Hash)
            $token = Str::random(15);
            $verificationUrl = url('/verify/' . $token); 

            // 3. SIAPKAN PATH FILE
            $fileName = time() . '_' . str_replace(' ', '_', $file->getClientOriginalName());
            $mentahPathRelative = $file->storeAs('public/sertifikat_mentah', $fileName);
            $mentahAbsolutePath = Storage::path($mentahPathRelative);
            
            $finalFileName = 'SECURED_' . $fileName;
            $finalPathRelative = 'public/sertifikat_final/' . $finalFileName;
            
            if (!Storage::exists('public/sertifikat_final')) {
                Storage::makeDirectory('public/sertifikat_final');
            }
            $finalAbsolutePath = Storage::path($finalPathRelative);

            // ==========================================
            // 4. STEMPEL QR DULUAN (IDE JENIUS HANIF)
            // ==========================================
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

            // SIMPAN PDF FINAL YANG SUDAH DISTEMPEL
            $pdf->Output($finalAbsolutePath, 'F');

            // ==========================================
            // 5. BARU KITA PROSES KRIPTOGRAFINYA! 
            // (Hash dokumen final, bukan mentahan)
            // ==========================================
            
            $fileHash = hash_file('sha3-512', $finalAbsolutePath);

            $privateKey = RSA::loadPrivateKey($activeKey->private_key);
            $signatureBiner = $privateKey->sign($fileHash);
            $digitalSignature = base64_encode($signatureBiner);

            // ==========================================
            // 6. SIMPAN SEMUANYA KE DATABASE
            // ==========================================
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
                'file_path'         => $finalPathRelative,  // Simpan path final
                'document_hash'     => $fileHash,           // Hash dokumen final!
                'digital_signature' => $digitalSignature,
                'qr_code_path'      => $token,              // KITA SIMPAN TOKEN DI SINI
                'publish_date'      => now()->toDateString(),
                'status'            => 'secured'            
            ]);

            // Optional: Hapus file mentah agar server tidak penuh
            Storage::delete($mentahPathRelative);

            // ==========================================

            $qrCode = QrCode::size(200)->generate($verificationUrl);
            $downloadLink = route('sertifikat.download', ['filename' => $finalFileName]);

            return view('sertifikat.success', [
                'qrCode'       => $qrCode,
                'hash'         => $fileHash,
                'signature'    => $digitalSignature,
                'peserta'      => $participant->name,
                'downloadLink' => $downloadLink
            ]);
        }

        return back()->with('error', 'Gagal memproses file.');
    }

    public function downloadFile($filename)
    {
        $path = 'public/sertifikat_final/' . $filename;
        if (Storage::exists($path)) {
            return Storage::download($path);
        }
        return abort(404, 'Maaf, file sertifikat tidak ditemukan di server.');
    }
}
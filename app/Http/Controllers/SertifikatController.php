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
use Carbon\Carbon;

class SertifikatController extends Controller
{
    public function index(Request $request)
    {
        // 1. Ambil daftar instansi untuk dropdown (Wajib ada agar dropdown tidak kosong)
        $institutions = Participant::select('institution')
                        ->distinct()
                        ->whereNotNull('institution')
                        ->pluck('institution');

        $search = $request->input('search');
        $status = $request->input('status');
        $program = $request->input('program');
        
        // Tangkap input filter baru
        $institution = $request->input('institution');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $certificates = Certificate::with(['participant', 'user'])
            ->when($search, function($query) use ($search) {
                $query->where('certificate_number', 'like', "%{$search}%")
                    ->orWhereHas('participant', function($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%")
                          ->orWhere('identifier_number', 'like', "%{$search}%");
                    });
            })
            ->when($status, function($query) use ($status) {
                $query->where('status', $status);
            })
            ->when($program, function($query) use ($program) {
                $query->whereHas('participant', function($q) use ($program) {
                    $q->where('program', $program);
                });
            })
           
            ->when($institution, function($query) use ($institution) {
                $query->whereHas('participant', function($q) use ($institution) {
                    $q->where('institution', $institution);
                });
            })
           
            ->when($startDate, function($query) use ($startDate) {
                $query->whereDate('publish_date', '>=', $startDate);
            })
            ->when($endDate, function($query) use ($endDate) {
                $query->whereDate('publish_date', '<=', $endDate);
            })
            ->latest()
            ->paginate(10)
            ->withQueryString(); 

        return view('sertifikat.index', compact('certificates', 'institutions'));
    }

    public function create()
    {
        $participants = Participant::whereNotIn('id', function($query) {
            $query->select('participant_id')
                  ->from('certificates')
                  ->where('status', 'secured');
        })->get();

        return view('sertifikat.create', compact('participants'));
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'participant_id' => 'required|exists:participants,id',
            'grades'         => 'required|array',
        ]);

        $participant = Participant::findOrFail($request->participant_id);

        $activeKey = RsaKey::where('is_active', true)->first();
        if (!$activeKey) {
            return back()->with('error', 'Kunci Master RSA belum aktif. Silakan aktifkan kunci di menu Kriptografi.');
        }

        $tahun = date('Y');
        $kodeProgram = ($participant->program == 'Magang Industri') ? 'MAGANG' : 'PKL';

        $lastCert = Certificate::whereYear('created_at', $tahun)
                               ->where('certificate_number', 'like', "%/$kodeProgram/%")
                               ->latest()
                               ->first();
                               
        $noUrut = $lastCert ? ((int) substr($lastCert->certificate_number, -3)) + 1 : 1;
        $autoNumber = "CZH/" . $kodeProgram . "/" . $tahun . "/" . str_pad($noUrut, 3, '0', STR_PAD_LEFT);

        // 3. Kalkulasi Nilai, Rata-rata, dan Predikat
        $totalNilai = array_sum($request->grades);
        $rataRata = $totalNilai / 10;
        
        $predikat = '';
        if ($rataRata > 90) { $predikat = 'Amat Baik'; }
        elseif ($rataRata >= 75) { $predikat = 'Baik'; }
        elseif ($rataRata >= 65) { $predikat = 'Cukup'; }
        else { $predikat = 'Kurang'; }

        // 4. Penyiapan Teks Dinamis & Tanggal
        Carbon::setLocale('id');
        $tanggalSekarang = Carbon::now()->translatedFormat('j F Y'); 
        
        $tglMulai = Carbon::parse($participant->start_date)->translatedFormat('j F Y');
        $tglSelesai = Carbon::parse($participant->end_date)->translatedFormat('j F Y');
        $rentangMagang = $tglMulai . ' s/d ' . $tglSelesai;

        // -- Teks Otomatis --
        $judulSertifikat = strtoupper('Sertifikat ' . $participant->program);
        $teksPembuka = "Dengan ini CAZH ACADEMY memberikan penghargaan sekaligus keterangan berupa " . ucwords(strtolower($judulSertifikat));
        
        // PERBAIKAN: Mengubah jadi tag HTML agar Tanggal dan Nilai menjadi BOLD
        $htmlPengantar1 = "Dalam kegiatan " . $participant->program . " dari tanggal <b>" . $rentangMagang . "</b> dengan hasil penilaian <b>" . $rataRata . " (" . $predikat . ")</b>.";
        $teksPengantar2 = "Berikut komponen penilaian :";
        
        // --- SUDAH DIUBAH KE PEMBIMBING ---
        $namaPembimbing = auth()->user()->name ?? 'Pembimbing'; // Ambil nama pembimbing yang login

        $token = Str::random(15);
        $verificationUrl = url('/verify/' . $token); 

        // 5. Penyiapan Path Template
        $templatePath = storage_path('app/templates/Dummy-Sertifikat.pdf'); 
        if (!file_exists($templatePath)) {
            return back()->with('error', 'File template sertifikat tidak ditemukan di storage/app/templates/');
        }

        $finalFileName = 'SECURED_' . time() . '_PESERTA_' . $participant->id . '.pdf';
        $finalPathRelative = 'public/sertifikat_final/' . $finalFileName;
        if (!Storage::exists('public/sertifikat_final')) {
            Storage::makeDirectory('public/sertifikat_final');
        }
        $finalAbsolutePath = Storage::path($finalPathRelative);

        // --- 6. GENERATE PDF DENGAN FPDI ---
        $pdf = new Fpdi();
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->SetAutoPageBreak(false); 

        $pageCount = $pdf->setSourceFile($templatePath);
        $templateId = $pdf->importPage(1); 
        $size = $pdf->getTemplateSize($templateId);

        $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
        $pdf->useTemplate($templateId);

        // --- A. TULIS JUDUL (WARNA UNGU CAZH) & TEKS PEMBUKA ---
        
        $pdf->SetTextColor(103, 58, 183);
        $pdf->SetFont('times', 'BI', 24);
        $pdf->SetXY(0, 32); 
        $pdf->Cell(0, 10, $judulSertifikat, 0, 0, 'C'); 

        $pdf->SetTextColor(0, 0, 0);

        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->SetXY(0, 42); 
        $pdf->Cell(0, 10, 'No. ' . $autoNumber, 0, 0, 'C');

        $pdf->SetFont('helvetica', '', 12);
        $pdf->SetXY(25, 52); 
        $pdf->Cell(0, 10, $teksPembuka, 0, 0, 'L');

        // --- B. TULIS IDENTITAS PESERTA ---
        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->SetXY(95, 70); 
        $pdf->Cell(100, 10, $participant->name, 0, 0, 'L');
        
        $pdf->SetFont('helvetica', '', 12);
        $pdf->SetXY(95, 75); 
        $pdf->Cell(100, 10, $participant->identifier_number, 0, 0, 'L');
        
        $pdf->SetXY(95, 80); 
        $pdf->Cell(100, 10, $participant->study_program, 0, 0, 'L');
        
        $pdf->SetXY(95, 85); 
        $pdf->Cell(100, 10, $participant->institution, 0, 0, 'L');

        // --- C. TULIS KALIMAT PENGANTAR (2 BARIS RATA KIRI) ---
        $pdf->SetFont('helvetica', '', 11);
        
        // PERBAIKAN: Menggunakan writeHTMLCell untuk mengeksekusi tag <b>
        // Parameter: (width, height, x, y, html, border, ln, fill, reseth, align, autopadding)
        $pdf->writeHTMLCell(0, 10, 25, 100, $htmlPengantar1, 0, 1, false, true, 'L', true);
        
        $pdf->SetXY(25, 106); 
        $pdf->Cell(0, 10, $teksPengantar2, 0, 0, 'L'); 

        // --- D. TULIS NILAI (10 Komponen) ---
        $grades = $request->grades;
        $pdf->SetFont('helvetica', '', 11);
        
        $pdf->SetXY(112, 129); $pdf->Cell(20, 10, $grades['inisiatif'], 0, 0, 'C');
        $pdf->SetXY(112, 136); $pdf->Cell(20, 10, $grades['kedisiplinan'], 0, 0, 'C');
        $pdf->SetXY(112, 143); $pdf->Cell(20, 10, $grades['kejujuran'], 0, 0, 'C');
        $pdf->SetXY(112, 150); $pdf->Cell(20, 10, $grades['ketelitian'], 0, 0, 'C');
        $pdf->SetXY(112, 157); $pdf->Cell(20, 10, $grades['kerja_sama'], 0, 0, 'C');

        $pdf->SetXY(228, 129); $pdf->Cell(20, 10, $grades['pemahaman_tugas'], 0, 0, 'C');
        $pdf->SetXY(228, 136); $pdf->Cell(20, 10, $grades['komunikasi'], 0, 0, 'C');
        $pdf->SetXY(228, 143); $pdf->Cell(20, 10, $grades['perencanaan_projek'], 0, 0, 'C');
        $pdf->SetXY(228, 150); $pdf->Cell(20, 10, $grades['pengerjaan_projek'], 0, 0, 'C');
        $pdf->SetXY(228, 157); $pdf->Cell(20, 10, $grades['presentasi_projek'], 0, 0, 'C');

        // --- E. TULIS TANGGAL & TANDA TANGAN (PEMBIMBING) ---
        $pdf->SetFont('helvetica', '', 12);
        $pdf->SetXY(195, 168); 
        $pdf->Cell(50, 10, $tanggalSekarang, 0, 0, 'L'); 

        $pdf->SetFont('helvetica', 'BU', 12);
        $pdf->SetXY(150, 200); 
        // --- SUDAH DIUBAH KE PEMBIMBING ---
        $pdf->Cell(60, 10, strtoupper($namaPembimbing), 0, 0, 'C');

        $style = array('border' => false, 'padding' => 0, 'fgcolor' => array(0, 0, 0), 'bgcolor' => array(255, 255, 255));
        $pdf->write2DBarcode($verificationUrl, 'QRCODE,M', 230, 175, 25, 25, $style, 'N');
        
        $pdf->SetFont('helvetica', 'B', 8);
        $pdf->SetXY(230, 201); 
        $pdf->Cell(25, 4, 'TERVERIFIKASI', 0, 0, 'C');

        $pdf->Output($finalAbsolutePath, 'F');

        // --- 7. PROSES KRIPTOGRAFI ---
        $startHashTime = microtime(true);
        $fileHashHex = hash_file('sha3-512', $finalAbsolutePath);
        $endHashTime = microtime(true);
        $waktuHashing = $endHashTime - $startHashTime; 

        $fileHashBinerFull = '';
        foreach (str_split($fileHashHex) as $char) {
            $fileHashBinerFull .= str_pad(base_convert($char, 16, 2), 4, '0', STR_PAD_LEFT);
        }

        $startRsaTime = microtime(true);
        $privateKey = RSA::loadPrivateKey($activeKey->private_key);
        $signatureBiner = $privateKey->sign($fileHashHex); 
        $digitalSignature = base64_encode($signatureBiner);
        $endRsaTime = microtime(true);
        $waktuRsa = $endRsaTime - $startRsaTime; 

        // --- 8. SIMPAN DATA KE DATABASE ---
        $certificate = Certificate::create([
            'participant_id'     => $participant->id,
            'user_id'            => auth()->id() ?? 1,
            'key_id'             => $activeKey->id,      
            'certificate_number' => $autoNumber, 
            'grades'             => json_encode($request->grades),
            'file_path'          => $finalPathRelative,  
            'document_hash'      => $fileHashHex, 
            'digital_signature'  => $digitalSignature,
            'qr_code_path'       => $token,              
            'publish_date'       => now()->toDateString(),
            'status'             => 'secured'            
        ]);

        $qrCode = QrCode::size(200)->generate($verificationUrl);
        $downloadLink = route('sertifikat.download', ['filename' => $finalFileName]);

        // PERUBAHAN: Gunakan redirect()->route()->with() untuk Flash Session
        return redirect()->route('sertifikat.success')->with([
            'qrCode'        => (string) $qrCode, // Cast ke string agar aman masuk session
            'hash'          => $fileHashHex,
            'hashBiner'     => $fileHashBinerFull,
            'waktuHashing'  => number_format($waktuHashing, 5),
            'waktuRsa'      => number_format($waktuRsa, 5), 
            'signature'     => $digitalSignature,
            'peserta'       => $participant->name,
            'downloadLink'  => $downloadLink,
            'token'         => $token
        ]);
    }
    
    // --- TAMBAHAN: FUNGSI BARU UNTUK MENANGKAP REDIRECT HALAMAN SUKSES ---
    public function showSuccess()
    {
        // Cegah akses langsung ke URL jika tidak ada sesi
        if (!session()->has('token')) {
            return redirect()->route('sertifikat.create');
        }

        return view('sertifikat.success', [
            'qrCode'        => session('qrCode'),
            'hash'          => session('hash'),
            'hashBiner'     => session('hashBiner'),
            'waktuHashing'  => session('waktuHashing'),
            'waktuRsa'      => session('waktuRsa'),
            'signature'     => session('signature'),
            'peserta'       => session('peserta'),
            'downloadLink'  => session('downloadLink'),
            'token'         => session('token'),
        ]);
    }

    public function revoke($id)
    {
        $certificate = Certificate::findOrFail($id);
        $certificate->update(['status' => 'dicabut']);
        return back()->with('success', 'Sertifikat atas nama ' . $certificate->participant->name . ' telah dicabut.');
    }

    public function downloadFile($filename)
    {
        $path = 'public/sertifikat_final/' . $filename;
        if (Storage::exists($path)) {
            return Storage::download($path);
        }
        return abort(404, 'File sertifikat tidak ditemukan di server.');
    }
    
    public function previewFile($filename)
    {
        $path = 'public/sertifikat_final/' . $filename;
        
        if (Storage::exists($path)) {
            // response()->file() akan membuka PDF langsung di browser (tab baru)
            return response()->file(Storage::path($path));
        }

        return abort(404, 'File sertifikat tidak ditemukan di server.');
    }
}
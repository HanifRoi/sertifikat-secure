<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Certificate;
use App\Models\RsaKey;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Menghitung Quick Stats (Kartu Atas)
        $totalSertifikat = Certificate::count();
        $sertifikatAktif = Certificate::where('status', 'secured')->count();
        $sertifikatDicabut = Certificate::where('status', 'dicabut')->count();
        
        // Cek status Kunci Master
        $rsaKey = RsaKey::where('is_active', true)->first();

        // 2. Mengambil 5 Riwayat Terakhir untuk Tabel Log
        $recentCertificates = Certificate::with('participant')->latest()->take(5)->get();

        // 3. Menyiapkan Data untuk Grafik (Jumlah penerbitan per bulan di tahun ini)
        $certificatesThisYear = Certificate::whereYear('publish_date', date('Y'))->get();
        
        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'];
        $monthlyCounts = array_fill(0, 12, 0); // Siapkan array berisi 12 angka nol

        foreach ($certificatesThisYear as $cert) {
            $monthIndex = Carbon::parse($cert->publish_date)->month - 1; // Ambil bulan (1-12) dan kurangi 1 untuk index array (0-11)
            $monthlyCounts[$monthIndex]++;
        }

        return view('dashboard.index', compact(
            'totalSertifikat', 'sertifikatAktif', 'sertifikatDicabut', 
            'rsaKey', 'recentCertificates', 'months', 'monthlyCounts'
        ));
    }
}
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
        $totalSertifikat = Certificate::count();
        $sertifikatAktif = Certificate::where('status', 'secured')->count();
        $sertifikatDicabut = Certificate::where('status', 'dicabut')->count();

        $rsaKey = RsaKey::where('is_active', true)->first();
        $recentCertificates = Certificate::with('participant')->latest()->take(5)->get();
        $certificatesThisYear = Certificate::whereYear('publish_date', date('Y'))->get();
        
        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'];
        $monthlyCounts = array_fill(0, 12, 0);

        foreach ($certificatesThisYear as $cert) {
            $monthIndex = Carbon::parse($cert->publish_date)->month - 1;
            $monthlyCounts[$monthIndex]++;
        }

        return view('dashboard.index', compact(
            'totalSertifikat', 'sertifikatAktif', 'sertifikatDicabut', 
            'rsaKey', 'recentCertificates', 'months', 'monthlyCounts'
        ));
    }
}
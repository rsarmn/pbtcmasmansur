<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Kamar;
use App\Models\Pengunjung;

class AdminController extends Controller
{
    public function __construct()
    {
        // pakai FQCN langsung agar tidak tergantung alias di Kernel
        $this->middleware(\App\Http\Middleware\AdminAuth::class);
    }

    public function dashboard()
    {
        $totalKamar = Kamar::count();
        $kamarKosong = Kamar::where('status','kosong')->count();
        $kamarTerisi = max(0, $totalKamar - $kamarKosong);
        // Hanya hitung pengunjung yang sudah approved
        $jumlahPengunjung = Pengunjung::whereIn('payment_status', ['paid', 'lunas'])->count();
        // recent activity: latest approved bookings, today's check-ins, upcoming check-outs
        $recentBookings = Pengunjung::whereIn('payment_status', ['paid', 'lunas'])->latest()->take(6)->get();
        $today = now()->toDateString();
        $todayCheckins = Pengunjung::whereIn('payment_status', ['paid', 'lunas'])->whereDate('check_in', $today)->get();
        $upcomingCheckouts = Pengunjung::whereIn('payment_status', ['paid', 'lunas'])->whereDate('check_out', '>=', $today)->orderBy('check_out')->take(6)->get();

        return view('admin.dashboard', compact(
            'totalKamar','kamarKosong','kamarTerisi','jumlahPengunjung',
            'recentBookings','todayCheckins','upcomingCheckouts'
        ));
    }
}

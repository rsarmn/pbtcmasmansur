<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Kamar;
use App\Models\Pengunjung;
use Illuminate\Support\Facades\DB;

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
        // Per-hari metrics (hari ini)
        $today = now()->toDateString();

        // bookings active today
        $activeBookings = Pengunjung::whereIn('payment_status', ['paid', 'lunas'])
            ->whereDate('check_in', '<=', $today)
            ->whereDate('check_out', '>=', $today)
            ->get();

        // Count unique occupied room codes for today
        $codes = [];
        foreach ($activeBookings as $booking) {
            if ($booking->kode_kamar) {
                $rooms = array_filter(array_map('trim', explode(',', $booking->kode_kamar)));
                $codes = array_merge($codes, $rooms);
            }
        }
        $uniqueCodes = array_unique($codes);
        $kamarTerisi = count($uniqueCodes);
        $kamarKosong = max(0, $totalKamar - $kamarTerisi);

        // Jumlah pengunjung hari ini = SUM jumlah_peserta untuk bookings aktif hari ini
        $jumlahPengunjung = Pengunjung::whereIn('payment_status', ['paid', 'lunas'])
            ->whereDate('check_in', '<=', $today)
            ->whereDate('check_out', '>=', $today)
            ->select(DB::raw('COALESCE(SUM(CAST(jumlah_peserta AS UNSIGNED)), 0) as total'))
            ->value('total');

        // Check-ins hari ini (jumlah peserta yang mulai menginap hari ini)
        $checkinHariIni = Pengunjung::whereIn('payment_status', ['paid', 'lunas'])
            ->whereDate('check_in', $today)
            ->select(DB::raw('COALESCE(SUM(CAST(jumlah_peserta AS UNSIGNED)), 0) as total'))
            ->value('total');

        // Occupancy rate (persentase kamar terisi hari ini)
        $occupancyRate = $totalKamar ? round(($kamarTerisi / $totalKamar) * 100, 1) : 0;
        // current date for todays/checkin queries
        $today = now()->toDateString();
        // recent activity: latest approved bookings
        $recentBookings = Pengunjung::whereIn('payment_status', ['paid', 'lunas'])->latest()->take(6)->get();
        $todayCheckins = Pengunjung::whereIn('payment_status', ['paid', 'lunas'])->whereDate('check_in', $today)->get();
        $upcomingCheckouts = Pengunjung::whereIn('payment_status', ['paid', 'lunas'])->whereDate('check_out', '>=', $today)->orderBy('check_out')->take(6)->get();

        return view('admin.dashboard', compact(
            'totalKamar','kamarKosong','kamarTerisi','jumlahPengunjung',
            'recentBookings','todayCheckins','upcomingCheckouts', 'checkinHariIni', 'occupancyRate'
        ));
    }
}

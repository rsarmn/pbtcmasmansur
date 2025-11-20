<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kamar;
use App\Models\Pengunjung;

class ReportController extends Controller
{
    public function __construct()
    {
        // admin routes protected by AdminAuth middleware in routes/web.php
    }

    // Admin view: monthly report
    public function monthly(Request $r)
    {
        // Get month from request or default to current month
        $selectedMonth = $r->input('month', now()->format('Y-m'));
        
        try {
            $date = \Carbon\Carbon::createFromFormat('Y-m', $selectedMonth);
        } catch (\Exception $e) {
            $date = now();
            $selectedMonth = $date->format('Y-m');
        }

        $monthStart = $date->copy()->startOfMonth()->toDateString();
        $monthEnd = $date->copy()->endOfMonth()->toDateString();

        $totalKamar = Kamar::count();
        $kamarKosong = Kamar::where('status','kosong')->count();
        $kamarTerisi = max(0, $totalKamar - $kamarKosong);
        $jumlahPengunjung = Pengunjung::count();

        // bookings in selected month (by check_in)
        $bookingsThisMonth = Pengunjung::whereBetween('check_in', [$monthStart, $monthEnd])
            ->orderBy('check_in', 'asc')
            ->get();

        return view('admin.report_monthly', compact(
            'totalKamar','kamarKosong','kamarTerisi','jumlahPengunjung','bookingsThisMonth','monthStart','monthEnd','selectedMonth'
        ));
    }

    // Public JSON endpoint for testing or lightweight integrations
    public function monthlyJson(Request $r)
    {
        $month = $r->input('month'); // optional YYYY-MM format
        if ($month) {
            try {
                $dt = \Carbon\Carbon::createFromFormat('Y-m', $month);
            } catch (\Exception $e) {
                $dt = now();
            }
        } else {
            $dt = now();
        }

        $start = $dt->copy()->startOfMonth()->toDateString();
        $end = $dt->copy()->endOfMonth()->toDateString();

        $totalKamar = Kamar::count();
        $kamarKosong = Kamar::where('status','kosong')->count();
        $kamarTerisi = max(0, $totalKamar - $kamarKosong);
        $jumlahPengunjung = Pengunjung::count();
        $bookingsCount = Pengunjung::whereBetween('check_in', [$start, $end])->count();

        return response()->json([
            'month' => $dt->format('Y-m'),
            'total_kamar' => $totalKamar,
            'kamar_kosong' => $kamarKosong,
            'kamar_terisi' => $kamarTerisi,
            'jumlah_pengunjung' => $jumlahPengunjung,
            'bookings_this_month' => $bookingsCount,
        ]);
    }

    // Download monthly report as PDF (reuse BookingController export if available)
    public function exportPdf(Request $r)
    {
        // reuse bookings export in BookingController if present
        $bookings = Pengunjung::latest()->get();
        $meta = [
            'title' => 'Laporan Booking Penginapan',
            'date' => now()->format('d M Y'),
        ];
        if (class_exists('\Barryvdh\DomPDF\Facade\Pdf')) {
            $pdfFacade = '\Barryvdh\DomPDF\Facade\Pdf';
            $pdf = call_user_func([$pdfFacade, 'loadView'], 'admin.booking.pdf', compact('bookings','meta'));
            $filename = 'report-'.now()->format('Ymd-His').'.pdf';
            return $pdf->download($filename);
        }

        return response()->view('admin.booking.pdf', compact('bookings','meta'))
            ->header('Content-Type', 'text/html');
    }

    // Export bookings and room status as CSV (Excel-friendly)
    public function exportCsv(Request $r)
    {
        $bookings = Pengunjung::latest()->get();
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="report-'.now()->format('Ymd-His').'.csv"',
        ];

        $columns = ['Nama','No Identitas','Jenis Tamu','Check In','Check Out','Kode Kamar','Payment Status','No Telp'];

        $callback = function() use ($bookings, $columns) {
            $fh = fopen('php://output', 'w');
            fputcsv($fh, $columns);
            foreach ($bookings as $b) {
                fputcsv($fh, [
                    $b->nama,
                    $b->no_identitas,
                    $b->jenis_tamu,
                    $b->check_in,
                    $b->check_out,
                    $b->nomor_kamar,
                    $b->payment_status_label,
                    $b->no_telp ?? $b->no_telp_pic ?? '-',
                ]);
            }
            fclose($fh);
        };

        return response()->stream($callback, 200, $headers);
    }

    // Export monthly report as PDF (or print-ready HTML)
    public function exportMonthlyPdf(Request $r)
    {
        $selectedMonth = $r->input('month', now()->format('Y-m'));
        
        try {
            $date = \Carbon\Carbon::createFromFormat('Y-m', $selectedMonth);
        } catch (\Exception $e) {
            $date = now();
        }

        $monthStart = $date->copy()->startOfMonth()->toDateString();
        $monthEnd = $date->copy()->endOfMonth()->toDateString();

        $bookings = Pengunjung::whereBetween('check_in', [$monthStart, $monthEnd])
            ->orderBy('check_in', 'asc')
            ->get();
        
        $totalKamar = Kamar::count();
        $kamarKosong = Kamar::where('status','kosong')->count();
        $kamarTerisi = max(0, $totalKamar - $kamarKosong);

        $meta = [
            'title' => 'Laporan Bulanan - ' . $date->format('F Y'),
            'date' => now()->format('d M Y H:i'),
            'month' => $date->format('F Y'),
            'total_kamar' => $totalKamar,
            'kamar_kosong' => $kamarKosong,
            'kamar_terisi' => $kamarTerisi,
            'total_booking' => $bookings->count(),
        ];

        $filename = 'laporan-bulanan-'.$date->format('Y-m').'.pdf';

        // Try to use DomPDF if available
        if (class_exists('\Barryvdh\DomPDF\Facade\Pdf')) {
            try {
                $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.report.monthly_pdf', compact('bookings','meta'));
                $pdf->setPaper('A4', 'portrait');
                return $pdf->download($filename);
            } catch (\Exception $e) {
                // Continue to fallback
            }
        }

        // Fallback: Return print-ready HTML page with auto-print script
        // User can print to PDF using browser's Print > Save as PDF
        return view('admin.report.monthly_pdf_print', compact('bookings','meta','filename'));
    }

    // Export monthly report as CSV
    public function exportMonthlyCsv(Request $r)
    {
        $selectedMonth = $r->input('month', now()->format('Y-m'));
        
        try {
            $date = \Carbon\Carbon::createFromFormat('Y-m', $selectedMonth);
        } catch (\Exception $e) {
            $date = now();
        }

        $monthStart = $date->copy()->startOfMonth()->toDateString();
        $monthEnd = $date->copy()->endOfMonth()->toDateString();

        $bookings = Pengunjung::whereBetween('check_in', [$monthStart, $monthEnd])
            ->orderBy('check_in', 'asc')
            ->get();

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="laporan-bulanan-'.$date->format('Y-m').'.csv"',
        ];

        $columns = ['No','Nama','Jenis Tamu','No Identitas/PIC','Check In','Check Out','Kode Kamar','Status Pembayaran','No Telp','Jumlah Kamar','Special Request'];

        $callback = function() use ($bookings, $columns) {
            $fh = fopen('php://output', 'w');
            // Add BOM for Excel UTF-8 support
            fprintf($fh, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($fh, $columns);
            $no = 1;
            foreach ($bookings as $b) {
                fputcsv($fh, [
                    $no++,
                    $b->nama,
                    $b->jenis_tamu,
                    $b->jenis_tamu == 'Corporate' ? $b->nama_pic : $b->no_identitas,
                    $b->check_in,
                    $b->check_out,
                    $b->kode_kamar ?? $b->nomor_kamar ?? '-',
                    $b->payment_status_label,
                    $b->no_telp_pic ?? $b->no_telp ?? '-',
                    $b->jumlah_kamar ?? 1,
                    $b->special_request ?? '-',
                ]);
            }
            fclose($fh);
        };

        return response()->stream($callback, 200, $headers);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Pengunjung;
use App\Models\Kamar;
use Illuminate\Http\Request;

class AdminBookingController extends Controller
{
    public function index(Request $request)
    {
        // Filter jenis tamu: individu / corporate
        $filter = $request->query('jenis_tamu');

        $bookings = Pengunjung::when($filter, function ($q) use ($filter) {
                $q->where('jenis_tamu', $filter);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.booking.index', compact('bookings', 'filter'));
    }

    public function show($id)
    {
        $booking = Pengunjung::findOrFail($id);
        // booking->kode_kamar may be a comma-separated list; use first kode
        $first = null;
        if (!empty($booking->kode_kamar)) {
            $parts = explode(',', $booking->kode_kamar);
            $first = trim($parts[0]);
        }
        $kamar = null;
        if ($first) {
            $kamar = Kamar::where('kode_kamar', $first)->first();
            if (!$kamar && is_numeric($first)) {
                $kamar = Kamar::find($first);
            }
        }

        return view('admin.booking.detail', compact('booking', 'kamar'));
    }

    public function destroy($id)
    {
        $booking = Pengunjung::findOrFail($id);
        $nama = $booking->jenis_tamu == 'corporate' ? $booking->nama_pic : $booking->nama;
        $booking->delete();

        return redirect()->route('admin.booking.index')->with('success', 'Booking atas nama ' . $nama . ' berhasil dihapus');
    }
}

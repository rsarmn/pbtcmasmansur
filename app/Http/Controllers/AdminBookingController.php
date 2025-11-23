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
        $kamar = Kamar::find($booking->kode_kamar);

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

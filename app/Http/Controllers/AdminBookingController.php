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
        Pengunjung::findOrFail($id)->delete();

        return back()->with('success', 'Data booking berhasil dihapus');
    }
}

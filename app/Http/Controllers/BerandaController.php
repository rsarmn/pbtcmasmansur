<?php

namespace App\Http\Controllers;

use App\Models\Beranda;
use App\Models\Kamar;
use App\Models\Pengunjung;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BerandaController extends Controller
{
    public function show(Request $request)
    {
        $data = Beranda::find(1);

        // Ambil daftar tipe kamar (tanpa jumlah)
        $roomTypes = Kamar::select('jenis_kamar', 'fasilitas', 'harga')
            ->groupBy('jenis_kamar', 'fasilitas', 'harga')
            ->get()
            ->map(function ($room) {
                $room->kamar_id = Kamar::where('jenis_kamar', $room->jenis_kamar)
                    ->where('status', 'kosong')
                    ->value('id');
                return $room;
            });

        // Jika user memasukkan tanggal check-in / check-out
        if ($request->has(['checkin', 'checkout'])) {

            $checkIn  = $request->checkin;
            $checkOut = $request->checkout;

            $roomTypes = $roomTypes->map(function ($room) use ($checkIn, $checkOut) {

                // Total kamar KOSONG berdasarkan jenis kamar
                $total = Kamar::where('jenis_kamar', $room->jenis_kamar)
                    ->where('status', 'kosong')
                    ->count();

                // Ambil ID kamar kosong
                $kamarList = Kamar::where('jenis_kamar', $room->jenis_kamar)
                    ->where('status', 'kosong')
                    ->pluck('id');

                // Hitung booking yang overlap tanggalnya
                $booked = Pengunjung::whereIn('kode_kamar', $kamarList)
                    ->where(function ($q) use ($checkIn, $checkOut) {
                        $q->whereBetween('check_in', [$checkIn, $checkOut])
                          ->orWhereBetween('check_out', [$checkIn, $checkOut])
                          ->orWhere(function ($q2) use ($checkIn, $checkOut) {
                              $q2->where('check_in', '<', $checkIn)
                                 ->where('check_out', '>', $checkOut);
                          });
                    })
                    ->count();

                // Hitung tersisa kamar
                $room->tersisa = max(0, $total - $booked);

                return $room;
            });
        }

        return view('index', [
            'data'     => $data,
            'roomTypes'=> $roomTypes,
            'checkIn'  => $request->checkin,
            'checkOut' => $request->checkout,
        ]);
    }

    // ============================
    // ADMIN EDIT BERANDA
    // ============================

    public function edit()
    {
        $data = DB::table('beranda')->where('id', 1)->first();
        return view('beranda', compact('data'));
    }

    public function update(Request $request, $id)
    {
        $data = [
            'instagram'   => $request->instagram,
            'email'       => $request->email,
            'whatsapp'    => $request->whatsapp,
            'location'    => $request->location,
            'maps_link'   => $request->maps_link,
            'slider1_text'=> $request->slider1_text,
            'slider2_text'=> $request->slider2_text,
        ];

        if ($request->hasFile('slider1_image')) {
            $path = $request->file('slider1_image')->store('img', 'public');
            $data['slider1_image'] = 'storage/' . $path;
        }

        if ($request->hasFile('slider2_image')) {
            $path = $request->file('slider2_image')->store('img', 'public');
            $data['slider2_image'] = 'storage/' . $path;
        }

        DB::table('beranda')->where('id', $id)->update($data);

        return redirect()->route('beranda.edit')->with('success', 'Beranda berhasil diperbarui!');
    }


    // ============================
    // AJAX CEK KAMAR TERSISA
    // ============================

    public function cekKamar(Request $request)
    {
        $checkIn  = $request->checkin;
        $checkOut = $request->checkout;

        $roomTypes = Kamar::select('jenis_kamar', 'fasilitas', 'harga')
            ->groupBy('jenis_kamar', 'fasilitas', 'harga')
            ->get();

        $result = [];

        foreach ($roomTypes as $room) {

            // Hitung kamar kosong
            $total = Kamar::where('jenis_kamar', $room->jenis_kamar)
                ->where('status', 'kosong')
                ->count();

            // Ambil ID kamar kosong
            $kamarList = Kamar::where('jenis_kamar', $room->jenis_kamar)
                ->where('status', 'kosong')
                ->pluck('id');

            // Hitung booking yang overlap
            $booked = Pengunjung::whereIn('kode_kamar', $kamarList)
                ->where(function ($q) use ($checkIn, $checkOut) {
                    $q->whereBetween('check_in', [$checkIn, $checkOut])
                      ->orWhereBetween('check_out', [$checkIn, $checkOut])
                      ->orWhere(function ($q2) use ($checkIn, $checkOut) {
                          $q2->where('check_in', '<', $checkIn)
                             ->where('check_out', '>', $checkOut);
                      });
                })
                ->count();

            // Kamar tersisa
            $tersisa = max(0, $total - $booked);
            $result[$room->jenis_kamar] = $tersisa;
        }

        return response()->json($result);
    }
}

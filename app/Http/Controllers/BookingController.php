<?php

namespace App\Http\Controllers;

use App\Models\Kamar;
use App\Models\Pengunjung;
use Illuminate\Http\Request;
use Carbon\Carbon;

class BookingController extends Controller
{
    // ==================================================
    // =================== INDIVIDU =====================
    // ==================================================

    public function bookingIndividu(Request $request)
    {
        $roomId = $request->query('kamar');

        $selectedRoom = null;
        if ($roomId) {
            $selectedRoom = Kamar::where('kode_kamar', $roomId)->first();
            if (!$selectedRoom && is_numeric($roomId)) {
                $selectedRoom = Kamar::find($roomId);
            }
        }

        return view('booking.individu', [
            'selectedRoom' => $selectedRoom
        ]);
    }

    public function storeIndividu(Request $request)
    {
        $request->validate([
            'nama'            => 'required|string',
            'no_identitas'    => ['required','string','regex:/^[0-9]+$/'],
            'check_in'        => 'required|date',
            'check_out'       => 'required|date|after:check_in',
            'no_telp'         => ['required','string','regex:/^[0-9+\- ]+$/'],
            'jumlah_peserta'  => 'nullable|integer|min:1',
            'special_request' => 'nullable|string',
            'bukti_identitas' => 'required|image|mimes:jpg,jpeg,png|max:2048',
            'kode_kamar'      => 'required',
        ]);

        // Upload bukti identitas
        $buktiIdentitasPath = null;
        if ($request->hasFile('bukti_identitas')) {
            $buktiIdentitasPath = $request->file('bukti_identitas')
                                         ->store('bukti_identitas', 'public');
        }

        // Normalize kode kamar
        $submitted = is_array($request->kode_kamar) ? $request->kode_kamar : [$request->kode_kamar];
        $mappedKodes = [];
        $invalid = [];

        foreach ($submitted as $v) {
            $v = trim($v);
            $km = Kamar::where('kode_kamar', $v)->first();
            if (!$km && is_numeric($v)) {
                $km = Kamar::find($v);
            }
            if ($km) {
                $mappedKodes[] = $km->kode_kamar;
            } else {
                $invalid[] = $v;
            }
        }

        if (!empty($invalid)) {
            return back()->withErrors(['kode_kamar' => 'Kamar tidak valid: ' . implode(', ', $invalid)]);
        }

        $mappedKodes = array_unique($mappedKodes);
        $kamarString = implode(',', $mappedKodes);

        $pengunjung = Pengunjung::create([
            'nama'            => $request->nama,
            'jenis_tamu'      => 'individu',
            'no_identitas'    => $request->no_identitas,
            'check_in'        => $request->check_in,
            'check_out'       => $request->check_out,
            'no_telp'         => $request->no_telp,
            'jumlah_peserta'  => $request->input('jumlah_peserta', 1),
            'jumlah_kamar'    => $request->jumlah_kamar ?? 1,
            'special_request' => $request->special_request,
            'bukti_identitas' => $buktiIdentitasPath,
            'kode_kamar'      => $kamarString,
            'payment_status'  => 'pending'
        ]);

        // Mark kamar terisi
        Kamar::whereIn('kode_kamar', $mappedKodes)->update(['status' => 'terisi']);

        return redirect()->route('booking.payment', $pengunjung->id);
    }

    // ==================================================
    // =================== CORPORATE ====================
    // ==================================================

    public function bookingCorporate(Request $request)
    {
        $roomId = $request->query('kamar');

        $selectedRoom = Kamar::where('kode_kamar', $roomId)->first();
        if (!$selectedRoom && is_numeric($roomId)) {
            $selectedRoom = Kamar::find($roomId);
        }

        if (!$selectedRoom) {
            return redirect('/')->with('error', 'Kamar tidak ditemukan.');
        }

        $kamars = Kamar::where('status', 'kosong')->get();

        return view('booking.corporate', compact('kamars', 'selectedRoom'));
    }

    public function storeCorporate(Request $request)
    {
        $request->validate([
            'nama_pic'              => 'nullable|string',
            'no_identitas'          => 'nullable|string',
            'asal_persyarikatan'    => 'nullable|string',
            'tanggal_persyarikatan' => 'nullable|date',
            'nama_kegiatan'         => 'nullable|string',
            'no_telp_pic'           => ['nullable','string','regex:/^[0-9+\- ]+$/'],
            'check_in'              => 'nullable|date',
            'check_out'             => 'nullable|date|after:check_in',
            'jumlah_peserta'        => 'nullable|integer|min:1',
            'bukti_identitas'       => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'jenis_tamu'            => 'required|in:individu,corporate',
            'special_request'       => 'nullable|string',
            'kode_kamar'            => 'required|array',
        ]);

        // Upload bukti identitas
        $buktiIdentitasPath = null;
        if ($request->hasFile('bukti_identitas')) {
            $buktiIdentitasPath = $request->file('bukti_identitas')
                                         ->store('bukti_identitas', 'public');
        }

        // Convert all kamar to kode_kamar
        $mappedKodes = [];
        $invalid = [];

        foreach ($request->kode_kamar as $v) {
            $v = trim($v);
            $km = Kamar::where('kode_kamar', $v)->first();
            if (!$km && is_numeric($v)) {
                $km = Kamar::find($v);
            }
            if ($km) $mappedKodes[] = $km->kode_kamar;
            else $invalid[] = $v;
        }

        if (!empty($invalid)) {
            return back()->withErrors(['kode_kamar' => 'Kamar tidak valid: ' . implode(', ', $invalid)]);
        }

        $mappedKodes = array_unique($mappedKodes);
        $jumlahKamar = count($mappedKodes);
        $kamarString = implode(',', $mappedKodes);

        $pengunjung = Pengunjung::create([
            'nama_pic'              => $request->nama_pic,
            'no_identitas'          => $request->no_identitas,
            'jenis_tamu'            => 'corporate',
            'asal_persyarikatan'    => $request->asal_persyarikatan,
            'tanggal_persyarikatan' => $request->tanggal_persyarikatan,
            'nama_kegiatan'         => $request->nama_kegiatan,
            'no_telp_pic'           => $request->no_telp_pic,
            'check_in'              => $request->check_in,
            'check_out'             => $request->check_out,
            'jumlah_peserta'        => $request->jumlah_peserta ?? 1,
            'jumlah_kamar'          => $request->jumlah_kamar ?? 1,
            'special_request'       => $request->special_request,
            'bukti_identitas'       => $buktiIdentitasPath,
            'kode_kamar'            => $kamarString,
            'payment_status'        => 'pending'
        ]);

        Kamar::whereIn('kode_kamar', $mappedKodes)->update(['status' => 'terisi']);

        return redirect()->route('booking.payment', $pengunjung->id);
    }

    // ==================================================
    // ====================== PAYMENT ===================
    // ==================================================

    public function payment($id)
    {
        $pengunjung = Pengunjung::findOrFail($id);
        $kamar = Kamar::where('kode_kamar', $pengunjung->kode_kamar)->first();

        // Hitung durasi menginap
        $durasi = \Carbon\Carbon::parse($pengunjung->check_in)
                    ->diffInDays(\Carbon\Carbon::parse($pengunjung->check_out));

        // AMBIL jumlah kamar untuk perhitungan total
        $jumlahKamar = $pengunjung->jumlah_kamar ?? 1;

        // Hitung harga kamar TOTAL
        $totalKamar = $kamar->harga * $durasi * $jumlahKamar;

        return view('booking.payment', [
            'pengunjung' => $pengunjung,
            'kamar' => $kamar,
            'durasi' => $durasi,
            'totalKamar' => $totalKamar,
            'totalPembayaran' => $totalKamar, // sama saja
        ]);
    }

    // ==================================================
    // ====================== SUCCESS ===================
    // ==================================================

    public function success($id)
    {
        $pengunjung = Pengunjung::findOrFail($id);

        $kamarIds = explode(',', $pengunjung->kode_kamar);
        $first = trim($kamarIds[0]);

        $kamar = Kamar::where('kode_kamar', $first)->first();
        if (!$kamar && is_numeric($first)) {
            $kamar = Kamar::find($first);
        }

        $checkIn  = Carbon::parse($pengunjung->check_in);
        $checkOut = Carbon::parse($pengunjung->check_out);
        $durasi   = $checkIn->diffInDays($checkOut);

        $totalKamar = 0;
        foreach ($kamarIds as $kid) {
            $rm = Kamar::where('kode_kamar', trim($kid))->first();
            if (!$rm && is_numeric($kid)) {
                $rm = Kamar::find($kid);
            }
            if ($rm) {
                $totalKamar += $rm->harga * $durasi;
            }
        }

        // ONLY kamar (no snack/makan)
        $totalPembayaran = $totalKamar;

        return view('booking.success', compact('pengunjung', 'totalPembayaran'));
    }

    // ==================================================
    // =================== UPLOAD PAYMENT ===============
    // ==================================================

    public function uploadBuktiPembayaran(Request $request, $id)
    {
        $request->validate([
            'bukti_pembayaran' => 'required|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        $pengunjung = Pengunjung::findOrFail($id);

        $path = $request->file('bukti_pembayaran')->store('bukti_pembayaran', 'public');

        // Hitung total harga kamar
        $totalHarga = $this->calculateTotalHarga($pengunjung);

        $pengunjung->update([
            'bukti_pembayaran' => $path,
            'payment_status'   => 'konfirmasi_booking',
            'total_harga'      => $totalHarga,
        ]);

        return redirect()->route('booking.success', $id)->with('upload_success', true);
    }

    private function calculateTotalHarga(Pengunjung $pengunjung)
    {
        $kamarIds = explode(',', $pengunjung->kode_kamar);

        $checkIn  = Carbon::parse($pengunjung->check_in);
        $checkOut = Carbon::parse($pengunjung->check_out);
        $durasi   = $checkIn->diffInDays($checkOut);

        $totalKamar = 0;
        foreach ($kamarIds as $kid) {
            $km = Kamar::where('kode_kamar', trim($kid))->first();
            if (!$km && is_numeric($kid)) {
                $km = Kamar::find($kid);
            }
            if ($km) {
                $totalKamar += $km->harga * $durasi;
            }
        }

        return $totalKamar;
    }
}

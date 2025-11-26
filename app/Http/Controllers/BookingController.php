<?php

namespace App\Http\Controllers;

use App\Models\Kamar;
use App\Models\Pengunjung;
use App\Models\MenuPesmaBoga;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BookingController extends Controller
{
    // ============= INDIVIDU =============
    public function bookingIndividu(Request $request)
    {
        $roomId = $request->query('kamar');

        // Ambil data kamar jika ada - prefer kode_kamar then numeric id
        $selectedRoom = null;
        if ($roomId) {
            $selectedRoom = \DB::table('kamars')->where('kode_kamar', $roomId)->first();
            if (!$selectedRoom && is_numeric($roomId)) {
                $selectedRoom = \DB::table('kamars')->where('id', $roomId)->first();
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
            // accept kode_kamar string or numeric id; we'll map and validate below
            'kode_kamar'      => 'required',
        ]);

        $buktiIdentitasPath = null;
        if ($request->hasFile('bukti_identitas')) {
            $buktiIdentitasPath = $request->file('bukti_identitas')
                                         ->store('bukti_identitas', 'public');
        }

        // normalize kode_kamar: if client submitted numeric IDs, convert to kode_kamar strings
        $submittedKamar = is_array($request->kode_kamar) ? $request->kode_kamar : [$request->kode_kamar];
        $mappedKodes = [];
        $invalidKamar = [];
        foreach ($submittedKamar as $v) {
            $v = trim($v);
            // Prefer lookup by kode_kamar; fallback to id
            $km = Kamar::where('kode_kamar', $v)->first();
            if (!$km && is_numeric($v)) {
                $km = Kamar::find($v);
            }
            if ($km) {
                $mappedKodes[] = $km->kode_kamar;
            } else {
                $invalidKamar[] = $v;
            }
        }
        if (!empty($invalidKamar)) {
            return back()->withErrors(['kode_kamar' => 'Kamar tidak valid: ' . implode(', ', $invalidKamar)])->withInput();
        }

        // remove duplicates
        $mappedKodes = array_values(array_unique($mappedKodes));

        $kodeString = implode(',', array_filter($mappedKodes));

        $pengunjung = Pengunjung::create([
            'nama'            => $request->nama,
            'jenis_tamu'      => $request->input('jenis_tamu', 'individu'),
            'no_identitas'    => $request->no_identitas,
            'check_in'        => $request->check_in,
            'check_out'       => $request->check_out,
            'no_telp'         => $request->no_telp,
            'jumlah_peserta'  => $request->input('jumlah_peserta', 1),
            'jumlah_kamar'    => $request->input('jumlah_kamar', 1),
            'special_request' => $request->special_request,
            'bukti_identitas' => $buktiIdentitasPath,
            'kode_kamar'      => $kodeString,
            'payment_status'  => 'pending',
        ]);

        // Mark selected kamar as terisi so status reflects booking in real-time
        try {
            // mark rooms by kode_kamar
            if (!empty($mappedKodes)) {
                Kamar::whereIn('kode_kamar', $mappedKodes)->update(['status' => 'terisi']);
            }
        } catch (\Exception $e) {}

        return redirect()
            ->route('booking.payment', $pengunjung->id);
    }

    // ============= CORPORATE =============
    public function bookingCorporate(Request $request)
    {
        $roomId = $request->query('kamar'); // ambil id/kode dari query

        // Try lookup by kode_kamar first, fallback to id
        $selectedRoom = Kamar::where('kode_kamar', $roomId)->first();
        if (!$selectedRoom && is_numeric($roomId)) {
            $selectedRoom = Kamar::find($roomId);
        }

        if (!$selectedRoom) {
            return redirect('/')->with('error', 'Kamar tidak ditemukan.');
        }

        $kamars = Kamar::where('status', 'kosong')->get();

        $snacks = MenuPesmaBoga::where('tersedia', 1)->where('jenis', 'snack')->get();
        $makans = MenuPesmaBoga::where('tersedia', 1)->where('jenis', 'makan')->get();

        return view('booking.corporate', compact('kamars', 'snacks', 'makans', 'selectedRoom'));
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
            'kebutuhan_snack'       => 'nullable|array',
            'kebutuhan_makan'       => 'nullable|array',
            'bukti_identitas'       => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'jenis_tamu'            => 'required|in:individu,corporate',
            'special_request'       => 'nullable|string',
            'kode_kamar'            => 'required|array',
            // accept kode_kamar strings or numeric ids; we'll map/validate below
            'kode_kamar.*'          => 'string',
        ]);

        $buktiIdentitasPath = null;
        if ($request->hasFile('bukti_identitas')) {
            $buktiIdentitasPath = $request->file('bukti_identitas')
                                         ->store('bukti_identitas', 'public');
        }

         $snackData = [];
         $makanData = [];

        foreach ($request->kebutuhan_snack ?? [] as $menuId => $item) {
            if (!empty($item['pilih'])) {

                $menu = MenuPesmaBoga::find($menuId);

                if ($menu) {
                    $snackData[] = [
                        'menu_id' => $menu->id,
                        'nama'    => $menu->nama_menu,
                        'harga'   => $menu->harga,
                        'porsi'   => $item['porsi'] ?? 1,
                    ];
                }
            }
        }

        foreach ($request->kebutuhan_makan ?? [] as $menuId => $item) {
            if (!empty($item['pilih'])) {

                $menu = MenuPesmaBoga::find($menuId);

                if ($menu) {
                    $makanData[] = [
                        'menu_id' => $menu->id,
                        'nama'    => $menu->nama_menu,
                        'harga'   => $menu->harga,
                        'porsi'   => $item['porsi'] ?? 1,
                    ];
                }
            }
        }

        // Convert array of room IDs or kode strings to comma-separated kode_kamar
        $kamarIds = $request->kode_kamar ?? [];
        $mappedKodes = [];
        $invalidKamar = [];
        foreach ($kamarIds as $v) {
            $v = trim($v);
            // Prefer kode_kamar lookup then fallback to id
            $km = Kamar::where('kode_kamar', $v)->first();
            if (!$km && is_numeric($v)) {
                $km = Kamar::find($v);
            }
            if ($km) {
                $mappedKodes[] = $km->kode_kamar;
            } else {
                $invalidKamar[] = $v;
            }
        }
        if (!empty($invalidKamar)) {
            return back()->withErrors(['kode_kamar' => 'Kamar tidak valid: ' . implode(', ', $invalidKamar)])->withInput();
        }
        // remove duplicates and compute jumlahKamar from mapped values
        $mappedKodes = array_values(array_unique($mappedKodes));
        $kamarString = implode(',', array_filter($mappedKodes));
        $jumlahKamar = count($mappedKodes);

        $pengunjung = Pengunjung::create([
            'nama_pic'              => $request->nama_pic,
            'no_identitas'          => $request->no_identitas,
            'jenis_tamu'            => $request->jenis_tamu,
            'asal_persyarikatan'    => $request->asal_persyarikatan,
            'tanggal_persyarikatan' => $request->tanggal_persyarikatan,
            'nama_kegiatan'         => $request->nama_kegiatan,
            'no_telp_pic'           => $request->no_telp_pic,
            'check_in'              => $request->check_in,
            'check_out'             => $request->check_out,
            'jumlah_peserta'        => $request->jumlah_peserta ?? 1,
            'jumlah_kamar'          => $jumlahKamar,
            'special_request'       => $request->special_request,
            'kebutuhan_snack'       => json_encode($snackData),
            'kebutuhan_makan'       => json_encode($makanData),
            'bukti_identitas'       => $buktiIdentitasPath,
            'kode_kamar'            => $kamarString,
            'payment_status'        => 'pending',
        ]);

        // Mark selected kamar as terisi so status reflects booking in real-time
        try {
            if (!empty($mappedKodes)) {
                Kamar::whereIn('kode_kamar', $mappedKodes)->update(['status' => 'terisi']);
            }
        } catch (\Exception $e) {
            // ignore any errors to not block booking
        }

        return redirect()
            ->route('booking.payment', $pengunjung->id);
    }

    // ============= PAYMENT =============
    public function payment($id)
    {
        $pengunjung = Pengunjung::findOrFail($id);
        
        // Get room IDs (could be comma-separated)
        $kamarIds = explode(',', $pengunjung->kode_kamar);
        $firstKamarId = trim($kamarIds[0]);
        $kamar = Kamar::where('kode_kamar', $firstKamarId)->first();
        if (!$kamar && is_numeric($firstKamarId)) {
            $kamar = Kamar::find($firstKamarId);
        }

        $checkIn  = Carbon::parse($pengunjung->check_in);
        $checkOut = Carbon::parse($pengunjung->check_out);
        $durasi   = $checkIn->diffInDays($checkOut);
        $jumlahKamar = $pengunjung->jumlah_kamar ?? count($kamarIds);

        // ===========================================
        // 1. Hitung total kamar (semua kamar yang dipilih)
        // ===========================================
        $totalKamar = 0;
        foreach ($kamarIds as $kamarId) {
            $rm = Kamar::where('kode_kamar', trim($kamarId))->first();
            if (!$rm && is_numeric(trim($kamarId))) {
                $rm = Kamar::find(trim($kamarId));
            }
            if ($rm) {
                $totalKamar += ($rm->harga ?? 0) * $durasi;
            }
        }

        // ===========================================
        // 2. Ambil & Hitung total menu
        // ===========================================
        $totalMenu = 0;
        $detailMenus = [];

        $snacks = json_decode($pengunjung->kebutuhan_snack ?? '[]', true);
        $makans = json_decode($pengunjung->kebutuhan_makan ?? '[]', true);

        foreach (array_merge($snacks, $makans) as $item) {

            // pastikan menu_id ada
            if (!isset($item['menu_id'])) continue;

            $menu = MenuPesmaBoga::find($item['menu_id']);
            if (!$menu) continue;

            $porsi = $item['porsi'] ?? 1;
            $total = $menu->harga * $porsi;

            // Tambahkan ke rincian
            $detailMenus[] = [
                'nama'  => $menu->nama_menu,
                'harga' => $menu->harga,
                'porsi' => $porsi,
                'total' => $total
            ];

            $totalMenu += $total;
        }

        $totalPembayaran = $totalKamar + $totalMenu;

        return view('booking.payment', compact(
            'pengunjung',
            'kamar',
            'durasi',
            'jumlahKamar',
            'totalKamar',
            'totalMenu',
            'totalPembayaran',
            'detailMenus'
        ));
    }

    // ============= SUCCESS PAGE =============
    public function success($id)
    {
        $pengunjung = Pengunjung::findOrFail($id);
        
        // Get room IDs (could be comma-separated)
        $kamarIds = explode(',', $pengunjung->kode_kamar);
        $firstKamarId = trim($kamarIds[0]);
        $kamar = Kamar::where('kode_kamar', $firstKamarId)->first();
        if (!$kamar && is_numeric($firstKamarId)) {
            $kamar = Kamar::find($firstKamarId);
        }

        $checkIn  = Carbon::parse($pengunjung->check_in);
        $checkOut = Carbon::parse($pengunjung->check_out);
        $durasi   = $checkIn->diffInDays($checkOut);
        $jumlahKamar = $pengunjung->jumlah_kamar ?? count($kamarIds);

        // Hitung total kamar (semua kamar yang dipilih)
        $totalKamar = 0;
        foreach ($kamarIds as $kamarId) {
            $rm = Kamar::where('kode_kamar', trim($kamarId))->first();
            if (!$rm && is_numeric(trim($kamarId))) {
                $rm = Kamar::find(trim($kamarId));
            }
            if ($rm) {
                $totalKamar += ($rm->harga ?? 0) * $durasi;
            }
        }

        // Hitung total menu
        $totalMenu = 0;
        $snacks = json_decode($pengunjung->kebutuhan_snack ?? '[]', true);
        $makans = json_decode($pengunjung->kebutuhan_makan ?? '[]', true);

        foreach (array_merge($snacks, $makans) as $item) {
            if (!isset($item['menu_id'])) continue;
            $menu = MenuPesmaBoga::find($item['menu_id']);
            if (!$menu) continue;
            $porsi = $item['porsi'] ?? 1;
            $totalMenu += $menu->harga * $porsi;
        }

        $totalPembayaran = $totalKamar + $totalMenu;

        return view('booking.success', compact('pengunjung', 'totalPembayaran'));
    }

    public function uploadBuktiPembayaran(Request $request, $id)
    {
        $request->validate([
            'bukti_pembayaran' => 'required|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        $pengunjung = Pengunjung::findOrFail($id);

        // Simpan file ke storage
        $path = $request->file('bukti_pembayaran')->store('bukti_pembayaran', 'public');

        // Calculate total harga
        $totalHarga = $this->calculateTotalHarga($pengunjung);

        // Update ke database
        $pengunjung->update([
            'bukti_pembayaran' => $path,
            'payment_status' => 'konfirmasi_booking', // Update status setelah upload bukti
            'total_harga' => $totalHarga,
        ]);

        return redirect()
            ->route('booking.success', $id)
            ->with('upload_success', true);
    }

    // Helper method to calculate total harga
    private function calculateTotalHarga(Pengunjung $pengunjung)
    {
        $kamarIds = explode(',', $pengunjung->kode_kamar);
        $firstKamarId = trim($kamarIds[0]);
        
        $checkIn  = Carbon::parse($pengunjung->check_in);
        $checkOut = Carbon::parse($pengunjung->check_out);
        $durasi   = $checkIn->diffInDays($checkOut);

        // Hitung total kamar (semua kamar yang dipilih)
        $totalKamar = 0;
        foreach ($kamarIds as $kamarId) {
            $kamar = Kamar::where('kode_kamar', trim($kamarId))->first();
            if (!$kamar && is_numeric(trim($kamarId))) {
                $kamar = Kamar::find(trim($kamarId));
            }
            if ($kamar) {
                $totalKamar += ($kamar->harga ?? 0) * $durasi;
            }
        }

        // Hitung total menu
        $totalMenu = 0;
        $snacks = json_decode($pengunjung->kebutuhan_snack ?? '[]', true);
        $makans = json_decode($pengunjung->kebutuhan_makan ?? '[]', true);

        foreach (array_merge($snacks, $makans) as $item) {
            if (!isset($item['menu_id'])) continue;
            $menu = MenuPesmaBoga::find($item['menu_id']);
            if (!$menu) continue;
            $porsi = $item['porsi'] ?? 1;
            $totalMenu += $menu->harga * $porsi;
        }

        return $totalKamar + $totalMenu;
    }
}

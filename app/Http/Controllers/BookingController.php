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

        // Ambil data kamar jika ada
        $selectedRoom = null;
        if ($roomId) {
            $selectedRoom = \DB::table('kamars')->where('id', $roomId)->first();
        }

        return view('booking.individu', [
            'selectedRoom' => $selectedRoom
        ]);
    }

    public function storeIndividu(Request $request)
    {
        $request->validate([
            'nama'            => 'nullable|string',
            'no_identitas'    => 'nullable|string',
            'check_in'        => 'nullable|date',
            'check_out'       => 'nullable|date|after:check_in',
            'no_telp'         => 'nullable|string',
            'special_request' => 'nullable|string',
            'bukti_identitas' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'kode_kamar'      => 'required|exists:kamars,id',
        ]);

        $buktiIdentitasPath = null;
        if ($request->hasFile('bukti_identitas')) {
            $buktiIdentitasPath = $request->file('bukti_identitas')
                                         ->store('bukti_identitas', 'public');
        }

        $pengunjung = Pengunjung::create([
            'nama'            => $request->nama,
            'jenis_tamu'      => $request->input('jenis_tamu', 'individu'),
            'no_identitas'    => $request->no_identitas,
            'check_in'        => $request->check_in,
            'check_out'       => $request->check_out,
            'no_telp'         => $request->no_telp,
            'jumlah_kamar'    => 1,
            'special_request' => $request->special_request,
            'bukti_identitas' => $buktiIdentitasPath,
            'kode_kamar'      => $request->kode_kamar,
        ]);

        Kamar::where('id', $request->kode_kamar)
            ->update(['status' => 'terisi']);

        return redirect()
            ->route('booking.payment', $pengunjung->id)
            ->with('success', 'Data booking berhasil disimpan!');
    }

    // ============= CORPORATE =============
    public function bookingCorporate(Request $request)
    {
        $roomId = $request->query('kamar'); // ambil id dari query

        $selectedRoom = Kamar::find($roomId);

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
            'no_telp_pic'           => 'nullable|string',
            'check_in'              => 'nullable|date',
            'check_out'             => 'nullable|date|after:check_in',
            'jumlah_peserta'        => 'nullable|integer|min:1',
            'kebutuhan_snack'       => 'nullable|array',
            'kebutuhan_makan'       => 'nullable|array',
            'bukti_identitas'       => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'jenis_tamu'            => 'required|in:individu,corporate',
            'special_request'       => 'nullable|string',
            'kode_kamar'            => 'required|exists:kamars,id',
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
            'jumlah_kamar'          => 1,
            'special_request'       => $request->special_request,
            'kebutuhan_snack'       => json_encode($snackData),
            'kebutuhan_makan'       => json_encode($makanData),
            'bukti_identitas'       => $buktiIdentitasPath,
            'kode_kamar'            => $request->kode_kamar,
        ]);

        Kamar::where('id', $request->kode_kamar)
            ->update(['status' => 'terisi']);

        return redirect()
            ->route('booking.payment', $pengunjung->id)
            ->with('success', 'Data booking corporate berhasil disimpan!');
    }

    // ============= PAYMENT =============
    public function payment($id)
    {
        $pengunjung = Pengunjung::findOrFail($id);
        $kamar = Kamar::find($pengunjung->kode_kamar);

        $checkIn  = Carbon::parse($pengunjung->check_in);
        $checkOut = Carbon::parse($pengunjung->check_out);
        $durasi   = $checkIn->diffInDays($checkOut);
        $jumlahKamar = $pengunjung->jumlah_kamar ?? 1;

        // ===========================================
        // 1. Hitung total kamar
        // ===========================================
        $totalKamar = ($kamar->harga ?? 0) * $durasi * $jumlahKamar;

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

    public function uploadBuktiPembayaran(Request $request, $id)
    {
        $request->validate([
            'bukti_pembayaran' => 'required|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        $pengunjung = Pengunjung::findOrFail($id);

        // Simpan file ke storage
        $path = $request->file('bukti_pembayaran')->store('bukti_pembayaran', 'public');

        // Update ke database
        $pengunjung->update([
            'bukti_pembayaran' => $path,
        ]);

        return redirect()
            ->route('booking.payment', $id)
            ->with('success', 'Bukti pembayaran berhasil diunggah!');
    }
}

<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Pengunjung;
use App\Models\Kamar;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage; // Tambahkan ini jika belum ada

class PengunjungController extends Controller
{
    public function __construct(){
        $this->middleware(\App\Http\Middleware\AdminAuth::class);
    }

    public function index(){
        $pengunjungs = Pengunjung::latest()->get();
        return view('admin.pengunjung', compact('pengunjungs'));
    }

    public function pending(){
        // bookings that have not completed payment yet
        $pengunjungs = Pengunjung::where('payment_status','pending')->latest()->get();
        // also provide list of available rooms for assigning during approval
        $availableRooms = Kamar::where('status','kosong')->get();
        return view('admin.pengunjung.pending', compact('pengunjungs','availableRooms'));
    }

    public function konfirmasiPembayaran(){
        // dedicated payment confirmation page - show pending bookings
        $pengunjungs = Pengunjung::whereIn('payment_status', ['pending', 'konfirmasi_booking'])->latest()->get();
        return view('admin.pembayaran_konfirmasi', compact('pengunjungs'));
    }

    /**
     * Upload payment proof for a pending booking
     */
    public function uploadPayment(Request $r, $id)
    {
        $p = Pengunjung::findOrFail($id);
        $r->validate([
            'bukti_pembayaran' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120'
        ]);

        try {
            // Check if file exists in request
            if (!$r->hasFile('bukti_pembayaran')) {
                throw new \Exception('No file uploaded');
            }
            
            $file = $r->file('bukti_pembayaran');
            
            // Check if file is valid
            if (!$file->isValid()) {
                throw new \Exception('Uploaded file is not valid: ' . $file->getErrorMessage());
            }
            
            // Make sure directory exists
            $dir = storage_path('app/public/bukti_pembayaran');
            if (!is_dir($dir)) {
                mkdir($dir, 0775, true);
                chmod($dir, 0775);
            }

            // Store file dengan disk 'public'
            $path = $file->store('bukti_pembayaran', 'public');
            
            // Path akan jadi: bukti_pembayaran/xxx.png (tanpa public/)
            // Kita perlu tambah 'public/' untuk consistency
            $fullPath = 'public/' . $path;
            
            // Verify file was saved
            $savedPath = storage_path('app/public/' . $path);
            if (!file_exists($savedPath)) {
                throw new \Exception('File not saved to: ' . $savedPath);
            }
            
            // Update database
            $p->bukti_pembayaran = $fullPath;
            $p->save();

            return back()->with('success','✓ Bukti pembayaran berhasil diupload! File: ' . basename($path));
            
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Upload Payment Error for ID ' . $id . ': ' . $e->getMessage());
            return back()->withErrors(['upload' => 'Gagal upload: ' . $e->getMessage()]);
        }
    }

    /**
     * Approve a pending booking: mark as paid and optionally assign kamar(s)
     */
    public function approve(Request $r, $id)
    {
        $p = Pengunjung::findOrFail($id);
        // require that bukti_pembayaran exists before approving (CATATAN: ADMIN BISA MEMBYPASS JIKA MENGGUNAKAN FORM ASSIGN)
        // Saya hapus validasi bukti_pembayaran agar admin bisa approve & assign walaupun belum ada bukti,
        // karena admin mungkin sudah mendapat bukti via WA. Jika Anda ingin bukti wajib, aktifkan lagi kode di bawah.
        /*
        if (empty($p->bukti_pembayaran)) {
            return back()->withErrors(['bukti' => 'Tidak dapat meng-approve tanpa bukti pembayaran. Silakan minta pengunjung mengupload bukti.']);
        }
        */
        $r->validate([
            'assign_kamar' => 'nullable|array',
            'assign_kamar.*' => 'string'
        ]);

        $assigned = $r->input('assign_kamar', []);
        if (!empty($assigned)) {
            // assign room numbers as comma separated
            $p->kode_kamar = implode(',', $assigned);
            // mark each kamar as terisi
            Kamar::whereIn('kode_kamar', $assigned)->update(['status' => 'terisi']);
        }

        $p->payment_status = 'lunas';
        $p->save();

        return back()->with('success','Booking telah di-approve dan status pembayaran diperbarui.');
    }

    public function create(){
        $kamars = Kamar::where('status','kosong')->get();
        return view('admin.pengunjung.create', compact('kamars'));
    }

    public function store(Request $r){
        $r->validate([
            'nama'=>'required',
            'check_in' => 'required|date',
            'check_out' => 'required|date|after:check_in',
            'kode_kamar' => 'nullable|string',
        ]);

        // VALIDASI: Cek apakah ada booking yang overlap di kamar dan tanggal yang sama
        if ($r->filled('kode_kamar')) {
            $kamarIds = explode(',', $r->kode_kamar);
            
            foreach ($kamarIds as $kamar) {
                $conflict = Pengunjung::where('kode_kamar', 'like', '%' . trim($kamar) . '%')
                    ->where(function($q) use ($r) {
                        // Check if dates overlap
                        $q->whereBetween('check_in', [$r->check_in, $r->check_out])
                          ->orWhereBetween('check_out', [$r->check_in, $r->check_out])
                          ->orWhere(function($query) use ($r) {
                              $query->where('check_in', '<=', $r->check_in)
                                    ->where('check_out', '>=', $r->check_out);
                          });
                    })
                    ->whereNotIn('payment_status', ['rejected']) // exclude rejected bookings
                    ->exists();

                if ($conflict) {
                    return back()->withErrors([
                        'kode_kamar' => "Kamar {$kamar} sudah dibooking pada tanggal {$r->check_in} sampai {$r->check_out}. Silakan pilih kamar atau tanggal lain."
                    ])->withInput();
                }
            }
        }

        Pengunjung::create($r->all());
        return redirect()->route('pengunjung.index')->with('success','Data pengunjung ditambah');
    }

    public function destroy($id){
        $pengunjung = Pengunjung::findOrFail($id);
        $pengunjung->delete();
        return back()->with('success','Pengunjung dihapus');
    }

    public function reject(Request $r, $id)
    {
        $p = Pengunjung::findOrFail($id);
        // mark as rejected
        $p->payment_status = 'rejected'; 
        $p->save();
        return back()->with('success','Booking telah ditolak (status diubah menjadi Ditolak).');
    }

    public function show($id)
    {
        $p = Pengunjung::findOrFail($id);
        return view('admin.pengunjung.show', compact('p'));
    }

    public function edit($id)
    {
        $p = Pengunjung::findOrFail($id);
        $kamars = Kamar::all(); // show all rooms, not just empty ones
        return view('admin.pengunjung.edit', compact('p', 'kamars'));
    }

    public function update(Request $r, $id)
    {
        $p = Pengunjung::findOrFail($id);
        
        $r->validate([
            'nama' => 'required',
            'check_in' => 'required|date',
            'check_out' => 'required|date|after:check_in',
            'kode_kamar' => 'nullable|array',
            'kode_kamar.*' => 'string',
            'jumlah_kamar' => 'nullable|integer|min:1',
            'payment_status' => 'nullable|in:pending,konfirmasi_booking,paid,lunas,rejected',
        ]);

        // Convert array kode_kamar to comma-separated string
        if ($r->has('kode_kamar') && is_array($r->kode_kamar)) {
            $kamarIds = $r->kode_kamar;
            
            // VALIDASI: Cek overlap untuk setiap kamar
            foreach ($kamarIds as $kamar) {
                $conflict = Pengunjung::where('id', '!=', $id) // exclude current booking
                    ->where('kode_kamar', 'like', '%' . trim($kamar) . '%')
                    ->where(function($q) use ($r) {
                        // Check if dates overlap
                        $q->whereBetween('check_in', [$r->check_in, $r->check_out])
                          ->orWhereBetween('check_out', [$r->check_in, $r->check_out])
                          ->orWhere(function($query) use ($r) {
                              $query->where('check_in', '<=', $r->check_in)
                                    ->where('check_out', '>=', $r->check_out);
                          });
                    })
                    ->whereNotIn('payment_status', ['rejected'])
                    ->exists();

                if ($conflict) {
                    return back()->withErrors([
                        'kode_kamar' => "Kamar {$kamar} sudah dibooking pada tanggal {$r->check_in} sampai {$r->check_out}. Silakan pilih kamar atau tanggal lain."
                    ])->withInput();
                }
            }
            
            // Convert to comma-separated string for storage
            $r->merge(['kode_kamar' => implode(',', $kamarIds)]);
        }

        $p->update($r->all());
        
        return redirect()->route('pengunjung.index')->with('success', 'Data pengunjung berhasil diupdate');
    }

    public function showCheckin($id)
    {
        $p = Pengunjung::findOrFail($id);
        return view('admin.pengunjung.checkin', compact('p'));
    }

    public function processCheckin(Request $r, $id)
    {
        $p = Pengunjung::findOrFail($id);
        
        // Validate identity document is provided
        $r->validate([
            'bukti_identitas' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'identity_type' => 'required|in:KTP,KTM,SIM',
        ]);

        // Store identity document
        $path = $r->file('bukti_identitas')->store('public/bukti_identitas');
        $p->bukti_identitas = $path;
        $p->identity_type = $r->identity_type;
        
        // Update room status to terisi if kamar assigned
        if ($p->kode_kamar ?? $p->nomor_kamar) {
            $kodaKamar = $p->kode_kamar ?? $p->nomor_kamar;
            $kamarIds = explode(',', $kodaKamar);
            
            // Use runtime column detection
            if (Schema::hasColumn('kamars', 'kode_kamar')) {
                Kamar::whereIn('kode_kamar', $kamarIds)->update(['status' => 'terisi']);
            } else {
                Kamar::whereIn('nomor_kamar', $kamarIds)->update(['status' => 'terisi']);
            }
        }
        
        $p->save();

        return redirect()->route('pengunjung.show', $id)->with('success', 'Check-in berhasil. Identitas tersimpan dan kamar ditandai terisi.');
    }

    public function processCheckout(Request $r, $id)
    {
        $p = Pengunjung::findOrFail($id);
        
        // Return room to available status
        if ($p->kode_kamar ?? $p->nomor_kamar) {
            $kodaKamar = $p->kode_kamar ?? $p->nomor_kamar;
            $kamarIds = explode(',', $kodaKamar);
            
            // Use runtime column detection
            if (Schema::hasColumn('kamars', 'kode_kamar')) {
                Kamar::whereIn('kode_kamar', $kamarIds)->update(['status' => 'kosong']);
            } else {
                Kamar::whereIn('nomor_kamar', $kamarIds)->update(['status' => 'kosong']);
            }
        }

        // Note: Identity document returned - admin should note this manually
        return redirect()->route('pengunjung.index')->with('success', 'Checkout berhasil. Kamar dikembalikan ke status kosong. Jangan lupa kembalikan identitas kepada tamu.');
    }
}
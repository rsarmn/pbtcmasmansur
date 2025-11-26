<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Pengunjung;
use App\Models\Kamar;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage; // Tambahkan ini jika belum ada
use Illuminate\Support\Facades\Log;

class PengunjungController extends Controller
{
    public function __construct(){
        $this->middleware(\App\Http\Middleware\AdminAuth::class);
    }

    public function index(){
        // Hanya tampilkan pengunjung yang sudah approved (paid/lunas)
        $pengunjungs = Pengunjung::whereIn('payment_status', ['paid', 'lunas'])->latest()->get();
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
            // map assigned values to kode_kamar (allow kode_kamar or numeric id)
            $mapped = [];
            $invalid = [];
            foreach ($assigned as $v) {
                $v = trim($v);
                $km = Kamar::where('kode_kamar', $v)->first();
                if (!$km && is_numeric($v)) {
                    $km = Kamar::find($v);
                }
                if ($km) {
                    $mapped[] = $km->kode_kamar;
                } else {
                    $invalid[] = $v;
                }
            }
            if (!empty($invalid)) {
                return back()->withErrors(['assign_kamar' => 'Kamar tidak valid: ' . implode(', ', $invalid)])->withInput();
            }
            // assign room numbers as comma separated
            $p->kode_kamar = implode(',', $mapped);
            // mark each kamar as terisi
            Kamar::whereIn('kode_kamar', $mapped)->update(['status' => 'terisi']);
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
            $r->validate([
                'nama'=>'required',
                'check_in' => 'required|date',
                'check_out' => 'required|date|after:check_in',
                'kode_kamar' => 'nullable',
                'no_identitas' => ['nullable','string','regex:/^[0-9]+$/'],
                'no_telp' => ['nullable','string','regex:/^[0-9+\- ]+$/'],
                'jumlah_kamar' => 'nullable|integer|min:1',
            ]);

        if ($r->filled('kode_kamar')) {
            // allow either comma-separated string or array, and map numeric ids to kode_kamar
            if (is_array($r->kode_kamar)) {
                $rawIds = $r->kode_kamar;
            } else {
                $rawIds = explode(',', $r->kode_kamar);
            }
            $kamarIds = [];
            $invalidKamar = [];
            foreach ($rawIds as $v) {
                $v = trim($v);
                // Prefer lookup by kode_kamar; fallback to numeric id for compatibility
                $km = Kamar::where('kode_kamar', $v)->first();
                if (!$km && is_numeric($v)) {
                    $km = Kamar::find($v);
                }
                if ($km) {
                    $kamarIds[] = $km->kode_kamar;
                } else {
                    $invalidKamar[] = $v;
                }
            }
            // dedupe
            $kamarIds = array_values(array_unique($kamarIds));
            if (!empty($invalidKamar)) {
                return back()->withErrors(['kode_kamar' => 'Kamar tidak valid: ' . implode(', ', $invalidKamar)])->withInput();
            }
            // normalize into comma-separated kode_kamar
            $r->merge(['kode_kamar' => implode(',', array_filter($kamarIds))]);
        }
                    // Convert kebutuhan_snack/makan arrays into JSON strings if present
                    if ($r->has('kebutuhan_snack') && is_array($r->kebutuhan_snack)) {
                        $r->merge(['kebutuhan_snack' => json_encode($r->kebutuhan_snack)]);
                    } else {
                        $r->merge(['kebutuhan_snack' => json_encode([])]);
                    }

                    if ($r->has('kebutuhan_makan') && is_array($r->kebutuhan_makan)) {
                        $r->merge(['kebutuhan_makan' => json_encode($r->kebutuhan_makan)]);
                    } else {
                        $r->merge(['kebutuhan_makan' => json_encode([])]);
                    }

                    // Normalize kode_kamar to comma-separated string (already mapped earlier)
                    if ($r->has('kode_kamar')) {
                        if (is_array($r->kode_kamar)) {
                            $r->merge(['kode_kamar' => implode(',', $r->kode_kamar)]);
                        }
                    }

                    // Conflict check: ensure none of the requested kode_kamar overlap existing bookings
                    $kamarCheckIds = [];
                    if ($r->filled('kode_kamar')) {
                        $kamarCheckIds = array_filter(array_map('trim', is_array($r->kode_kamar) ? $r->kode_kamar : explode(',', $r->kode_kamar)));
                    }

                    foreach ($kamarCheckIds as $kamar) {
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

        // Create booking
        $peng = Pengunjung::create($r->all());

        // If kode_kamar present, mark those rooms as terisi so status updates in admin list
        try {
            $kamarIdsFinal = [];
            if ($r->filled('kode_kamar')) {
                $kamarIdsFinal = array_filter(array_map('trim', is_array($r->kode_kamar) ? $r->kode_kamar : explode(',', $r->kode_kamar)));
            }
            if (!empty($kamarIdsFinal)) {
                Kamar::whereIn('kode_kamar', $kamarIdsFinal)->update(['status' => 'terisi']);
            }
        } catch (\Exception $e) {
            // ignore errors — booking succeeded regardless
        }

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
        Log::info('PengunjungController@update called', ['id' => $id, 'input_keys' => array_keys($r->all())]);
        
        $r->validate([
            'nama' => 'nullable|string',
            'check_in' => 'required|date',
            'check_out' => 'required|date|after:check_in',
            'kode_kamar' => 'nullable|array',
            'kode_kamar.*' => 'string',
            'jumlah_kamar' => 'nullable|integer|min:1',
            'payment_status' => 'nullable|in:pending,konfirmasi_booking,paid,lunas,rejected',
            'kebutuhan_snack' => 'nullable|array',
            'kebutuhan_snack.*' => 'string',
            'kebutuhan_makan' => 'nullable|array',
            'kebutuhan_makan.*' => 'string',
        ]);

        // Convert array kode_kamar to comma-separated string (map numeric ids to kode_kamar)
        if ($r->has('kode_kamar') && is_array($r->kode_kamar)) {
            $rawKamarIds = $r->kode_kamar;
            $kamarIds = [];
            $invalid = [];
            foreach ($rawKamarIds as $v) {
                $v = trim($v);
                $km = Kamar::where('kode_kamar', $v)->first();
                if (!$km && is_numeric($v)) {
                    $km = Kamar::find($v);
                }
                if ($km) {
                    $kamarIds[] = $km->kode_kamar;
                } else {
                    $invalid[] = $v;
                }
            }
            if (!empty($invalid)) {
                return back()->withErrors(['kode_kamar' => 'Kamar tidak valid: ' . implode(', ', $invalid)])->withInput();
            }

            // VALIDASI: Cek overlap untuk setiap kamar (menggunakan mapped kode_kamar)
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

        // Convert array kebutuhan_snack to JSON string
        if ($r->has('kebutuhan_snack')) {
            $snacks = $r->kebutuhan_snack ?? [];
            $r->merge(['kebutuhan_snack' => json_encode($snacks)]);
        } else {
            $r->merge(['kebutuhan_snack' => json_encode([])]);
        }
        
        // Convert array kebutuhan_makan to JSON string
        if ($r->has('kebutuhan_makan')) {
            $meals = $r->kebutuhan_makan ?? [];
            $r->merge(['kebutuhan_makan' => json_encode($meals)]);
        } else {
            $r->merge(['kebutuhan_makan' => json_encode([])]);
        }

        $updated = $p->update($r->all());
        Log::info('PengunjungController@update result', ['id' => $id, 'updated' => $updated, 'pengunjung' => $p->toArray()]);

        return redirect()->route('pengunjung.show', $id)->with('success', 'Data pengunjung berhasil diupdate');
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
<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Kamar;
use Illuminate\Support\Facades\Schema;

class KamarController extends Controller
{
    public function __construct(){
        // use FQCN to avoid alias issues
        $this->middleware(\App\Http\Middleware\AdminAuth::class);
    }

    public function index(){
        // Some environments may still have the old column 'nomor_kamar' in the DB.
        // Choose ordering column at runtime to avoid SQL errors before migration runs.
        if (Schema::hasColumn('kamars', 'kode_kamar')) {
            $kamars = Kamar::orderBy('kode_kamar')->get();
        } else {
            $kamars = Kamar::orderBy('nomor_kamar')->get();
        }
        return view('admin.kamar', compact('kamars'));
    }

    public function create(){ return view('admin.kamar.create'); }

    public function store(Request $r){
        $r->validate([
            // Validate against whichever column exists in database
            'kode_kamar' => (Schema::hasColumn('kamars','kode_kamar') ? 'required|unique:kamars,kode_kamar' : 'nullable'),
            'nomor_kamar' => (!Schema::hasColumn('kamars','kode_kamar') ? 'required|unique:kamars,nomor_kamar' : 'nullable'),
            'jenis_kamar'=>'required','harga'=>'nullable|numeric'
        ]);
        // Build data mapping depending on DB column
        $data = $r->only(['kode_kamar','nomor_kamar','jenis_kamar','gedung','harga','fasilitas','status']);
        if (!Schema::hasColumn('kamars','kode_kamar') && isset($data['kode_kamar'])) {
            // If DB still uses nomor_kamar, map incoming kode_kamar input to nomor_kamar
            $data['nomor_kamar'] = $data['kode_kamar'];
            unset($data['kode_kamar']);
        }
        // filter only the final keys present
        $final = array_intersect_key($data, array_flip(['kode_kamar','nomor_kamar','jenis_kamar','gedung','harga','fasilitas','status']));
        Kamar::create($final);
        return redirect()->route('kamar.index')->with('success','Kamar ditambahkan');
    }

    public function edit($id){ $kamar = Kamar::findOrFail($id); return view('admin.kamar.edit', compact('kamar')); }

    public function update(Request $r, $id){
        $r->validate(['jenis_kamar'=>'required']);
        $kamar = Kamar::findOrFail($id);
        $kamar->update($r->only(['jenis_kamar','gedung','harga','fasilitas','status']));
        return redirect()->route('kamar.index')->with('success','Kamar diperbarui');
    }

    public function destroy($id){
        $kamar = Kamar::findOrFail($id);
        $kamar->delete();
        return back()->with('success','Kamar dihapus');
    }

    /**
     * Show availability checker for a given room (by nomor_kamar)
     */
    public function check(Request $r, $nomor)
    {
    $kamar = Kamar::where('kode_kamar', $nomor)->firstOrFail();

        $start = $r->input('start');
        $end = $r->input('end');
        $available = null;
        $overlapping = [];

        if ($start && $end) {
            try {
                $available = $kamar->isAvailableBetween($start, $end);
                // get overlapping bookings if not available
                if (!$available) {
                    $overlapping = \App\Models\Pengunjung::whereRaw("FIND_IN_SET(?, kode_kamar)", [$nomor])
                        ->where(function($q) use ($start, $end){
                            $q->whereBetween('check_in', [$start, $end])
                              ->orWhereBetween('check_out', [$start, $end])
                              ->orWhere(function($q2) use ($start, $end){
                                  $q2->where('check_in','<',$start)->where('check_out','>',$end);
                              });
                        })->get();
                }
            } catch (\Exception $e) {
                $available = null;
            }
        }

        return view('admin.kamar.check', compact('kamar','start','end','available','overlapping'));
    }
}

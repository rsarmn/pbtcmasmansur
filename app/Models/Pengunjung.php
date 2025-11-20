<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pengunjung extends Model
{
    use HasFactory;
    protected $fillable = [
      'nama','no_identitas','identity_type','jenis_tamu','check_in','check_out','kode_kamar',
      'asal_persyarikatan','tanggal_persyarikatan','nama_kegiatan','nama_pic','no_telp_pic','no_telp',
      'jumlah_peserta','jumlah_kamar','special_request',
      'payment_status','kebutuhan_snack','kebutuhan_makan','bukti_identitas','bukti_pembayaran'
    ];

      // Human readable payment status label
    public function getPaymentStatusLabelAttribute()
    {
        $s = $this->payment_status ?? '';
        $map = [
            'pending' => 'Menunggu Pembayaran', // Diubah sedikit agar lebih spesifik
            'konfirmasi_booking' => 'Menunggu Konfirmasi',
            'paid' => 'Lunas',
            'lunas' => 'Lunas',
            'rejected' => 'Ditolak',
        ];
        return $map[strtolower($s)] ?? ucfirst($s);
    }
}
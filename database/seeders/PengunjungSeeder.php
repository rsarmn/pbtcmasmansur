<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pengunjung;

class PengunjungSeeder extends Seeder
{
    public function run()
    {
        // richer test data with identity type, phone, payment status and sample evidence files
        $pengunjungs = [
            [
                'nama' => 'Ahmad Zulkifli',
                'no_identitas' => '3201012345670001',
                'identity_type' => 'KTP',
                'jenis_tamu' => 'Individu',
                'check_in' => '2025-10-15',
                'check_out' => '2025-10-17',
                'kode_kamar' => '102',
                'no_telp' => '081234567890',
                'payment_status' => 'konfirmasi_booking',
                'bukti_identitas' => 'samples/bukti_identitas_ahmad.jpg',
                'bukti_pembayaran' => 'samples/bukti_pembayaran_ahmad.jpg',
            ],
            [
                'nama' => 'Siti Rahma',
                'no_identitas' => '3201012345670002',
                'identity_type' => 'SIM',
                'jenis_tamu' => 'Corporate',
                'check_in' => '2025-10-16',
                'check_out' => '2025-10-20',
                'kode_kamar' => '202',
                'no_telp' => '082345678901',
                'payment_status' => 'pending',
                'nama_kegiatan' => 'Pelatihan SDM',
                'asal_persyarikatan' => 'PT. Contoh',
                'nama_pic' => 'Rina',
                'no_telp_pic' => '08199887766',
                'jumlah_peserta' => 12,
                'jumlah_kamar' => 6,
                'kebutuhan_snack' => 12,
                'kebutuhan_makan' => 12,
            ],
            [
                'nama' => 'Budi Santoso',
                'no_identitas' => '1234567890123',
                'identity_type' => 'KTP',
                'jenis_tamu' => 'Individu',
                'check_in' => '2025-10-14',
                'check_out' => '2025-10-18',
                'kode_kamar' => '101',
                'no_telp' => '081122334455',
                'payment_status' => 'lunas',
            ],
            // additional manual booking for testing room status transitions
            [
                'nama' => 'Test Booking - Occupied',
                'no_identitas' => 'TEST-0001',
                'identity_type' => 'KTM',
                'jenis_tamu' => 'Individu',
                'check_in' => now()->subDays(2)->toDateString(),
                'check_out' => now()->addDays(2)->toDateString(),
                'kode_kamar' => '303',
                'no_telp' => '08000000001',
                'payment_status' => 'konfirmasi_booking',
            ],
            [
                'nama' => 'Test Booking - Available After Checkout',
                'no_identitas' => 'TEST-0002',
                'identity_type' => 'KTP',
                'jenis_tamu' => 'Individu',
                'check_in' => now()->subDays(10)->toDateString(),
                'check_out' => now()->subDays(5)->toDateString(),
                'kode_kamar' => '304',
                'no_telp' => '08000000002',
                'payment_status' => 'lunas',
            ],
        ];

        foreach ($pengunjungs as $p) {
            Pengunjung::firstOrCreate(
                ['no_identitas' => $p['no_identitas']],
                $p
            );
        }
    }
}
<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pengunjung;

class DummyDataSeeder extends Seeder
{
    public function run()
    {
        // Create a few sample pengunjung for manual testing
        $samples = [
            [
                'nama' => 'Test Individu 1',
                'no_identitas' => 'ID-TEST-001',
                'identity_type' => 'KTP',
                'jenis_tamu' => 'individu',
                'check_in' => now()->toDateString(),
                'check_out' => now()->addDays(2)->toDateString(),
                'nomor_kamar' => null,
                'payment_status' => 'pending',
            ],
            [
                'nama' => 'Test Individu 2',
                'no_identitas' => 'ID-TEST-002',
                'identity_type' => 'SIM',
                'jenis_tamu' => 'individu',
                'check_in' => now()->subDays(3)->toDateString(),
                'check_out' => now()->subDays(1)->toDateString(),
                'nomor_kamar' => null,
                'payment_status' => 'paid',
            ],
            [
                'nama' => 'Event ABC',
                'no_identitas' => null,
                'identity_type' => null,
                'jenis_tamu' => 'corporate',
                'nama_kegiatan' => 'Pelatihan ABC',
                'nama_pic' => 'Pak Budi',
                'no_telp_pic' => '081234567890',
                'asal_persyarikatan' => 'Organisasi XYZ',
                'check_in' => now()->addDays(5)->toDateString(),
                'check_out' => now()->addDays(7)->toDateString(),
                'jumlah_peserta' => 20,
                'jumlah_kamar' => 10,
                'kebutuhan_snack' => '20 porsi',
                'kebutuhan_makan' => '20 porsi',
                'payment_status' => 'pending',
            ],
            [
                'nama' => 'Event Paid',
                'jenis_tamu' => 'corporate',
                'nama_kegiatan' => 'Seminar XYZ',
                'nama_pic' => 'Ibu Siti',
                'no_telp_pic' => '081298765432',
                'asal_persyarikatan' => 'Firma 123',
                'check_in' => now()->subDays(10)->toDateString(),
                'check_out' => now()->subDays(8)->toDateString(),
                'jumlah_peserta' => 15,
                'jumlah_kamar' => 8,
                'payment_status' => 'paid',
            ],
        ];

        foreach ($samples as $s) {
            Pengunjung::firstOrCreate([
                'nama' => $s['nama'],
                'check_in' => $s['check_in']
            ], $s);
        }
    }
}

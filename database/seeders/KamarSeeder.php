<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Kamar;

class KamarSeeder extends Seeder
{
    public function run()
    {
        $kamars = [];

        // Guestroom AC - 3 kamar
        for ($i = 1; $i <= 3; $i++) {
            $kamars[] = [
                    'kode_kamar' => sprintf('GR-%02d', $i),
                'jenis_kamar' => 'Guestroom AC',
                'gedung' => 'Guestroom',
                'harga' => 150000,
                'fasilitas' => 'AC, TV, Wifi, Toileters, satu set meja dan kursi',
                'status' => 'kosong',
            ];
        }

        // Gedung Kuning - Standard 12 kamar
        for ($i = 1; $i <= 12; $i++) {
            $kamars[] = [
                    'kode_kamar' => sprintf('KUN-STD-%02d', $i),
                'jenis_kamar' => 'Standard',
                'gedung' => 'Gedung Kuning',
                'harga' => 225000,
                'fasilitas' => 'AC, Wifi, Water heater, welcome drink (coffee, tea), toileters, satu set meja dan kursi',
                'status' => 'kosong',
            ];
        }

        // Gedung Kuning - Deluxe 2 kamar
        for ($i = 1; $i <= 2; $i++) {
            $kamars[] = [
                    'kode_kamar' => sprintf('KUN-DLX-%02d', $i),
                'jenis_kamar' => 'Deluxe',
                'gedung' => 'Gedung Kuning',
                'harga' => 275000,
                'fasilitas' => 'AC, Wifi, Water heater, welcome drink (coffee, tea), toileters, sofa, satu set meja dan kursi',
                'status' => 'kosong',
            ];
        }

        // Student room Non AC Gedung Hijau - 35 kamar
        for ($i = 1; $i <= 35; $i++) {
            $kamars[] = [
                    'kode_kamar' => sprintf('GH-NONAC-%02d', $i),
                'jenis_kamar' => 'Student Non AC',
                'gedung' => 'Gedung Hijau',
                'harga' => 150000,
                'fasilitas' => 'Kipas, Kamar mandi luar, 4 almari, 2 single bed tingkat, 4 set meja dan kursi',
                'status' => 'kosong',
            ];
        }

        // Student room Gedung Hijau - AC 9 kamar
        for ($i = 1; $i <= 9; $i++) {
            $kamars[] = [
                    'kode_kamar' => sprintf('GH-AC-%02d', $i),
                'jenis_kamar' => 'Student AC',
                'gedung' => 'Gedung Hijau',
                'harga' => 200000,
                'fasilitas' => 'AC, Kamar mandi dalam, 4 almari, 2 single bed tingkat, 4 set meja dan kursi',
                'status' => 'kosong',
            ];
        }

        // Student room Gedung Hijau - Non AC 32 kamar
        for ($i = 1; $i <= 32; $i++) {
            $kamars[] = [
                    'kode_kamar' => sprintf('GH-NONAC2-%02d', $i),
                'jenis_kamar' => 'Student Non AC',
                'gedung' => 'Gedung Hijau',
                'harga' => 150000,
                'fasilitas' => 'Kipas, Kamar mandi luar, 4 almari, 2 single bed tingkat, 4 set meja dan kursi',
                'status' => 'kosong',
            ];
        }

        foreach ($kamars as $kamar) {
                Kamar::firstOrCreate(
                    ['kode_kamar' => $kamar['kode_kamar']],
                $kamar
            );
        }
    }
}
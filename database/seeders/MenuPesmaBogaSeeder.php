<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MenuPesmaBoga;

class MenuPesmaBogaSeeder extends Seeder
{
    public function run()
    {
        $menus = [
            // Snack items
            ['nama_menu' => 'Kue Basah (per porsi)', 'jenis' => 'snack', 'harga' => 5000, 'deskripsi' => 'Kue basah tradisional', 'tersedia' => true],
            ['nama_menu' => 'Kue Kering (per box)', 'jenis' => 'snack', 'harga' => 15000, 'deskripsi' => 'Kue kering assorted', 'tersedia' => true],
            ['nama_menu' => 'Pisang Goreng (per porsi)', 'jenis' => 'snack', 'harga' => 8000, 'deskripsi' => 'Pisang goreng crispy', 'tersedia' => true],
            ['nama_menu' => 'Teh/Kopi (per cangkir)', 'jenis' => 'snack', 'harga' => 3000, 'deskripsi' => 'Minuman hangat', 'tersedia' => true],

            // Meal items
            ['nama_menu' => 'Nasi Box Standar', 'jenis' => 'makan', 'harga' => 20000, 'deskripsi' => 'Nasi putih + lauk + sayur', 'tersedia' => true],
            ['nama_menu' => 'Nasi Box Premium', 'jenis' => 'makan', 'harga' => 30000, 'deskripsi' => 'Nasi putih + lauk premium + sayur + buah', 'tersedia' => true],
            ['nama_menu' => 'Nasi Tumpeng Mini', 'jenis' => 'makan', 'harga' => 150000, 'deskripsi' => 'Untuk 10 porsi', 'tersedia' => true],
            ['nama_menu' => 'Prasmanan (per orang)', 'jenis' => 'makan', 'harga' => 35000, 'deskripsi' => 'Buffet lengkap', 'tersedia' => true],
        ];

        foreach ($menus as $menu) {
            MenuPesmaBoga::firstOrCreate(
                ['nama_menu' => $menu['nama_menu']],
                $menu
            );
        }
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MenuPesmaBoga extends Model
{
    protected $table = 'menu_pesma_boga';

    protected $fillable = [
        'nama_menu',
        'jenis',
        'harga',
        'deskripsi',
        'tersedia'
    ];
}

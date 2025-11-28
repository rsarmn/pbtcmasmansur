<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Create menu_pesma_boga table for consumption options
        Schema::create('menu_pesma_boga', function (Blueprint $table) {
            $table->id();
            $table->string('nama_menu');
            $table->enum('jenis', ['snack', 'makan'])->default('snack');
            $table->integer('harga')->default(0);
            $table->text('deskripsi')->nullable();
            $table->boolean('tersedia')->default(true);
            $table->timestamps();
        });

        // Add tanggal_persyarikatan to pengunjungs if not exists
        if (!Schema::hasColumn('pengunjungs', 'tanggal_persyarikatan')) {
            Schema::table('pengunjungs', function (Blueprint $table) {
                $table->date('tanggal_persyarikatan')->nullable()->after('asal_persyarikatan');
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('menu_pesma_boga');
        
        if (Schema::hasColumn('pengunjungs', 'tanggal_persyarikatan')) {
            Schema::table('pengunjungs', function (Blueprint $table) {
                $table->dropColumn('tanggal_persyarikatan');
            });
        }
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('kamars', function (Blueprint $table) {
        $table->id();
        $table->string('kode_kamar')->unique();
        $table->string('jenis_kamar');
        $table->string('gedung')->nullable();
        $table->integer('harga')->default(0);
        $table->text('fasilitas')->nullable();
        $table->enum('status', ['kosong','terisi','perawatan'])->default('kosong');
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kamars');
    }
};
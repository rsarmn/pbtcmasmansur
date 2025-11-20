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
    Schema::create('pengunjungs', function (Blueprint $table) {
        $table->id();
        $table->string('nama');
        $table->string('no_identitas')->nullable();
        $table->enum('jenis_tamu', ['individu','corporate'])->default('individu');
        $table->date('check_in')->nullable();
        $table->date('check_out')->nullable();
        $table->string('kode_kamar')->nullable(); //ganti dr nomor_kamar
        // corporate fields
        $table->string('asal_persyarikatan')->nullable();
        $table->string('nama_kegiatan')->nullable();
        $table->string('nama_pic')->nullable();
        $table->string('no_telp_pic')->nullable();
        $table->integer('jumlah_peserta')->nullable();
        $table->integer('jumlah_kamar')->nullable();
        $table->text('special_request')->nullable();

        // tambahan
        $table->string('identity_type')->nullable();
        $table->string('no_telp')->nullable();
        $table->string('payment_status')->nullable();
        $table->string('bukti_identitas')->nullable();
        $table->string('bukti_pembayaran')->nullable();
        $table->integer('kebutuhan_snack')->nullable();
        $table->integer('kebutuhan_makan')->nullable();

        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengunjungs');
    }
};
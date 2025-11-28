<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('beranda', function (Blueprint $table) {
            $table->id();
            $table->string('instagram')->nullable();
            $table->string('email')->nullable();
            $table->string('whatsapp')->nullable();
            $table->string('location')->nullable();
            $table->text('maps_link')->nullable();
            $table->string('slider1_image')->nullable();
            $table->string('slider1_text')->nullable();
            $table->string('slider2_image')->nullable();
            $table->string('slider2_text')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('beranda');
    }
};

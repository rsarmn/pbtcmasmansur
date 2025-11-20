<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Update payment_status enum to include 'konfirmasi_booking', 'lunas', 'rejected'
        if (Schema::hasColumn('pengunjungs', 'payment_status')) {
            // For MySQL, we need to alter the enum
            DB::statement("ALTER TABLE pengunjungs MODIFY COLUMN payment_status ENUM('pending', 'konfirmasi_booking', 'paid', 'lunas', 'rejected') DEFAULT 'pending'");
        }
    }

    public function down()
    {
        if (Schema::hasColumn('pengunjungs', 'payment_status')) {
            DB::statement("ALTER TABLE pengunjungs MODIFY COLUMN payment_status ENUM('pending', 'paid') DEFAULT 'pending'");
        }
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pengunjungs', function (Blueprint $table) {
            if (!Schema::hasColumn('pengunjungs', 'bukti_pembayaran')) {
                $table->string('bukti_pembayaran')->nullable()->after('bukti_identitas');
            }
        });
    }

    public function down(): void
    {
        Schema::table('pengunjungs', function (Blueprint $table) {
            if (Schema::hasColumn('pengunjungs', 'bukti_pembayaran')) {
                $table->dropColumn('bukti_pembayaran');
            }
        });
    }
};

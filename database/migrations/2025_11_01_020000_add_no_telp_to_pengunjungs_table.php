<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pengunjungs', function (Blueprint $table) {
            if (!Schema::hasColumn('pengunjungs', 'no_telp')) {
                $table->string('no_telp')->nullable()->after('no_telp_pic');
            }
        });
    }

    public function down(): void
    {
        Schema::table('pengunjungs', function (Blueprint $table) {
            if (Schema::hasColumn('pengunjungs', 'no_telp')) {
                $table->dropColumn('no_telp');
            }
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Rename for kamars table
        if (Schema::hasTable('kamars') && Schema::hasColumn('kamars', 'nomor_kamar')) {
            Schema::table('kamars', function (Blueprint $table) {
                $table->renameColumn('nomor_kamar', 'kode_kamar');
            });
        }

        // Rename for pengunjungs table
        if (Schema::hasTable('pengunjungs') && Schema::hasColumn('pengunjungs', 'nomor_kamar')) {
            Schema::table('pengunjungs', function (Blueprint $table) {
                $table->renameColumn('nomor_kamar', 'kode_kamar');
            });
        }
    }

    public function down()
    {
        if (Schema::hasTable('kamars') && Schema::hasColumn('kamars', 'kode_kamar')) {
            Schema::table('kamars', function (Blueprint $table) {
                $table->renameColumn('kode_kamar', 'nomor_kamar');
            });
        }

        if (Schema::hasTable('pengunjungs') && Schema::hasColumn('pengunjungs', 'kode_kamar')) {
            Schema::table('pengunjungs', function (Blueprint $table) {
                $table->renameColumn('kode_kamar', 'nomor_kamar');
            });
        }
    }
};

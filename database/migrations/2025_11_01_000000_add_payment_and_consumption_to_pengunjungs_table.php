<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pengunjungs', function (Blueprint $table) {
            if (!Schema::hasColumn('pengunjungs', 'payment_status')) {
                $table->enum('payment_status', ['pending','paid'])->default('pending')->after('special_request');
            }
            if (!Schema::hasColumn('pengunjungs', 'kebutuhan_snack')) {
                $table->string('kebutuhan_snack')->nullable()->after('payment_status');
            }
            if (!Schema::hasColumn('pengunjungs', 'kebutuhan_makan')) {
                $table->string('kebutuhan_makan')->nullable()->after('kebutuhan_snack');
            }
            if (!Schema::hasColumn('pengunjungs', 'bukti_identitas')) {
                $table->string('bukti_identitas')->nullable()->after('kebutuhan_makan');
            }
            if (!Schema::hasColumn('pengunjungs', 'identity_type')) {
                $table->string('identity_type')->nullable()->after('bukti_identitas');
            }
        });
    }

    public function down(): void
    {
        Schema::table('pengunjungs', function (Blueprint $table) {
            if (Schema::hasColumn('pengunjungs', 'payment_status')) {
                $table->dropColumn('payment_status');
            }
            if (Schema::hasColumn('pengunjungs', 'kebutuhan_snack')) {
                $table->dropColumn('kebutuhan_snack');
            }
            if (Schema::hasColumn('pengunjungs', 'kebutuhan_makan')) {
                $table->dropColumn('kebutuhan_makan');
            }
            if (Schema::hasColumn('pengunjungs', 'bukti_identitas')) {
                $table->dropColumn('bukti_identitas');
            }
            if (Schema::hasColumn('pengunjungs', 'identity_type')) {
                $table->dropColumn('identity_type');
            }
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tr_digital_signatures', function (Blueprint $table) {
            $table->string('tempat_surat')->nullable()->default('Jakarta')->after('tgl_waktu_surat');
            $table->string('zona_surat')->nullable()->default('Waktu Indonesia Barat')->after('tempat_surat');
        });
    }

    public function down(): void
    {
        Schema::table('tr_digital_signatures', function (Blueprint $table) {
            $table->dropColumn(['tempat_surat', 'zona_surat']);
        });
    }
};

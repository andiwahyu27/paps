<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tr_sidang_signatures', function (Blueprint $table) {
            $table->string('nomor_surat', 100)->nullable()->after('jenis_user');
        });
    }

    public function down(): void
    {
        Schema::table('tr_sidang_signatures', function (Blueprint $table) {
            $table->dropColumn('nomor_surat');
        });
    }
};

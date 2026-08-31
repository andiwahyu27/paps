<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tb_pengajuans', function (Blueprint $table) {
            $table->timestamp('rekomendasi_akreditasi_submitted_at')->nullable();
            $table->unsignedBigInteger('rekomendasi_akreditasi_submitted_by')->nullable();
            $table->index('rekomendasi_akreditasi_submitted_by', 'pengajuan_rekomendasi_submitted_by_index');
        });
    }

    public function down(): void
    {
        Schema::table('tb_pengajuans', function (Blueprint $table) {
            $table->dropIndex('pengajuan_rekomendasi_submitted_by_index');
            $table->dropColumn([
                'rekomendasi_akreditasi_submitted_at',
                'rekomendasi_akreditasi_submitted_by',
            ]);
        });
    }
};

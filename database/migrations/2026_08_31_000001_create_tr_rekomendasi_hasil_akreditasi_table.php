<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tr_rekomendasi_hasil_akreditasi', function (Blueprint $table) {
            $table->id();
            $table->integer('pengajuan_id');
            $table->string('kategori', 30);
            $table->text('isi');
            $table->unsignedInteger('urutan')->default(1);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();

            $table->foreign('pengajuan_id')
                ->references('id')->on('tb_pengajuans')->onDelete('cascade');
            $table->index(['pengajuan_id', 'kategori', 'urutan'], 'rekomendasi_pengajuan_kategori_urutan_index');
            $table->index('created_by');
            $table->index('updated_by');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tr_rekomendasi_hasil_akreditasi');
    }
};

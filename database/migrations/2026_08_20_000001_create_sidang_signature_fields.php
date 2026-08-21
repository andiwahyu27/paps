<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tb_pengajuans', function (Blueprint $table) {
            $table->string('ttd_sidang_token', 64)->nullable()->unique()->after('ttd_token');
            $table->timestamp('ba_sidang_submitted_at')->nullable()->after('ba_submitted_at');
        });

        Schema::create('tr_sidang_signatures', function (Blueprint $table) {
            $table->id();
            $table->integer('pengajuan_id');
            $table->foreign('pengajuan_id')->references('id')->on('tb_pengajuans')->onDelete('cascade');
            $table->string('jenis_user', 40);
            $table->string('nama_user');
            $table->string('jabatan_user')->nullable();
            $table->string('ttd', 500)->nullable();
            $table->date('tgl_surat')->nullable();
            $table->time('waktu_surat')->nullable();
            $table->string('tgl_waktu_surat')->nullable();
            $table->string('tempat_surat', 100)->nullable();
            $table->string('zona_surat', 100)->nullable();
            $table->string('hari_tanggal_surat', 255)->nullable();
            $table->string('status_ttd', 20)->default('pending');
            $table->timestamps();
            $table->unique(['pengajuan_id', 'jenis_user']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tr_sidang_signatures');
        Schema::table('tb_pengajuans', function (Blueprint $table) {
            $table->dropUnique(['ttd_sidang_token']);
            $table->dropColumn(['ttd_sidang_token', 'ba_sidang_submitted_at']);
        });
    }
};

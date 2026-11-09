<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('mt_kuesioner', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 60);
            $table->string('nama');
            $table->string('jenis', 60);
            $table->unsignedInteger('versi');
            $table->boolean('is_active')->default(true);
            $table->unique(['kode', 'versi']);
        });

        Schema::create('mt_pertanyaan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kuesioner_id')->constrained('mt_kuesioner');
            $table->string('kode', 40);
            $table->string('blok', 10);
            $table->text('pertanyaan');
            $table->string('tipe_pertanyaan', 20);
            $table->unsignedInteger('order_number');
            $table->json('opsi')->nullable();
            $table->unique(['kuesioner_id', 'kode']);
        });

        Schema::create('tr_monev', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('kuesioner_id')->constrained('mt_kuesioner');
            $table->unsignedBigInteger('profile_id');
            $table->unsignedBigInteger('jenis_pengajuan_id');
            $table->unsignedSmallInteger('report_year');
            $table->string('status', 20)->default('draft');
            $table->unsignedInteger('edit_version')->default(1);
            $table->unsignedBigInteger('submitted_by')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->json('identity_snapshot')->nullable();
            $table->json('accreditation_snapshot')->nullable();
            $table->unsignedBigInteger('legacy_report_id')->nullable()->unique();
            $table->timestamps();
            $table->unique(['profile_id', 'jenis_pengajuan_id', 'report_year'], 'monev_profile_program_year_uq');
        });

        Schema::create('tr_monev_jawaban', function (Blueprint $table) {
            $table->id();
            $table->foreignId('monev_id')->constrained('tr_monev')->cascadeOnDelete();
            $table->foreignId('pertanyaan_id')->constrained('mt_pertanyaan');
            $table->decimal('numeric_value', 24, 8)->nullable();
            $table->text('text_value')->nullable();
            $table->unique(['monev_id', 'pertanyaan_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tr_monev_jawaban');
        Schema::dropIfExists('tr_monev');
        Schema::dropIfExists('mt_pertanyaan');
        Schema::dropIfExists('mt_kuesioner');
    }
};

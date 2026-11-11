<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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
            $table->string('blok', 30);
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

        $questionnaireId = DB::table('mt_kuesioner')->insertGetId([
            'kode' => 'monitoring-akreditasi-pstk',
            'nama' => 'Monitoring Akreditasi PSTK',
            'jenis' => 'monitoring-akreditasi',
            'versi' => 1,
            'is_active' => true,
        ]);

        $questions = [
            ['identitas', '1', 'Nama lembaga pelatihan', 'string', 1],
            ['identitas', '2', 'Program pelatihan yang terakreditasi', 'string', 2],
            ['identitas', '3', 'Alamat', 'textarea', 3],
            ['identitas', '4', 'Nomor telepon', 'string', 4],
            ['identitas', '5', 'E-mail', 'string', 5],
            ['identitas', '6', 'Website', 'string', 6],
            ['identitas', '7', 'Nomor sertifikat akreditasi', 'string', 7],
            ['identitas', '8', 'Status akreditasi', 'string', 8],
            ['identitas', '9', 'Tahun pemantauan', 'integer', 9],
            ['identitas', 'has_training', 'Apakah lembaga menyelenggarakan pelatihan setelah terakreditasi?', 'radio', 10, ['Ya', 'Tidak']],
            ['pelaksanaan', '10', 'Jumlah pelatihan yang dilaksanakan pada tahun ini', 'integer', 11],
            ['pelaksanaan', '11', 'Jumlah peserta pelatihan pada tahun ini', 'integer', 12],
            ['pelaksanaan', '12a', 'Asal peserta pelatihan Internal', 'integer', 13],
            ['pelaksanaan', '12b', 'Asal peserta pelatihan Eksternal', 'integer', 14],
            ['pelaksanaan', '13', 'Kesesuaian pelaksanaan dengan program yang terakreditasi', 'radio', 15, ['Sesuai', 'Ada modifikasi minor', 'Tidak sesuai']],
            ['pelaksanaan', '14', 'Metode pelatihan yang digunakan', 'select', 16, ['Tatap muka', 'Daring', 'Blended learning']],
            ['pelaksanaan', '15', 'Penilaian kelulusan peserta pelatihan', 'radio', 17, ['Sangat memuaskan', 'Memuaskan', 'Cukup memuaskan', 'Tidak lulus']],
            ['pelaksanaan', '16', 'Penilaian penyelenggaraan pelatihan secara umum', 'radio', 18, ['Sangat baik', 'Baik', 'Kurang baik', 'Tidak baik']],
            ['sumber-daya', '17', 'Tenaga pengajar yang digunakan', 'select', 19, ['Internal', 'Eksternal']],
            ['sumber-daya', '18', 'Jabatan tenaga pengajar', 'select', 20, ['Widyaiswara', 'Pejabat Struktural', 'Fungsional Lainnya']],
            ['sumber-daya', '19', 'Ketersediaan dokumen mutu pelatihan', 'radio', 21, ['Lengkap', 'Sebagian', 'Tidak ada']],
            ['sumber-daya', '20', 'Pemutakhiran dokumen akreditasi', 'radio', 22, ['Sudah dilakukan', 'Belum dilakukan']],
            ['sumber-daya', '21', 'Kepatuhan terhadap persyaratan akreditasi', 'radio', 23, ['Terpenuhi', 'Sebagian', 'Tidak terpenuhi']],
            ['evaluasi', '22', 'Evaluasi peserta terhadap pelatihan', 'radio', 24, ['Sangat puas', 'Puas', 'Cukup', 'Kurang']],
            ['evaluasi', '23', 'Tindak lanjut hasil pelatihan', 'radio', 25, ['Ada', 'Tidak ada']],
            ['evaluasi', '23a', 'Keterangan tindak lanjut yang dilakukan', 'textarea', 26],
            ['evaluasi', '24', 'Kendala atau permasalahan dalam penyelenggaraan pelatihan', 'textarea', 27],
            ['evaluasi', '25', 'Rencana pelatihan tahun depan', 'textarea', 28],
            ['evaluasi', '26', 'Kebutuhan dukungan atau pembinaan dari instansi pembina/BPS', 'textarea', 29],
        ];

        foreach ($questions as $question) {
            DB::table('mt_pertanyaan')->insert([
                'kuesioner_id' => $questionnaireId,
                'kode' => $question[1],
                'blok' => $question[0],
                'pertanyaan' => $question[2],
                'tipe_pertanyaan' => $question[3],
                'order_number' => $question[4],
                'opsi' => isset($question[5]) ? json_encode($question[5], JSON_UNESCAPED_UNICODE) : null,
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('tr_monev_jawaban');
        Schema::dropIfExists('tr_monev');
        Schema::dropIfExists('mt_pertanyaan');
        Schema::dropIfExists('mt_kuesioner');
    }
};

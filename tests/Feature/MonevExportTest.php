<?php

namespace Tests\Feature;

use App\Models\MonitoringReport;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class MonevExportTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']);
        DB::purge('sqlite');
        $this->withoutMiddleware(\App\Http\Middleware\SecurityHeaders::class);

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedInteger('role');
            $table->timestamps();
        });
        Schema::create('tb_profile_lembagas', function (Blueprint $table) {
            $table->id();
            $table->string('nama_lembaga');
        });
        Schema::create('mt_jenis_pengajuans', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
        });
        Schema::create('monitoring_reports', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid');
            $table->unsignedBigInteger('profile_id');
            $table->unsignedBigInteger('jenis_pengajuan_id');
            $table->unsignedSmallInteger('report_year');
            $table->string('status');
            $table->unsignedInteger('edit_version')->default(1);
            $table->timestamps();
        });
        Schema::create('mt_kuesioner', function (Blueprint $table) {
            $table->id();
            $table->string('kode');
            $table->string('nama');
            $table->string('jenis');
            $table->unsignedInteger('versi');
            $table->boolean('is_active');
        });
        Schema::create('mt_pertanyaan', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('kuesioner_id');
            $table->string('kode');
            $table->string('blok');
            $table->string('pertanyaan');
            $table->string('tipe_pertanyaan');
            $table->unsignedInteger('order_number');
            $table->json('opsi')->nullable();
        });
        Schema::create('tr_monev', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid');
            $table->unsignedBigInteger('kuesioner_id');
            $table->unsignedBigInteger('profile_id');
            $table->unsignedBigInteger('jenis_pengajuan_id');
            $table->unsignedSmallInteger('report_year');
            $table->string('status');
            $table->unsignedInteger('edit_version');
            $table->unsignedBigInteger('legacy_report_id');
            $table->timestamps();
        });
        Schema::create('tr_monev_jawaban', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('monev_id');
            $table->unsignedBigInteger('pertanyaan_id');
            $table->decimal('numeric_value', 24, 8)->nullable();
            $table->text('text_value')->nullable();
        });
    }

    public function test_export_reads_current_draft_and_preserves_zero_multiple_choices_and_safe_text(): void
    {
        $report = $this->fixture();
        $response = $this->actingAs($this->secretariat())->get(route('monitoring-evaluasi.export', $report));

        $response->assertOk();
        $rows = $this->csvRows($response->streamedContent());
        $this->assertSame(['Lembaga', 'Program', 'Tahun', 'Status', 'Pertanyaan', 'Jawaban'], $rows[0]);
        $answers = array_column(array_slice($rows, 1), 5, 4);
        $this->assertSame('0', $answers['11']);
        $this->assertSame('0', $answers['12a']);
        $this->assertSame('0', $answers['12b']);
        $this->assertSame('Tatap muka, Daring', $answers['14']);
        $this->assertSame("'=HYPERLINK(\"evil\")", $answers['24']);
        $this->assertSame('draft', $rows[1][3]);
    }

    public function test_non_secretariat_cannot_export(): void
    {
        $report = $this->fixture();
        $user = User::create(['name' => 'Lembaga', 'role' => 4]);
        $this->actingAs($user)->get(route('monitoring-evaluasi.export', $report))->assertForbidden();
    }

    private function fixture(): MonitoringReport
    {
        DB::table('tb_profile_lembagas')->insert(['id' => 1, 'nama_lembaga' => 'Lembaga Uji']);
        DB::table('mt_jenis_pengajuans')->insert(['id' => 1, 'nama' => 'Komputer']);
        $report = MonitoringReport::create(['profile_id' => 1, 'jenis_pengajuan_id' => 1, 'report_year' => 2026, 'status' => 'draft']);
        $questionnaire = DB::table('mt_kuesioner')->insertGetId(['kode' => 'monitoring-akreditasi-pstk', 'nama' => 'Monev', 'jenis' => 'monev', 'versi' => 1, 'is_active' => 1]);
        $monev = DB::table('tr_monev')->insertGetId(['uuid' => $report->uuid, 'kuesioner_id' => $questionnaire, 'profile_id' => 1, 'jenis_pengajuan_id' => 1, 'report_year' => 2026, 'status' => 'draft', 'edit_version' => 1, 'legacy_report_id' => $report->id]);
        foreach ([['11', 'integer', 0, null], ['12a', 'integer', 0, null], ['12b', 'integer', 0, null], ['14', 'select', null, '["Tatap muka","Daring"]'], ['24', 'textarea', null, '=HYPERLINK("evil")']] as $order => [$code, $type, $numeric, $text]) {
            $question = DB::table('mt_pertanyaan')->insertGetId(['kuesioner_id' => $questionnaire, 'kode' => $code, 'blok' => 'uji', 'pertanyaan' => 'Pertanyaan '.$code, 'tipe_pertanyaan' => $type, 'order_number' => $order + 1]);
            DB::table('tr_monev_jawaban')->insert(['monev_id' => $monev, 'pertanyaan_id' => $question, 'numeric_value' => $numeric, 'text_value' => $text]);
        }
        return $report;
    }

    private function secretariat(): User
    {
        return User::create(['name' => 'Sekretariat', 'role' => 2]);
    }

    private function csvRows(string $csv): array
    {
        $stream = fopen('php://temp', 'w+');
        fwrite($stream, $csv);
        rewind($stream);
        $rows = [];
        while (($row = fgetcsv($stream)) !== false) $rows[] = $row;
        fclose($stream);
        return $rows;
    }
}

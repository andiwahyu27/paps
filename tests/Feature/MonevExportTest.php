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
        Schema::create('monitoring_accreditations', function (Blueprint $table) {
            $table->id();
            $table->date('valid_from');
            $table->date('valid_until');
        });
        Schema::create('monitoring_report_links', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('monev_id');
            $table->text('token_encrypted')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('deleted_at')->nullable();
        });
        $migration = require database_path('migrations/2026_11_11_000002_create_monev_submissions.php');
        $migration->up();
        $migration = require database_path('migrations/2026_11_11_000007_create_monitoring_reporting_periods.php');
        $migration->up();
    }

    public function test_export_reads_current_draft_and_preserves_zero_multiple_choices_and_safe_text(): void
    {
        $report = $this->fixture();
        $response = $this->actingAs($this->secretariat())->get(route('monitoring-evaluasi.export', $report));

        $response->assertOk();
        $rows = $this->csvRows($response->streamedContent());
        $this->assertSame(['Nama Lembaga', 'Program', 'Tahun', 'Status Submit', 'No Pertanyaan', 'Pertanyaan', 'Jawaban'], $rows[0]);
        $answers = array_column(array_slice($rows, 1), 6, 4);
        $this->assertSame('0', $answers['11']);
        $this->assertSame('Pertanyaan 11', $rows[1][5]);
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

    public function test_filtered_export_contains_only_matching_reports(): void
    {
        $report = $this->fixture();
        DB::table('tb_profile_lembagas')->insert(['id' => 2, 'nama_lembaga' => 'Lembaga Lain']);
        MonitoringReport::create(['profile_id' => 2, 'jenis_pengajuan_id' => 1, 'report_year' => 2025, 'status' => 'submitted']);

        $response = $this->actingAs($this->secretariat())->get(route('monitoring-evaluasi.export-filtered', [
            'year' => 2026,
            'status' => 'draft',
            'search' => 'Lembaga Uji',
        ]));

        $response->assertOk();
        $csv = $response->streamedContent();
        $this->assertStringContainsString('Lembaga Uji', $csv);
        $this->assertStringNotContainsString('Lembaga Lain', $csv);
        $this->assertSame(6, count($this->csvRows($csv)));
    }

    public function test_normalized_dashboard_keeps_last_submission_stats_and_exports_current_draft(): void
    {
        $legacy = $this->fixture();
        $migration = require database_path('migrations/2026_11_12_000001_add_questionnaire_version_labels.php');
        $migration->up();
        $report = \App\Models\Monev::firstOrFail();
        $this->assertSame('2025.1', $report->questionnaire->display_version);
        $report->update(['status' => 'revision_draft']);
        DB::table('monev_submissions')->insert([
            'monev_id' => $report->id, 'version' => 1, 'answers' => '{"has_training":"Tidak"}',
            'identity_snapshot' => '{}', 'accreditation_snapshot' => '{}',
            'submitted_at' => '2026-11-11 14:35:12', 'submitted_by' => 1, 'submission_channel' => 'account',
        ]);
        DB::table('mt_kuesioner')->where('id', $report->kuesioner_id)->update(['is_active' => false]);
        DB::table('mt_kuesioner')->insert([
            'kode' => 'monitoring-akreditasi-pstk', 'nama' => 'Monev baru', 'jenis' => 'versi-aktif', 'versi' => 2, 'is_active' => true,
        ]);
        config(['monev.use_normalized_reports' => true]);
        Schema::drop('monitoring_reports');
        $this->actingAs($this->secretariat());
        $dashboard = $this->get(route('monitoring-evaluasi', ['year' => 2026]))->assertOk();
        $dashboard->assertDontSee("@include('monitoring-akreditasi.report-status", false);
        $this->assertSame(0, $dashboard->viewData('stats')['training']);
        $dashboard->assertSeeText('Pelatihan Terselenggara');
        $detail = $this->get(route('monitoring-evaluasi.detail', $legacy))->assertOk();
        $this->assertSame(0, $detail->viewData('answers')[12]['internal']);
        $detail->assertSeeText('Tatap muka, Daring')->assertSeeText('Draf Revisi')
            ->assertSeeText('12a. Pertanyaan 12a')
            ->assertSeeText('Jenis Kuesioner')->assertSeeText('monev')->assertDontSeeText('versi-aktif')
            ->assertSeeText('Versi Kuesioner')->assertSeeText('2025.1')
            ->assertSeeText('Pelapor Terakhir')->assertSeeText('Sekretariat')
            ->assertSeeText('11/11/2026 14:35:12 WIB')->assertDontSeeText('Terlambat');
        $csv = $this->get(route('monitoring-evaluasi.export', $report))->assertOk()->streamedContent();
        $rows = array_column(array_slice($this->csvRows($csv), 1), 6, 4);
        $this->assertSame('0', $rows['12a']);
        $this->assertSame('Tatap muka, Daring', $rows['14']);
    }

    public function test_summary_omits_empty_and_inapplicable_answers_and_keeps_the_reporting_year(): void
    {
        $this->fixture();
        $report = \App\Models\Monev::firstOrFail();
        foreach ([
            ['has_training', 'Pelatihan diselenggarakan', 'Tidak'],
            ['6', 'Pertanyaan yang kosong', null],
            ['9', 'Tahun pemantauan', null],
        ] as $index => [$code, $label, $answer]) {
            $question = DB::table('mt_pertanyaan')->insertGetId([
                'kuesioner_id' => $report->kuesioner_id, 'kode' => $code, 'blok' => 'identitas',
                'pertanyaan' => $label, 'tipe_pertanyaan' => 'text', 'order_number' => 20 + $index,
            ]);
            if ($answer !== null) {
                DB::table('tr_monev_jawaban')->insert([
                    'monev_id' => $report->id, 'pertanyaan_id' => $question, 'text_value' => $answer,
                ]);
            }
        }
        config(['monev.use_normalized_reports' => true]);
        $this->actingAs($this->secretariat())->get(route('monitoring-evaluasi.detail', $report))->assertOk()
            ->assertSeeText('Belum dikirim')->assertSeeText('Tahun pemantauan')->assertSeeText('2026')
            ->assertSeeText('24. Pertanyaan 24')
            ->assertDontSeeText('Pertanyaan yang kosong')->assertDontSeeText('Tatap muka, Daring');
    }

    public function test_training_card_sums_question_ten_from_latest_submissions_and_respects_year_filter(): void
    {
        $this->fixture();
        $current = \App\Models\Monev::firstOrFail();
        $current->update(['status' => 'revision_draft']);
        $historicalLegacy = MonitoringReport::create([
            'profile_id' => 1, 'jenis_pengajuan_id' => 1,
            'report_year' => 2025, 'status' => 'submitted',
        ]);
        $historical = \App\Models\Monev::create([
            'uuid' => $historicalLegacy->uuid, 'kuesioner_id' => $current->kuesioner_id,
            'profile_id' => 1, 'jenis_pengajuan_id' => 1, 'report_year' => 2025,
            'status' => 'submitted', 'edit_version' => 1, 'legacy_report_id' => $historicalLegacy->id,
        ]);
        $secretariat = $this->secretariat();
        foreach ([[$current->id, 1, 3], [$current->id, 2, 5], [$historical->id, 1, 2]] as [$reportId, $version, $trainingCount]) {
            DB::table('monev_submissions')->insert([
                'monev_id' => $reportId, 'version' => $version,
                'answers' => json_encode(['10' => $trainingCount]),
                'identity_snapshot' => '{}', 'accreditation_snapshot' => '{}',
                'submitted_at' => '2026-11-11 10:00:00', 'submitted_by' => $secretariat->id,
                'submission_channel' => 'account',
            ]);
        }
        config(['monev.use_normalized_reports' => true]);
        $this->actingAs($secretariat);

        $allYears = $this->get(route('monitoring-evaluasi'))->assertOk();
        $this->assertSame(7, $allYears->viewData('stats')['training']);
        $currentYear = $this->get(route('monitoring-evaluasi', ['year' => 2026]))->assertOk();
        $this->assertSame(5, $currentYear->viewData('stats')['training']);
    }

    public function test_imported_2025_report_identifies_excel_as_the_source(): void
    {
        $this->fixture();
        $report = \App\Models\Monev::firstOrFail();
        $report->update(['report_year' => 2025, 'status' => 'submitted']);
        DB::table('monev_submissions')->insert([
            'monev_id' => $report->id, 'version' => 1, 'answers' => '{}',
            'identity_snapshot' => '{}', 'accreditation_snapshot' => '{}',
            'submitted_at' => '2025-12-31 10:00:00', 'submitted_by' => 9999,
            'submission_channel' => 'historical_import',
        ]);
        config(['monev.use_normalized_reports' => true]);

        $this->actingAs($this->secretariat())
            ->get(route('monitoring-evaluasi.detail', $report))
            ->assertOk()->assertSeeText('Import data dari Excel')->assertDontSeeText('Akun tidak ditemukan');
        $this->get(route('monitoring-evaluasi'))
            ->assertOk()->assertSeeText('Tidak Aktif')->assertDontSeeText('Program tidak aktif')
            ->assertDontSeeText('Laporan tahun lain');
        DB::table('monitoring_report_links')->insert([
            'monev_id' => $report->id,
            'token_encrypted' => \Illuminate\Support\Facades\Crypt::encryptString(str_repeat('a', 64)),
            'expires_at' => now()->addDay(),
        ]);
        $this->get(route('monitoring-evaluasi'))->assertOk()->assertSeeText('Aktif')
            ->assertDontSeeText('Program tidak aktif');
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

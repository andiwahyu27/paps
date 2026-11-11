<?php

namespace Tests\Feature;

use App\Models\MonitoringReport;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Tests\TestCase;

class MonevAccessTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']);
        DB::purge('sqlite');
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);
        $this->withoutMiddleware(\App\Http\Middleware\SecurityHeaders::class);

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('password')->nullable();
            $table->unsignedInteger('role');
            $table->unsignedBigInteger('id_profile')->nullable();
            $table->timestamps();
        });
        Schema::create('tb_profile_lembagas', function (Blueprint $table) {
            $table->id();
            $table->string('nama_lembaga');
            $table->string('alamat_lembaga')->nullable();
            $table->string('telepon')->nullable();
            $table->string('email')->nullable();
            $table->string('website')->nullable();
            $table->timestamps();
        });
        Schema::create('monitoring_accreditations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('profile_id');
            $table->unsignedBigInteger('jenis_pengajuan_id');
            $table->date('valid_from');
            $table->date('valid_until');
            $table->string('certificate_number')->nullable();
            $table->string('predicate')->nullable();
            $table->timestamps();
        });
        Schema::create('monitoring_reports', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid');
            $table->unsignedBigInteger('profile_id');
            $table->unsignedBigInteger('jenis_pengajuan_id');
            $table->unsignedSmallInteger('report_year');
            $table->string('status')->default('draft');
            $table->unsignedInteger('edit_version')->default(1);
            $table->unsignedInteger('submitted_version')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
        });
        Schema::create('monitoring_report_versions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('monitoring_report_id');
            $table->unsignedInteger('version');
            $table->json('answers');
            $table->string('submission_channel')->nullable();
            $table->unsignedBigInteger('submitted_by')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->json('identity_snapshot')->nullable();
            $table->json('accreditation_snapshot')->nullable();
            $table->timestamps();
        });
        Schema::create('mt_jenis_pengajuans', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
        });
        Schema::create('monitoring_report_links', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('monitoring_report_id')->nullable();
            $table->unsignedBigInteger('monev_id')->nullable();
            $table->string('token_hash')->unique();
            $table->text('token_encrypted');
            $table->unsignedBigInteger('created_by');
            $table->timestamp('expires_at');
            $table->timestamp('deleted_at')->nullable();
            $table->timestamps();
        });
        Schema::create('monitoring_report_link_audits', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('monitoring_report_id')->nullable();
            $table->unsignedBigInteger('monev_id')->nullable();
            $table->unsignedBigInteger('monitoring_report_link_id');
            $table->unsignedBigInteger('actor_id');
            $table->string('action');
            $table->timestamp('expires_at_before')->nullable();
            $table->timestamp('expires_at_after')->nullable();
            $table->timestamp('created_at');
        });
        $migration = require database_path('migrations/2026_11_10_000001_create_monev_questionnaire_tables.php');
        $migration->up();
        $migration = require database_path('migrations/2026_11_11_000002_create_monev_submissions.php');
        $migration->up();
    }

    public function test_member_of_another_profile_cannot_view_or_edit_report(): void
    {
        $report = $this->report();
        $outsider = $this->member(2);

        $this->actingAs($outsider)->get(route('monitoring-akreditasi.view', $report))->assertForbidden();
        $this->actingAs($outsider)->get(route('monitoring-akreditasi.edit', $report))->assertForbidden();
        $this->actingAs($outsider)->putJson(route('monitoring-akreditasi.update', $report), [
            'jenis_pengajuan_id' => 1,
            'report_year' => 2026,
            'edit_version' => 1,
            'answers' => [24 => 'Tidak ada'],
        ])->assertForbidden();
    }

    public function test_another_member_of_same_profile_can_view_a_draft(): void
    {
        $report = $this->report();
        $this->actingAs($this->member(1))->get(route('monitoring-akreditasi.view', $report))->assertOk();
    }

    public function test_stale_revision_is_rejected_without_overwriting_answers(): void
    {
        $report = $this->report();
        $report->update(['edit_version' => 2]);
        $this->actingAs($this->member(1))->putJson(route('monitoring-akreditasi.update', $report), [
            'jenis_pengajuan_id' => 1,
            'report_year' => 2026,
            'edit_version' => 1,
            'answers' => [24 => 'Jawaban baru'],
        ])->assertStatus(409);

        $this->assertSame(2, $report->fresh()->edit_version);
        $this->assertSame('Jawaban awal', $report->versions()->first()->answers[24]);
    }

    private function report(): MonitoringReport
    {
        DB::table('tb_profile_lembagas')->insert([
            ['id' => 1, 'nama_lembaga' => 'Lembaga Satu'],
            ['id' => 2, 'nama_lembaga' => 'Lembaga Dua'],
        ]);
        $report = MonitoringReport::create([
            'profile_id' => 1,
            'jenis_pengajuan_id' => 1,
            'report_year' => 2026,
            'status' => 'draft',
            'edit_version' => 1,
        ]);
        $report->versions()->create(['version' => 1, 'answers' => [24 => 'Jawaban awal']]);

        return $report;
    }

    public function test_link_submit_records_member_and_reopens_numeric_and_choice_answers(): void
    {
        config(['monev.use_normalized_reports' => true]);
        $report = $this->report();
        DB::table('mt_jenis_pengajuans')->insert(['id' => 1, 'nama' => 'Statistik']);
        DB::table('monitoring_accreditations')->insert([
            'profile_id' => 1, 'jenis_pengajuan_id' => 1, 'valid_from' => '2025-01-01',
            'valid_until' => '2027-01-01', 'certificate_number' => 'UJI', 'predicate' => 'A',
        ]);
        $this->member(1);
        $editor = $this->member(1);
        DB::table('tr_monev')->insert([
            'id' => 42, 'uuid' => $report->uuid, 'legacy_report_id' => $report->id,
            'profile_id' => 1, 'jenis_pengajuan_id' => 1, 'report_year' => 2026,
            'kuesioner_id' => DB::table('mt_kuesioner')->value('id'),
        ]);
        $monev = \App\Models\Monev::findOrFail(42);
        $link = app(\App\Services\MonitoringReportLinkService::class)->issue($report, $editor->id);
        $answers = [
            1 => 'Lembaga Satu', 2 => 'Statistik', 3 => 'Alamat', 4 => '0800',
            5 => 'uji@example.test', 7 => 'UJI', 8 => 'A', 9 => 2026, 'has_training' => 'Ya',
            10 => 1, 11 => 4, 12 => ['internal' => 0, 'external' => 4],
            13 => 'Sesuai', 14 => ['Tatap muka', 'Daring'], 15 => 'Memuaskan', 16 => 'Baik',
            17 => ['Internal'], 18 => ['Widyaiswara'], 19 => 'Lengkap', 20 => 'Sudah dilakukan',
            21 => 'Terpenuhi', 22 => 'Puas', 23 => 'Tidak ada', 24 => 'Nihil', 25 => 'Lanjut', 26 => 'Nihil',
        ];
        $payload = ['jenis_pengajuan_id' => 1, 'report_year' => 2026, 'edit_version' => 1, 'answers' => $answers];
        $this->actingAs($this->member(2))->getJson(route('monitoring-akreditasi.public.form', $link['token']))->assertForbidden();
        $this->actingAs($editor)->putJson(route('monitoring-akreditasi.update', $report), $payload)
            ->assertOk()->assertJsonPath('edit_version', 2);
        $this->putJson(route('monitoring-akreditasi.update', $monev), $payload)->assertStatus(409);
        $pinned = \App\Models\Monev::firstOrFail()->kuesioner_id;
        DB::table('mt_kuesioner')->where('id', $pinned)->update(['is_active' => false]);
        DB::table('mt_kuesioner')->insert([
            'kode' => 'monitoring-akreditasi-pstk', 'nama' => 'Versi baru',
            'jenis' => 'monitoring-akreditasi', 'versi' => 2, 'is_active' => true,
        ]);
        DB::table('mt_pertanyaan')->where('kuesioner_id', $pinned)->where('kode', '13')->update([
            'pertanyaan' => 'Kesesuaian menurut versi laporan',
            'opsi' => json_encode(['Sesuai instrumen lama']),
        ]);
        $payload['edit_version'] = 2;
        $payload['answers'][13] = 'Sesuai instrumen lama';
        $this->postJson(route('monitoring-akreditasi.public.submit', $link['token']), $payload)->assertOk();
        $this->assertDatabaseHas('tr_monev', ['legacy_report_id' => $report->id, 'submitted_by' => $editor->id]);
        $page = $this->get(route('monitoring-akreditasi.public.form', $link['token']))->assertOk();
        $reopened = $page->viewData('answers');
        $page->assertSeeText('Kesesuaian menurut versi laporan');
        $this->assertSame('Sesuai instrumen lama', $reopened[13]);
        $this->assertSame(['Tatap muka', 'Daring'], $reopened[14]);
        $this->assertSame(0, $reopened[12]['internal']);
        $this->assertSame(4, $reopened[12]['external']);
        $this->get(route('monitoring-akreditasi.edit', $monev))->assertOk();
        $this->post(route('monitoring-akreditasi.reset', $monev), ['edit_version' => 3])->assertRedirect();
        $this->assertDatabaseHas('monev_submissions', ['monev_id' => 42, 'submitted_by' => $editor->id, 'version' => 2]);
        $this->assertSame('revision_draft', $monev->fresh()->status);
        $remaining = app(\App\Services\MonitoringReportService::class)->answersForReport($monev->fresh());
        $this->assertSame('Lembaga Satu', $remaining[1]);
        $this->assertArrayNotHasKey(12, $remaining);
        $this->assertSame(1, $report->fresh()->edit_version);
        $this->assertSame(1, $report->versions()->count());
    }

    private function member(int $profileId): User
    {
        return User::create(['name' => 'Anggota '.$profileId, 'role' => 4, 'id_profile' => $profileId]);
    }

    public function test_backfill_preserves_latest_revision_and_last_submission(): void
    {
        $report = $this->report();
        $report->update(['status' => 'revision_draft', 'submitted_version' => 1, 'edit_version' => 3]);
        $report->versions()->where('version', 1)->update(['submitted_at' => now()]);
        $report->versions()->create(['version' => 2, 'answers' => [
            'has_training' => 'Ya', 10 => 1, 12 => ['internal' => 0, 'external' => 4],
            24 => 'Draf terbaru',
        ]]);

        $this->artisan('monev:backfill', ['--dry-run' => true])->assertExitCode(0);
        $this->assertDatabaseCount('tr_monev', 0);
        $this->artisan('monev:backfill')->assertExitCode(0);
        $this->artisan('monev:backfill')->assertExitCode(0);
        $this->assertDatabaseCount('tr_monev', 1);
        $monev = \App\Models\Monev::firstOrFail();
        $this->assertNull($monev->submitted_by);
        $this->assertNotNull($monev->submitted_at);
        $answers = app(\App\Services\MonitoringReportService::class)->answersForReport($report);
        $this->assertSame('Draf terbaru', $answers[24]);
        $this->assertSame(['internal' => 0, 'external' => 4], $answers[12]);

        $monev->answers()->whereHas('question', fn ($query) => $query->where('kode', '12a'))->delete();
        $this->artisan('monev:backfill', ['--repair-participants' => true, '--dry-run' => true])
            ->expectsOutput('1 nilai peserta siap dipulihkan.')->assertExitCode(0);
        $this->assertArrayNotHasKey('internal', app(\App\Services\MonitoringReportService::class)->answersForReport($report)[12]);
        $this->artisan('monev:backfill', ['--repair-participants' => true])->assertExitCode(0);
        $this->artisan('monev:backfill', ['--repair-participants' => true])
            ->expectsOutput('0 nilai peserta dipulihkan.')->assertExitCode(0);
        $restored = app(\App\Services\MonitoringReportService::class)->answersForReport($report)[12];
        $this->assertSame(0, $restored['internal']);
        $this->assertSame(4, $restored['external']);
    }

    public function test_expired_deleted_and_regenerated_links_follow_their_current_status(): void
    {
        $report = $this->report();
        $links = app(\App\Services\MonitoringReportLinkService::class);
        $member = $this->member(1);
        $this->actingAs($member);
        $report->versions()->delete();
        $original = $links->issue($report, $member->id);
        $monev = \App\Models\Monev::where('legacy_report_id', $report->id)->firstOrFail();
        $this->assertDatabaseHas('monitoring_report_links', ['id' => $original['link']->id, 'monev_id' => $monev->id]);
        $originalUrl = route('monitoring-akreditasi.public.form', $original['token']);
        $original['link']->update(['expires_at' => now()->subMinute()]);
        $this->getJson($originalUrl)->assertStatus(410);

        $replacement = $links->issue($report, $member->id);
        $this->getJson($originalUrl)->assertNotFound();
        $replacementUrl = route('monitoring-akreditasi.public.form', $replacement['token']);
        $this->get($replacementUrl)->assertOk();
        $links->delete($replacement['link'], $member->id);
        $this->assertDatabaseHas('monitoring_report_link_audits', [
            'monitoring_report_link_id' => $replacement['link']->id, 'action' => 'deleted', 'monev_id' => $monev->id,
        ]);
        $this->getJson($replacementUrl)->assertNotFound();
        $this->get(route('monitoring-akreditasi.edit', $report))->assertOk();
    }

    public function test_new_normalized_draft_uses_uuid_and_cannot_overwrite_an_existing_draft(): void
    {
        config(['monev.use_normalized_reports' => true]);
        $this->report();
        DB::table('mt_jenis_pengajuans')->insert(['id' => 2, 'nama' => 'Statistik']);
        DB::table('monitoring_accreditations')->insert([
            'profile_id' => 1, 'jenis_pengajuan_id' => 2, 'valid_from' => '2025-01-01',
            'valid_until' => '2027-01-01', 'certificate_number' => 'UJI', 'predicate' => 'A',
        ]);
        $payload = ['jenis_pengajuan_id' => 2, 'report_year' => 2026, 'answers' => [24 => 'Draf baru']];
        $response = $this->actingAs($this->member(1))->postJson(route('monitoring-akreditasi.store'), $payload);
        $report = \App\Models\Monev::firstOrFail();
        $response->assertOk()->assertJsonPath('edit_url', route('monitoring-akreditasi.edit', $report));
        $this->get($response->json('edit_url'))->assertOk()->assertSee('Draf baru');
        $payload['answers'][24] = 'Jawaban pengganti';
        $this->postJson(route('monitoring-akreditasi.store'), $payload)->assertStatus(409);
        $this->assertDatabaseCount('tr_monev', 1);
        $this->assertDatabaseCount('monitoring_reports', 1);
        $this->assertSame('Draf baru', app(\App\Services\MonitoringReportService::class)->answersForReport($report)[24]);
        $links = app(\App\Services\MonitoringReportLinkService::class);
        $issued = $links->issue($report, $this->app['auth']->id());
        $this->assertDatabaseHas('monitoring_report_links', ['id' => $issued['link']->id, 'monev_id' => $report->id, 'monitoring_report_id' => null]);
        $url = route('monitoring-akreditasi.public.form', $issued['token']);
        $this->get($url)->assertOk()->assertSee('Draf baru');
        $issued['link']->update(['expires_at' => now()->subMinute()]);
        $this->getJson($url)->assertStatus(410);
        $links->updateExpiry($issued['link'], $this->app['auth']->id(), now('Asia/Jakarta')->addDay()->format('Y-m-d H:i:s'));
        $this->get($url)->assertOk();
        $replacement = $links->issue($report, $this->app['auth']->id());
        $this->getJson($url)->assertNotFound();
        $links->delete($replacement['link'], $this->app['auth']->id());
        $this->getJson(route('monitoring-akreditasi.public.form', $replacement['token']))->assertNotFound();
    }
}

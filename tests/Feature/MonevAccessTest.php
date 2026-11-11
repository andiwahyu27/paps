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

    private function member(int $profileId): User
    {
        return User::create(['name' => 'Anggota '.$profileId, 'role' => 4, 'id_profile' => $profileId]);
    }
}

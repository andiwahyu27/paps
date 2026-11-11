<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        DB::statement('ALTER TABLE monev_submissions MODIFY submitted_by BIGINT UNSIGNED NULL');
        DB::statement("
            INSERT INTO monev_submissions
                (monev_id, version, answers, identity_snapshot, accreditation_snapshot, submitted_by, submitted_at, submission_channel)
            SELECT target.id, legacy.version, legacy.answers,
                COALESCE(legacy.identity_snapshot, '{}'), COALESCE(legacy.accreditation_snapshot, '{}'),
                legacy.submitted_by, legacy.submitted_at, legacy.submission_channel
            FROM monitoring_report_versions AS legacy
            JOIN tr_monev AS target ON target.legacy_report_id = legacy.monitoring_report_id
            LEFT JOIN monev_submissions AS archived ON archived.monev_id = target.id AND archived.version = legacy.version
            WHERE legacy.submitted_at IS NOT NULL AND archived.id IS NULL
        ");
    }

    public function down(): void
    {
        // Keep archived submissions available when rolling back application code.
    }
};

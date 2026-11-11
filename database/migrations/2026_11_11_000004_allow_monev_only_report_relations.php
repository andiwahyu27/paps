<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    private const TABLES = ['monitoring_report_links', 'monitoring_report_link_audits', 'monitoring_report_import_sources'];

    public function up(): void
    {
        foreach (self::TABLES as $table) {
            DB::statement("ALTER TABLE `{$table}` MODIFY monitoring_report_id BIGINT UNSIGNED NULL");
        }
    }

    public function down(): void
    {
        foreach (self::TABLES as $table) {
            if (DB::table($table)->whereNull('monitoring_report_id')->exists()) {
                throw new RuntimeException('Monev-only relations exist. Restore from the cutover backup to roll back.');
            }
        }
        foreach (self::TABLES as $table) {
            DB::statement("ALTER TABLE `{$table}` MODIFY monitoring_report_id BIGINT UNSIGNED NOT NULL");
        }
    }
};

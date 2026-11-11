<?php

use App\Services\MonevLegacyArchive;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    private const RELATIONS = [
        'monitoring_report_links',
        'monitoring_report_link_audits',
        'monitoring_report_import_sources',
    ];

    public function up(): void
    {
        if (!config('monev.use_normalized_reports')) {
            throw new RuntimeException('Aktifkan mode Monev sebelum cleanup.');
        }
        app(MonevLegacyArchive::class)->verify();

        $schema = DB::connection()->getDatabaseName();
        $unexpectedDependency = DB::table('information_schema.KEY_COLUMN_USAGE')
            ->where('REFERENCED_TABLE_SCHEMA', $schema)
            ->whereIn('REFERENCED_TABLE_NAME', ['monitoring_reports', 'monitoring_report_versions'])
            ->whereNotIn('TABLE_NAME', [...self::RELATIONS, 'monitoring_report_versions'])
            ->exists();
        if ($unexpectedDependency) {
            throw new RuntimeException('Masih ada foreign key lain ke tabel laporan lama.');
        }

        foreach (self::RELATIONS as $table) {
            $invalid = DB::table($table . ' as source')
                ->leftJoin('tr_monev as target', 'target.id', '=', 'source.monev_id')
                ->where(function ($query) {
                    $query->whereNull('target.id')->orWhere(function ($query) {
                        $query->whereNotNull('source.monitoring_report_id')
                            ->whereRaw('NOT (target.legacy_report_id <=> source.monitoring_report_id)');
                    });
                })->exists();
            if ($invalid) {
                throw new RuntimeException("Pemetaan Monev pada {$table} belum lengkap.");
            }
        }

        foreach (self::RELATIONS as $table) {
            DB::statement("ALTER TABLE `{$table}` MODIFY monev_id BIGINT UNSIGNED NOT NULL");
        }

        $grammar = DB::connection()->getQueryGrammar();
        foreach (self::RELATIONS as $table) {
            $legacyConstraints = DB::table('information_schema.KEY_COLUMN_USAGE')
                ->where('TABLE_SCHEMA', $schema)
                ->where('TABLE_NAME', $table)->where('COLUMN_NAME', 'monitoring_report_id')
                ->where('REFERENCED_TABLE_NAME', 'monitoring_reports')->pluck('CONSTRAINT_NAME');
            foreach ($legacyConstraints as $constraint) {
                DB::statement('ALTER TABLE ' . $grammar->wrapTable($table) . ' DROP FOREIGN KEY ' . $grammar->wrap($constraint));
            }
        }

        Schema::dropIfExists('monitoring_report_versions');
        Schema::dropIfExists('monitoring_reports');
    }

    public function down(): void
    {
        throw new RuntimeException('Pulihkan tabel lama dari backup dan arsip sebelum kembali ke skema lama.');
    }
};

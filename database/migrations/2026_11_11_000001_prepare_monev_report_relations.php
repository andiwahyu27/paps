<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    private const RELATIONS = [
        'monitoring_report_links' => 'mrl_monev_fk',
        'monitoring_report_link_audits' => 'mrla_monev_fk',
        'monitoring_report_import_sources' => 'mris_monev_fk',
    ];

    public function up(): void
    {
        foreach (array_keys(self::RELATIONS) as $table) {
            $unmapped = DB::table($table . ' as source')
                ->leftJoin('tr_monev as target', 'target.legacy_report_id', '=', 'source.monitoring_report_id')
                ->whereNull('target.id')->count();

            if ($unmapped > 0) {
                throw new RuntimeException("{$table} has {$unmapped} rows without a Monev report.");
            }
        }

        foreach (self::RELATIONS as $table => $constraint) {
            if (!Schema::hasColumn($table, 'monev_id')) {
                Schema::table($table, fn (Blueprint $blueprint) => $blueprint->foreignId('monev_id')->nullable());
            }

            $hasConstraint = DB::table('information_schema.KEY_COLUMN_USAGE')
                ->where('TABLE_SCHEMA', DB::connection()->getDatabaseName())
                ->where('TABLE_NAME', $table)->where('CONSTRAINT_NAME', $constraint)
                ->where('COLUMN_NAME', 'monev_id')->where('REFERENCED_TABLE_NAME', 'tr_monev')->exists();
            if (!$hasConstraint) {
                Schema::table($table, function (Blueprint $blueprint) use ($constraint) {
                    $blueprint->foreign('monev_id', $constraint)->references('id')->on('tr_monev')->restrictOnDelete();
                });
            }
        }

        DB::transaction(function () {
            foreach (array_keys(self::RELATIONS) as $table) {
                DB::table($table . ' as source')
                    ->join('tr_monev as target', 'target.legacy_report_id', '=', 'source.monitoring_report_id')
                    ->whereNull('source.monev_id')
                    ->update(['source.monev_id' => DB::raw('target.id')]);
            }
        });
    }

    public function down(): void
    {
        foreach (self::RELATIONS as $table => $constraint) {
            Schema::table($table, function (Blueprint $blueprint) use ($constraint) {
                $blueprint->dropForeign($constraint);
                $blueprint->dropColumn('monev_id');
            });
        }
    }
};

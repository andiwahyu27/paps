<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use RuntimeException;

class MonevLegacyArchive
{
    private const TABLES = [
        'monitoring_reports' => 'monev_legacy_reports_archive',
        'monitoring_report_versions' => 'monev_legacy_versions_archive',
    ];

    public function copyAndVerify(): void
    {
        foreach (self::TABLES as $source => $archive) {
            // MySQL LIKE keeps column types and indexes, but omits foreign keys.
            DB::statement("CREATE TABLE IF NOT EXISTS `{$archive}` LIKE `{$source}`");
        }

        DB::transaction(function () {
            foreach (self::TABLES as $source => $archive) {
                $columns = Schema::getColumnListing($source);
                $copy = DB::table($source . ' as source')
                    ->leftJoin($archive . ' as archived', 'archived.id', '=', 'source.id')
                    ->whereNull('archived.id')
                    ->select(array_map(fn ($column) => 'source.' . $column, $columns));
                DB::table($archive)->insertUsing($columns, $copy);
            }
            $this->verify();
        });
    }

    public function verify(): void
    {
        foreach (self::TABLES as $source => $archive) {
            if (!Schema::hasTable($archive)) {
                throw new RuntimeException("Arsip {$archive} belum tersedia.");
            }
            if (!Schema::hasTable($source)) {
                continue;
            }

            $diff = DB::table($source . ' as source')
                ->leftJoin($archive . ' as archived', 'archived.id', '=', 'source.id')
                ->where(function ($query) use ($source) {
                    $query->whereNull('archived.id');
                    $grammar = DB::connection()->getQueryGrammar();
                    foreach (Schema::getColumnListing($source) as $column) {
                        $left = $grammar->wrap('source.' . $column);
                        $right = $grammar->wrap('archived.' . $column);
                        $query->orWhereRaw("NOT ({$left} <=> {$right})");
                    }
                })->exists();

            if ($diff || DB::table($source)->count() !== DB::table($archive)->count()) {
                throw new RuntimeException("Arsip {$archive} berbeda dari tabel asal.");
            }
        }
    }
}

<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class MonitoringPreflight extends Command
{
    protected $signature = 'monitoring:preflight';
    protected $description = 'Memeriksa koneksi database monitoring dan tabel sumber';

    public function handle(): int
    {
        $checks = [
            'gen2' => fn () => DB::connection()->select('select 1'),
            'gen1' => fn () => DB::connection('mysql_gen1')->select('select 1'),
        ];
        foreach ($checks as $name => $check) {
            try { $check(); $this->info("{$name}: OK"); }
            catch (\Throwable $e) { $this->error("{$name}: GAGAL - " . $e->getMessage()); return self::FAILURE; }
        }
        foreach (['tb_profile_lembagas', 'tb_pengajuans', 'tb_pelatihans'] as $table) {
            try { $count = DB::connection('mysql_gen1')->table($table)->count(); $this->line("gen1.{$table}: {$count} baris"); }
            catch (\Throwable $e) { $this->error("gen1.{$table}: GAGAL - " . $e->getMessage()); return self::FAILURE; }
        }
        return self::SUCCESS;
    }
}

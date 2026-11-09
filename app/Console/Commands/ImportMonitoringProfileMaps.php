<?php

namespace App\Console\Commands;

use App\Models\MonitoringProfileMap;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ImportMonitoringProfileMaps extends Command
{
    protected $signature = 'monitoring:import-profile-maps {--file= : CSV source,source_profile_id,profile_id} {--dry-run}';
    protected $description = 'Mengimpor pemetaan profil monitoring';

    public function handle(): int
    {
        return $this->import('monitoring_profile_maps', ['source', 'source_profile_id', 'profile_id']);
    }

    private function import(string $table, array $headers): int
    {
        $file = $this->option('file');
        if (!$file || !is_readable($file)) { $this->error('File CSV tidak dapat dibaca.'); return self::FAILURE; }
        $handle = fopen($file, 'r'); $actual = array_map('trim', fgetcsv($handle));
        if ($actual !== $headers) { $this->error('Header CSV tidak sesuai: ' . implode(',', $headers)); return self::FAILURE; }
        $valid = 0; $errors = 0; $seen = [];
        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) !== count($headers)) { $errors++; $this->warn('Baris CSV salah jumlah kolom.'); continue; }
            $data = array_combine($headers, array_map('trim', $row));
            $key = $data['source'] . ':' . $data['source_profile_id'];
            $sourceConnection = $data['source'] === 'gen1' ? 'mysql_gen1' : ($data['source'] === 'gen2' ? null : false);
            $validRow = $sourceConnection !== false && ctype_digit($data['source_profile_id']) && ctype_digit($data['profile_id'])
                && DB::table('tb_profile_lembagas')->where('id', $data['profile_id'])->exists();
            if ($validRow && $sourceConnection) {
                try { $validRow = DB::connection($sourceConnection)->table('tb_profile_lembagas')->where('id', $data['source_profile_id'])->exists(); }
                catch (\Throwable $e) { $this->error('Koneksi gen1 gagal saat memvalidasi pemetaan.'); fclose($handle); return self::FAILURE; }
            } elseif ($validRow) {
                $validRow = DB::table('tb_profile_lembagas')->where('id', $data['source_profile_id'])->exists();
            }
            $existing = $validRow ? MonitoringProfileMap::where('source', $data['source'])->where('source_profile_id', $data['source_profile_id'])->first() : null;
            if (!$validRow || (isset($seen[$key]) && $seen[$key] !== $data['profile_id']) || ($existing && $existing->profile_id != $data['profile_id'])) {
                $errors++; $this->warn('Baris pemetaan tidak valid, ambigu, atau bertentangan dengan pemetaan tersimpan.'); continue;
            }
            $seen[$key] = $data['profile_id'];
            $valid++;
            if (!$this->option('dry-run')) {
                MonitoringProfileMap::firstOrCreate(['source' => $data['source'], 'source_profile_id' => $data['source_profile_id']], ['profile_id' => $data['profile_id']]);
            }
        }
        fclose($handle); $this->info(($this->option('dry-run') ? 'Dry run: ' : '') . "{$valid} valid, {$errors} gagal.");
        return $errors ? self::FAILURE : self::SUCCESS;
    }
}

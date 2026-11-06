<?php

namespace App\Console\Commands;

use App\Models\MonitoringProfileMap;
use Illuminate\Console\Command;

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
        $valid = 0; $errors = 0;
        while (($row = fgetcsv($handle)) !== false) {
            $data = array_combine($headers, array_map('trim', $row));
            if (!$data['source'] || !ctype_digit($data['source_profile_id']) || !ctype_digit($data['profile_id'])) { $errors++; $this->warn('Baris tidak valid: ' . implode(',', $row)); continue; }
            $valid++;
            if (!$this->option('dry-run')) MonitoringProfileMap::updateOrCreate(['source' => $data['source'], 'source_profile_id' => $data['source_profile_id']], ['profile_id' => $data['profile_id']]);
        }
        fclose($handle); $this->info(($this->option('dry-run') ? 'Dry run: ' : '') . "{$valid} valid, {$errors} gagal.");
        return $errors ? self::FAILURE : self::SUCCESS;
    }
}

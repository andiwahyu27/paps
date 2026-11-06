<?php

namespace App\Console\Commands;

use App\Models\MonitoringAccreditation;
use Illuminate\Console\Command;

class ImportMonitoringAccreditations extends Command
{
    protected $signature = 'monitoring:import-accreditations {--file= : CSV data resmi akreditasi} {--dry-run}';
    protected $description = 'Mengimpor register akreditasi monitoring';

    public function handle(): int
    {
        $headers = ['profile_id', 'jenis_pengajuan_id', 'certificate_number', 'predicate', 'valid_from', 'valid_until'];
        $file = $this->option('file');
        if (!$file || !is_readable($file)) { $this->error('File CSV tidak dapat dibaca.'); return self::FAILURE; }
        $handle = fopen($file, 'r'); $actual = array_map('trim', fgetcsv($handle));
        if ($actual !== $headers) { $this->error('Header CSV tidak sesuai: ' . implode(',', $headers)); return self::FAILURE; }
        $valid = 0; $errors = 0;
        while (($row = fgetcsv($handle)) !== false) {
            $data = array_combine($headers, array_map('trim', $row));
            $validRow = ctype_digit($data['profile_id']) && ctype_digit($data['jenis_pengajuan_id']) && in_array($data['predicate'], ['A', 'B', 'C'], true) && $data['certificate_number'] && strtotime($data['valid_from']) && strtotime($data['valid_until']) && $data['valid_from'] <= $data['valid_until'];
            if (!$validRow) { $errors++; $this->warn('Baris tidak valid: ' . implode(',', $row)); continue; }
            $valid++;
            if (!$this->option('dry-run')) MonitoringAccreditation::updateOrCreate(['profile_id' => $data['profile_id'], 'jenis_pengajuan_id' => $data['jenis_pengajuan_id'], 'certificate_number' => $data['certificate_number']], $data);
        }
        fclose($handle); $this->info(($this->option('dry-run') ? 'Dry run: ' : '') . "{$valid} valid, {$errors} gagal.");
        return $errors ? self::FAILURE : self::SUCCESS;
    }
}

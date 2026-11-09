<?php

namespace App\Console\Commands;

use App\Models\MonitoringAccreditation;
use App\Models\MonitoringProfileMap;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ImportMonitoringAccreditations extends Command
{
    protected $signature = 'monitoring:import-accreditations {--file= : CSV data resmi akreditasi} {--dry-run}';
    protected $description = 'Mengimpor register akreditasi monitoring';

    public function handle(): int
    {
        $headers = ['source', 'source_profile_id', 'jenis_pengajuan_id', 'certificate_number', 'predicate', 'valid_from', 'valid_until'];
        $file = $this->option('file');
        if (!$file || !is_readable($file)) { $this->error('File CSV tidak dapat dibaca.'); return self::FAILURE; }
        $handle = fopen($file, 'r'); $actual = array_map('trim', fgetcsv($handle));
        if ($actual !== $headers) { $this->error('Header CSV tidak sesuai: ' . implode(',', $headers)); return self::FAILURE; }
        $valid = 0; $errors = 0; $seen = [];
        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) !== count($headers)) { $errors++; $this->warn('Baris CSV salah jumlah kolom.'); continue; }
            $data = array_combine($headers, array_map('trim', $row));
            $map = $data['source'] === 'gen2'
                ? (object) ['profile_id' => $data['source_profile_id']]
                : MonitoringProfileMap::where('source', $data['source'])->where('source_profile_id', $data['source_profile_id'])->first();
            $reasons = [];
            if (!in_array($data['source'], ['gen1', 'gen2'], true)) $reasons[] = 'source harus gen1 atau gen2';
            if (!ctype_digit($data['source_profile_id'])) $reasons[] = 'source_profile_id harus angka';
            if ($data['source'] === 'gen2' && ctype_digit($data['source_profile_id']) && !DB::table('tb_profile_lembagas')->where('id', $data['source_profile_id'])->exists()) $reasons[] = 'profil gen2 tidak ditemukan';
            if (!ctype_digit($data['jenis_pengajuan_id']) || !DB::table('mt_jenis_pengajuans')->where('id', $data['jenis_pengajuan_id'])->exists()) $reasons[] = 'jenis_pengajuan_id tidak ditemukan di gen2';
            if ($data['predicate'] !== '' && !in_array($data['predicate'], ['A', 'B', 'C'], true)) $reasons[] = 'predicate harus kosong atau A, B, atau C';
            if (!$data['certificate_number']) $reasons[] = 'nomor sertifikat kosong';
            if (!$this->validDate($data['valid_from'])) $reasons[] = 'valid_from harus berformat YYYY-MM-DD';
            if (!$this->validDate($data['valid_until'])) $reasons[] = 'valid_until harus berformat YYYY-MM-DD';
            if ($this->validDate($data['valid_from']) && $this->validDate($data['valid_until']) && $data['valid_from'] > $data['valid_until']) $reasons[] = 'rentang masa berlaku terbalik';
            if (!$map) $reasons[] = 'pemetaan profil gen1 ke gen2 belum ada; dry-run mapping saja belum menyimpan pemetaan';
            $validRow = !$reasons;
            $key = ($map->profile_id ?? 'unmapped') . ':' . $data['jenis_pengajuan_id'] . ':' . $data['certificate_number'];
            $attributes = ['profile_id' => $map->profile_id ?? 0, 'jenis_pengajuan_id' => $data['jenis_pengajuan_id'], 'certificate_number' => $data['certificate_number']];
            $values = ['predicate' => $data['predicate'], 'valid_from' => $data['valid_from'], 'valid_until' => $data['valid_until'], 'source' => $data['source']];
            $existing = $validRow ? MonitoringAccreditation::where($attributes)->first() : null;
            $conflict = $existing && ($existing->predicate !== $values['predicate'] || $existing->valid_from->format('Y-m-d') !== $values['valid_from'] || $existing->valid_until->format('Y-m-d') !== $values['valid_until']);
            if (isset($seen[$key])) $reasons[] = 'duplikat pada CSV';
            if ($conflict) $reasons[] = 'berbeda dengan register akreditasi yang sudah tersimpan';
            if ($reasons) { $errors++; $this->warn('Baris ' . ($valid + $errors + 1) . ': ' . implode('; ', array_unique($reasons)) . '.'); continue; }
            $seen[$key] = true;
            $valid++;
            if (!$this->option('dry-run')) {
                MonitoringAccreditation::firstOrCreate($attributes, $values);
            }
        }
        fclose($handle); $this->info(($this->option('dry-run') ? 'Dry run: ' : '') . "{$valid} valid, {$errors} gagal.");
        return $errors ? self::FAILURE : self::SUCCESS;
    }

    private function validDate(string $value): bool
    {
        $date = \DateTime::createFromFormat('!Y-m-d', $value);
        return $date && $date->format('Y-m-d') === $value;
    }
}

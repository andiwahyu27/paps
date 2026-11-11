<?php

namespace App\Console\Commands;

use App\Models\MonitoringAccreditation;
use App\Models\Monev;
use App\Models\MonitoringReport;
use App\Models\MonitoringReportImportSource;
use App\Models\Profile;
use App\Services\MonitoringReportService;
use Carbon\Carbon;
use DateTime;
use DateTimeZone;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Throwable;

class ImportMonitoringReportHistory extends Command
{
    protected $signature = 'monitoring:import-history
        {--file= : File CSV hasil ekspor respons monitoring}
        {--source-ref= : Identitas spreadsheet dan gid, format sheet-id:gid}
        {--dry-run : Validasi tanpa menyimpan data}
        {--timestamp-format=d/m/Y G:i:s : Format timestamp PHP}
        {--source-timezone=Asia/Jakarta : Zona waktu timestamp sumber}';

    protected $description = 'Mengimpor histori monitoring PSTK dari CSV sebagai versi laporan terkirim';

    private const HEADERS = [
        'Timestamp',
        '1. Nama Lembaga Pelatihan',
        '2. Program Pelatihan yang Terakreditasi:',
        '3. Alamat',
        '4. Nomor Telepon',
        '5. E-mail',
        '6. Website',
        '7. Nomor Sertifikat Akreditasi',
        '8. Status Akreditasi',
        '9. Tahun Pemantauan',
        'Apakah lembaga menyelenggarakan pelatihan setelah terakreditasi?',
        '10. Jumlah pelatihan yang dilaksanakan pada tahun ini',
        '11. Jumlah peserta pelatihan pada tahun ini',
        '12a. Jumlah peserta pelatihan yang berasal dari internal:',
        '12b. Jumlah peserta pelatihan yang berasal dari eksternal',
        '13. Kesesuaian pelaksanaan dengan program yang terakreditasi',
        '14. Metode pelatihan yang digunakan',
        '15. Penilaian kelulusan peserta pelatihan',
        '16. Penilaian penyelenggaraan pelatihan secara umum',
        '17. Tenaga pengajar yang digunakan',
        '18. Jabatan tenaga pengajar:',
        '19. Ketersediaan dokumen mutu pelatihan (SOP, panduan, silabus, RPP)',
        '20. Pemutakhiran dokumen akreditasi (kurikulum, silabus, data SDM, dsb)',
        '21. Kepatuhan terhadap persyaratan akreditasi (tenaga pengajar, sarana, kurikulum, dll)',
        '22. Evaluasi peserta terhadap pelatihan:',
        '23. Tindak lanjut hasil pelatihan (monitoring alumni, penerapan hasil belajar, dsb)',
        '24. Kendala atau permasalahan dalam penyelenggaraan pelatihan',
        '25. Rencana pelatihan tahun depan',
        '26. Kebutuhan dukungan atau pembinaan dari instansi pembina/BPS',
    ];

    /** Explicit labels from the 2025 instrument mapped to Gen2's canonical master labels. */
    private const INSTITUTION_ALIASES = [
        'BADAN PENGEMBANGAN SUMBER DAYA MANUSIA DAERAH PROVINSI JAWA TENGAH' => 'BPSDMD PROVINSI JAWA TENGAH',
        'PUSDIKLAT TEKFUNGHAN BPSDM HAN KEMHAN' => 'PUSDIKLAT TEKFUNGHAN BADIKLAT KEMHAN RI',
        'BADAN PENGEMBANGAN SUMBER DAYA MANUSIA DAERAH PROVINSI KALIMANTAN SELATAN' => 'BPSDMD PROVINSI KALIMANTAN SELATAN',
    ];

    private const PROGRAM_ALIASES = [
        'PROGRAM PELATIHAN TEKNIS DI BIDANG SISTEM TEKNOLOGI BERBASIS KOMPUTER' => 'SISTEM TEKNOLOGI BERBASIS KOMPUTER',
    ];

    public function handle(MonitoringReportService $monitoring): int
    {
        $file = $this->option('file');
        $sourceRef = trim((string) $this->option('source-ref'));

        if (!$file || !is_readable($file)) {
            $this->error('File CSV tidak dapat dibaca.');
            return self::FAILURE;
        }

        if ($sourceRef === '' || strlen($sourceRef) > 120) {
            $this->error('source-ref wajib diisi dan maksimal 120 karakter.');
            return self::FAILURE;
        }

        try {
            if (!Schema::hasTable('monitoring_report_import_sources')) {
                $this->error('Tabel provenance belum tersedia. Jalankan migration monitoring_report_import_sources terlebih dahulu.');
                return self::FAILURE;
            }

            $parsed = $this->readCsv($file);
            $prepared = $this->prepareRows(
                $parsed['rows'],
                $sourceRef,
                (string) $this->option('timestamp-format'),
                (string) $this->option('source-timezone')
            );
        } catch (Throwable $exception) {
            $this->error('Preflight gagal karena struktur file atau database tidak dapat diperiksa. Tidak ada data yang ditulis.');
            return self::FAILURE;
        }

        $blocked = 0;
        foreach ($prepared['blocked'] as $rowError) {
            $blocked++;
            $this->warn('Baris ' . $rowError['row'] . ': ' . implode('; ', $rowError['reasons']) . '.');
        }

        $validCount = count($prepared['ready']);
        $this->info(($this->option('dry-run') ? 'Dry run: ' : '') . $validCount . ' siap diimpor, '
            . $prepared['already'] . ' sudah diimpor, ' . $prepared['duplicates'] . ' duplikat identik dilewati, '
            . $parsed['blank'] . ' baris kosong dilewati, ' . $blocked . ' terblokir.');

        if ($blocked > 0) {
            $this->error('Tidak ada baris yang ditulis karena preflight menemukan masalah.');
            return self::FAILURE;
        }

        if ($this->option('dry-run') || $validCount === 0) {
            return self::SUCCESS;
        }

        try {
            DB::transaction(function () use ($prepared, $monitoring) {
                foreach ($prepared['ready'] as $item) {
                    $source = [
                        'source_ref' => $item['source_ref'],
                        'source_key' => $item['source_key'],
                        'content_hash' => $item['content_hash'],
                        'source_row' => $item['source_row'],
                        'source_timestamp' => $item['source_timestamp'],
                        'raw_responses' => $item['raw_responses'],
                        'imported_at' => now(),
                    ];
                    if (config('monev.use_normalized_reports')) {
                        $monev = $monitoring->importMonev(
                            $item['profile'], $item['accreditation']->jenis_pengajuan_id, $item['year'], $item['answers'],
                            [
                                'identity_snapshot' => $item['identity_snapshot'],
                                'accreditation_snapshot' => $item['accreditation_snapshot'],
                                'submitted_by' => null,
                                'submitted_at' => $item['submitted_at'],
                            ]
                        );
                        MonitoringReportImportSource::create($source + ['monev_id' => $monev->id]);
                        continue;
                    }
                    $report = $item['report'];
                    if (!$report) {
                        $report = new MonitoringReport();
                        $report->profile_id = $item['profile']->id;
                        $report->jenis_pengajuan_id = $item['accreditation']->jenis_pengajuan_id;
                        $report->report_year = $item['year'];
                    }

                    $report->status = 'submitted';
                    $report->edit_version = 2;
                    $report->submitted_version = 1;
                    $report->updated_by = null;
                    $report->save();

                    $submission = $report->versions()->create([
                        'version' => 1,
                        'answers' => $item['answers'],
                        'identity_snapshot' => $item['identity_snapshot'],
                        'accreditation_snapshot' => $item['accreditation_snapshot'],
                        'submitted_by' => null,
                        'submitted_at' => $item['submitted_at'],
                        'submission_channel' => 'historical_import',
                    ]);

                    $monev = $monitoring->syncNormalized($report, $item['answers'], $submission);
                    if (Schema::hasColumn('monitoring_report_import_sources', 'monev_id')) {
                        if (!$monev) {
                            throw new \RuntimeException('Laporan histori belum dipetakan ke Monev.');
                        }
                        $source['monev_id'] = $monev->id;
                    }
                    $report->importSources()->create($source);
                }
            });
        } catch (Throwable $exception) {
            $this->error('Transaksi impor gagal dan dibatalkan. Periksa migration dan koneksi database Gen2.');
            return self::FAILURE;
        }

        $this->info('Impor histori berhasil disimpan ke database Gen2.');
        return self::SUCCESS;
    }

    private function readCsv(string $file): array
    {
        $handle = fopen($file, 'r');
        if ($handle === false) {
            throw new \RuntimeException('CSV tidak dapat dibuka.');
        }

        $header = fgetcsv($handle);
        if (!$header || count($header) !== count(self::HEADERS)) {
            fclose($handle);
            throw new \RuntimeException('Header CSV tidak sesuai.');
        }

        $header[0] = preg_replace('/^\xEF\xBB\xBF/', '', (string) $header[0]);
        if ($header !== self::HEADERS) {
            fclose($handle);
            throw new \RuntimeException('Header CSV tidak sesuai.');
        }

        $rows = [];
        $blank = 0;
        $line = 1;
        while (($cells = fgetcsv($handle)) !== false) {
            $line++;
            if ($cells === [null] || $cells === []) {
                $cells = array_fill(0, count(self::HEADERS), '');
            }

            if (count($cells) !== count(self::HEADERS)) {
                $rows[] = ['row' => $line, 'cells' => $cells, 'invalid' => 'jumlah kolom harus 29'];
                continue;
            }

            $cells = array_map(function ($cell) {
                return $cell === null ? '' : (string) $cell;
            }, $cells);

            foreach ($cells as $cell) {
                if (preg_match('//u', $cell) !== 1) {
                    $rows[] = ['row' => $line, 'cells' => $cells, 'invalid' => 'encoding bukan UTF-8'];
                    continue 2;
                }
            }

            $hasResponse = false;
            foreach (array_slice($cells, 1) as $cell) {
                if (trim($cell) !== '') {
                    $hasResponse = true;
                    break;
                }
            }

            if (!$hasResponse) {
                $blank++;
                continue;
            }

            $rows[] = ['row' => $line, 'cells' => $cells, 'invalid' => null];
        }

        fclose($handle);
        return ['rows' => $rows, 'blank' => $blank];
    }

    private function prepareRows(array $rows, string $sourceRef, string $timestampFormat, string $sourceTimezone): array
    {
        $normalized = config('monev.use_normalized_reports');
        $accreditations = MonitoringAccreditation::with(['profile', 'jenisPengajuan'])->get();
        $ready = [];
        $blocked = [];
        $already = 0;
        $duplicates = 0;
        $seenSourceKeys = [];
        $seenReportKeys = [];

        foreach ($rows as $row) {
            $rowNumber = $row['row'];
            if ($row['invalid']) {
                $blocked[] = ['row' => $rowNumber, 'reasons' => [$row['invalid']]];
                continue;
            }

            $cells = $row['cells'];
            $reasons = [];
            $rawTimestamp = trim($cells[0]);
            $institution = $cells[1];
            $program = $cells[2];
            $certificate = $cells[7];
            $predicate = $this->predicateValue($cells[8]);
            $yearRaw = trim($cells[9]);

            if ($rawTimestamp === '' || !$this->parseTimestamp($rawTimestamp, $timestampFormat, $sourceTimezone)) {
                $reasons[] = 'timestamp kosong atau tidak sesuai format';
            }
            if ($this->normalizeText($institution) === '') $reasons[] = 'nama lembaga kosong';
            if ($this->normalizeText($program) === '') $reasons[] = 'program kosong';
            if ($this->normalizeCertificate($certificate) === '') $reasons[] = 'nomor sertifikat kosong';
            if ($predicate === null) $reasons[] = 'predikat harus A/B/C atau Terakreditasi A/B/C';
            if (!ctype_digit($yearRaw) || (int) $yearRaw < 2020 || (int) $yearRaw > now()->year) {
                $reasons[] = 'tahun pemantauan tidak valid';
            }

            if ($reasons) {
                $blocked[] = ['row' => $rowNumber, 'reasons' => $reasons];
                continue;
            }

            $year = (int) $yearRaw;
            $normalizedCertificate = $this->normalizeCertificate($certificate);
            $normalizedInstitution = $this->normalizeText($institution);
            $canonicalSourceInstitution = $this->canonicalInstitution($institution);
            $matches = $accreditations->filter(function ($accreditation) use ($normalizedCertificate) {
                return $this->normalizeCertificate($accreditation->certificate_number) === $normalizedCertificate;
            })->values();

            if ($matches->count() !== 1) {
                $blocked[] = ['row' => $rowNumber, 'reasons' => [$matches->isEmpty()
                    ? 'sertifikat tidak ditemukan di register monitoring'
                    : 'nomor sertifikat cocok dengan lebih dari satu register']];
                continue;
            }

            $accreditation = $matches->first();
            $profile = $accreditation->profile;
            $programModel = $accreditation->jenisPengajuan;
            if (!$profile || !$programModel) {
                $reasons[] = 'profil atau program pada register tidak ditemukan';
            } else {
                if ($this->canonicalInstitution($profile->nama_lembaga) !== $canonicalSourceInstitution) {
                    $reasons[] = 'nama lembaga berbeda (CSV: "' . trim($institution) . '"; Gen2: "' . trim((string) $profile->nama_lembaga) . '")';
                }
                if ($this->canonicalProgram($programModel->nama) !== $this->canonicalProgram($program)) {
                    $reasons[] = 'program berbeda (CSV: "' . trim($program) . '"; Gen2: "' . trim((string) $programModel->nama) . '")';
                }
            }
            if ($accreditation->predicate !== $predicate) {
                $reasons[] = 'predikat berbeda (CSV: "' . $predicate . '"; register Gen2: "' . (string) $accreditation->predicate . '")';
            }
            if (!$accreditation->valid_from || !$accreditation->valid_until
                || $accreditation->valid_from->format('Y-m-d') > $year . '-12-31'
                || $accreditation->valid_until->format('Y-m-d') < $year . '-01-01') {
                $reasons[] = 'masa akreditasi tidak mencakup tahun pemantauan';
            }

            if ($reasons) {
                $blocked[] = ['row' => $rowNumber, 'reasons' => $reasons];
                continue;
            }

            $timestamp = $this->parseTimestamp($rawTimestamp, $timestampFormat, $sourceTimezone);
            $sourceKey = $this->hashJson([$sourceRef, $rawTimestamp, $normalizedCertificate, $normalizedInstitution]);
            $contentHash = $this->hashJson(array_combine(self::HEADERS, $cells));
            $reportKey = $profile->id . ':' . $accreditation->jenis_pengajuan_id . ':' . $year;

            if (isset($seenSourceKeys[$sourceKey])) {
                if ($seenSourceKeys[$sourceKey] === $contentHash) {
                    $duplicates++;
                } else {
                    $blocked[] = ['row' => $rowNumber, 'reasons' => ['source key duplikat dengan isi berbeda']];
                }
                continue;
            }
            $seenSourceKeys[$sourceKey] = $contentHash;

            if (isset($seenReportKeys[$reportKey])) {
                $blocked[] = ['row' => $rowNumber, 'reasons' => ['lebih dari satu respons mengarah ke lembaga-program-tahun yang sama']];
                continue;
            }
            $seenReportKeys[$reportKey] = true;

            $sourceRelation = $normalized ? 'monev' : 'report';
            $sourceImport = MonitoringReportImportSource::with($sourceRelation)->where('source_key', $sourceKey)->first();
            if ($sourceImport) {
                $sourceReport = $sourceImport->{$sourceRelation};
                $sameReport = $sourceReport
                    && (int) $sourceReport->profile_id === (int) $profile->id
                    && (int) $sourceReport->jenis_pengajuan_id === (int) $accreditation->jenis_pengajuan_id
                    && (int) $sourceReport->report_year === $year;
                if ($sourceImport->content_hash === $contentHash && $sameReport) {
                    $already++;
                } else {
                    $blocked[] = ['row' => $rowNumber, 'reasons' => ['source key sudah tersimpan dengan isi atau laporan berbeda']];
                }
                continue;
            }

            $reports = $normalized ? Monev::withCount(['answers', 'submissions']) : MonitoringReport::withCount('versions');
            $report = $reports
                ->where('profile_id', $profile->id)
                ->where('jenis_pengajuan_id', $accreditation->jenis_pengajuan_id)
                ->where('report_year', $year)
                ->first();
            $hasContent = $normalized
                ? $report && ($report->answers_count > 0 || $report->submissions_count > 0 || $report->submitted_at || $report->edit_version !== 1)
                : $report && ($report->versions_count > 0 || $report->submitted_version !== null);
            if ($hasContent) {
                $blocked[] = ['row' => $rowNumber, 'reasons' => ['laporan sudah memiliki versi dan tidak berasal dari import ini']];
                continue;
            }

            $answers = $this->mapAnswers($cells, $rowNumber, $reasons);
            if ($reasons) {
                $blocked[] = ['row' => $rowNumber, 'reasons' => $reasons];
                continue;
            }

            $ready[] = [
                'source_ref' => $sourceRef,
                'source_key' => $sourceKey,
                'content_hash' => $contentHash,
                'source_row' => $rowNumber,
                'source_timestamp' => $rawTimestamp,
                'raw_responses' => $this->rawResponses($cells),
                'submitted_at' => $timestamp,
                'profile' => $profile,
                'accreditation' => $accreditation,
                'year' => $year,
                'report' => $report,
                'answers' => $answers,
                'identity_snapshot' => $this->identitySnapshot($profile, $programModel),
                'accreditation_snapshot' => $this->accreditationSnapshot($accreditation, $programModel),
            ];
        }

        return compact('ready', 'blocked', 'already', 'duplicates');
    }

    private function mapAnswers(array $cells, int $rowNumber, array &$reasons): array
    {
        $answers = [];
        $noTraining = strtolower(trim($cells[10])) === 'tidak';
        $trainingResponse = strtolower(trim($cells[10]));
        if (!in_array($trainingResponse, ['ya', 'tidak'], true)) {
            $reasons[] = 'jawaban penyelenggaraan pelatihan harus Ya atau Tidak';
            return $answers;
        }

        $countCells = [10 => 11, 11 => 12, '12.internal' => 13, '12.external' => 14];
        $counts = [];
        foreach ($countCells as $question => $cellIndex) {
            $raw = trim($cells[$cellIndex]);
            if ($raw === '-') {
                $counts[$question] = 0;
                continue;
            }
            if ($noTraining && $raw === '') {
                $counts[$question] = 0;
                continue;
            }
            if (!ctype_digit($raw)) {
                $reasons[] = 'jumlah pada pertanyaan 10–12 harus berupa bilangan bulat nonnegatif';
                continue;
            }
            $counts[$question] = (int) $raw;
            if ($noTraining && $counts[$question] !== 0) {
                $reasons[] = 'jumlah harus nol ketika sumber menjawab Tidak menyelenggarakan pelatihan';
            }
        }

        if (count($counts) !== 4) return $answers;
        if ($counts[10] !== 0 || !$noTraining) {
            if ($counts['12.internal'] + $counts['12.external'] !== $counts[11]) {
                $reasons[] = 'jumlah peserta internal dan eksternal tidak sama dengan jumlah peserta';
            }
        }

        $answers[10] = $counts[10];
        $answers[11] = $counts[11];
        $answers[12] = ['internal' => $counts['12.internal'], 'external' => $counts['12.external']];
        $answers[13] = $cells[15];
        $answers[14] = $this->splitChoices($cells[16]);
        $answers[15] = $cells[17];
        $answers[16] = $cells[18];
        $answers[17] = $this->splitChoices($cells[19]);
        $answers[18] = $this->splitChoices($cells[20]);
        $answers[19] = $cells[21];
        $answers[20] = $cells[22];
        $answers[21] = $cells[23];
        $answers[22] = $cells[24];
        $answers[23] = $cells[25];
        $answers[24] = $cells[26];
        $answers[25] = $cells[27];
        $answers[26] = $cells[28];

        return $answers;
    }

    private function rawResponses(array $cells): array
    {
        $raw = ['after_accreditation_training' => $cells[10]];
        foreach (range(10, 26) as $question) {
            if ($question === 12) {
                $raw['12a_internal'] = $cells[13];
                $raw['12b_external'] = $cells[14];
                continue;
            }
            $raw[(string) $question] = $cells[$question + 2];
        }
        return $raw;
    }

    private function identitySnapshot(Profile $profile, $program): array
    {
        return array_merge($profile->only(['id', 'nama_lembaga', 'alamat_lembaga', 'telepon', 'email', 'website']), [
            'jenis_pengajuan_id' => $program->id,
            'nama_program' => $program->nama,
        ]);
    }

    private function accreditationSnapshot(MonitoringAccreditation $accreditation, $program): array
    {
        return [
            'id' => $accreditation->id,
            'jenis_pengajuan_id' => $accreditation->jenis_pengajuan_id,
            'nama_program' => $program->nama,
            'certificate_number' => $accreditation->certificate_number,
            'predicate' => $accreditation->predicate,
            'valid_from' => optional($accreditation->valid_from)->format('Y-m-d'),
            'valid_until' => optional($accreditation->valid_until)->format('Y-m-d'),
            'source' => $accreditation->source,
            'source_application_id' => $accreditation->source_application_id,
        ];
    }

    private function parseTimestamp(string $value, string $format, string $timezone): ?Carbon
    {
        try {
            $date = DateTime::createFromFormat('!' . $format, $value, new DateTimeZone($timezone));
        } catch (Throwable $exception) {
            return null;
        }

        $errors = DateTime::getLastErrors();
        if (!$date || ($errors && ($errors['warning_count'] > 0 || $errors['error_count'] > 0))) {
            return null;
        }

        return Carbon::instance($date)->setTimezone(config('app.timezone'));
    }

    private function predicateValue(string $value): ?string
    {
        $value = trim($value);
        if (in_array($value, ['A', 'B', 'C'], true)) return $value;
        if (preg_match('/^Terakreditasi ([ABC])$/u', $value, $matches)) return $matches[1];
        return null;
    }

    private function normalizeText(?string $value): string
    {
        $value = (string) $value;
        $value = preg_replace('/^\s+|\s+$/u', '', $value);
        $value = preg_replace('/\s+/u', ' ', $value);
        return mb_strtoupper($value, 'UTF-8');
    }

    private function normalizeCertificate(?string $value): string
    {
        $value = (string) $value;
        $value = preg_replace('/\s+/u', '', $value);
        return mb_strtoupper($value, 'UTF-8');
    }

    private function canonicalInstitution(?string $value): string
    {
        $normalized = $this->normalizeText($value);
        return self::INSTITUTION_ALIASES[$normalized] ?? $normalized;
    }

    private function canonicalProgram(?string $value): string
    {
        $normalized = $this->normalizeText($value);
        return self::PROGRAM_ALIASES[$normalized] ?? $normalized;
    }

    private function splitChoices(string $value): array
    {
        if (trim($value) === '') return [];
        return array_values(array_filter(array_map('trim', explode(',', $value)), function ($item) {
            return $item !== '';
        }));
    }

    private function hashJson(array $data): string
    {
        return hash('sha256', json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR));
    }
}

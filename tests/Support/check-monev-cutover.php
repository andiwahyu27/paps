<?php

use App\Models\MonitoringAccreditation;
use App\Console\Commands\ImportMonitoringReportHistory;
use App\Models\MonitoringReportLink;
use App\Models\Monev;
use App\Models\User;
use App\Services\MonitoringReportLinkService;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

require dirname(__DIR__, 2) . '/vendor/autoload.php';
$app = require dirname(__DIR__, 2) . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$database = $argv[1] ?? '';
$config = $app['config'];
$connection = $config->get('database.default');
$connectionConfig = $config->get('database.connections.' . $connection);

if (!in_array($database, ['paps_restore_monev_dryst', 'paps'], true)
    || $connectionConfig['driver'] !== 'mysql'
    || !in_array($connectionConfig['host'], ['127.0.0.1', 'localhost'], true)) {
    throw new RuntimeException('Jalankan pemeriksaan hanya pada MySQL dev lokal atau skema restore.');
}

$config->set('database.connections.' . $connection . '.database', $database);
$config->set('session.driver', 'array');
$config->set('cache.default', 'array');
$app->instance('env', 'testing');
DB::purge($connection);
if (DB::selectOne('SELECT DATABASE() AS name')->name !== $database) {
    throw new RuntimeException('Nama skema MySQL tidak sesuai.');
}
if ($database === 'paps_restore_monev_dryst') {
    $config->set('monev.use_normalized_reports', true);
    foreach (['monitoring_reports', 'monitoring_report_versions'] as $table) {
        DB::statement("CREATE TEMPORARY TABLE `{$table}` (legacy_access_disabled INT)");
    }
} elseif (!$config->get('monev.use_normalized_reports')) {
    throw new RuntimeException('Mode Monev belum aktif pada konfigurasi dev.');
}

$request = static function (User $user, string $method, string $path, array $payload = [], array $expected = [200]) use ($app) {
    Auth::guard('web')->setUser($user);
    $request = Request::create($path, $method, [], [], [], [
        'HTTP_ACCEPT' => 'application/json',
        'CONTENT_TYPE' => 'application/json',
    ], json_encode($payload, JSON_THROW_ON_ERROR));
    $response = $app->make(Kernel::class)->handle($request);
    if (!in_array($response->getStatusCode(), $expected, true)) {
        $safePath = preg_replace('~(/form/)[a-f0-9]{64}~', '$1[token]', $path);
        throw new RuntimeException("{$method} {$safePath}: HTTP {$response->getStatusCode()}.");
    }
    return $response;
};

DB::beginTransaction();
try {
    $secretary = User::where('role', 2)->firstOrFail();
    $fixture = null;
    foreach (Monev::whereNotNull('legacy_report_id')->get() as $report) {
        $member = User::where('role', 4)->where('id_profile', $report->profile_id)->first();
        $accreditation = MonitoringAccreditation::where('profile_id', $report->profile_id)
            ->where('jenis_pengajuan_id', $report->jenis_pengajuan_id)
            ->whereDate('valid_from', '<=', $report->report_year . '-12-31')
            ->whereDate('valid_until', '>=', $report->report_year . '-01-01')->first();
        if ($member && $accreditation) {
            $fixture = [$report, $member, $accreditation];
            break;
        }
    }
    if (!$fixture) throw new RuntimeException('Tidak ditemukan laporan dengan akun lembaga dan akreditasi yang sesuai.');
    [$report, $member, $accreditation] = $fixture;
    $base = '/monitoring-akreditasi-pstk/' . $report->legacy_report_id;
    $request($member, 'GET', $base . '/edit');
    $request($secretary, 'GET', '/monitoring-evaluasi?year=' . $report->report_year);
    $request($secretary, 'GET', '/monitoring-evaluasi/' . $report->uuid);
    $csv = $request($secretary, 'GET', '/monitoring-evaluasi/' . $report->uuid . '/export');
    ob_start();
    $csv->sendContent();
    if (!str_contains(ob_get_clean(), 'Lembaga,Program,Tahun,Status,Pertanyaan,Jawaban')) {
        throw new RuntimeException('Header CSV tidak sesuai.');
    }
    $request($secretary, 'POST', '/monitoring-evaluasi/' . $report->uuid . '/link/regenerate', [], [302]);
    $link = MonitoringReportLink::where('monev_id', $report->id)->latest('id')->firstOrFail();
    if ($link->monitoring_report_id !== null) throw new RuntimeException('Link baru masih mengacu ke ID lama.');
    $url = '/monitoring-akreditasi-pstk/form/' . app(MonitoringReportLinkService::class)->decrypt($link);
    $request($member, 'GET', $url);

    $payload = [
        'jenis_pengajuan_id' => $report->jenis_pengajuan_id,
        'report_year' => $report->report_year,
        'edit_version' => $report->edit_version,
        'answers' => [
            1 => $report->profile->nama_lembaga, 2 => $report->jenisPengajuan->nama,
            3 => 'Alamat uji', 4 => '0800', 5 => 'uji@example.test',
            7 => $accreditation->certificate_number, 8 => $accreditation->predicate, 9 => $report->report_year,
            'has_training' => 'Tidak', 24 => 'Uji cutover', 25 => 'Uji', 26 => 'Uji',
        ],
    ];
    $request($member, 'POST', $url . '/draft', $payload);
    $request($member, 'POST', $url . '/draft', $payload, [409]);
    $payload['edit_version']++;
    $request($member, 'POST', $url . '/submit', $payload);
    $reopened = $request($member, 'GET', $url);
    if (!str_contains($reopened->getContent(), 'Uji cutover')) throw new RuntimeException('Jawaban kiriman tidak muncul kembali.');
    $submission = $report->fresh()->latestSubmission;
    if ((int) $submission?->submitted_by !== $member->id) throw new RuntimeException('Metadata kiriman tidak sesuai.');
    $request($member, 'POST', $base . '/reset', ['edit_version' => $payload['edit_version'] + 1], [302]);
    if ($report->fresh()->latestSubmission?->id !== $submission->id) throw new RuntimeException('Reset mengubah arsip kiriman.');

    $importYear = null;
    for ($year = max(2020, $accreditation->valid_from->year); $year <= min(now()->year, $accreditation->valid_until->year); $year++) {
        if (!Monev::where('profile_id', $report->profile_id)->where('jenis_pengajuan_id', $report->jenis_pengajuan_id)->where('report_year', $year)->exists()) {
            $importYear = $year;
            break;
        }
    }
    if (!$importYear) throw new RuntimeException('Tidak ada tahun kosong untuk menguji impor.');
    $file = tmpfile();
    try {
        $headers = (new ReflectionClass(ImportMonitoringReportHistory::class))->getConstant('HEADERS');
        $row = array_pad([
            "01/01/{$importYear} 9:00:00", $report->profile->nama_lembaga, $report->jenisPengajuan->nama,
            'Alamat uji', '0800', 'uji@example.test', '', $accreditation->certificate_number,
            $accreditation->predicate, (string) $importYear, 'Tidak', '0', '0', '0', '0',
        ], count($headers), '');
        $row[26] = 'Uji impor cutover';
        fputcsv($file, $headers);
        fputcsv($file, $row);
        fflush($file);
        $args = ['--file' => stream_get_meta_data($file)['uri'], '--source-ref' => 'uji-cutover:0'];
        if (Artisan::call('monitoring:import-history', $args) !== 0
            || Artisan::call('monitoring:import-history', $args) !== 0
            || !str_contains(Artisan::output(), '1 sudah diimpor')) {
            throw new RuntimeException('Uji impor atau pengulangan impor gagal.');
        }
        $source = DB::table('monitoring_report_import_sources')->where('source_ref', 'uji-cutover:0')->first();
        if (!$source?->monev_id || $source->monitoring_report_id !== null) throw new RuntimeException('Provenance impor tidak sesuai.');
    } finally {
        fclose($file);
    }
} finally {
    DB::rollBack();
}

echo "Lulus di {$database}: URL lama, dashboard, detail, CSV, link, draf, konflik revisi, submit, buka ulang, reset, dan impor. Transaksi uji dibatalkan.", PHP_EOL;

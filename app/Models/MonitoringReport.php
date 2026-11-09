<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class MonitoringReport extends Model
{
    protected $guarded = ['id'];
    protected $casts = ['report_year' => 'integer', 'edit_version' => 'integer', 'submitted_version' => 'integer'];

    protected static function booted(): void
    {
        static::creating(function (self $report) { $report->uuid ??= (string) Str::uuid(); });
    }

    public function profile() { return $this->belongsTo(Profile::class); }
    public function jenisPengajuan() { return $this->belongsTo(JenisPengajuan::class, 'jenis_pengajuan_id'); }
    public function versions() { return $this->hasMany(MonitoringReportVersion::class); }
    public function links() { return $this->hasMany(MonitoringReportLink::class, 'monitoring_report_id'); }
    public function submittedVersion() { return $this->hasOne(MonitoringReportVersion::class)->whereColumn('version', 'monitoring_reports.submitted_version'); }
}

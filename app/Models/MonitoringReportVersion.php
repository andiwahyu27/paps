<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MonitoringReportVersion extends Model
{
    protected $guarded = ['id'];
    protected $casts = ['answers' => 'array', 'identity_snapshot' => 'array', 'accreditation_snapshot' => 'array', 'submitted_at' => 'datetime'];
    public function report() { return $this->belongsTo(MonitoringReport::class, 'monitoring_report_id'); }
}

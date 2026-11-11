<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MonitoringReportLink extends Model
{
    protected $guarded = ['id'];
    protected $casts = ['expires_at' => 'datetime', 'deleted_at' => 'datetime'];

    public function report() { return $this->belongsTo(MonitoringReport::class, 'monitoring_report_id'); }
    public function monev() { return $this->belongsTo(Monev::class, 'monev_id'); }
}

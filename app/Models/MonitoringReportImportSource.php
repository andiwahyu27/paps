<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MonitoringReportImportSource extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'raw_responses' => 'array',
        'imported_at' => 'datetime',
    ];

    public function report()
    {
        return $this->belongsTo(MonitoringReport::class, 'monitoring_report_id');
    }
}

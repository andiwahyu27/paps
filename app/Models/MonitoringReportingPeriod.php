<?php

namespace App\Models;

use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class MonitoringReportingPeriod extends Model
{
    public $timestamps = false;
    protected $fillable = ['report_year', 'opens_at', 'closes_at'];
    protected $casts = ['report_year' => 'integer'];

    public static function settings()
    {
        return DB::table('monitoring_reporting_settings')->where('id', 1);
    }

    public static function global(): ?self
    {
        return static::whereIn('id', static::settings()->select('period_id'))->first();
    }

    public function getOpensAtAttribute(string $value): CarbonImmutable
    {
        return CarbonImmutable::parse($value, 'UTC');
    }

    public function getClosesAtAttribute(string $value): CarbonImmutable
    {
        return CarbonImmutable::parse($value, 'UTC');
    }

    public function stateAt(CarbonInterface $time): string
    {
        if ($time->lt($this->opens_at)) {
            return 'upcoming';
        }

        return $time->lt($this->closes_at) ? 'open' : 'closed';
    }
}

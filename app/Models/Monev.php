<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Monev extends Model
{
    protected $table = 'tr_monev';
    protected $guarded = ['id'];
    protected $casts = ['identity_snapshot' => 'array', 'accreditation_snapshot' => 'array', 'submitted_at' => 'datetime', 'edit_version' => 'integer', 'report_year' => 'integer', 'profile_id' => 'integer', 'jenis_pengajuan_id' => 'integer'];

    protected static function booted(): void
    {
        static::creating(function (self $monev) { $monev->uuid ??= (string) Str::uuid(); });
    }

    public function questionnaire() { return $this->belongsTo(MonevKuesioner::class, 'kuesioner_id'); }
    public function answers() { return $this->hasMany(MonevJawaban::class, 'monev_id'); }
    public function profile() { return $this->belongsTo(Profile::class); }
    public function jenisPengajuan() { return $this->belongsTo(JenisPengajuan::class, 'jenis_pengajuan_id'); }
    public function links() { return $this->hasMany(MonitoringReportLink::class, 'monev_id'); }
    public function submissions() { return $this->hasMany(MonevSubmission::class); }
    public function latestSubmission() { return $this->hasOne(MonevSubmission::class)->ofMany('version', 'max'); }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }
}

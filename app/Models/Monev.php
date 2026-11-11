<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Monev extends Model
{
    protected $table = 'tr_monev';
    protected $guarded = ['id'];
    protected $casts = ['identity_snapshot' => 'array', 'accreditation_snapshot' => 'array', 'submitted_at' => 'datetime'];

    protected static function booted(): void
    {
        static::creating(function (self $monev) { $monev->uuid ??= (string) Str::uuid(); });
    }

    public function questionnaire() { return $this->belongsTo(MonevKuesioner::class, 'kuesioner_id'); }
    public function answers() { return $this->hasMany(MonevJawaban::class, 'monev_id'); }
}

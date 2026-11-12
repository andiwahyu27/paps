<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MonevKuesioner extends Model
{
    protected $table = 'mt_kuesioner';
    public $timestamps = false;
    protected $guarded = ['id'];
    protected $casts = ['is_active' => 'boolean'];

    public function getDisplayVersionAttribute(): string
    {
        return $this->versi_label ?: '2025.' . $this->versi;
    }

    public function questions() { return $this->hasMany(MonevPertanyaan::class, 'kuesioner_id'); }
}

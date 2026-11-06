<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MonitoringAccreditation extends Model
{
    protected $guarded = ['id'];
    protected $casts = ['valid_from' => 'date', 'valid_until' => 'date'];

    public function profile() { return $this->belongsTo(Profile::class); }
    public function jenisPengajuan() { return $this->belongsTo(JenisPengajuan::class, 'jenis_pengajuan_id'); }
}

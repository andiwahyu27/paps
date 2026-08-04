<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JenisPengajuan extends Model
{
    use HasFactory;

    protected $table = "mt_jenis_pengajuans";

    public function pengajuan()
    {
        return $this->hasMany(Pengajuan::class, 'jenis');
    }
}

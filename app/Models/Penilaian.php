<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Penilaian extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'tb_penilaians';
    protected $fillable = ['id_asesor', 'id_pengajuan', 'id_item_penilaian', 'pra_paska', 'catatan', 'rekomendasi', 'pengecekan_visitasi', 'catatan_sidang', 'nilai'];
}

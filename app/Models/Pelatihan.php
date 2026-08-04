<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Pelatihan extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = "tb_pelatihans";
    protected $fillable = ["id_pengajuan", "nama", "angkatan", "tahun"];

    public function fullname()
    {
        return $this->nama . ' ' . $this->angkatan . ' ' . $this->tahun;
    }

    public function pengajuan()
    {
        return $this->belongsTo(Pengajuan::class, 'id_pengajuan');
    }

}

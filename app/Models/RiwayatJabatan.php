<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class RiwayatJabatan extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = "tb_riwayat_jabatan";
    protected $guarded = ['id', 'created_at','updated_at','deleted_at'];

    public function tenaga():BelongsTo {
        return $this->belongsTo(Tenaga::class);
    }
}

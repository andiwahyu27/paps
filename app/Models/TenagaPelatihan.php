<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TenagaPelatihan extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = "tb_pelatihan_tenagas";
    protected $guarded = ['id'];

    public function tenaga()
    {
        return $this->belongsTo(Tenaga::class, 'id_tenaga');
 	}
}

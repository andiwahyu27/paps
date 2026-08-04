<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RiwayatPelatihan extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = "tb_riwayat_pelatihan";
    protected $guarded = ['id', 'created_at','updated_at','deleted_at'];

    public function tenagas() {
        return $this->hasMany(Tenaga::class);
    }
}

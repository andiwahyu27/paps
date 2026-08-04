<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DokumenPelatihan extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = "tb_pelatihan_dokumens";
    protected $guarded = ['id'];
}

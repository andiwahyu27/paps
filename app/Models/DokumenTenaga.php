<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DokumenTenaga extends Model
{
    use HasFactory;

    protected $table = "tb_tenaga_dokumens";

    protected $guarded = ['id'];
}

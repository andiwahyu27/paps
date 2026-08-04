<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Pangkat extends Model
{
    use HasFactory;

    protected $table = "mt_pangkat";
    protected $fillable = ["nama"];

    public function tenaga()
    {
        return $this->belongsTo(Tenaga::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subunsur extends Model
{
    use HasFactory;

    protected $table = "mt_subunsurs";

    public function items()
    {
        return $this->hasMany(Item::class, 'id_subunsur');
    }
}

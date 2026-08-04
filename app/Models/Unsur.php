<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Unsur extends Model
{
    use HasFactory;

    protected $table = "mt_unsurs";

    public function subunsurs()
    {
        return $this->hasMany(Subunsur::class, 'id_unsur');
    }
}

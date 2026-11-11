<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MonevPertanyaan extends Model
{
    protected $table = 'mt_pertanyaan';
    public $timestamps = false;
    protected $guarded = ['id'];
    protected $casts = ['opsi' => 'array'];

    public function questionnaire() { return $this->belongsTo(MonevKuesioner::class, 'kuesioner_id'); }
}

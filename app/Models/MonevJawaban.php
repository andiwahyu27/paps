<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MonevJawaban extends Model
{
    protected $table = 'tr_monev_jawaban';
    public $timestamps = false;
    protected $guarded = ['id'];

    public function monev() { return $this->belongsTo(Monev::class, 'monev_id'); }
    public function question() { return $this->belongsTo(MonevPertanyaan::class, 'pertanyaan_id'); }
}

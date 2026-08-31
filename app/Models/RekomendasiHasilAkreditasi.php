<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RekomendasiHasilAkreditasi extends Model
{
    protected $table = 'tr_rekomendasi_hasil_akreditasi';

    public const KATEGORI_DIPERTAHANKAN = 'dipertahankan';
    public const KATEGORI_DIPERBAIKI = 'diperbaiki';

    protected $fillable = [
        'pengajuan_id', 'kategori', 'isi', 'urutan', 'created_by', 'updated_by',
    ];

    public function pengajuan(): BelongsTo
    {
        return $this->belongsTo(Pengajuan::class, 'pengajuan_id');
    }
}

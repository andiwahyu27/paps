<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SidangSignature extends Model
{
    protected $table = 'tr_sidang_signatures';

    public const SIGNER_TYPES = [
        'ketua_majelis',
        'sekretaris_majelis',
        'anggota_majelis',
    ];

    protected $fillable = [
        'pengajuan_id', 'jenis_user', 'nomor_surat', 'nama_user', 'jabatan_user', 'ttd',
        'tgl_surat', 'waktu_surat', 'tgl_waktu_surat', 'tempat_surat',
        'zona_surat', 'hari_tanggal_surat', 'status_ttd',
    ];

    protected $casts = [
        'tgl_surat' => 'date',
        'waktu_surat' => 'datetime:H:i:s',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function pengajuan(): BelongsTo
    {
        return $this->belongsTo(Pengajuan::class, 'pengajuan_id');
    }

    public static function forPengajuan(int $pengajuanId)
    {
        return self::where('pengajuan_id', $pengajuanId)->get()->keyBy('jenis_user');
    }

    public static function isFullySigned(int $pengajuanId): bool
    {
        return self::where('pengajuan_id', $pengajuanId)
            ->whereIn('jenis_user', self::SIGNER_TYPES)
            ->where('status_ttd', 'signed')
            ->count() === count(self::SIGNER_TYPES);
    }
}

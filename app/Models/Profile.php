<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Profile extends Model
{
    use HasFactory;

    protected $table = "tb_profile_lembagas";
    protected $guarded = ['id', 'created_at', 'updated_at'];

    public static $updateRules = [
        'nama_pimpinan' => 'max:100',
        'nip_pimpinan' => 'max:20',
        'jabatan_pimpinan' => 'max:100',
        'unit_kerja' => 'max:100',
        'alamat_unit_kerja' => 'max:255',
        'path_surat_pernyataan_pimpinan' => 'mimes:pdf',
        'nama_lembaga' => 'max:100',
        'telepon' => 'max:20',
        'faksimili' => 'max:20',
        'email' => 'max:100',
        'website' => 'max:100',
        'provinsi' => 'max:11',
        'kabupaten_kota' => 'max:11',
        'alamat_lembaga' => 'max:255',
        'nomor_sk_pemerintah' => 'max:100',
        'tanggal_sk_pemerintah' => 'date|nullable',
        'tentang_sk_pemerintah' => 'max:100',
        'path_sk_pemerintah' => 'mimes:pdf',
        'no_surat_izin_operasional' => 'max:100',
        'tanggal_surat_izin_operasional' => 'date|nullable',
        'penerbit_surat_izin_operasional' => 'max:100',
        'path_surat_izin_operasional' => 'mimes:pdf',
        'nomor_akte_pendirian' => 'max:100',
        'tanggal_akte_pendirian' => 'date|nullable',
        'ttd_akte_pendirian' => 'max:100',
        'path_akte_pendirian' => 'mimes:pdf',
        'path_rencana_keiatan' => 'mimes:pdf',
        'path_kegiatan_diklat' => 'mimes:pdf',
        'path_pembiayaan' => 'mimes:pdf',
        'path_sop_perencanaan' => 'nullable|mimes:pdf',
        'path_sop_pelaksanaan' => 'nullable|mimes:pdf',
        'path_sop_evalap' => 'nullable|mimes:pdf',
	    'path_bukti_sosialisasi_sop' => 'nullable|mimes:pdf',
    	'path_laporan_penjaminan_mutu' => 'nullable|mimes:pdf',
        'tgl_dibuka' => 'date|nullable',
        'tgl_ditutup' => 'date|nullable',
    ];

    public function pic()
    {
        return $this->hasMany(User::class, 'id_profile');
    }

    public function pengajuans()
    {
        return $this->hasMany(Pengajuan::class, 'id_profile');
    }

    /**
     * is_lock dinamis (Opsi C - tanpa kolom tgl_dibuka/tgl_ditutup):
     * dihitung ulang setiap kali dibaca berdasarkan tgl_surat dari
     * tr_digital_signatures (hasil generate tanda tangan).
     *
     * start_reupload = H+1 hari kerja, end_reupload = H+3 hari kerja.
     */
    public function getIsLockAttribute($value)
    {
        $today = Carbon::today();
        $startReupload = $this->getStartReuploadAttribute();
        $endReupload = $this->getEndReuploadAttribute();

        if (!$startReupload || !$endReupload) {
            return $value;
        }

        if ($today->gt($endReupload)) {
            return 1; // lewat end_reupload → terkunci otomatis
        }
        if ($today->gte($startReupload)) {
            return 0; // dalam rentang start..end → terbuka
        }

        return $value; // sebelum start_reupload → ikuti nilai DB
    }

    private function addBusinessDays(Carbon $date, int $days): Carbon
    {
        $result = $date->copy();
        $added = 0;
        while ($added < $days) {
            $result->addDay();
            if (!$result->isWeekend()) {
                $added++;
            }
        }
        return $result;
    }

    private function latestSignatureDate()
    {
        return \App\Models\DigitalSignature::whereIn('pengajuan_id', function ($q) {
            $q->select('id')->from('tb_pengajuans')->where('id_profile', $this->id);
        })->whereNotNull('tgl_surat')->orderByDesc('tgl_surat')->value('tgl_surat');
    }

    public function getStartReuploadAttribute()
    {
        $tglSurat = $this->latestSignatureDate();
        if (!$tglSurat) {
            return null;
        }
        return $this->addBusinessDays(Carbon::parse($tglSurat), 1);
    }

    public function getEndReuploadAttribute()
    {
        $tglSurat = $this->latestSignatureDate();
        if (!$tglSurat) {
            return null;
        }
        return $this->addBusinessDays(Carbon::parse($tglSurat), 3);
    }

    public function provinsiWilayah()
    {
        return $this->belongsTo(Wilayah::class, 'provinsi');
    }

    public function kabupatenWilayah()
    {
        return $this->belongsTo(Wilayah::class, 'kabupaten_kota');
    }
}

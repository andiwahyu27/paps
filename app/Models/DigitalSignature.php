<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class DigitalSignature extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'tr_digital_signatures';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'pengajuan_id',
        'jenis_user',
        'nama_user',
        'jabatan_user',
        'ttd',
        'tgl_surat',
        'waktu_surat',
        'tgl_waktu_surat',
        'status_ttd'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'tgl_surat' => 'date',
        'waktu_surat' => 'datetime:H:i:s',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the pengajuan that owns the digital signature.
     */
    public function pengajuan()
    {
        return $this->belongsTo(Pengajuan::class, 'pengajuan_id');
    }

    /**
     * Get signatures for a specific pengajuan
     *
     * @param int $pengajuanId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getPengajuanSignatures($pengajuanId)
    {
        return self::where('pengajuan_id', $pengajuanId)
                   ->where('status_ttd', 'signed')
                   ->orderBy('tgl_waktu_surat', 'asc')
                   ->get();
    }

    /**
     * Check if pengajuan is fully signed
     *
     * @param int $pengajuanId
     * @return bool
     */
    public static function isPengajuanFullySigned($pengajuanId)
    {
        $requiredSigners = ['asesor1', 'asesor2', 'asesor3', 'kepala'];
        $existingSigners = self::where('pengajuan_id', $pengajuanId)
                              ->where('status_ttd', 'signed')
                              ->pluck('jenis_user')
                              ->toArray();
        
        return count(array_intersect($requiredSigners, $existingSigners)) === count($requiredSigners);
    }

    /**
     * Create a new signature record
     *
     * @param array $data
     * @return self
     */
    public static function createSignature($data)
    {
        return self::create([
            'pengajuan_id' => $data['pengajuan_id'],
            'jenis_user' => $data['jenis_user'],
            'nama_user' => $data['nama_user'],
            'jabatan_user' => $data['jabatan_user'],
            'ttd' => $data['ttd'] ?? null,
            'tgl_surat' => $data['tgl_surat'] ?? null,
            'waktu_surat' => $data['waktu_surat'] ?? null,
            'tgl_waktu_surat' => $data['tgl_waktu_surat'] ?? self::generateIndonesianDateTime(),
            'status_ttd' => $data['status_ttd'] ?? 'signed'
        ]);
    }

    /**
     * Generate Indonesian formatted date time string
     *
     * @param Carbon|null $dateTime
     * @return string
     */
    public static function generateIndonesianDateTime($dateTime = null)
    {
        if (!$dateTime) {
            $dateTime = Carbon::now('Asia/Jakarta');
        }

        $timezoneLabels = [
            'Asia/Jakarta' => 'Waktu Indonesia Barat',
            'Asia/Makassar' => 'Waktu Indonesia Tengah',
            'Asia/Jayapura' => 'Waktu Indonesia Timur',
        ];
        $timezoneLabel = $timezoneLabels[$dateTime->getTimezone()->getName()] ?? 'Waktu Indonesia Barat';

        // Array hari dalam bahasa Indonesia
        $days = [
            'Sunday' => 'Minggu',
            'Monday' => 'Senin', 
            'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis',
            'Friday' => 'Jumat',
            'Saturday' => 'Sabtu'
        ];

        // Array bulan dalam bahasa Indonesia
        $months = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        $dayName = $days[$dateTime->format('l')];
        $day = $dateTime->format('j');
        $monthName = $months[(int)$dateTime->format('n')];
        $year = $dateTime->format('Y');
        $time = $dateTime->format('H.i');

        return "{$dayName} Tanggal {$day} {$monthName} {$year}, Pukul {$time} {$timezoneLabel}";
    }
}

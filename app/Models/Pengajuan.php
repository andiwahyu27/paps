<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Pengajuan extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = "tb_pengajuans";
    protected $guarded = ["id", "created_at", "updated_at", "deleted_at"];
    
    /**
     * Find pengajuan by ID
     *
     * @param int $id
     * @return Pengajuan|null
     */
    public static function findById($id)
    {
        return static::where('id', $id)->first();
    }
    
    /**
     * Find pengajuan by ID or fail
     *
     * @param int $id
     * @return Pengajuan
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public static function findByIdOrFail($id)
    {
        return static::where('id', $id)->firstOrFail();
    }
    public function profile()
    {
        return $this->belongsTo(Profile::class, 'id_profile');
    }

    public function jenis()
    {
        return $this->belongsTo(JenisPengajuan::class, 'id_jenis');
    }

    public function pelatihan()
    {
        return $this->hasMany(Pelatihan::class, 'id_pengajuan');
    }

    public function asesor1()
    {
        return $this->belongsTo(User::class, 'id_asesor1');
    }

    public function asesor2()
    {
        return $this->belongsTo(User::class, 'id_asesor2');
    }

    public function asesor3()
    {
        return $this->belongsTo(User::class, 'id_asesor3');
    }

    public static function getasesor($id)
    {
        $pengajuan = Pengajuan::find($id);
        $id_asesor = auth()->user()->id;
        if ($pengajuan->id_asesor1 == $id_asesor) {
            return 1;
        } elseif ($pengajuan->id_asesor2 == $id_asesor) {
            return 2;
        } elseif ($pengajuan->id_asesor3 == $id_asesor) {
            return 3;
        }
    }

    public function checkasesor()
    {
        $pengajuan = $this;
        $id_asesor = auth()->user()->id;
        if ($pengajuan->id_asesor1 == $id_asesor) {
            return 1;
        } elseif ($pengajuan->id_asesor2 == $id_asesor) {
            return 2;
        } elseif ($pengajuan->id_asesor3 == $id_asesor) {
            return 3;
        }
    }

    public function ispravisit1()
    {
        $asesor = $this->checkasesor();
        switch ($asesor) {
            case 1:
                return $this->pra_visit_asesor1;
                break;
            case 2:
                return $this->pra_visit_asesor2;
                break;
            case 3:
                return $this->pra_visit_asesor3;
                break;
        }
    }

    public function ispravisit2()
    {
        return $this->pra_visit2_asesor;
    }

    public function isvisitasi()
    {
        return $this->visitasi;
    }

    public function ispaskavisit()
    {
        return $this->paska_visit;
    }

    public function isfinal()
    {
        return $this->final;
    }

    public function isketua()
    {
        $asesor = $this->checkasesor();
        switch ($asesor) {
            case 1:
                return true;
                break;
            case 2:
                return false;
                break;
            case 3:
                return false;
                break;
        }
    }
}

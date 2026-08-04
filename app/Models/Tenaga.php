<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Tenaga extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = "tb_tenaga";
    protected $guarded = ['id', 'created_at', 'updated_at', 'deleted_at'];

    // Jenis Tenaga
    // 1: Fasilitator
    // 2: Pengelola Pelatihan
    // 3: Pengelola Kelas
    // 4: Pengelola SI
    // 5: Analis Kebutuhan Diklat

    public static $createRules = [
        'nama' => 'required|max:255|min:3',
        'nik' => 'required|digits:16',
        'nip' => 'required|min:5',
        'tempat_lahir' => 'required|min:3',
        'tanggal_lahir' => 'required|date',
        'id_pangkat' => 'required',
        'jabatan' => 'required|min:3',
        'alamat_kantor' => 'required',
        'telp_kantor' => 'required',
        'alamat_rumah' => 'required|min:3',
        'telp_rumah' => 'nullable',
        'hp' => 'required|digits_between:9,14',
        'email' => 'required',
        'npwp' => 'required|digits:16',
        'jenis_tenaga' => 'required'
    ];

    public static $updateRules = [
        'nama' => 'required|max:255|min:3',
        'nik' => 'required|digits:16',
        'nip' => 'required|min:5',
        'tempat_lahir' => 'required|min:3',
        'tanggal_lahir' => 'required|date',
        'id_pangkat' => 'required',
        'jabatan' => 'required|min:3',
        'alamat_kantor' => 'required',
        'telp_kantor' => 'required',
        'alamat_rumah' => 'required|min:3',
        'telp_rumah' => 'nullable',
        'hp' => 'required|digits_between:9,14',
        'email' => 'required',
        'npwp' => 'required|digits:16'
    ];

    public function r_jabatans()
    {
        return $this->hasMany(RiwayatJabatan::class);
    }
    public function r_kerjas()
    {
        return $this->hasMany(RiwayatKerja::class);
    }
    public function r_pelatihans()
    {
        return $this->hasMany(RiwayatPelatihan::class);
    }
    public function r_pendidikans()
    {
        return $this->hasMany(RiwayatPendidikan::class);
    }
    public function pangkat()
    {
        return $this->hasMany(Pangkat::class);
    }
}

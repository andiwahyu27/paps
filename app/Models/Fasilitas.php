<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Fasilitas extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = "tb_fasilitas";

    protected $guarded = ['id', 'created_at','updated_at','deleted_at'];

    public static $createRules = [
        'nama'=> 'required|max:255|min:3',
        'jumlah' => 'required|numeric',
        'status' => 'required|digits:1',
        'tipe' => 'required|digits:1',
        'keterangan' => 'max:255',
        'path_foto' => 'image',
        'path_dokumen' => 'mimes:pdf',
    ];

    public static $updateRules = [
        'id' => 'required',
        'nama'=> 'max:255|min:3',
        'jumlah' => 'numeric',
        'status' => 'digits:1',
        'keterangan' => 'max:255',
        'path_foto' => 'image',
        'path_dokumen' => 'mimes:pdf',
    ];
}

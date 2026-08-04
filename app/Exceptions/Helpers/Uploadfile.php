<?php
namespace App\Helpers;
 
use Illuminate\Support\Facades\DB;
 
class Uploadfile {

    public static function upload($idUser, $file, $fileCategory) {
        $fileName = $idUser."-".time().".".$file->getClientOriginalExtension();
        $file->move($fileCategory,$fileName);
        $filePath = $fileCategory."/".$fileName;

        return $filePath;
    }
}
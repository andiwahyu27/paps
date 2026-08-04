<?php
namespace App\Helpers;
 
use Illuminate\Support\Facades\DB;
 
class Uploadfile {
	//file lama
  	/*
    public static function upload($idUser, $file, $fileCategory) {
        $fileName = $idUser."-".time().".".$file->getClientOriginalExtension();
        $file->move($fileCategory,$fileName);
        $filePath = $fileCategory."/".$fileName;

        return $filePath;
    }
    */
  	//file baru
  	public static function upload($idUser, $file, $fileCategory) {
        $fileName = $idUser . "-" . time() . "-" . uniqid() . "." . $file->getClientOriginalExtension();
        $file->move($fileCategory, $fileName);
        $filePath = $fileCategory . "/" . $fileName;

        return $filePath;
    }
}
<?php

namespace App\Services;  
use App\RiwayatEmail;
use Helper;
use Auth;  

class RiwayatEmailService 
{
    public function first($id, $kategori)
    {
        return RiwayatEmail::with('UpdatedBy')->where("id_reference", $id)->where("kategori", $kategori)->first();
    }
 
}







<?php

namespace App\Services;  
use App\SupirSO;
use Helper;
use Auth;  

class SupirSoService 
{
    public function get($id_so)
    {
        return SupirSO::where("id_so", $id_so)->where("is_aktif", "0")->get();
    }

    public function store($id_so, $request)
    {
        try {
            $supir = new SupirSO();
            $supir->id_so = $id_so;
            $supir->id_supir = $request->supir;
            $supir->created_by = Auth::user()->id_user;
            $supir->save();
        } catch (\Exception $e) {
            throw new \Exception("Tambah supir tidak berhasil. ".$e->getMessage(), 1);
        }
    } 

    public function update($request)
    {
        $id = Helper::decodex($request->id_supir_so);
        try { 
            $supir = SupirSO::findOrFail($id);
            $supir->id_supir = $request->supir;
            $supir->updated_by = Auth::user()->id_user;
            $supir->save();       
        } catch (\Exception $e) {
            throw new Exception("Update supir tidak berhasil. ".$e->getMessage(), 1);
        }
    }
}







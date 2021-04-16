<?php

namespace App\Services;  
use App\RiwayatEmail; 
use Carbon\Carbon;
use Helper;
use Auth;
use DB;

class AppService 
{ 
	public function storeRiwayatEmail($id, $kategori)
	{
		 try {
		 	$riwayat_email = RiwayatEmail::firstOrNew(["id_reference" => $id, "kategori" => $kategori]);
            $riwayat_email->jumlah = $riwayat_email->jumlah + 1;
            $riwayat_email->kategori = $kategori; 
            $riwayat_email->created_by = Auth::user()->id_user;
            $riwayat_email->updated_by = Auth::user()->id_user;
            $riwayat_email->save();

		 } catch (\Exception $e) {
		 	throw new Exception($e->getMessage(), 1);	
		 }
	} 
}










<?php

namespace App\Services; 
use App\SKPPATM;
use Helper;
use Auth;
use PDF;
use DB;

class SkppAtmService 
{ 
	public function store($request, $id_skpp)
	{	 
		try {
		 	$atm = [];
	        for ($i=0; $i < count($request->atm) ; $i++) { 
	            $x["id_atm"] = Helper::decodex($request->atm[$i]);
	            $x["id_skpp"] = $id_skpp;
	            $atm[] = $x;
	        }
	        DB::table("tr_skpp_atm")->insert($atm);
		} catch (\Exception $e) {
		 	throw new \Exception("Tambah atm tidak berhasil ". $e->getMessage(), 1);
		}
	} 

	public function update($request, $id_skpp)
	{	
		try {
			SKPPATM::where("id_skpp", $id_skpp)->delete();
			$this->store($request, $id_skpp);
		} catch (\Exception $e) {
			throw new \Exception("Update atm tidak berhasil ". $e->getMessage(), 1);
		}
	}
}

















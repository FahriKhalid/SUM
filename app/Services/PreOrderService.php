<?php

namespace App\Services; 
use App\Lampiran;
use App\PreOrder; 
use App\Barang;
use Helper;
use Auth;
use PDF;
use DB;

class PreOrderService 
{

	public function nomorPo($id)
	{
		return PreOrder::findOrFail($id)->no_po;
	}

	public function lastKodePreOrder(){
		$skpp = PreOrder::withTrashed()->selectRaw('left(no_po, 4) as last')->orderBy('last', 'desc')->first();
		 
		if($skpp){
			$last_number = sprintf("%04d", ((int)substr($skpp->last, 0, 4) + 1));
			$nomor_skpp = $last_number.'/SUM-PO/'.Helper::integerToRoman(date('m')).'.'.date('Y');
		} else {
			$nomor_skpp = '0001/SUM-PO/'.Helper::integerToRoman(date('m')).'.'.date('Y');
		}

		return $nomor_skpp;
	}

	public function suratPreOrder($id)
	{
		$info["pre_order"]          = PreOrder::with('CreatedBy','Produsen','Status')->findOrFail($id);
        $info["po"]                 = Barang::with('Produk')->where("id_pre_order", $id)->get();
        $info["lampiran"]           = Lampiran::where("id_reference", $id)->where("kategori", "PRE ORDER")->get();
        $info["profil_perusahaan"]  = DB::table("ms_profil_perusahaan")->first();
        $pdf = PDF::loadview('surat.pembelian.surat_po', compact('info')); 
        
        return [
        	'info' => $info,
        	'pdf' => $pdf
        ];
	}
 
}

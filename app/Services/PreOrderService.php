<?php

namespace App\Services; 
use App\PreOrder; 
use Auth;
use Helper;

class PreOrderService 
{
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

	
 
}

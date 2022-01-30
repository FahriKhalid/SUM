<?php
namespace App\Services;
use App\Invoice;
use Validator;
use Helper;
use Auth;
use PDF;
use DB;

class InvoiceService
{
	public function lastKodeTagihan()
	{
		$data = Invoice::selectRaw('left(no_tagihan, 4) as last')->orderBy('last', 'desc')->first();
		
		if($data){
			$last_number = sprintf("%04d", ((int)substr($data->last, 0, 4) + 1));
			$nomor_tagihan = $last_number.'/TGH-SKPP/'.Helper::integerToRoman(date('m')).'.'.date('Y');
		} else {
			$nomor_tagihan = '0001/TGH-SKPP/'.Helper::integerToRoman(date('m')).'.'.date('Y');
		}

		return $nomor_tagihan;
	}
}
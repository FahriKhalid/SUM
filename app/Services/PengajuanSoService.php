<?php

namespace App\Services; 
use App\BarangPengajuanSo;
use App\PengajuanSo; 
use App\Barang;
use Exception;
use Helper;
use Auth;

class PengajuanSoService 
{
	public function lastKodePengajuanPo(){
		$pso = PengajuanSo::selectRaw('left(no_pengajuan_so, 4) as last')->orderBy('last', 'desc')->first();
		 
		if($pso){
			$last_number = sprintf("%04d", ((int)substr($pso->last, 0, 4) + 1));
			$nomor = $last_number.'/SUM-PSO/'.Helper::integerToRoman(date('m')).'.'.date('Y');
		} else {
			$nomor = '0001/SUM-PSO/'.Helper::integerToRoman(date('m')).'.'.date('Y');
		}

		return $nomor;
	}

	public static function sisaKuantitasPO($id_barang, $id_barang_pengajuan_so = null)
    { 
        $po = Barang::findOrFail($id_barang);  

        if($id_barang_pengajuan_so != null){
            $kuantitas = BarangPengajuanSo::where("id_barang", $id_barang)->where("id_barang_pengajuan_so", "!=", $id_barang_pengajuan_so)->sum("kuantitas");
            $sisa = $po->kuantitas - $kuantitas;
        } else {
           	$kuantitas = BarangPengajuanSo::where("id_barang", $id_barang)->sum("kuantitas");
        	$sisa = $po->kuantitas - $kuantitas;
        } 

        return $sisa;
    }
 
}

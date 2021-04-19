<?php

namespace App\Services; 
use App\BarangPengajuanSo;
use App\PengajuanSo; 
use App\Barang;
use Exception;
use Helper;
use Auth;
use PDF;
use DB;

class PengajuanSoService 
{
    protected $model;

    public function __construct(PengajuanSo $PengajuanSo){
        $this->model = $PengajuanSo;
    }

    public function NomorPengajuanSO($id)
    {   
        return $this->model::findOrFail($id)->no_pengajuan_so;
    }

	public function lastKodePengajuanPo(){
		$pso = $this->model::selectRaw('left(no_pengajuan_so, 4) as last')->orderBy('last', 'desc')->first();
		 
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

    public function suratPengajuanSo($id)
    {
    	$info["pengajuan_so"] = $this->model::with("PreOrder")->findOrFail($id);
        $info["profil_perusahaan"]  = DB::table("ms_profil_perusahaan")->first();
        $pdf = PDF::loadview('surat.pembelian.surat_pengajuan_so', compact('info', 'id')); 

        return [
            'info' => $info, 
            'pdf' => $pdf
        ];
    }
 
}

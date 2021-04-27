<?php

namespace App\Services; 
use App\SuratKuasa;
use App\SKSO;
use App\SOPO;
use Helper;
use Auth;
use PDF;
use DB;

class SuratKuasaService 
{
    public function nomor($id)
    {
        return SuratKuasa::findOrFail($id)->no_sk;
    }

    public function lastKodeSk()
    {
        $so = SuratKuasa::selectRaw('left(no_sk, 4) as last')->orderBy('last', 'desc')->first();
         
        if($so){
            $last_number = sprintf("%04d", ((int)substr($so->last, 0, 4) + 1));
            $nomor_skpp = $last_number.'/SUM-SK/'.Helper::integerToRoman(date('m')).'.'.date('Y');
        } else {
            $nomor_skpp = '0001/SUM-SK/'.Helper::integerToRoman(date('m')).'.'.date('Y');
        }

        return $nomor_skpp;
    }

    public static function sisaKuantitasSOPO($id_so_po, $id_sk_so = null)
    { 
        $po = SOPO::findOrFail($id_so_po);
        
        if($id_sk_so != null){
            $kuantitas = SKSO::where("id_so_po", $id_so_po)->where("id_sk_so", "!=", $id_sk_so)->sum("kuantitas"); 
            $sisa = $po->kuantitas - $kuantitas;
        } else {
            $kuantitas = SKSO::where("id_so_po", $id_so_po)->sum("kuantitas");
            $sisa = $po->kuantitas - $kuantitas;
        }  
        return $sisa;
    }

    public static function validateMaxKuantitasSO($id_so_po, $jumlah, $id_sk_so = null){

        if($id_sk_so == null){
            $data = SOPO::findOrFail($id_so_po);
            $kuantitas = $data->kuantitas;
        } else {
            $kuantitas = self::sisaKuantitasSOPO($id_so_po, $id_sk_so);
        }
        
    
        if($jumlah > $kuantitas){
            throw new \Exception("Maksimal kuantitas tidak boleh lebih dari ".  $kuantitas, 1);
        }
    }

    public function validateAllKuantitas($kuantitas)
    {
        if($kuantitas != null){
            if((count(array_keys($kuantitas, 0)) == count($kuantitas)) === true){
                throw new \Exception("Total kuantitas tidak boleh kosong", 1); 
            }
        }
    }

    public function suratKuasa($id)
    { 
        $info["surat_kuasa"] = SuratKuasa::with('Gudang','Supir')->findOrFail($id);
        $info["profil_perusahaan"]  = DB::table("ms_profil_perusahaan")->first();
        $info["skso"] = SKSO::with('SOPO')->where("id_sk", $id)->get();   
        $pdf = PDF::loadview('surat.penjualan.surat_kuasa', compact('info', 'id')); 
        return [
            'info' => $info,
            'pdf' => $pdf
        ];
    }
}







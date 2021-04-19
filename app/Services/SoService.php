<?php

namespace App\Services; 
use App\Barang;
use App\SOPO;
use App\SO;
use Helper;
use Auth;
use PDF;
use DB;

class SoService 
{

    public function nomor($id)
    {
        return SO::findOrFail($id)->no_so;
    }

    public function lastKodeSo()
    {
        $so = SO::selectRaw('left(no_so, 4) as last')->orderBy('last', 'desc')->first();
         
        if($so){
            $last_number = sprintf("%04d", ((int)substr($so->last, 0, 4) + 1));
            $nomor_skpp = $last_number.'/SUM-SO/'.Helper::integerToRoman(date('m')).'.'.date('Y');
        } else {
            $nomor_skpp = '0001/SUM-SO/'.Helper::integerToRoman(date('m')).'.'.date('Y');
        }

        return $nomor_skpp;
    }

    public function validateMaxKuantitasPO($id_barang, $jumlah, $id_so_po = null)
    {
        if($id_so_po == null){
            $skpp = Barang::findOrFail($id_barang);
            $kuantitas = $skpp->kuantitas;
        } else {
            $kuantitas = $this->sisaKuantitasPO($id_barang, $id_so_po);
        } 
    
        if($jumlah > $kuantitas){
            throw new \Exception("Maksimal kuantitas tidak boleh lebih dari ".  $kuantitas, 1);
        }
    }

    public static function sisaKuantitasPO($id_barang, $id_so_po = null)
    { 
        $po = Barang::findOrFail($id_barang); 
        if($id_so_po != null){
            $kuantitas = SOPO::where("id_barang", $id_barang)->where("id_so_po", "!=", $id_so_po)->sum("kuantitas");
            $sisa = $po->kuantitas - $kuantitas;
        } else {
            $kuantitas = SOPO::where("id_barang", $id_barang)->sum("kuantitas");
             
            $sisa = $po->kuantitas - $kuantitas;
        } 

        return $sisa;
    }

    public static function validateAllKuantitas($kuantitas){
        if($kuantitas != null){
            if((count(array_keys($kuantitas, 0)) == count($kuantitas)) === true){
                throw new \Exception("Total kuantitas tidak boleh kosong", 1); 
            }
        }
    }

    public function suratSo($id)
    {
        $info["so"] = SO::with('SKPP')->findOrFail($id);
        $info["sopo"] = SOPO::with('SO','Barang')->where("id_so", $id)->get();  
        $info["profil_perusahaan"]  = DB::table("ms_profil_perusahaan")->first();
        $pdf = PDF::loadview('surat.penjualan.surat_so', compact('info', 'id')); 
        return [
            'info' => $info,
            'pdf' => $pdf
        ];
    }
}

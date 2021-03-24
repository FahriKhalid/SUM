<?php

namespace App\Services; 
use App\Barang;
use App\SKPP;
use Auth;
use Helper;

class BarangService 
{

	public function store($request, $id, $kategori){
		try {
			$new_produk = [];
            for ($i=0; $i < count($request->new_produk) ; $i++) { 
                if($kategori == "skpp"){
                    $x["id_skpp"] = $id;
                }else{
                    $x["id_pre_order"] = $id;
                }
                $x["id_produk"] = $request->new_produk[$i];
                $x["incoterm"] = $request->new_incoterm[$i];
                $x["kuantitas"] = $request->new_kuantitas[$i];
                $x["harga_jual"] = Helper::decimal($request->new_harga_jual[$i]);
                $x["nilai"] = Helper::decimal($request->new_nilai[$i]);
                $x["created_by"] = Auth::user()->id_user;
                $new_produk[] = $x;
            }
            Barang::insert($new_produk);
		} catch (Exception $e) {
			return response()->json(['status' => 'error', 'message' => $e->getMessage()]); 
		}
	}

	public function update($request){ 
		try {
			for ($i=0; $i < count($request->produk) ; $i++) 
            {    
                $id_barang = Helper::decodex($request->id_po[$i]);
              
                $produk = [
                    "id_produk" => $request->produk[$i],
                    "incoterm" => $request->incoterm[$i],
                    "kuantitas" => $request->kuantitas[$i],
                    "harga_jual" => Helper::decimal($request->harga_jual[$i]),
                    "nilai" => Helper::decimal($request->nilai[$i]),
                    "updated_by" => Auth::user()->id_user,
                ]; 
                Barang::where("id_barang", $id_barang)->update($produk);
            }
		} catch (\Exception $e) {
			return response()->json(['status' => 'error', 'message' => $e->getMessage()]); 
		}
	} 


    public function total_pembayaran($kategori, $id)
    {
        if ($kategori == "penjualan"){
            $total = Barang::where("id_skpp", $id)->sum('nilai');
        } else {
            $total = SKPP::where("id_skpp", $id)->first();  
            if($total){
                $total = $total->total_pembayaran;
            } else {
                $total = 0;
            }
            
        }

        // $total = SKPP::where("id_skpp", $id)->first();   

        // if($total){
        //     $total = $total->total_pembayaran;
        // } else {
        //     $total = 0;
        // } 
        
        return $total;
    }
}

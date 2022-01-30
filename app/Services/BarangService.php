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
                $x["kuantitas"] = Helper::decimal($request->new_kuantitas[$i]);
                $x["harga_jual"] = Helper::decimal($request->new_harga_jual[$i]);
                $x["nilai"] = Helper::decimal($request->new_nilai[$i]);
                $x["created_by"] = Auth::user()->id_user;
                $new_produk[] = $x;
            }
            Barang::insert($new_produk);
		} catch (\Exception $e) {
			throw new \Exception("Tambah produk tidak berhasil ".$e->getMessage(), 1);
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
                    "kuantitas" => Helper::decimal($request->kuantitas[$i]),
                    "harga_jual" => Helper::decimal($request->harga_jual[$i]),
                    "nilai" => Helper::decimal($request->nilai[$i]),
                    "updated_by" => Auth::user()->id_user,
                ]; 
                Barang::where("id_barang", $id_barang)->update($produk);
            }
		} catch (\Exception $e) {
			throw new \Exception("Update produk tidak berhasil ".$e->getMessage(), 1);
		}
	} 


    public function total_pembayaran($kategori, $id)
    { 
        $total = Barang::when($kategori == "penjualan", function($query) use ($id){
                $query->where("id_skpp", $id);
            })->when($kategori == "pembelian", function($query) use ($id){
                $query->where("id_pre_order", $id);
            })->sum('nilai');
        
        return $total;
    }
}

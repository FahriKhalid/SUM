<?php

namespace App\Services;   
use App\Stok;
use App\SOPO;
use Helper;
use Auth;
use DB;

class StokService 
{
    public function add($id_produk, $jumlah)
    {
    	$barang = Stok::where("id_produk", $id_produk)->first();

    	if($barang) // update
    	{  
	    	$barang->jumlah = (int)$barang->jumlah + (int)$jumlah;
	    	$barang->updated_by = Auth::user()->id_user;
	    	$barang->save();
    	}
    	else // add
    	{
    		$stok = new Stok;
	    	$stok->id_produk = $id_produk; 
	    	$stok->jumlah = $jumlah;
	    	$stok->created_by = Auth::user()->id_user;
	    	$stok->save();
    	}
    }   

    public function minus($id_so)
    {
        $sopo = SOPO::where("id_so", $id_so)->get();

        foreach ($sopo as $barang) 
        { 
            $stok = Stok::where("id_produk", $barang->Barang->Produk->id_produk)->first();

            if($stok)
            { 
                if(((int)$stok->jumlah - (int)$barang->kuantitas) < 0){
                    throw new \Exception("Stok tidak dapat dikurangi", 1);
                }
                $stok->jumlah = (int)$stok->jumlah - (int)$barang->kuantitas;
                $stok->save();
            }
        }
    }    

    public function addStok($id_produk, $jumlah_minus)
    {
        $stok = Stok::where("id_produk", $id_produk)->first();
        $stok->jumlah = $stok->jumlah + $jumlah_minus;
        $stok->save();
    }

    public function minusStok($id_produk, $jumlah_minus)
    {
        $stok = Stok::where("id_produk", $id_produk)->first();
 
        if($jumlah_minus > $stok->jumlah){
            throw new \Exception("Error Processing Request", 1);
        }

        $stok->jumlah = $stok->jumlah - $jumlah_minus;
        $stok->save();
    }
}

<?php

namespace App\Services;
use Illuminate\Support\Str;  
use App\LogTransaksi; 
use Auth;
use Helper;

class LogTransaksiService 
{  
    public function storePembelian($id_pre_order, $id_produk, $id_barang, $kuantitas)
    {
        $log = new LogTransaksi;
        $log->id_pre_order = $id_pre_order;
        $log->id_produk = $id_produk;
        $log->id_barang = $id_barang;
        $log->kuantitas = $kuantitas;
        $log->kategori = "plus";
        $log->created_by = Auth::user()->id_user;
        $log->save();
    }

    public function storePenjualan($id_skpp, $id_produk, $id_barang, $kuantitas)
    {
        $log = new LogTransaksi;
        $log->id_skpp = $id_skpp;
        $log->id_produk = $id_produk;
        $log->id_barang = $id_barang;
        $log->kuantitas = $kuantitas;
        $log->kategori = "minus";
        $log->created_by = Auth::user()->id_user;
        $log->save();
    }
}

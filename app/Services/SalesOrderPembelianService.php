<?php

namespace App\Services; 
use App\Services\LogTransaksiService;
use App\Services\PengajuanSoService;
use App\Services\StokService;
use App\Services\SoService;
use App\BarangPengajuanSo;
use App\Barang;
use App\SOPO;
use App\SO;
use Exception;
use Helper;
use Auth;

class SalesOrderPembelianService 
{
    protected $LogTransaksiService, $PengajuanSoService, $StokService, $SoService; 

    public function __construct(
        LogTransaksiService $LogTransaksiService, 
        PengajuanSoService $PengajuanSoService,
        StokService $StokService,
        SoService $SoService)
    {
        $this->LogTransaksiService = $LogTransaksiService; 
        $this->PengajuanSoService = $PengajuanSoService;
        $this->StokService = $StokService;
        $this->SoService = $SoService;  
    }


    public function storeSOPO($request, $id_pre_order, $so)
    {  
        try {
            $data_sopo = [];
            if ($request->is_pengajuan_so == 1) {
                $id_pengajuan_so = Helper::decodex($request->id_pengajuan_so);
                $barang_pengajuan_so = BarangPengajuanSo::where("id_pengajuan_so", $id_pengajuan_so)->get(); 

                foreach ($barang_pengajuan_so as $value) 
                {
                    $this->SoService->validateMaxKuantitasPO($value->id_barang, $value->kuantitas);
                    $this->StokService->add($value->id_produk, $value->kuantitas);
                    $this->LogTransaksiService->storePembelian($id_pre_order, $value->id_produk, $value->id_barang, $value->kuantitas);

                    $data_sopo[] = [
                        "id_barang" => $value->id_barang,
                        "id_so" => $so->id_so,
                        "kuantitas" => Helper::decimal($value->kuantitas),
                        "created_by" => Auth::user()->id_user
                    ];
                }
            } else {
                for ($i=0; $i < count($request->id_barang) ; $i++) 
                { 
                    if(Helper::decimal($request->kuantitas[0]) > 0)
                    { 
                        $id_barang = Helper::decodex($request->id_barang[$i]);
                        $id_produk = Helper::decodex($request->id_produk[$i]);
                        $kuantitas = Helper::decimal($request->kuantitas[$i]);

                        $this->SoService->validateMaxKuantitasPO($id_barang, $kuantitas);
                        $this->StokService->add(Helper::decodex($request->id_produk[$i]), $kuantitas);
                        $this->LogTransaksiService->storePembelian($id_pre_order, $id_produk, $id_barang, $kuantitas);

                        $data_sopo[] = [
                            "id_barang" => $id_barang,
                            "id_so" => $so->id_so,
                            "kuantitas" => $kuantitas,
                            "created_by" => Auth::user()->id_user
                        ];
                    } 
                }
            }
            SOPO::insert($data_sopo); 
        } catch (Exception $e) {
            throw new Exception("Tambah SOPO tidak berhasil ".$e->getMessage(), 1);
        }
    }
 
}

<?php

namespace App\Services;  
use App\Services\SoService;
use App\BarangPengajuanSo;
use App\Barang;
use App\SOPO;
use App\SO;
use Exception;
use Helper;
use Auth;

class SalesOrderPenjualanService 
{
    protected $SoService;

    public function __construct(SoService $SoService)
    {
        $this->SoService = $SoService;
    }

    public function storeSoSementara($request, $id_skpp)
    { 
        try {
            $so = new SO;
            $so->id_skpp = $id_skpp;
            $so->no_so = $this->SoService->lastKodeSo();
            $so->is_sementara = 1;
            $so->id_status = 7; // HOLD
            $so->created_by = Auth::user()->id_user;
            $so->save();

            $data_sopo = [];
            for ($i=0; $i < count($request->id_barang) ; $i++) 
            { 
                if($request->kuantitas[$i] != 0)
                { 
                    $id_barang = Helper::decodex($request->id_barang[$i]); 
                    $this->SoService->validateMaxKuantitasPO($id_barang, $request->kuantitas[$i]);  

                    $data_sopo[] = [
                        "id_barang" => $id_barang,
                        "id_so" => $so->id_so,
                        "kuantitas" => $request->kuantitas[$i],
                        "created_by" => Auth::user()->id_user
                    ];
                } 
            }
            SOPO::insert($data_sopo); 

            return ['id_so' => $so->id_so];
        } catch (Exception $e) {
            throw new Exception("Tambah SOPO tidak berhasil ".$e->getMessage(), 1);
        }
    }
}

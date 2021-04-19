<?php

namespace App\Services; 
use App\Services\SoService;
use App\Barang;
use App\SOPO;
use App\SO;
use Helper;
use Auth; 

class SoPoService 
{
    protected $model, $SoService;

    public function __construct(
        SOPO $SOPO, 
        SoService $SoService
    ){
        $this->model = $SOPO;
        $this->SoService = $SoService;
    }

    public function get($id_so)
    {
        return $this->model::with('SO','Barang')->where("id_so", $id_so)->get();  
    }

    public function store($id_so, $request)
    {
        try {
            $data_sopo = [];
            for ($i=0; $i < count($request->id_po) ; $i++) 
            { 
                if($request->kuantitas[$i] != 0)
                {
                    $id_po = Helper::decodex($request->id_po[$i]);
                    $this->SoService->validateMaxKuantitasPO($id_po, $request->kuantitas[$i]);

                    $data_sopo[] = [
                        "id_barang" => $id_po,
                        "id_so" => $id_so,
                        "kuantitas" => $request->kuantitas[$i],
                        "created_by" => Auth::user()->id_user
                    ];
                } 
            }
            $this->model::insert($data_sopo);
        } catch (\Exception $e) {
            throw new \Exception("Tambah SOPO tidak berhasil. ". $e->getMessage(), 1);
        }
    } 
}

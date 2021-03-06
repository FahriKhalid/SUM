<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\Datatables\Datatables; 
use App\Services\SoService;
use App\Services\SkppService;
use App\Customer;
use App\SupirSO;
use App\Gudang;
use App\Produk;
use App\Status;
use App\Supir;
use App\SKPP;
use App\SOPO;
use App\Barang;
use App\SO;
use Validator;
use Helper;
use Auth;
use PDF;
use DB;

class SalesorderController extends Controller
{
    protected $SoService, $SkppService; 

    public function __construct(SoService $SoService, SkppService $SkppService){
        $this->SoService = $SoService; 
        $this->SkppService = $SkppService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($id)
    {
        $id_skpp = Helper::decodex($id);

        $info["po"] = Barang::where("id_skpp", $id_skpp)->get();

        $info["so"] = SO::where("id_skpp", $id_skpp)->get(); 

        return view('salesorder.index', compact('info', 'id'));
    }

    public function data(SO $SO, Request $request, $id)
    {
        $id_skpp = Helper::decodex($id);

        $data = $SO->query()->where("id_skpp", $id_skpp)->with('CreatedBy', 'Status', 'SupirAktif');

         return Datatables::of($data)->addIndexColumn()->addColumn('action', function ($data){ 
            return '<div class="btn-group btn-group-sm" role="group">
                    <button id="btnGroupDrop1" type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                      Aksi
                    </button>
                    <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                        <a class="dropdown-item detail" href="'.url('/salesorder/show/'.Helper::encodex($data->id_so)).'"><i class="fa fa-search"></i> Detail</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" target="_blank" href="'.url('salesorder/surat_so/'.Helper::encodex($data->id_so)).'"><i class="fa fa-file"></i> Surat SO</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="'.url("salesorder/edit/".Helper::encodex($data->id_so)).'"><i class="fa fa-edit"></i> Edit</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item hapus" url="'.url('salesorder/destroy/'.Helper::encodex($data->id_so)).'"  href="javascript:void(0);"><i class="fa fa-trash"></i> Hapus</a>
                    </div>
                </div>';
        })->addColumn('no_so', function($data){ 
            return '<a href="'.url('/salesorder/show/'.Helper::encodex($data->id_so)).'">'.$data->no_so.'</a>';
        })->addColumn('supir', function($data){ 
            if(count($data->SupirAktif) > 0){
                return $data->SupirAktif[0]->Supir->nama;
            } 
        })->addColumn('alat_angkut', function($data){ 
            if(count($data->SupirAktif) > 0){
                return $data->SupirAktif[0]->Supir->kendaraan;
            }
        })->addColumn('kuantitas', function($data){ 
            return $data->totalKuantitasPO().' MT';
        })->addColumn('status', function($data){ 
            return $data->Status->status;
        })->addColumn('check', function($data){
            return '<div class="checkbox-yajra form-control-lg ">
            <div class="custom-control custom-checkbox checkbox-yajra-custom">
                      <input type="checkbox" class="custom-control-input check-so" no_so="'.$data->no_so.'" id="'.Helper::encodex($data->id_so).'" value="'.Helper::encodex($data->id_so).'">
                      <label class="custom-control-label" for="'.Helper::encodex($data->id_so).'"></label>
                    </div>
            </div>';
        })
        ->rawColumns(['action','check','no_so'])->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($id)
    {
        $id_skpp = Helper::decodex($id);

        $info["skpp"] = SKPP::selectRaw("*, left(no_skpp, 4) as no_dokumen")->findOrFail($id_skpp);

        $info["no_so"] = $this->SoService->lastKodeSo();
 
        $info["customer"] = Customer::where("is_aktif", 1)->get();
        
        $info["po"] = Barang::with('Produk')->where("id_skpp", $id_skpp)->get();

        $info["supir"] = Supir::where("is_aktif", 1)->get(); 

        $info["status"] = Status::where("kategori", "SO")->orderBy("ordering")->get();  

        return view('salesorder.create', compact('id','info'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store($id, Request $request)
    {
        $rules = [
            'id_skpp'               => 'required',
            'nomor_so'              => 'required|unique:tr_so,no_so',
            'supir'                 => 'required|exists:ms_supir,id_supir',
            'tujuan'                => 'required',  
            'status'                => 'required',  
            'id_po'                 => 'required|array',
            'id_po.*'               => 'required|distinct',  
            'kuantitas'             => 'required|array',
            'kuantitas.*'           => 'required|min:1'
        ]; 
 
        $messages = [
            'nomor_so.required'     => 'Nomor sales order wajib diisi', 
            'nomor_so.unique'       => 'Nomor sales order sudah pernah terdaftar pilih nomor sales order yang lain',
            'supir.required'        => 'Penanggung jawab wajib diisi', 
            'status.required'       => 'Status wajib diisi', 
            'supir.exist'           => 'Penanggung jawab tidak valid', 
            'tujuan.required'       => 'Tujuan wajib diisi', 
            'id_po.required'        => 'PO wajib diisi', 
            'kuantitas.required'    => 'kuantitas wajib diisi'

        ];

        $validator = Validator::make($request->all(), $rules, $messages);
 
        if($validator->fails()){ 
            return response()->json(['status' => 'error', 'message' => $validator->errors()->all()]); 
        }

        DB::beginTransaction();
        try {

            // cek total kuantitas tidak boleh kosong
            $this->SoService->validateAllKuantitas($request->kuantitas);

            // insert SO
            $so = new SO();
            $so->id_skpp = Helper::decodex($request->id_skpp);
            $so->no_so = $request->nomor_so; 
            $so->tujuan = $request->tujuan;
            $so->id_status = $request->status;
            $so->created_by = Auth::user()->id_user;
            $so->save();

            // insert SOPO
            $data_sopo = [];
  
            for ($i=0; $i < count($request->id_po) ; $i++) 
            { 
                if($request->kuantitas[$i] != 0)
                {
                    $id_po = Helper::decodex($request->id_po[$i]);
                    $this->SoService->validateMaxKuantitasPO($id_po, $request->kuantitas[$i]);

                    $data_sopo[] = [
                        "id_barang" => $id_po,
                        "id_so" => $so->id_so,
                        "kuantitas" => $request->kuantitas[$i],
                        "created_by" => Auth::user()->id_user
                    ];
                } 
            }

            SOPO::insert($data_sopo);

            // insert Supir PO
            $supir = new SupirSO();
            $supir->id_so = $so->id_so;
            $supir->id_supir = $request->supir;
            $supir->created_by = Auth::user()->id_user;
            $supir->save();

            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'Tambah sales order berhasil']); 

        } catch (\Exception $e) { 
            DB::rollback();
            return response()->json(['status' => 'error', 'message' => 'Error. '. $e->getMessage()]); 
        }
        
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) 
    {
        $id_so = Helper::decodex($id);

        $info["so"] = SO::with("SupirAktif")->findOrFail($id_so); 
        
        $info["sopo"] = SOPO::with('SO','Barang')->where("id_so", $id_so)->get();  

        $info["supir"] = Supir::where("id_supir", "!=", $info["so"]->SupirAktif[0]->id_supir)->get();

        $info["riwayat_supir"] = SupirSO::where("id_so", $id_so)->where("is_aktif", "0")->get();

        return view('salesorder.show', compact('id', 'info'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $id_so = Helper::decodex($id);

        $info["so"] = SO::with("SupirAktif", "SKPP")->findOrFail($id_so);

        $info["so_po"] = SOPO::with("Barang")->where("id_so", $id_so)->get(); 

        $info["skpp"] = SKPP::selectRaw("*, left(no_skpp, 4) as no_dokumen")->findOrFail($info["so"]->SKPP->id_skpp);

        $info["customer"] = Customer::where("is_aktif", 1)->get(); 

        $info["supir"] = Supir::where("is_aktif", 1)->get();  

        return view('salesorder.edit', compact('info', 'id'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {   
        $id_so = Helper::decodex($id);

        $rules = [ 
            'nomor_so'              => 'required|unique:tr_so,no_so,'.$id_so.',id_so',
            'supir'                 => 'required|exists:ms_supir,id_supir',
            'tujuan'                => 'required',   
            'id_supir_so'           => 'required',
            'id_so_po.*'            => 'required|distinct',   
            'kuantitas.*'           => 'required'
        ]; 
 
        $messages = [
            'nomor_so.required'     => 'Nomor sales order wajib diisi', 
            'nomor_so.unique'       => 'Nomor sales order sudah pernah terdaftar pilih nomor sales order yang lain',
            'supir.required'        => 'Penanggung jawab waji diisi', 
            'supir.exist'           => 'Penanggung jawab tidak valid', 
            'tujuan.required'       => 'Tujuan wajib diisi', 
            'id_so_po.required'     => 'PO wajib diisi', 
            'kuantitas.required'    => 'kuantitas wajib diisi'

        ];

        $validator = Validator::make($request->all(), $rules, $messages);
 
        if($validator->fails()){ 
            return response()->json(['status' => 'error', 'message' => $validator->errors()->all()]); 
        }

        DB::beginTransaction();
        try {

            // cek total kuantitas tidak boleh kosong
            $this->SoService->validateAllKuantitas($request->kuantitas);

            // insert SO
            $so = SO::findOrFail($id_so); 
            $so->no_so = $request->nomor_so; 
            $so->tujuan = $request->tujuan;
            $so->updated_by = Auth::user()->id_user;
            $so->save();
 
            for ($i=0; $i < count($request->id_so_po) ; $i++) 
            { 
                if($request->kuantitas[$i] != 0)
                {
                    $id_so_po = Helper::decodex($request->id_so_po[$i]);

                    $sopo = SOPO::findOrFail($id_so_po);
                    $this->SoService->validateMaxKuantitasPO($sopo->id_barang, $request->kuantitas[$i], $id_so_po);
                    $sopo->kuantitas = $request->kuantitas[$i];
                    $sopo->updated_by = Auth::user()->id_user;
                    $sopo->save();
                } 
            }
 
            if($so->SupirAktif[0]->Supir->id_supir != $request->supir){
                // update Supir PO
                $supir = SupirSO::findOrFail(Helper::decodex($request->id_supir_so));
                $supir->id_supir = $request->supir;
                $supir->updated_by = Auth::user()->id_user;
                $supir->save();
            }
            
            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'Update sales order berhasil']); 

        } catch (\Exception $e) { 
            DB::rollback();
            return response()->json(['status' => 'error', 'message' => 'Error. '. $e->getMessage()]); 
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $id = Helper::decodex($id); 

        try {
            SO::findOrFail($id)->delete();
            SOPO::where("id_so", $id)->delete();
            SupirSO::where("id_so", $id)->delete();

            return response()->json(['status' => 'success', 'message' => 'Hapus sales order berhasil']); 
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]); 
        } 
    }

    /**
     * PDF Surat SO
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function surat_so($id)
    {
        $id = Helper::decodex($id);
        
        $info["so"] = SO::with('SKPP')->findOrFail($id);

        $info["sopo"] = SOPO::with('SO','Barang')->where("id_so", $id)->get();  

        $pdf = PDF::loadview('salesorder.surat_so', compact('info', 'id')); 

        return $pdf->setPaper('a4')->stream();
    }
    
    /**
     * Update status SO
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update_status(Request $request)
    { 
        $status = Helper::decodex($request->status);

        DB::beginTransaction();
        try {

            for ($i=0; $i < count($request->id) ; $i++) 
            { 
                $id_so = Helper::decodex($request->id[$i]);
                $so = SO::findOrFail($id_so);

                if($status == 7){
                    $keterangan = "hold";
                } elseif($status == 6) {
                    $keterangan = "on process";
                } elseif ($status == 5) {
                    $keterangan = "delivered";
                }

                $this->SkppService->LogPenjulalan($so->id_skpp, $so->id_status, $status, "Update SO menjadi ". $keterangan);

                $so->id_status = $status;
                $so->updated_by = Auth::user()->id_user;
                $so->save();
            }

            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'Update status sales order berhasil']); 
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => 'error', 'message' => 'Error. '. $e->getMessage()]);
        }
    }


    /**
     * Data html SOPO 
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function sopo($id)
    {
        $id_so = Helper::decodex($id);

        $info["so"] = SO::with('Invoice')->findOrFail($id_so);
        
        $info["sopo"] = SOPO::where("id_so", $id_so)->get();
 
        return response()->json([ 
            'html' => view('salesorder.table_sopo', compact('info'))->render()
        ]); 
    }
}

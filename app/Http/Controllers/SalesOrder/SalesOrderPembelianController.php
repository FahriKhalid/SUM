<?php

namespace App\Http\Controllers\SalesOrder;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables; 
use App\Services\LogTransaksiService;
use App\Services\PembayaranService;
use App\Services\StokService;
use App\Services\SkppService;
use App\Services\SoService;
use App\BarangPengajuanSo;
use App\PengajuanSo;
use App\Customer;
use App\SupirSO;
use App\Barang;
use App\Gudang;
use App\Produk;
use App\Status;
use App\Supir;
use App\SKPP;
use App\SOPO;
use App\SO;
use Validator;
use Helper;
use Auth;
use PDF;
use DB;

class SalesOrderPembelianController extends Controller
{
    protected $LogTransaksiService, $PembayaranService, $StokService, $SoService, $SkppService; 

    public function __construct(
        LogTransaksiService $LogTransaksiService,
        PembayaranService $PembayaranService, 
        StokService $StokService,
        SoService $SoService, 
        SkppService $SkppService)
    {
        $this->LogTransaksiService = $LogTransaksiService;
        $this->PembayaranService = $PembayaranService;
        $this->StokService = $StokService;
        $this->SoService = $SoService; 
        $this->SkppService = $SkppService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }


    /**
     * Display data of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function data(SO $SO, Request $request, $id)
    {
        $id_skpp = Helper::decodex($id);

        $data = $SO->query()->where("id_skpp", $id_skpp)->with('CreatedBy', 'Status', 'SupirAktif');

         return Datatables::of($data)->addIndexColumn()->addColumn('action', function ($data){ 

            $aksi = '';
            if(true)
            {
                $aksi .='<div class="dropdown-divider"></div>
                        <a class="dropdown-item hapus" show="'.url('pembelian/salesorder/show_produk/'.Helper::encodex($data->id_so)).'" url="'.url('pembelian/salesorder/destroy/'.Helper::encodex($data->id_so)).'" href="javascript:void(0);"><i class="fa fa-trash"></i> Hapus</a>';
            }

            return '<div class="btn-group btn-group-sm" role="group">
                    <button id="btnGroupDrop1" type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                      Aksi
                    </button>
                    <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                        <a href="javascript:void(0);" class="dropdown-item detail" url="'.url('pembelian/salesorder/detail/'.Helper::encodex($data->id_so)).'"><i class="fa fa-search"></i> Detail</a>
                        <div class="dropdown-divider"></div> 
                        <a class="dropdown-item" href="'.url("pembelian/salesorder/edit/".Helper::encodex($data->id_so)).'"><i class="fa fa-edit"></i> Edit</a> 
                        '.$aksi.'
                    </div>
                </div>';
        })->addColumn('no_so', function($data){ 
            return $data->no_so;
        })->addColumn('kuantitas', function($data){ 
            return $data->totalKuantitasPO().' MT';
        })->addColumn('status', function($data){ 
            return $data->Status->status;
        })->addColumn('created_by', function($data){ 
            return $data->CreatedBy->nama;
        })->rawColumns(['action','check'])->make(true);
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($id)
    {
        $id_pre_order = Helper::decodex($id);  

        $info["skpp"] = SKPP::selectRaw("*, left(no_skpp, 4) as no_dokumen")->where("id_pre_order", $id_pre_order)->first(); 

        $info["piutang"] = $this->PembayaranService->sisaHutang("pembelian", $info["skpp"]->id_skpp); 

        $info["no_so"] = $this->SoService->lastKodeSo();
 
        $info["customer"] = Customer::where("is_aktif", 1)->get();
        
        $info["po"] = Barang::with('Produk')->where("id_pre_order", $id_pre_order)->get(); 

        $info["supir"] = Supir::where("is_aktif", 1)->get(); 

        $info["status"] = Status::whereIn("status", ["Draft", "Final"])->orderBy("id_status")->get();  

        $info["pengajuan_so"] = PengajuanSo::where("id_pre_order", $id_pre_order)->get();  

        return view('salesorder.pembelian.create', compact('id','info'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $id)
    {
        $id_pre_order = Helper::decodex($request->id_pre_order);

        $rules = [
            'id_skpp'               => 'required',
            'nomor_so'              => 'required|unique:tr_so,no_so,NULL,id_so,deleted_at,NULL', 
            'id_barang'             => 'required|array',
            'id_barang.*'           => 'required|distinct',  
            // 'kuantitas'             => 'required|array',
            // 'kuantitas.*'           => 'required|numeric|min:1',
            'id_pre_order'          => 'required',
            'file'                  => 'required|max:2000|mimes:pdf',
            //'status'                => 'required|in:1,8',
        ]; 
 
        $messages = [
            'nomor_so.required'     => 'Nomor sales order wajib diisi', 
            'nomor_so.unique'       => 'Nomor sales order sudah pernah terdaftar pilih nomor sales order yang lain',
            'supir.required'        => 'Penanggung jawab wajib diisi', 
            'status.required'       => 'Status wajib diisi', 
            'supir.exist'           => 'Penanggung jawab tidak valid', 
            'tujuan.required'       => 'Tujuan wajib diisi', 
            'id_barang.required'    => 'Produk wajib diisi', 
            'kuantitas.required'    => 'kuantitas wajib diisi',
            'kuantitas.*.min'          => 'Kuantitas tidak boleh 0',
            'file.required'      => 'File sales order wajib diisi',
            'file.max'           => 'Ukuran file sales order terlalu besar. Maks 2 Mb',
            'file.mimes'         => 'Ekstensi file sales order tidak valid'

        ];

        if($request->is_pengajuan_so == 1){
            $new_rule = ["id_pengajuan_so" => "required"];
            $new_message = ["id_pengajuan_so.required" => "Nomor pengajuan so wajib dipilih"];
            
            $rules = array_merge($new_rule, $rules);
            $messages = array_merge($new_message, $messages);   
        }

        $validator = Validator::make($request->all(), $rules, $messages);
 
        if($validator->fails()){ 
            return response()->json(['status' => 'error', 'message' => $validator->errors()->all()]); 
        } 
 
        
        DB::beginTransaction();
        try {
 
            // insert SO
            $so = new SO();
            $so->id_skpp = Helper::decodex($request->id_skpp);
            $so->no_so = $request->nomor_so;  
            $so->id_status = $request->status;
            $so->created_by = Auth::user()->id_user;
            // upload file SO
            $namafile = Helper::RemoveSpecialChar($request->nomor_so).'.'.$request->file->getClientOriginalExtension();
            $so->file = $namafile;
            $request->file->move('file_so', $namafile);
            $so->save();
 
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
                        "kuantitas" => $value->kuantitas,
                        "created_by" => Auth::user()->id_user
                    ];
                }
            } else {
                for ($i=0; $i < count($request->id_barang) ; $i++) 
                { 
                    if($request->kuantitas[$i] != 0)
                    { 
                        $id_barang = Helper::decodex($request->id_barang[$i]);
                        $id_produk = Helper::decodex($request->id_produk[$i]);

                        $this->SoService->validateMaxKuantitasPO($id_barang, $request->kuantitas[$i]);
                        $this->StokService->add(Helper::decodex($request->id_produk[$i]), $request->kuantitas[$i]);
                        $this->LogTransaksiService->storePembelian($id_pre_order, $id_produk, $id_barang, $request->kuantitas[$i]);

                        $data_sopo[] = [
                            "id_barang" => $id_barang,
                            "id_so" => $so->id_so,
                            "kuantitas" => $request->kuantitas[$i],
                            "created_by" => Auth::user()->id_user
                        ];
                    } 
                }
            }
            SOPO::insert($data_sopo); 

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
        $id_pre_order = Helper::decodex($id);  

        $info["skpp"] = SKPP::where("id_pre_order", $id_pre_order)->first(); 

        return view('salesorder.pembelian.show', compact('id', 'info'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function detail($id)
    { 
        $id_so = Helper::decodex($id);  

        $info["sales_order"] = SO::findOrFail($id_so);

        return response()->json([
            'html' => view('salesorder.pembelian.detail', compact('id', 'info'))->render()
        ]);
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

        $info["status"] = Status::whereIn("status", ["Draft", "Final"])->orderBy("id_status")->get();  

        return view('salesorder.pembelian.edit', compact('info', 'id'));
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
            'file'                  => 'nullable|sometimes|max:2000|mimes:pdf'
        ]; 
 
        $messages = [
            'nomor_so.required'     => 'Nomor sales order wajib diisi', 
            'nomor_so.unique'       => 'Nomor sales order sudah pernah terdaftar pilih nomor sales order yang lain',  
            'file.max'              => 'Ukuran file sales order terlalu besar. Maks 2 Mb',
            'file.mimes'            => 'Ekstensi file sales order tidak valid'

        ];

        $validator = Validator::make($request->all(), $rules, $messages);
 
        if($validator->fails()){ 
            return response()->json(['status' => 'error', 'message' => $validator->errors()->all()]); 
        }

        DB::beginTransaction();

        try { 
            $so = SO::findOrFail($id_so); 

            if($so->no_so != $request->nomor_so)
            {
                rename("file_so/".$so->file, "file_so/".Helper::RemoveSpecialChar($request->nomor_so).'.pdf');
            }

            $so->no_so = $request->nomor_so;  
            $so->updated_by = Auth::user()->id_user;

            if($request->has('file'))
            {
                if(file_exists('file_so/'.$so->file)){
                    unlink('file_so/'.$so->file);
                }

                // upload file SO
                $namafile = Helper::RemoveSpecialChar($request->nomor_so).'.'.$request->file->getClientOriginalExtension();
                $so->file = $namafile;
                $request->file->move('file_so', $namafile);
            }

            $so->save(); 

            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'Update sales order berhasil']); 

        } catch (\Exception $e) { 
            DB::rollback();
            return response()->json(['status' => 'error', 'message' => 'Error. '. $e->getMessage()]); 
        }
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function showProduk($id)
    {
        $id_so = Helper::decodex($id);

        $info["sales_order"] = SO::findOrFail($id_so);

        return response()->json([
            'html' => view('salesorder.pembelian.detail_produk', compact('id', 'info'))->render()
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $id = Helper::decodex($id); 

        DB::beginTransaction();
        try { 

            $so = SO::findOrFail($id);
            $so->update(['deleted_by' => Auth::user()->id_user]);

            if($request->has('stok_minus'))
            {
                $this->StokService->minus($id);
                $so->update(['is_stok_minus' => '1']);
            } 

            $so->delete();

            $sopo = SOPO::where("id_so", $id);
            $sopo->update(['deleted_by' => Auth::user()->id_user]);
            $sopo->delete();

            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'Hapus sales order berhasil']); 
        } catch (\Exception $e) {

            DB::rollback();
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]); 
        } 
    }
}

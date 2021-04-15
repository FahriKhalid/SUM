<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Yajra\Datatables\Datatables;
use App\Mail\SendEmail;
use App\Services\PengajuanSoService;
use App\BarangPengajuanSo;
use App\PengajuanSo;
use App\Barang;
use App\SKPP;
use Validator;
use Helper;
use Auth;
use PDF;
use DB;

class PengajuanSoController extends Controller
{
    public $PengajuanSoService;

    public function __construct(PengajuanSoService $PengajuanSoService)
    {
        $this->PengajuanSoService = $PengajuanSoService;
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
    public function data(PengajuanSo $PengajuanSo, Request $request, $id)
    {
        $id_pre_order = Helper::decodex($id);

        $data = $PengajuanSo->query()->where("id_pre_order", $id_pre_order)->with('CreatedBy');

        return Datatables::of($data)->addIndexColumn()->addColumn('action', function ($data){ 

            return '<div class="btn-group btn-group-sm" role="group">
                    <button id="btnGroupDrop1" type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                      Aksi
                    </button>
                    <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                        <a class="dropdown-item detail-pengajuan-so" url="'.url('pembelian/pengajuan_so/detail/'.Helper::encodex($data->id_pengajuan_so)).'"><i class="fa fa-search"></i> Detail</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" target="_blank" href="'.url('pembelian/pengajuan_so/surat_pengajuan_so/'.Helper::encodex($data->id_pengajuan_so)).'"><i class="fa fa-download"></i> Download</a>
                        
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="'.url("pembelian/pengajuan_so/edit/".Helper::encodex($data->id_pengajuan_so)).'"><i class="fa fa-edit"></i> Edit</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item delete-pengajuan-so" url="'.url('pembelian/pengajuan_so/destroy/'.Helper::encodex($data->id_pengajuan_so)).'"  href="javascript:void(0);"><i class="fa fa-trash"></i> Hapus</a>
                        
                    </div>
                  </div>';

        })->addColumn('created_by', function($data){ 

            return $data->CreatedBy->nama;
            
        })->rawColumns(['action','pembayaran','no_po'])->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($id)
    {
        $id_pre_order = Helper::decodex($id);  
        $info["no_pengajuan_so"] = $this->PengajuanSoService->lastKodePengajuanPo();
        $info["po"] = Barang::where("id_pre_order", $id_pre_order)->get(); 
        $info["skpp"] = SKPP::where("id_pre_order", $id_pre_order)->first();

        return view('pengajuan_so.create', compact('info', 'id'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $id)
    {
        $id_pre_order = Helper::decodex($id); 

        $rules = [  
            'id_produk.*'           => 'required|distinct',
            'id_barang.*'           => 'required', 
            'kuantitas.*'           => 'required|numeric|min:1',
            'harga_beli.*'          => 'required',
            'nilai.*'               => 'required', 
        ]; 
 
        $messages = [ 
            'id_produk.*.required'     => 'Produk wajib diisi', 
            'id_produk.*.exists'       => 'Produk tidak valid',
            'kuantitas.*.required'     => 'Kuantitas wajib diisi',
            'kuantitas.*.min'          => 'Kuantitas tidak boleh 0',
            'harga_beli.*.required'    => 'Harga beli wajib diisi',
            'nilai.*.required'         => 'Nilai wajib diisi', 
        ];
         
        $validator = Validator::make($request->all(), $rules, $messages);
 
        if($validator->fails()){ 
            return response()->json(['status' => 'error', 'message' => $validator->errors()->all()]); 
        }

        DB::beginTransaction();
        try {
            
            $pso = new PengajuanSo;
            $pso->no_pengajuan_so = $this->PengajuanSoService->lastKodePengajuanPo();
            $pso->id_pre_order = $id_pre_order;
            $pso->created_by = Auth::user()->id_user;
            $pso->save();

            $barang = [];
            for($i=0; $i < count($request->id_produk); $i++)
            {
                $barang[] =[
                    "id_pengajuan_so" => $pso->id_pengajuan_so,
                    "id_produk" => Helper::decodex($request->id_produk[$i]), 
                    "id_barang" => Helper::decodex($request->id_barang[$i]),
                    "kuantitas" => $request->kuantitas[$i],
                    "harga_jual" => Helper::decimal($request->harga_beli[$i]),
                    "nilai" => Helper::decimal($request->nilai[$i]),
                    "created_by" => Auth::user()->id_user
                ];
            }
            BarangPengajuanSo::insert($barang);

            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'Tambah pengajuan sales order berhasil']); 
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]); 
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
        //
    }

     /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function detail($id)
    {
        $id_pengajuan_so = Helper::decodex($id);

        $info["pengajuan_so"] = PengajuanSo::with("PreOrder")->findOrFail($id_pengajuan_so);

        return response()->json(view("pengajuan_so.detail", compact("info", "id"))->render());
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $id_pengajuan_so = Helper::decodex($id);  
        $info["pengajuan_so"] = PengajuanSo::with("PreOrder")->findOrFail($id_pengajuan_so); 
        return view('pengajuan_so.edit', compact('info', 'id'));
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
        $id_pengajuan_so = Helper::decodex($id);

        $rules = [  
            'id_barang_pengajuan_so.*' => 'required|distinct',
            'id_produk.*'           => 'required|distinct',
            'id_barang.*'           => 'required', 
            'kuantitas.*'           => 'required',
            'harga_beli.*'          => 'required',
            'nilai.*'               => 'required', 
        ]; 
 
        $messages = [ 
            'id_produk.*.required'     => 'Produk wajib diisi', 
            'id_produk.*.exists'       => 'Produk tidak valid',
            'kuantitas.*.required'     => 'Kuantitas wajib diisi',
            'harga_beli.*.required'    => 'Harga beli wajib diisi',
            'nilai.*.required'         => 'Nilai wajib diisi', 
        ];
         
        $validator = Validator::make($request->all(), $rules, $messages);
 
        if($validator->fails()){ 
            return response()->json(['status' => 'error', 'message' => $validator->errors()->all()]); 
        }

        try {
            for($i=0; $i < count($request->id_produk); $i++)
            {
                $id_barang_pengajuan_so = Helper::decodex($request->id_barang_pengajuan_so[$i]);

                $barang = [ 
                    "id_produk" => Helper::decodex($request->id_produk[$i]), 
                    "id_barang" => Helper::decodex($request->id_barang[$i]),
                    "kuantitas" => $request->kuantitas[$i],
                    "harga_jual" => Helper::decimal($request->harga_beli[$i]),
                    "nilai" => Helper::decimal($request->nilai[$i]),
                    "updated_by" => Auth::user()->id_user
                ];

                BarangPengajuanSo::findOrFail($id_barang_pengajuan_so)->update($barang);
            }

            return response()->json(['status' => 'success', 'message' => 'Update pengajuan sales order berhasil']); 

        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]); 
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
        $id_pengajuan_so = Helper::decodex($id);
        try {
            PengajuanSo::findOrFail($id_pengajuan_so)->delete();     
            return response()->json(['status' => 'success', 'message' => 'Hapus pengajuan sales order berhasil']); 
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]); 
        }
    }

    public function surat_pengajuan_so($id)
    {
        $id_pengajuan_so = Helper::decodex($id);
        $pdf = $this->PengajuanSoService->suratPengajuanSo($id_pengajuan_so);

        return $pdf["pdf"]->setPaper('a4')->stream(Helper::RemoveSpecialChar($pdf["info"]["pengajuan_so"]->no_pengajuan_so).'.pdf');
    }

    public function table_view($id)
    {
        $id_pengajuan_so = Helper::decodex($id);
        $info["pengajuan_so"] = PengajuanSo::with("PreOrder")->findOrFail($id_pengajuan_so);

        return response()->json(view("pengajuan_so.table_view", compact("info", "id"))->render());
    }

    /**
     * Send email surat pre order
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function send_email($id)
    {   
        try {
            $id_pengajuan_so = Helper::decodex($id);
            $po = PengajuanSo::findOrFail($id_pengajuan_so);
            $email_tujuan = $po->PreOrder->Produsen->email;

            $pdf = $this->PengajuanSoService->suratPengajuanSo($id_pengajuan_so);
            Mail::to($email_tujuan)->send(new SendEmail("PRE ORDER", $pdf["pdf"])); 

            return response()->json(['status' => 'success', 'message' => 'Kirim email ke '.$email_tujuan.' berhasil']); 
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]); 
        }
    }
}

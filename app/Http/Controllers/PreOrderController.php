<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Yajra\Datatables\Datatables; 
use App\Mail\SendEmail;
use App\Services\PembayaranService;
use App\Services\PreOrderService;
use App\Services\LampiranService;
use App\Services\BarangService;
use App\Services\AppService;
use App\RiwayatEmail;
use App\Lampiran;
use App\PreOrder;
use App\Produsen;
use App\Booking;
use App\Barang;
use App\Produk; 
use App\Status;
use Validator;
use Helper;
use Auth;
use PDF;
use DB;

class PreOrderController extends Controller
{

    public  $PembayaranService,
            $LampiranService, 
            $PreOrderService, 
            $BarangService, 
            $AppService;

    public function __construct(
        PembayaranService $PembayaranService,
        LampiranService $LampiranService, 
        PreOrderService $PreOrderService, 
        BarangService $BarangService, 
        AppService $AppService
    )
    {
        $this->PembayaranService = $PembayaranService;
        $this->LampiranService = $LampiranService;
        $this->PreOrderService = $PreOrderService;
        $this->BarangService = $BarangService;
        $this->AppService = $AppService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $info["produsen"] = Produsen::get();
        $info["status_skpp"] = Status::whereIn("id_status", [1,2,3])->get();
        $info["status_pembayaran"] = Status::whereIn("id_status", [9,10,11])->get();
        return view('pre_order.index', compact('info'));
    }


    /**
     * Data of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function data(PreOrder $PreOrder, Request $request)
    {
        $data = $PreOrder->query()->with('CreatedBy','Produsen','Status');

        if($request->no_po != ""){
            $data->where("no_po", "LIKE", "%".$request->no_po."%");
        }
        if($request->no_skpp != ""){ 
            $data->whereHas("SKPP", function($query) use ($request){
                $query->where("no_skpp", "LIKE", "%".$request->no_skpp."%");
            });
        }
        if($request->produsen != ""){ 
            $data->where("id_produsen", Helper::decodex($request->produsen));
        }
        if($request->terakhir_pembayaran != ""){ 
            $tanggal = Helper::dateFormat($request->terakhir_pembayaran, true, 'Y-m-d');
            $data->whereHas("SKPP", function($query) use ($tanggal){
                $query->where("terakhir_pembayaran", $tanggal);
            }); 
        }
        if($request->pembayaran != ""){  
            if(Helper::decodex($request->pembayaran) == 9) {
                $data->whereHas("SKPP", function($query){
                    $query->whereDoesntHave("PembayaranTerakhir");
                });
            } else { 
                if(Helper::decodex($request->pembayaran) == 10){   
                    $data->whereHas("SKPP.PembayaranTerakhir", function($query){ 
                        $query->where("sisa_hutang", ">", 0); 
                    });
                } else {
                    $data->whereHas("SKPP.PembayaranTerakhir", function($query){ 
                        $query->where("sisa_hutang", 0); 
                    });
                }
            } 
        }
        if($request->created_by != ""){ 
            $data->whereHas("CreatedBy", function($query) use ($request){
                $query->where("nama", "LIKE", "%".$request->created_by."%");
            });
        }
        if($request->created_at != ""){ 
            $tanggal = Helper::dateFormat($request->created_at, true, 'Y-m-d');
            $data->where("created_at", "LIKE", "%".$tanggal."%");
        }

        return Datatables::of($data)->addIndexColumn()->addColumn('action', function ($data){ 
            return '<div class="btn-group btn-group-sm" role="group">
                    <button id="btnGroupDrop1" type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                      Aksi
                    </button>
                    <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                        <a class="dropdown-item detail" href="'.url('pembelian/pre_order/show/'.Helper::encodex($data->id_pre_order)).'"><i class="fa fa-search"></i> Detail</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" target="_blank" href="'.url('pembelian/pre_order/surat_po/'.Helper::encodex($data->id_pre_order)).'"><i class="fa fa-download"></i> Download</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="'.url("pembelian/pre_order/edit/".Helper::encodex($data->id_pre_order)).'"><i class="fa fa-edit"></i> Edit</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item hapus" url="'.url('pembelian/pre_order/destroy/'.Helper::encodex($data->id_pre_order)).'"  href="javascript:void(0);"><i class="fa fa-trash"></i> Hapus</a>
                    </div>
                  </div>';
        })->addColumn('no_po', function($data){ 
            return '<a href="'.url('pembelian/pre_order/show/'.Helper::encodex($data->id_pre_order)).'">'.$data->no_po.'</a>';         
        })->addColumn('produsen', function($data){ 
            return $data->Produsen->perusahaan.' - '.$data->Produsen->nama;            
        })->addColumn('skpp', function($data){ 
            return $data->SKPP->no_skpp;            
        })->addColumn('terakhir_pembayaran', function($data){ 
            return $data->SKPP->terakhir_pembayaran;            
        })->addColumn('status_terakhir_pembayaran', function($data){ 
            return Helper::dateWarning($data->SKPP->terakhir_pembayaran);  
        })->addColumn('status', function($data){ 
            return $data->Status->status;            
        })->addColumn('pembayaran', function($data){             
            $skpp = $data->SKPP->no_skpp;            
            if($skpp != '-')
            { 
                if ($data->SKPP->PembayaranTerakhir->sisa_hutang == null) {
                    return 'Belum bayar';
                } elseif($data->SKPP->PembayaranTerakhir->sisa_hutang != null && $data->SKPP->PembayaranTerakhir->sisa_hutang > 00.0){
                    return 'Belum lunas';
                } elseif($data->SKPP->PembayaranTerakhir->sisa_hutang != null && $data->SKPP->PembayaranTerakhir->sisa_hutang == 00.0) {
                    return 'Lunas';
                } 
            } else {
                return '-';
            }             
        })->addColumn('created_by', function($data){ 
            return $data->CreatedBy->nama;            
        })->rawColumns(['action','pembayaran', 'no_po'])->make(true);
    } 
    

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $info["produsen"] = Produsen::get();
        $info["produk"] = Produk::where("is_aktif", 1)->get();
        $info["no_po"] = $this->PreOrderService->lastKodePreOrder();

        return view('pre_order.create', compact('info'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {    
        $rules = [
            'status'                => 'required|in:1,2',
            'no_po'                 => 'required|unique:tr_pre_order,no_po',
            'produsen'              => 'required|exists:ms_produsen,id_produsen', 
            'new_produk.*'              => 'required|exists:ms_produk,id_produk|distinct', 
            'new_kuantitas.*'           => 'required',
            'new_harga_beli.*'          => 'required',
            'new_nilai.*'               => 'required', 
        ]; 
 
        $messages = [
            'status.required'       => 'Status pre order wajib diisi', 
            'no_po.required'        => 'Nomor PO wajib diisi', 
            'no_po.unique'          => 'Nomor PO sudah pernah terdaftar pilih nomor PO yang lain',
            'produsen.required'     => 'Produsen waji diisi', 
            'produsen.exists'       => 'Produsen tidak valid',  
            'produk.*.required'     => 'Produk wajib diisi', 
            'new_produk.*.required'  => 'Kuantitas wajib diisi',
            'new_harga_beli.*.required' => 'Harga beli wajib diisi',
            'new_nilai.*.required'      => 'Nilai wajib diisi', 
        ];
        
        if($request->is_lampiran == 1)
        {
            $rule_lampiran = [
                'new_nama_file.*'         => 'required',
                'new_file.*'              => 'required|max:2000|mimes:doc,docx,pdf,jpg,jpeg,png', 
            ];

            $rules = array_merge($rules, $rule_lampiran);

            $message_lampiran = [
                'new_nama_file.*.required' => 'Nama file wajib diisi',
                'new_file.*.required' => 'File wajib diisi',
                'new_file.*.max' => 'Ukuran file terlalu besar, maks 2 Mb',
                'new_file.*.mimes' => 'Ekstensi file yang diizinkan hanya jpg, jpeg, png, doc, docx dan pdf',
            ];

            $messages = array_merge($messages, $message_lampiran);
        }

        $validator = Validator::make($request->all(), $rules, $messages);
 
        if($validator->fails()){ 
            return response()->json(['status' => 'error_validate', 'message' => $validator->errors()->all()]); 
        }

        DB::beginTransaction();
        try {
            // insert SKPP
            $po = new PreOrder;
            $po->no_po = $request->no_po;
            $po->id_produsen = $request->produsen; 
            $po->id_status = $request->status; 
            $po->created_by = Auth::user()->id_user;
            $po->save();

            // insert po
            $this->BarangService->store($request, $po->id_pre_order, "PRE ORDER");

            if($request->is_lampiran == 1){
                // insert lampiran
                $this->LampiranService->store($request, $po->id_pre_order, Helper::RemoveSpecialChar($po->no_po), "PRE ORDER");
            }

            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'Tambah Pre Order berhasil', 'id_pre_order' => Helper::encodex($po->id_pre_order)]); 
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
        $id_pre_order = Helper::decodex($id);
        $info["pre_order"] = PreOrder::with('Produsen', 'Lampiran')->findOrFail($id_pre_order);
        $info["po"] = Barang::with('Produk')->where("id_pre_order", $id_pre_order)->get(); 
        $info["email"] = $info["pre_order"]->Produsen->email;
        $info["riwayat_email"] = RiwayatEmail::with('UpdatedBy')->where("id_reference", $id_pre_order)->where("kategori", "pre order")->first();
        
        return view('pre_order.show', compact('id', 'info'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $id_pre_order = Helper::decodex($id); 
        $info["produsen"] = Produsen::get();
        $info["produk"] = Produk::where("is_aktif", 1)->get(); 
        $info["barang"] = Barang::where("id_pre_order", $id_pre_order)->get(); 
        $info["pre_order"] = PreOrder::findOrFail($id_pre_order);   
        $info["piutang"] = $this->PembayaranService->sisaHutang("pembelian", $info["pre_order"]->SKPP->id_skpp);   

        return view('pre_order.edit', compact('info', 'id'));
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
        $id_pre_order = Helper::decodex($id);

        $rules = [
            'status'                => 'required|in:1,2',
            'no_po'                 => 'required|unique:tr_pre_order,no_po,'.$id_pre_order.',id_pre_order',
            'produsen'              => 'required|exists:ms_produsen,id_produsen', 
            'produk.*'              => 'required|exists:ms_produk,id_produk|distinct', 
            'kuantitas.*'           => 'required',
            'harga_beli.*'          => 'required',
            'nilai.*'               => 'required', 
        ]; 
 
        $messages = [
            'status.required'       => 'Status pre order wajib diisi', 
            'no_po.required'        => 'Nomor PO wajib diisi', 
            'no_po.unique'          => 'Nomor PO sudah pernah terdaftar pilih nomor PO yang lain',
            'produsen.required'     => 'Produsen waji diisi', 
            'produsen.exists'       => 'Produsen tidak valid',  
            'produk.*.required'     => 'Produk wajib diisi', 
            'kuantitas.*.required'  => 'Kuantitas wajib diisi',
            'harga_beli.*.required' => 'Harga beli wajib diisi',
            'nilai.*.required'      => 'Nilai wajib diisi', 
        ];
        
        if($request->has('new_produk'))
        {
            $rule_new_produk = [
                'new_produk.*'      => 'required|exists:ms_produk,id_produk|distinct',
                'new_incoterm.*'    => 'required' 
            ];

            $messages_new_produk = [ 
                'new_incoterm.*.required'       => 'Incoterm wajib diisi', 
                'new_produk.*.exists'           => 'Produk tidak valid', 
                'new_produk.*.required'     => 'Produk wajib diisi',  
                'new_kuantitas.*.required'      => 'Kuantitas wajib diisi',
                'new_harga_beli.*.required'     => 'Harga beli wajib diisi',
                'new_nilai.*.required'          => 'Nilai wajib diisi',
            ];

            $rules = array_merge($rules, $rule_new_produk); 
            $messages = array_merge($messages, $messages_new_produk);
        } 

        if($request->is_lampiran == 1)
        {
            if($request->has('nama_file')){
                $rule_lampiran = [
                    'nama_file.*'       => 'required',
                    //'file.*'            => 'nullable|max:2000|mimes:doc,docx,pdf,jpg,jpeg,npg', 
                ];
                
                $message_lampiran = [
                    'nama_file.*.required' => 'Nama file wajib diisi',
                    'file.*.required' => 'File wajib diisi',
                    'file.*.max' => 'Ukuran file terlalu besar, maks 2 Mb',
                    'file.*.mimes' => 'Ekstensi file yang diizinkan hanya jpg, jpeg, png, doc, docx dan pdf',
                ];

                $rules = array_merge($rules, $rule_lampiran); 
                $messages = array_merge($messages, $message_lampiran);
            }

            if($request->has('new_nama_file')){
                $new_rule_lampiran = [
                    'new_nama_file.*'       => 'required',
                    'new_file.*'            => 'required|max:2000|mimes:doc,docx,pdf,jpg,jpeg,png', 
                ];

                $new_message_lampiran = [
                    'new_nama_file.*.required' => 'Nama file wajib diisi',
                    'new_file.*.required' => 'File wajib diisi',
                    'new_file.*.max' => 'Ukuran file terlalu besar, maks 2 Mb',
                    'new_file.*.mimes' => 'Ekstensi file yang diizinkan hanya jpg, jpeg, png, doc, docx dan pdf',
                ];

                $rules = array_merge($rules, $new_rule_lampiran);
                $messages = array_merge($messages, $new_message_lampiran); 
            }
        }

        $validator = Validator::make($request->all(), $rules, $messages);
 
        if($validator->fails()){ 
            return response()->json(['status' => 'error_validate', 'message' => $validator->errors()->all()]); 
        }

        DB::beginTransaction();
        try {

            // insert SKPP
            $po = PreOrder::findOrFail($id_pre_order);
            $po->no_po = $request->no_po;
            $po->id_produsen = $request->produsen; 
            $po->id_status = $request->status; 
            $po->created_by = Auth::user()->id_user;
            $po->save();

            // update po
            $this->BarangService->update($request, $id_pre_order);
 
            // insert po
            if($request->has('new_produk')){
                $this->BarangService->store($request, $id_pre_order, "po");
            }

            // lampiran
            $nama_file = Helper::RemoveSpecialChar($this->PreOrderService->nomorPo($id_pre_order));
            $this->LampiranService->call($request, $id_pre_order, $nama_file, "PRE ORDER");
             
            DB::commit();
            return response()->json(['status' => 'success','message' => 'Update Pre Order berhasil']); 
        } catch (\Exception $e) {
            DB::rollback();
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
        try {
            $pre_order = PreOrder::findOrFail(Helper::decodex($id));
            $pre_order->update(['deleted_by' => Auth::user()->id_user]);
            $pre_order->delete();
            return response()->json(['status' => 'success', 'message' => 'Hapus Po berhasil']); 
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]); 
        }
    }

    public function surat_po($id)
    {
        $id = Helper::decodex($id); 
        $pdf = $this->PreOrderService->suratPreOrder($id);
        
        return $pdf["pdf"]->setPaper('a4')->stream(Helper::RemoveSpecialChar($pdf["info"]["pre_order"]->no_po).'.pdf'); 
    }

    /**
     * Send email surat pre order
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function send_email($id)
    {   
        DB::beginTransaction();
        try {
            $id_po = Helper::decodex($id);
            $po = PreOrder::findOrFail($id_po);
            $email_tujuan = $po->Produsen->email;

            $lampiran = [];
            if($po->Lampiran != null && count($po->Lampiran) > 0){
                foreach ($po->Lampiran as $value) {
                    $x["name_file"] = $value->file;
                    $x["url_file"] = asset('lampiran/'.$value->file);
                    $lampiran[] = $x;
                }  
            }

            $pdf = $this->PreOrderService->suratPreOrder($id_po);
            Mail::to($email_tujuan)->send(new SendEmail("PRE ORDER", $pdf["pdf"], $lampiran)); 
            $this->AppService->storeRiwayatEmail($id_po, "pre order");

            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'Kirim email ke '.$email_tujuan.' berhasil']); 
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]); 
        }
    }
}

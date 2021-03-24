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
use App\Lampiran;
use App\PreOrder;
use App\Produsen;
use App\Booking;
use App\Barang;
use App\Produk; 
use Validator;
use Helper;
use Auth;
use PDF;
use DB;

class PreOrderController extends Controller
{

    public $PreOrderService, $LampiranService, $BarangService, $PembayaranService;

    public function __construct(
        PreOrderService $PreOrderService, 
        LampiranService $LampiranService, 
        BarangService $BarangService, 
        PembayaranService $PembayaranService)
    {
        $this->PreOrderService = $PreOrderService;
        $this->LampiranService = $LampiranService;
        $this->BarangService = $BarangService;
        $this->PembayaranService = $PembayaranService;
    }

    public function data(PreOrder $PreOrder, Request $request)
    {
        $data = $PreOrder->query()->with('CreatedBy','Produsen','Status','SKPP');

        return Datatables::of($data)->addIndexColumn()->addColumn('action', function ($data){ 

            $aksi = '';
            // if($data->id_status < 2){
                $aksi .= '<div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="'.url("pembelian/pre_order/edit/".Helper::encodex($data->id_pre_order)).'"><i class="fa fa-edit"></i> Edit</a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item hapus" url="'.url('pembelian/pre_order/destroy/'.Helper::encodex($data->id_pre_order)).'"  href="javascript:void(0);"><i class="fa fa-trash"></i> Hapus</a>';
            //}

            return '<div class="btn-group btn-group-sm" role="group">
                    <button id="btnGroupDrop1" type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                      Aksi
                    </button>
                    <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                        <a class="dropdown-item detail" href="'.url('pembelian/pre_order/show/'.Helper::encodex($data->id_pre_order)).'"><i class="fa fa-search"></i> Detail</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" target="_blank" href="'.url('pembelian/pre_order/surat_po/'.Helper::encodex($data->id_pre_order)).'"><i class="fa fa-download"></i> Download</a>
                        
                        '.$aksi.'
                        
                    </div>
                  </div>';

        })->addColumn('produsen', function($data){ 

            return $data->Produsen->perusahaan;
            
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
                $pembayaran = $this->PembayaranService->sisaHutang("pembelian", $data->SKPP->id_skpp);   

                if ($pembayaran == null) {
                    return 'Belum dibayar';
                } elseif($pembayaran > 0){
                    return 'Belum lunas';
                } elseif($pembayaran == 00.0) {
                    return 'Lunas';
                } 
            } else {
                return '-';
            } 
            
        })->addColumn('created_by', function($data){ 

            return $data->CreatedBy->nama;
            
        })->rawColumns(['action','pembayaran','no_po'])->make(true);
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('pre_order.index');
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
        
        if($request->is_lampiran == 1)
        {
            $rule_lampiran = [
                'nama_file.*'         => 'required',
                'file.*'              => 'required|max:2000|mimes:doc,docx,pdf', 
            ];

            $rules = array_merge($rules, $rule_lampiran);

            $message_lampiran = [
                'nama_file.*.required' => 'Nama file wajib diisi',
                'file.*.required' => 'File wajib diisi',
                'file.*.max' => 'Ukuran file terlalu besar, maks 2 Mb',
                'file.*.mimes' => 'Ekstensi file yang diizinkan hanya doc, docx dan pdf',
            ];

            $messages = array_merge($messages, $message_lampiran);
        }

        $validator = Validator::make($request->all(), $rules, $messages);
 
        if($validator->fails()){ 
            return response()->json(['status' => 'error', 'message' => $validator->errors()->all()]); 
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
            $produk = [];
            for ($i=0; $i < count($request->produk) ; $i++) { 
                $x["id_pre_order"] = $po->id_pre_order;
                $x["id_produk"] = $request->produk[$i];
                $x["incoterm"] = $request->incoterm[$i];
                $x["kuantitas"] = $request->kuantitas[$i]; 
                $x["harga_jual"] = Helper::decimal($request->harga_beli[$i]);
                $x["nilai"] = Helper::decimal($request->nilai[$i]);
                $x["created_by"] = Auth::user()->id_user;
                $produk[] = $x;
            }
            Barang::insert($produk);

             // insert lampiran
            if($request->is_lampiran == 1){
                $lampiran = [];
                $file = $request->file('file');
                for ($i=0; $i < count($file) ; $i++) { 
                    $namafile = 'lampiran-'.Str::random(8).'.'.$file[$i]->getClientOriginalExtension();
                    $z["id_pre_order"] = $po->id_pre_order;
                    $z["nama"] = $request->nama_file[$i];
                    $z["file"] = $namafile;
                    $z["size"]  = $file[$i]->getSize(); 
                    $z["ekstensi"] = $file[$i]->getClientOriginalExtension();
                    $z["keterangan"] = $request->keterangan_file[$i];
                    $z["created_by"] = Auth::user()->id_user; 
                    $lampiran[] = $z; 
                    $tujuan_upload = 'lampiran'; 
                    $file[$i]->move($tujuan_upload, $namafile);
                }
                Lampiran::insert($lampiran);
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

        $info["pre_order"] = PreOrder::with('Produsen')->findOrFail($id_pre_order);

        $info["po"]         = Barang::with('Produk')->where("id_pre_order", $id_pre_order)->get();

        $info["lampiran"]   = Lampiran::where("id_pre_order", $id_pre_order)->get(); 
        
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
                    //'file.*'            => 'nullable|max:2000|mimes:doc,docx,pdf', 
                ];
                
                $message_lampiran = [
                    'nama_file.*.required' => 'Nama file wajib diisi',
                    'file.*.required' => 'File wajib diisi',
                    'file.*.max' => 'Ukuran file terlalu besar, maks 2 Mb',
                    'file.*.mimes' => 'Ekstensi file yang diizinkan hanya doc, docx dan pdf',
                ];

                $rules = array_merge($rules, $rule_lampiran); 
                $messages = array_merge($messages, $message_lampiran);
            }

            if($request->has('new_nama_file')){
                $new_rule_lampiran = [
                    'new_nama_file.*'       => 'required',
                    'new_file.*'            => 'required|max:2000|mimes:doc,docx,pdf', 
                ];

                $new_message_lampiran = [
                    'new_nama_file.*.required' => 'Nama file wajib diisi',
                    'new_file.*.required' => 'File wajib diisi',
                    'new_file.*.max' => 'Ukuran file terlalu besar, maks 2 Mb',
                    'new_file.*.mimes' => 'Ekstensi file yang diizinkan hanya doc, docx dan pdf',
                ];

                $rules = array_merge($rules, $new_rule_lampiran);
                $messages = array_merge($messages, $new_message_lampiran); 
            }
        }

        $validator = Validator::make($request->all(), $rules, $messages);
 
        if($validator->fails()){ 
            return response()->json(['status' => 'error', 'message' => $validator->errors()->all()]); 
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

            // delete all attachment
            if(!$request->has('is_lampiran'))
            {
                $this->LampiranService->destroy($id_pre_order, "po");
            }
            else
            {
                // update attachment
                if($request->has('nama_file')){ 
                    $this->LampiranService->update($request);
                }

                // store attachment
                if($request->has('new_file')){
                    $this->LampiranService->store($request, $id_pre_order, "po");
                }
            }
             

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

        $info["pre_order"]          = PreOrder::with('CreatedBy','Produsen','Status')->findOrFail($id);
        $info["po"]                 = Barang::with('Produk')->where("id_pre_order", $id)->get();
        $info["lampiran"]           = Lampiran::where("id_pre_order", $id)->get();
        $info["profil_perusahaan"]  = DB::table("ms_profil_perusahaan")->first();

        $pdf = PDF::loadview('pre_order.surat_po', compact('info')); 
        return $pdf->setPaper('a4')->stream(); 
    }
}

<?php

namespace App\Http\Controllers\SKPP;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request; 
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Yajra\Datatables\Datatables; 
use App\Services\LogTransaksiService;
use App\Services\PembayaranService;
use App\Services\LampiranService;
use App\Services\SkppAtmService;
use App\Services\BarangService;
use App\Services\StokService;
use App\Services\SkppService;
use App\Services\AppService;
use App\Mail\SendEmail;
use App\RiwayatEmail;
use App\Customer;
use App\Lampiran;
use App\Produk;
use App\Status;
use App\SKPP;
use App\ATM;
use App\Barang;
use Validator;
use Helper;
use Auth;
use PDF;
use DB;

class SkppPenjualanController extends Controller
{
    protected $LogTransaksiService, 
                $PembayaranService, 
                $LampiranService, 
                $SkppAtmService, 
                $BarangService, 
                $SkppService, 
                $StokService, 
                $AppService;

    protected $draft, $confirm, $approve, $unapprove;

    public function __construct(
        LogTransaksiService $LogTransaksiService,
        PembayaranService $PembayaranService,
        LampiranService $LampiranService, 
        SkppAtmService $SkppAtmService,
        BarangService $BarangService, 
        SkppService $SkppService,
        StokService $StokService,
        AppService $AppService
    ){
        $this->LogTransaksiService = $LogTransaksiService;
        $this->PembayaranService = $PembayaranService;
        $this->LampiranService = $LampiranService;
        $this->SkppAtmService = $SkppAtmService;
        $this->BarangService = $BarangService;
        $this->SkppService = $SkppService;
        $this->StokService = $StokService;
        $this->AppService = $AppService;
        $this->draft = 1;
        $this->confirm = 2;
        $this->approve = 3;
        $this->unapprove = 4;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $info["customer"] = Customer::get();
        $info["status_skpp"] = Status::whereIn("id_status", [1,2,3])->get();
        $info["status_pembayaran"] = Status::whereIn("id_status", [9,10,11])->get();
        return view('skpp.penjualan.index', compact('info'));
    }


    public function data(SKPP $SKPP, Request $request)
    {
        $data = $SKPP->query()->with('CreatedBy','Customer','Status','Pembayaran')->where("kategori", "penjualan");

        if($request->no_skpp != ""){
            $data->where("no_skpp", "LIKE", "%".$request->no_skpp."%");
        }
        if($request->customer != ""){ 
            $data->where("id_customer", Helper::decodex($request->customer));
        }
        if($request->terakhir_pembayaran != ""){ 
            $tanggal = Helper::dateFormat($request->terakhir_pembayaran, true, 'Y-m-d');
            $data->where("terakhir_pembayaran", $tanggal);
        }
        if($request->status != ""){ 
            $data->where("id_status", Helper::decodex($request->status));
        }
        if($request->pembayaran != ""){ 

            if(Helper::decodex($request->pembayaran) == 9) {
                $data->whereDoesntHave("PembayaranTerakhir");
            } else {
                $data->whereHas("PembayaranTerakhir", function($query) use ($request){
                    if(Helper::decodex($request->pembayaran) == 10){
                        $query->where("sisa_hutang", ">", 00.0); 
                    } else {
                        $query->where("sisa_hutang", 00.0); 
                    }
                });
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

            $aksi = '';
            if($data->id_status == 1){
                $aksi .= '<div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="'.url("penjualan/skpp/edit/".Helper::encodex($data->id_skpp)).'"><i class="fa fa-edit"></i> Edit</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item hapus" url="'.url('penjualan/skpp/destroy/'.Helper::encodex($data->id_skpp)).'"  href="javascript:void(0);"><i class="fa fa-trash"></i> Hapus</a>';
            }

            return '<div class="btn-group btn-group-sm" role="group">
                    <button id="btnGroupDrop1" type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                      Aksi
                    </button>
                    <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                        <a class="dropdown-item detail" href="'.url('penjualan/skpp/show/'.Helper::encodex($data->id_skpp)).'"><i class="fa fa-search"></i> Detail</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" target="_blank" href="'.url('penjualan/skpp/preview/'.Helper::encodex($data->id_skpp)).'"><i class="fa fa-download"></i> Download</a>
                        '.$aksi.'
                    </div>
                  </div>';

        })->addColumn('no_skpp', function($data){ 
            return '<a href="'.url('penjualan/skpp/show/'.Helper::encodex($data->id_skpp)).'">'.$data->no_skpp.'</a>';
        })->addColumn('customer', function($data){ 
            return $data->Customer->perusahaan;
        })->addColumn('status', function($data){ 
            return $data->Status->status;            
        })->addColumn('terakhir_pembayaran', function($data){ 
            return $data->terakhir_pembayaran;            
        })->addColumn('status_terakhir_pembayaran', function($data){ 
            return Helper::dateWarning($data->terakhir_pembayaran);  
        })->addColumn('pembayaran', function($data){ 
            if ($data->PembayaranTerakhir->sisa_hutang == null) {
                return 'Belum dibayar';
            } elseif($data->PembayaranTerakhir->sisa_hutang != null && $data->PembayaranTerakhir->sisa_hutang > 00.0){
                return 'Belum lunas';
            } elseif($data->PembayaranTerakhir->sisa_hutang != null && $data->PembayaranTerakhir->sisa_hutang == 00.0) {
                return 'Lunas';
            } 
        })->addColumn('created_by', function($data){ 
            return $data->CreatedBy->nama;
        })->rawColumns(['action','pembayaran','no_skpp'])->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $info["customer"] = Customer::get();
        $info["produk"] = Produk::where("is_aktif", 1)->get();
        $info["no_skpp"] = $this->SkppService->lastKodeSkpp(); 
        $info["atm"] = ATM::where("is_aktif", 1)->get();
        return view('skpp.penjualan.create', compact("info"));
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
            'status'                => 'required',
            'nomor_skpp'            => 'required|unique:tr_skpp,no_skpp',
            'customer'              => 'required|exists:ms_customer,id_customer',
            'syarat_penyerahan'     => 'required',
            'batas_akhir_pengambilan'     => 'required',    
            'produk.*'              => 'required|exists:ms_produk,id_produk|distinct',
            'incoterm.*'            => 'required',
            'kuantitas.*'           => 'required|numeric|min:1',
            'harga_jual.*'          => 'required',
            'nilai.*'               => 'required',
            'atm.*'                 => 'required',
        ]; 
 
        $messages = [
            'status.required'               => 'Status tidak valid',
            'nomor_skpp.required'           => 'Nomor SKPP wajib diisi', 
            'nomor_skpp.unique'             => 'Nomor SKPP sudah pernah terdaftar pilih nomor SKPP yang lain',
            'customer.required'             => 'Customer waji diisi', 
            'customer.exists'               => 'Customer tidak valid',
            'syarat_penyerahan.required'    => 'Gudang pengambilan wajib diisi',
            'batas_akhir_pengambilan.required'    => 'Batas akhir pengambilan wajib diisi', 
            'incoterm.*.required'           => 'Incoterm wajib diisi',
            'produk.*.required'             => 'Produk wajib diisi', 
            'kuantitas.*.required'          => 'Kuantitas wajib diisi',
            'kuantitas.*.min'               => 'Kuantitas tidak boleh 0',
            'harga_jual.*.required'         => 'Harga jual wajib diisi',
            'nilai.*.required'              => 'Nilai wajib diisi',
            'atm.*.required'                  => 'ATM wajib diisi',
            'atm.*.exists'                    => 'ATM tidak valid'
        ];

        if($request->is_lampiran == 1){
            $rule_lampiran = [
                'nama_file.*'         => 'required',
                'file.*'              => 'required|max:2000|mimes:doc,docx,pdf,jpg,jpeg,png', 
            ];

            $rules = array_merge($rules, $rule_lampiran);

            $message_lampiran = [
                'nama_file.*.required' => 'Nama file wajib diisi',
                'file.*.required' => 'File wajib diisi',
                'file.*.max' => 'Ukuran file terlalu besar, maks 2 Mb',
                'file.*.mimes' => 'Ekstensi file yang diizinkan hanya jpg, jpeg, png, doc, docx dan pdf',
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
            $skpp = new SKPP;
            $skpp->kategori = "penjualan";
            $skpp->no_skpp = $request->nomor_skpp;
            $skpp->id_customer = $request->customer;
            $skpp->syarat_penyerahan = $request->syarat_penyerahan;
            $skpp->jadwal_penyerahan = $request->jadwal_penyerahan;
            $skpp->batas_akhir_pengambilan = $request->batas_akhir_pengambilan == null ? null : Helper::dateFormat($request->batas_akhir_pengambilan, true, 'Y-m-d');
            $skpp->terakhir_pembayaran = Helper::dateFormat($request->batas_akhir_pembayaran, true, 'Y-m-d');
            $skpp->biaya_ongkir = $request->ongkir != null ? Helper::decimal($request->ongkir) : null;
            $skpp->created_by = Auth::user()->id_user;
            $skpp->id_status = $request->status;
            $skpp->total_pembayaran = $this->SkppService->requestTotalPembayaran($request); 
            $skpp->save();

            // insert po
            $this->BarangService->store($request, $skpp->id_skpp, "skpp");

            // insert atm
            $this->SkppAtmService->store($request, $skpp->id_skpp);

            if($request->is_lampiran == 1){
                // insert lampiran
                $this->LampiranService->store($request, $skpp->id_skpp, Helper::RemoveSpecialChar($skpp->no_skpp), "SKPP");
            } 

            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'Tambah SKPP berhasil', 'id_skpp' => Helper::encodex($skpp->id_skpp)]); 
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => 'error', 'message' => $e->getMessage().' '. $e->getLine()]); 
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
        $id_skpp = Helper::decodex($id); 
        $info["kategori"]   = "penjualan";
        $info["skpp"]       = SKPP::with('CreatedBy','Customer','Status', 'SKPPATM', 'Lampiran')->findOrFail($id_skpp);
        $info["po"]         = Barang::with('Produk')->where("id_skpp", $id_skpp)->get(); 
        $info["email"]      = $info["skpp"]->Customer->email;
        $info["riwayat_email"] = RiwayatEmail::with('UpdatedBy')->where("id_reference", $id_skpp)->where("kategori", "skpp")->first();

        return view('skpp.penjualan.show', compact('info','id'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $id_skpp = Helper::decodex($id);
        $info["skpp"]  = SKPP::with('CreatedBy','Customer','Status','Lampiran')->findOrFail($id_skpp);
        $info["customer"] = Customer::get();
        $info["produk"] = Produk::where("is_aktif", 1)->get();
        $info["po"] = Barang::where("id_skpp", $id_skpp)->get(); 
        $info["atm"] = ATM::where("is_aktif", 1)->get();

        $info["id_atm"] = [];
        foreach ($info["skpp"]->SKPPATM as $value) {
            $info["id_atm"][] = $value->id_atm;
        }

        return view('skpp.penjualan.edit', compact('info','id'));
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
        $id =  Helper::decodex($id);

        $rules = [
            'status'                => 'required',
            'nomor_skpp'            => 'required|unique:tr_skpp,no_skpp,'.$id.',id_skpp',
            'customer'              => 'required|exists:ms_customer,id_customer',
            'syarat_penyerahan'     => 'required',
            'jadwal_penyerahan'     => 'required',    
            'produk.*'              => 'required|exists:ms_produk,id_produk|distinct',
            'incoterm.*'            => 'required',
            'kuantitas.*'           => 'required',
            'harga_jual.*'          => 'required',
            'nilai.*'               => 'required', 
            'atm.*'                 => 'required',
        ]; 
 
        $messages = [
            'status.required'               => 'Status tidak valid',
            'nomor_skpp.required'           => 'Nomor SKPP wajib diisi', 
            'nomor_skpp.unique'             => 'Nomor SKPP sudah pernah terdaftar pilih nomor SKPP yang lain',
            'customer.required'             => 'Customer waji diisi', 
            'customer.exist'                => 'Customer tidak valid',
            'syarat_penyerahan.required'    => 'Gudang pengambilan wajib diisi',
            'jadwal_penyerahan.required'    => 'Jadwal penyerahan wajib diisi', 
            'incoterm.*.required'           => 'Incoterm wajib diisi', 
            'produk.*.exists'               => 'Produk tidak valid', 
            'new_produk.*.required'         => 'Produk wajib diisi', 
            'new_produk.*.exists'           => 'Produk tidak valid', 
            'kuantitas.*.required'          => 'Kuantitas wajib diisi',
            'harga_jual.*.required'         => 'Harga jual wajib diisi',
            'nilai.*.required'              => 'Nilai wajib diisi',
            'atm.*.required'                  => 'ATM wajib diisi',
            'atm.*.exists'                    => 'ATM tidak valid'
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
                'new_produk.*.required'         => 'Produk wajib diisi',  
                'new_kuantitas.*.required'      => 'Kuantitas wajib diisi',
                'new_harga_jual.*.required'     => 'Harga jual wajib diisi',
                'new_nilai.*.required'          => 'Nilai wajib diisi',
            ];

            $rules = array_merge($rules, $rule_new_produk); 
            $messages = array_merge($messages, $messages_new_produk);
        }  
            
        if($request->is_lampiran == 1)
        {
            $rule_lampiran = [
                'nama_file.*'       => 'required', 
            ];
            
            $message_lampiran = [
                'nama_file.*.required' => 'Nama file wajib diisi',
                'file.*.required' => 'File wajib diisi',
                'file.*.max' => 'Ukuran file terlalu besar, maks 2 Mb',
                'file.*.mimes' => 'Ekstensi file yang diizinkan hanya jpg, jpeg, png, doc, docx dan pdf',
            ];

            $rules = array_merge($rules, $rule_lampiran); 
            $messages = array_merge($messages, $message_lampiran);

            if($request->has('new_nama_file')){
                $new_rule_lampiran = [
                    'new_nama_file.*'       => 'required',
                    'new_file.*'            => 'required|max:2000|mimes:doc,docx,pdf,png,jpg,jpeg', 
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
            $total_pembayaran = 0;
            for ($j=0; $j < count($request->nilai); $j++) { 
                $total_pembayaran += Helper::decimal($request->nilai[$j]);
            }
            // update skpp
            $skpp = SKPP::findOrFail($id);
            $skpp->no_skpp = $request->nomor_skpp;
            $skpp->id_customer = $request->customer;
            $skpp->syarat_penyerahan = $request->syarat_penyerahan;
            $skpp->jadwal_penyerahan = $request->jadwal_penyerahan;
            $skpp->terakhir_pembayaran = Helper::dateFormat($request->batas_akhir_pembayaran, true, 'Y-m-d');
            $skpp->batas_akhir_pengambilan = $request->batas_akhir_pengambilan == null ? null : Helper::dateFormat($request->batas_akhir_pengambilan, true, 'Y-m-d');
            $skpp->biaya_ongkir = $request->ongkir != null ? Helper::decimal($request->ongkir) : null;
            $skpp->updated_by = Auth::user()->id_user;
            $skpp->id_status = $request->status;
            $skpp->total_pembayaran = $total_pembayaran; 
            $skpp->save();
    
            // update atm
            $this->SkppAtmService->update($request, $id);

            // update po
            $this->BarangService->update($request, $id);
            
            // insert po
            if($request->has('new_produk')){
                $this->BarangService->store($request, $id, "skpp");
            }

            //lampiran
            $nama_file = Helper::RemoveSpecialChar($this->SkppService->nomorSkpp($id));
            $this->LampiranService->call($request, $id, $nama_file, "SKPP"); 

            $info["customer"]   = Customer::get();
            $info["produk"]     = Produk::where("is_aktif", 1)->get();
            $info["po"]         = Barang::where("id_skpp", $id)->get(); 
            $info["lampiran"]   = $skpp->Lampiran;

            DB::commit();
            return response()->json([   
                'status' => 'success', 
                'message' => 'Update SKPP berhasil', 
                'form_edit_lampiran' => view('skpp.penjualan.form_edit_lampiran', compact('info'))->render(),
                'form_edit_po' => view('skpp.penjualan.form_edit_po', compact('info'))->render(),
            ]); 
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => 'error', 'message' => $e->getMessage().' Line '.$e->getLine()]); 
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
            $skpp = SKPP::findOrFail(Helper::decodex($id));
            $skpp->update(['deleted_by' => Auth::user()->id_user]);
            $skpp->delete();
            return response()->json(['status' => 'success', 'message' => 'Hapus SKPP berhasil']); 
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]); 
        }
    }

    public function preview($id)
    { 
        $pdf = $this->SkppService->suratSKPP(Helper::decodex($id));
        return $pdf["pdf"]->setPaper('a4')->stream(Helper::RemoveSpecialChar($pdf["info"]["skpp"]->no_skpp).'.pdf'); 
    }

    public function confirm($id)
    {
        $id_skpp = Helper::decodex($id); 
        DB::beginTransaction();
        try {
            $update = SKPP::findOrFail($id_skpp);
            $this->SkppService->LogPenjulalan($id_skpp, $update->id_status, $this->confirm, "SKPP di confirm");
            $update->id_status = $this->confirm;
            $update->catatan_revisi = null;
            $update->save(); 

            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'Konfirmasi SKPP berhasil']); 
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]); 
        }
    }

    public function revisi(Request $request, $id)
    {
        $rules = [ 
            'catatan_revisi'  => 'required'
        ]; 
 
        $messages = [
            'catatan_revisi.required'   => 'Catatn revisi wajib diisi' 
        ];

        $validator = Validator::make($request->all(), $rules, $messages);
 
        if($validator->fails()){ 
            return response()->json(['status' => 'error', 'message' => $validator->errors()->all()]); 
        }

        $id_skpp = Helper::decodex($id);

        DB::beginTransaction();
        try {
            $update = SKPP::findOrFail($id_skpp);  
            $this->SkppService->LogPenjulalan($id_skpp, $update->id_status, $this->draft, $request->catatan_revisi);
            $this->SkppService->addStok($id_skpp);
            $update->id_status = $this->draft;
            $update->catatan_revisi = $request->catatan_revisi;
            $update->save();

            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'Revisi SKPP berhasil']); 
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);    
        }
    }


    public function approve(Request $request, $id)
    {
        $id_skpp = Helper::decodex($id); 
        DB::beginTransaction();
        try {
            $skpp = SKPP::with('Customer')->findOrFail($id_skpp);
            $this->SkppService->LogPenjulalan($id_skpp, $skpp->id_status, $this->approve, "SKPP di approve");
            $skpp->id_status = $this->approve;
            $skpp->catatan_revisi = null;
            $skpp->save(); 

            $this->SkppService->minusStok($id_skpp);

            if($request->is_send_email && $request->is_send_email == 1){
               $this->send_email($id);
            }
            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'Approve SKPP berhasil']); 
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]); 
        }
 
    }

    public function unapprove(Request $request, $id)
    {
        $id_skpp = Helper::decodex($id); 
        DB::beginTransaction();
        try {
            $data = SKPP::with('Customer')->findOrFail($id_skpp);
            $data->id_status = 2;
            $data->catatan_revisi = null;
            $data->save();

            $this->SkppService->addStok($id_skpp);

            if($request->is_send_email && $request->is_send_email == 1){
               //$this->send_email($id);
            }

            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'Unapprove SKPP berhasil']); 
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]); 
        }
    }
    
    /**
     * Send email SKPP
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function send_email($id)
    {   
        DB::beginTransaction();
        try {
            $id_skpp = Helper::decodex($id);
            $skpp = SKPP::findOrFail($id_skpp);
            $email_tujuan = $skpp->Customer->email;

            $lampiran = [];
            if($skpp->Lampiran != null && count($skpp->Lampiran) > 0){
                foreach ($skpp->Lampiran as $value) {
                    $x["name_file"] = $value->file;
                    $x["url_file"] = asset('lampiran/'.$value->file);
                    $lampiran[] = $x;
                }  
            }
            $pdf = $this->SkppService->suratSKPP($id_skpp); 
            Mail::to($email_tujuan)->send(new SendEmail("SKPP", $pdf["pdf"], $lampiran)); 
            $this->AppService->storeRiwayatEmail($id_skpp, "skpp");

            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'Kirim email ke '.$email_tujuan.' berhasil']); 
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]); 
        }
    }
}

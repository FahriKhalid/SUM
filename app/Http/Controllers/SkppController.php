<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Yajra\Datatables\Datatables; 
use App\Services\LampiranService;
use App\Services\SkppService;
use App\Services\BarangService;
use App\Mail\SendEmail;
use App\Customer;
use App\Lampiran;
use App\Produk;
use App\SKPP;
use App\ATM;
use App\Barang;
use Validator;
use Helper;
use Auth;
use PDF;
use DB;

class SkppController extends Controller
{

    protected $LampiranService, $BarangService, $SkppService;
    protected $draft, $confirm, $approve, $unapprove;

    public function __construct(LampiranService $LampiranService, BarangService $BarangService, SkppService $SkppService){
        $this->LampiranService = $LampiranService;
        $this->BarangService = $BarangService;
        $this->SkppService = $SkppService;
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
        return view('skpp.index');
    }


    public function data(SKPP $SKPP, Request $request)
    {
        $data = $SKPP->query()->with('CreatedBy','Customer','Status','Pembayaran')->where("kategori", "penjualan");

        return Datatables::of($data)->addIndexColumn()->addColumn('action', function ($data){ 

            return '<div class="btn-group btn-group-sm" role="group">
                    <button id="btnGroupDrop1" type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                      Aksi
                    </button>
                    <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                        <a class="dropdown-item detail" href="'.url('skpp/show/'.Helper::encodex($data->id_skpp)).'"><i class="fa fa-search"></i> Detail</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" target="_blank" href="'.url('skpp/preview/'.Helper::encodex($data->id_skpp)).'"><i class="fa fa-download"></i> Download</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="'.url("skpp/edit/".Helper::encodex($data->id_skpp)).'"><i class="fa fa-edit"></i> Edit</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item hapus" url="'.url('skpp/destroy/'.Helper::encodex($data->id_skpp)).'"  href="javascript:void(0);"><i class="fa fa-trash"></i> Hapus</a>
                    </div>
                  </div>';

        })->addColumn('customer', function($data){ 

            return $data->Customer->perusahaan;
            
        })->addColumn('no_skpp', function($data){ 

            return '<a href="'.url('skpp/show/'.Helper::encodex($data->id_skpp)).'">'.$data->no_skpp.'</a>';
            
        })->addColumn('status', function($data){ 

            return $data->Status->status;
            
        })->addColumn('pembayaran', function($data){ 

            if ($data->Pembayaran == null) {
                return 'Belum dibayar';
            } elseif($data->Pembayaran != null && $data->Pembayaran->sisa_hutang > 0){
                return 'Belum lunas';
            } elseif($data->Pembayaran != null && $data->Pembayaran->sisa_hutang == 00.0) {
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
        return view('skpp.create', compact("info"));
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
            'nomor_skpp'            => 'required|unique:tr_skpp,no_skpp',
            'customer'              => 'required|exists:ms_customer,id_customer',
            'syarat_penyerahan'     => 'required',
            'jadwal_penyerahan'     => 'required',    
            'produk.*'              => 'required|exists:ms_produk,id_produk|distinct',
            'incoterm.*'            => 'required',
            'kuantitas.*'           => 'required',
            'harga_jual.*'          => 'required',
            'nilai.*'               => 'required',
            'atm'                   => 'required|exists:tr_atm,id_atm',
        ]; 
 
        $messages = [
            'nomor_skpp.required'   => 'Nomor SKPP wajib diisi', 
            'nomor_skpp.unique'     => 'Nomor SKPP sudah pernah terdaftar pilih nomor SKPP yang lain',
            'customer.required'     => 'Customer waji diisi', 
            'customer.exists'        => 'Customer tidak valid',
            'syarat_penyerahan.required' => 'Syarat penyerahan wajib diisi',
            'jadwal_penyerahan.required' => 'Jadwal penyerahan wajib diisi', 
            'incoterm.*.required'   => 'Incoterm wajib diisi',
            'produk.*.required'     => 'Produk wajib diisi', 
            'kuantitas.*.required'  => 'Kuantitas wajib diisi',
            'harga_jual.*.required' => 'Harga jual wajib diisi',
            'nilai.*.required'      => 'Nilai wajib diisi',
            'atm.required'          => 'ATM wajib diisi',
            'atm.exists'             => 'ATM tidak valid'
        ];

        if($request->is_lampiran == 1){
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

            $total_pembayaran = 0;
            for ($j=0; $j < count($request->nilai); $j++) { 
                $total_pembayaran += Helper::decimal($request->nilai[$j]);
            }

            // insert SKPP
            $skpp = new SKPP;
            $skpp->kategori = "penjualan";
            $skpp->no_skpp = $request->nomor_skpp;
            $skpp->id_customer = $request->customer;
            $skpp->syarat_penyerahan = $request->syarat_penyerahan;
            $skpp->jadwal_penyerahan = $request->jadwal_penyerahan;
            $skpp->batas_akhir_pengambilan = $request->batas_akhir_pengambilan == null ? null : Helper::dateFormat($request->batas_akhir_pengambilan, true, 'Y-m-d');
            $skpp->biaya_ongkir = $request->ongkir != null ? Helper::decimal($request->ongkir) : null;
            $skpp->created_by = Auth::user()->id_user;
            $skpp->id_status = $request->is_draft == 1 ? 1 : 2;
            $skpp->total_pembayaran = $total_pembayaran;
            $skpp->id_atm = $request->atm;
            $skpp->save();

            // insert po
            $produk = [];
            for ($i=0; $i < count($request->produk) ; $i++) { 
                $x["id_skpp"] = $skpp->id_skpp;
                $x["id_produk"] = $request->produk[$i];
                $x["incoterm"] = $request->incoterm[$i];
                $x["kuantitas"] = $request->kuantitas[$i];
                $x["harga_jual"] = Helper::decimal($request->harga_jual[$i]);
                $x["nilai"] = Helper::decimal($request->nilai[$i]);
                $x["created_by"] = Auth::user()->id_user;
                $produk[] = $x;
            }
            DB::table("tr_barang")->insert($produk);

             // insert po
            if($request->is_lampiran == 1){
                $lampiran = [];
                $file = $request->file('file');
                for ($i=0; $i < count($file) ; $i++) { 
                    $namafile = 'lampiran-'.Str::random(8).'.'.$file[$i]->getClientOriginalExtension();
                    $z["id_skpp"] = $skpp->id_skpp;
                    $z["nama"] = $request->nama_file[$i];
                    $z["file"] = $namafile;
                    $z["size"]  = $file[$i]->getSize(); 
                    $z["ekstensi"] = $file[$i]->getClientOriginalExtension();
                    $z["keterangan"] = $request->keterangan_file[$i];
                    $z["created_by"] = Auth::user()->id_user; 
                    $lampiran[] = $z; 

                    $tujuan_upload = 'lampiran';
                    // upload file
                    $file[$i]->move($tujuan_upload, $namafile);
                }
                DB::table("tr_lampiran")->insert($lampiran);
            }

            DB::commit();

            return response()->json(['status' => 'success', 'message' => 'Tambah SKPP berhasil', 'id_skpp' => Helper::encodex($skpp->id_skpp)]); 
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
        $id_skpp = Helper::decodex($id);

        $info["kategori"]   = "penjualan";

        $info["skpp"]       = SKPP::with('CreatedBy','Customer','Status')->findOrFail($id_skpp);

        $info["po"]         = Barang::with('Produk')->where("id_skpp", $id_skpp)->get();

        $info["lampiran"]   = Lampiran::where("id_skpp", $id_skpp)->get();

        return view('skpp.show', compact('info','id'));
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

        $info["skpp"]  = SKPP::with('CreatedBy','Customer','Status')->findOrFail($id_skpp);

        $info["customer"] = Customer::get();

        $info["produk"] = Produk::where("is_aktif", 1)->get();

        $info["po"] = Barang::where("id_skpp", $id_skpp)->get(); 

        $info["lampiran"] = Lampiran::where("id_skpp", $id_skpp)->get();

        $info["atm"] = ATM::where("is_aktif", 1)->get();

        return view('skpp.edit', compact('info','id'));
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
            'nomor_skpp'            => 'required|unique:tr_skpp,no_skpp,'.$id.',id_skpp',
            'customer'              => 'required|exists:ms_customer,id_customer',
            'syarat_penyerahan'     => 'required',
            'jadwal_penyerahan'     => 'required',    
            'produk.*'              => 'required|exists:ms_produk,id_produk|distinct',
            'incoterm.*'            => 'required',
            'kuantitas.*'           => 'required',
            'harga_jual.*'          => 'required',
            'nilai.*'               => 'required', 
            'atm'                   => 'required|exists:tr_atm,id_atm',
        ]; 
 
        $messages = [
            'nomor_skpp.required'           => 'Nomor SKPP wajib diisi', 
            'nomor_skpp.unique'             => 'Nomor SKPP sudah pernah terdaftar pilih nomor SKPP yang lain',
            'customer.required'             => 'Customer waji diisi', 
            'customer.exist'                => 'Customer tidak valid',
            'syarat_penyerahan.required'    => 'Syarat penyerahan wajib diisi',
            'jadwal_penyerahan.required'    => 'Jadwal penyerahan wajib diisi', 
            'incoterm.*.required'           => 'Incoterm wajib diisi', 
            'produk.*.exists'               => 'Produk tidak valid', 
            'new_produk.*.required'         => 'Produk wajib diisi', 
            'new_produk.*.exists'           => 'Produk tidak valid', 
            'kuantitas.*.required'          => 'Kuantitas wajib diisi',
            'harga_jual.*.required'         => 'Harga jual wajib diisi',
            'nilai.*.required'              => 'Nilai wajib diisi',
            'atm.required'                  => 'ATM wajib diisi',
            'atm.exists'                    => 'ATM tidak valid'
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
                'file.*'            => 'required|max:2000|mimes:doc,docx,pdf', 
            ];
            
            $message_lampiran = [
                'nama_file.*.required' => 'Nama file wajib diisi',
                'file.*.required' => 'File wajib diisi',
                'file.*.max' => 'Ukuran file terlalu besar, maks 2 Mb',
                'file.*.mimes' => 'Ekstensi file yang diizinkan hanya doc, docx dan pdf',
            ];

            $rules = array_merge($rules, $rule_lampiran); 
            $messages = array_merge($messages, $message_lampiran);

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
        
        try {
            // update skpp
            $skpp = SKPP::findOrFail($id);
            $skpp->no_skpp = $request->nomor_skpp;
            $skpp->id_customer = $request->customer;
            $skpp->syarat_penyerahan = $request->syarat_penyerahan;
            $skpp->jadwal_penyerahan = $request->jadwal_penyerahan;
            $skpp->batas_akhir_pengambilan = $request->batas_akhir_pengambilan == null ? null : Helper::dateFormat($request->batas_akhir_pengambilan, true, 'Y-m-d');
            $skpp->biaya_ongkir = $request->ongkir != null ? Helper::decimal($request->ongkir) : null;
            $skpp->updated_by = Auth::user()->id_user;
            $skpp->id_status = $request->is_draft == 1 ? 1 : 2;
            $skpp->id_atm = $request->atm;
            $skpp->save();
 
            // update po
            $this->BarangService->update($request, $id);
 
            // insert po
            if($request->has('new_produk')){
                $this->BarangService->store($request, $id, "skpp");
            }

            // delete all attachment
            if(!$request->has('is_lampiran'))
            {
                $this->LampiranService->destroy($id, "skpp");
            }
            else
            {
                // update attachment
                if($request->has('nama_file')){ 
                    $this->LampiranService->update($request);
                }

                // store attachment
                if($request->has('new_file')){
                    $this->LampiranService->store($request, $id, "skpp");
                }
            }
            
            $info["customer"]   = Customer::get();
            $info["produk"]     = Produk::where("is_aktif", 1)->get();
            $info["po"]         = Barang::where("id_skpp", $id)->get(); 
            $info["lampiran"]   = Lampiran::where("id_skpp", $id)->get();

            return response()->json([   
                'status' => 'success', 
                'message' => 'Update SKPP berhasil', 
                'form_edit_lampiran' => view('skpp.form_edit_lampiran', compact('info'))->render(),
                'form_edit_po' => view('skpp.form_edit_po', compact('info'))->render(),
            ]); 

        } catch (\Exception $e) {
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
            SKPP::findOrFail(Helper::decodex($id))->delete();
            return response()->json(['status' => 'success', 'message' => 'Hapus SKPP berhasil']); 
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]); 
        }
    }

    public function preview($id)
    {
        $id = Helper::decodex($id);

        $info["skpp"]               = SKPP::with('CreatedBy','Customer','Status','ATM')->findOrFail($id);

        $info["po"]                 = Barang::with('Produk')->where("id_skpp", $id)->get();

        $info["lampiran"]           = Lampiran::where("id_skpp", $id)->get();

        $info["profil_perusahaan"]  = DB::table("ms_profil_perusahaan")->first();

        $pdf = PDF::loadview('skpp.penjualan.surat_skpp', compact('info')); 

        return $pdf->setPaper('a4')->stream(); 
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
        try {
            $update = SKPP::with('Customer')->findOrFail($id_skpp);
            $this->SkppService->LogPenjulalan($id_skpp, $update->id_status, $this->approve, "SKPP di approve");

            $update->id_status = $this->approve;
            $update->catatan_revisi = null;
            $update->save();

            if($request->is_send_email && $request->is_send_email == 1){
               $this->send_email($update);
            }

            return response()->json(['status' => 'success', 'message' => 'Approve SKPP berhasil']); 
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]); 
        }
 
    }

    public function unapprove(Request $request, $id)
    {
        $id = Helper::decodex($id); 
        try {
            $data = SKPP::with('Customer')->findOrFail($id);
            $data->id_status = 2;
            $data->catatan_revisi = null;
            $data->save();

            if($request->is_send_email && $request->is_send_email == 1){
               $this->send_email($data);
            }

            return response()->json(['status' => 'success', 'message' => 'Unapprove SKPP berhasil']); 
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]); 
        }
    }
    

    public function send_email($skpp)
    {
        try {
            
            // $to_name = $skpp->Customer->nama;
            // $to_email =  $skpp->Customer->email;
            // $data = array('name'=> "SKPP", "body" => "A test mail");

            Mail::to("fahri.halid@gmail.com")->send(new SendEmail());
             
            if (Mail::failures()) {
                return response()->json(['status' => 'success', 'message' => 'Kirim email ke '.$to_email.' tidak berhasil']); 
            }else{
                return response()->json(['status' => 'success', 'message' => 'Kirim email ke '.$to_email.' berhasil']); 
            }
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]); 
        }
    }
}










<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Yajra\Datatables\Datatables;
use App\Services\PengajuanSoService;
use App\Services\LampiranService;
use App\Services\AppService;
use App\Mail\SendEmail;
use App\BarangPengajuanSo;
use App\RiwayatEmail;
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
    public $PengajuanSoService, $LampiranService, $AppService;

    public function __construct(
        PengajuanSoService $PengajuanSoService,
        LampiranService $LampiranService,
        AppService $AppService
    ){
        $this->PengajuanSoService = $PengajuanSoService;
        $this->LampiranService = $LampiranService;
        $this->AppService = $AppService;
    }

    
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return view('pengajuan_so.show', compact('id'));
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
        })->addColumn('kuantitas', function($data){ 
            return $data->totalKuantitasBarangPengajuanSo().' MT';  
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
            'kuantitas.*'           => 'required|min:1',
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
                    "kuantitas" => Helper::decimal($request->kuantitas[$i]),
                    "incoterm" => $request->incoterm[$i],
                    "harga_jual" => Helper::decimal($request->harga_beli[$i]),
                    "nilai" => Helper::decimal($request->nilai[$i]),
                    "created_by" => Auth::user()->id_user
                ];
            }
            BarangPengajuanSo::insert($barang);

            if($request->is_lampiran == 1){ 
                $this->LampiranService->store($request, $pso->id_pengajuan_so, Helper::RemoveSpecialChar($pso->no_pengajuan_so), "PENGAJUAN SO");
            } 

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
    public function detail($id)
    {
        $id_pengajuan_so = Helper::decodex($id);
        $info["pengajuan_so"] = PengajuanSo::with("PreOrder")->findOrFail($id_pengajuan_so);
        $info["riwayat_email"] = RiwayatEmail::with('UpdatedBy')->where("id_reference", $id_pengajuan_so)->where("kategori", "pengajuan so")->first();

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
        $info["pengajuan_so"] = PengajuanSo::with("PreOrder", "Lampiran")->findOrFail($id_pengajuan_so); 

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
            for($i=0; $i < count($request->id_produk); $i++)
            {
                $id_barang_pengajuan_so = Helper::decodex($request->id_barang_pengajuan_so[$i]); 
                $barang = [ 
                    "id_produk"     => Helper::decodex($request->id_produk[$i]), 
                    "id_barang"     => Helper::decodex($request->id_barang[$i]),
                    "kuantitas"     => Helper::decimal($request->kuantitas[$i]),
                    "harga_jual"    => Helper::decimal($request->harga_beli[$i]),
                    "nilai"         => Helper::decimal($request->nilai[$i]),
                    "updated_by"    => Auth::user()->id_user
                ]; 

                BarangPengajuanSo::findOrFail($id_barang_pengajuan_so)->update($barang);
            }

            // delete all attachment
            if($request->is_lampiran != 1)
            {
                $this->LampiranService->destroy($id_pengajuan_so, "PENGAJUAN SO");
            }
            else if($request->is_lampiran == 1)
            {
                $nama_file = Helper::RemoveSpecialChar($this->PengajuanSoService->NomorPengajuanSO($id_pengajuan_so));

                // update attachment
                if($request->has('nama_file')){ 
                    $this->LampiranService->update($request, $nama_file);
                }

                // store attachment
                if($request->has('new_file')){
                    $this->LampiranService->store($request, $id_pengajuan_so, $nama_file, "PENGAJUAN SO");
                }
            }

            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'Update pengajuan sales order berhasil']); 
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
        DB::beginTransaction();
        try {
            $id_pengajuan_so = Helper::decodex($id);
            $po = PengajuanSo::findOrFail($id_pengajuan_so);
            $email_tujuan = $po->PreOrder->Produsen->email;

            $lampiran = [];
            if($po->Lampiran != null && count($po->Lampiran) > 0){
                foreach ($po->Lampiran as $value) {
                    $x["name_file"] = $value->file;
                    $x["url_file"] = asset('lampiran/'.$value->file);
                    $lampiran[] = $x;
                }  
            }

            $pdf = $this->PengajuanSoService->suratPengajuanSo($id_pengajuan_so);
            Mail::to($email_tujuan)->send(new SendEmail("PRE ORDER", $pdf["pdf"], $lampiran)); 
            $this->AppService->storeRiwayatEmail($id_pengajuan_so, "pengajuan so");

            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'Kirim email ke '.$email_tujuan.' berhasil']); 
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]); 
        }
    }
}

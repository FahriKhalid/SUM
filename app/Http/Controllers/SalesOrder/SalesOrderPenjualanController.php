<?php

namespace App\Http\Controllers\SalesOrder;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request; 
use Yajra\Datatables\Datatables;
use App\Services\RiwayatEmailService; 
use App\Services\LampiranService;
use App\Services\SupirSoService;
use App\Services\SkppService;
use App\Services\SoPoService;
use App\Services\AppService;
use App\Services\SoService;
use App\Mail\SendEmail;
use App\RiwayatEmail;
use App\Customer;
use App\Lampiran;
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

class SalesOrderPenjualanController extends Controller
{
    protected $RiwayatEmailService, $LampiranService, $SupirSoService, $SoPoService, $SoService, $SkppService, $AppService; 

    public function __construct(
        RiwayatEmailService $RiwayatEmailService,
        LampiranService $LampiranService, 
        SupirSoService $SupirSoService,
        SoPoService $SoPoService,
        SkppService $SkppService,
        AppService $AppService,
        SoService $SoService
    ){
        $this->RiwayatEmailService = $RiwayatEmailService;
        $this->LampiranService = $LampiranService;
        $this->SupirSoService = $SupirSoService;
        $this->SoPoService = $SoPoService;
        $this->SkppService = $SkppService;
        $this->AppService = $AppService;
        $this->SoService = $SoService; 
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
        
        return view('salesorder.penjualan.index', compact('info', 'id'));
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
                        <a class="dropdown-item detail" href="'.url('penjualan/salesorder/show/'.Helper::encodex($data->id_so)).'"><i class="fa fa-search"></i> Detail</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" target="_blank" href="'.url('penjualan/salesorder/surat_so/'.Helper::encodex($data->id_so)).'"><i class="fa fa-download"></i> Download</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="'.url("penjualan/salesorder/edit/".Helper::encodex($data->id_so)).'"><i class="fa fa-edit"></i> Edit</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item hapus" url="'.url('penjualan/salesorder/destroy/'.Helper::encodex($data->id_so)).'"  href="javascript:void(0);"><i class="fa fa-trash"></i> Hapus</a>
                    </div>
                </div>';
        })->addColumn('no_so', function($data){ 
            return '<a href="'.url('penjualan/salesorder/show/'.Helper::encodex($data->id_so)).'">'.$data->no_so.'</a>';
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

        return view('salesorder.penjualan.create', compact('id','info'));
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
            'nomor_so_pengambilan'  => 'required',
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
            'nomor_so_pengambilan.required'  => 'Nomor sales order pengaambilan wajib diisi',
            'supir.required'        => 'Penanggung jawab wajib diisi', 
            'status.required'       => 'Status wajib diisi', 
            'supir.exist'           => 'Penanggung jawab tidak valid', 
            'tujuan.required'       => 'Tujuan wajib diisi', 
            'id_po.required'        => 'PO wajib diisi', 
            'kuantitas.required'    => 'kuantitas wajib diisi',
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

            // cek total kuantitas tidak boleh kosong
            $this->SoService->validateAllKuantitas($request->kuantitas);

            // insert SO
            $so = new SO();
            $so->id_skpp = Helper::decodex($request->id_skpp);
            $so->no_so = $request->nomor_so; 
            $so->no_so_pengambilan = $request->nomor_so_pengambilan; 
            $so->tujuan = $request->tujuan;
            $so->id_status = $request->status;
            $so->created_by = Auth::user()->id_user;
            $so->save();

            // insert SOPO
            $this->SoPoService->store($so->id_so, $request);

            // insert Supir PO
            $this->SupirSoService->store($so->id_so, $request);

            // insert lampiran
            if($request->is_lampiran == 1){
                $this->LampiranService->store($request, $so->id_so, Helper::RemoveSpecialChar($so->no_so), "SALES ORDER");
            } 

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
        
        $info["so"] = SO::with("SupirAktif", "Lampiran")->findOrFail($id_so); 
        $info["sopo"] = $this->SoPoService->get($id_so);
        $info["riwayat_supir"] = $this->SupirSoService->get($id_so);
        $info["email"] = $info["so"]->SKPP->Customer->email;
        $info["riwayat_email"] = $this->RiwayatEmailService->first($id_so, "SALES ORDER");
        if($info["so"]->is_sementara != 1){
            $info["supir"] = Supir::where("id_supir", "!=", $info["so"]->SupirAktif[0]->id_supir)->get();
        }

        return view('salesorder.penjualan.show', compact('id', 'info'));
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
        $info["so_po"] = $this->SoPoService->get($id_so);
        $info["skpp"] = SKPP::selectRaw("*, left(no_skpp, 4) as no_dokumen")->findOrFail($info["so"]->SKPP->id_skpp);
        $info["customer"] = Customer::where("is_aktif", 1)->get(); 
        $info["supir"] = Supir::where("is_aktif", 1)->get();  

        return view('salesorder.penjualan.edit', compact('info', 'id'));
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
            'nomor_so'              => 'required|unique:tr_so,no_so,'.$id_so.',id_so,deleted_at,NULL',
            'nomor_so_pengambilan'  => 'required',
            'supir'                 => 'required|exists:ms_supir,id_supir',
            'tujuan'                => 'required',   
            'id_supir_so'           => 'nullable',
            'id_so_po.*'            => 'required|distinct',   
            'kuantitas.*'           => 'required'
        ]; 
 
        $messages = [
            'nomor_so.required'     => 'Nomor sales order wajib diisi', 
            'nomor_so.unique'       => 'Nomor sales order sudah pernah terdaftar pilih nomor sales order yang lain',
            'nomor_so_pengambilan.required'  => 'Nomor sales order pengaambilan wajib diisi',
            'supir.required'        => 'Penanggung jawab waji diisi', 
            'supir.exist'           => 'Penanggung jawab tidak valid', 
            'tujuan.required'       => 'Tujuan wajib diisi', 
            'id_so_po.required'     => 'PO wajib diisi', 
            'kuantitas.required'    => 'kuantitas wajib diisi'

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

            // cek total kuantitas tidak boleh kosong
            $this->SoService->validateAllKuantitas($request->kuantitas);

            // insert SO
            $so = SO::findOrFail($id_so); 
            $so->is_sementara = 0;
            $so->no_so = $request->nomor_so; 
            $so->no_so_pengambilan = $request->nomor_so_pengambilan; 
            $so->tujuan = $request->tujuan;
            $so->updated_by = Auth::user()->id_user;
            $so->save(); 

            // Update SOPO
            $this->SoPoService->update($request);

            // update Supir PO 
            $this->SupirSoService->update($so, $request); 
            
            // lampiran
            $nama_file = Helper::RemoveSpecialChar($this->SoService->nomor($id_so));
            $this->LampiranService->call($request, $id_so, $nama_file, "SALES ORDER"); 
            
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
        $pdf = $this->SoService->suratSo($id);
        return $pdf["pdf"]->setPaper('a4')->stream(Helper::RemoveSpecialChar($pdf["info"]["so"]->no_so.'.pdf'));
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
            'html' => view('salesorder.penjualan.table_sopo', compact('info'))->render()
        ]); 
    }


    /**
     * Send email surat SO
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function send_email($id)
    {  
        DB::beginTransaction();
        try {
            $id_so = Helper::decodex($id);
            $so = SO::findOrFail($id_so);
            $email_tujuan = $so->SKPP->Customer->email;

            $lampiran = [];
            if($so->Lampiran != null && count($so->Lampiran) > 0){
                foreach ($so->Lampiran as $value) {
                    $x["name_file"] = $value->file;
                    $x["url_file"] = asset('lampiran/'.$value->file);
                    $lampiran[] = $x;
                }  
            }

            $pdf = $this->SoService->suratSo($id_so);
            Mail::to($email_tujuan)->send(new SendEmail("SALES ORDER", $pdf["pdf"], $lampiran)); 
            $this->AppService->storeRiwayatEmail($id_so, "sales order");

            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'Kirim email ke '.$email_tujuan.' berhasil']); 
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]); 
        }
    }
}

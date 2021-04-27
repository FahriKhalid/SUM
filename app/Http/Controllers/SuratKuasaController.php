<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables; 
use App\Services\SuratKuasaService;
use App\Services\LampiranService;
use App\Services\AppService;
use App\Mail\SendEmail;
use App\RiwayatEmail;
use App\SuratKuasa;
use App\Gudang;
use App\Supir;
use App\SOPO;
use App\SKSO;
use App\SO;
use Validator;
use Helper;
use Auth;
use PDF;
use DB;

class SuratKuasaController extends Controller
{
    protected $SuratKuasaService;
    protected $LampiranService;
    protected $AppService;

    public function __construct(
        SuratKuasaService $SuratKuasaService,
        LampiranService $LampiranService,
        AppService $AppService
    ){
        $this->SuratKuasaService = $SuratKuasaService;
        $this->LampiranService = $LampiranService;
        $this->AppService = $AppService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($id)
    {    
        $info["so"] = SO::with('SKPP')->findOrFail(Helper::decodex($id));  
        $info["email"] = $info["so"]->SKPP->Customer->email;
        return view('surat_kuasa.index', compact('info', 'id'));
    }

    
    /**
     * Display data of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function data(SuratKuasa $suratKuasa, Request $request, $id)
    {
        $id_so = Helper::decodex($id);
        $data = $suratKuasa->query()->where("id_so", $id_so)->with('CreatedBy', 'Status', 'Supir', 'Gudang');
        return Datatables::of($data)->addIndexColumn()->addColumn('action', function ($data){ 

            return '<div class="btn-group btn-group-sm" role="group">
                    <button id="btnGroupDrop1" type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                      Aksi
                    </button>
                    <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                        <a class="dropdown-item detail" url="'.url('surat_kuasa/show/'.Helper::encodex($data->id_sk)).'" href="javascript:void(0);"><i class="fa fa-search"></i> Detail</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" target="_blank" href="'.url('surat_kuasa/surat_kuasa/'.Helper::encodex($data->id_sk)).'"><i class="fa fa-download"></i> Download</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="'.url("surat_kuasa/edit/".Helper::encodex($data->id_sk)).'"><i class="fa fa-edit"></i> Edit</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item hapus" url="'.url('surat_kuasa/destroy/'.Helper::encodex($data->id_sk)).'"  href="javascript:void(0);"><i class="fa fa-trash"></i> Hapus</a>
                    </div>
                  </div>';

        })->addColumn('supir', function($data){ 
            return $data->Supir->nama;
        })->addColumn('kendaraan', function($data){ 
            return $data->Supir->kendaraan;
        })->addColumn('gudang', function($data){             
            return $data->Gudang->nama;            
        })->addColumn('kuantitas', function($data){             
            return Helper::currency($data->totalKuantitasPO());            
        })->addColumn('created_by', function($data){             
            return $data->CreatedBy->nama;            
        })->rawColumns(['action'])->make(true);
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($id)
    {
        $id_so = Helper::decodex($id);
        $info["so"] = SO::with("SKPP")->findOrFail($id_so); 
        $info["no_sk"] = $this->SuratKuasaService->lastKodeSk(); 
        $info["gudang"] = Gudang::with("Produsen")->where("is_aktif", 1)->get();
        $info["supir"] = Supir::where("is_aktif", 1)->get();
        $info["so_po"] = SOPO::with("SO")->where("id_so", $id_so)->get();   
     
        return view('surat_kuasa.create', compact('info','id'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $id)
    {
        $rules = [
            'nomor_sk'      => 'required|unique:tr_sk,no_sk',
            'supir'         => 'required|exists:ms_supir,id_supir',
            'gudang'        => 'required|exists:ms_gudang,id_gudang',
            'id_so_po.*'    => 'required',
            'kuantitas.*'   => 'required'
        ];
 
        $messages = [
            'nomor_sk.required' => 'Nomor surat kuasa wajib diisi', 
            'nomor_sk.unique'   => 'Nomor surat kuasa sudah pernah terdaftar pilih nomor surat kuasa yang lain',
            'supir.required'    => 'Supir wajib diisi',
            'supir.exists'      => 'Supir tidak valid',
            'gudang.required'   => 'Gudang wajib diisi', 
            'gudang.exists'     => 'Gudang tidak valid',
            'id_so_po.*.required'   => 'ID produk tidak boleh kosong',
            'kuantitas.*.required'  => 'kuantitas produk twajib diisi'
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
            $this->SuratKuasaService->validateAllKuantitas($request->kuantitas);

            $sk = new SuratKuasa();
            $sk->id_so = Helper::decodex($id);
            $sk->no_sk = $request->nomor_sk;
            $sk->id_supir = $request->supir;
            $sk->id_gudang = $request->gudang;
            $sk->created_by = Auth::user()->id_user;
            $sk->save();
 
            for ($i=0; $i < count($request->id_so_po) ; $i++) 
            {       
                if($request->kuantitas[$i] != 0)
                {
                    $id_so_po = Helper::decodex($request->id_so_po[$i]);
                    $this->SuratKuasaService->validateMaxKuantitasSO($id_so_po, Helper::decimal($request->kuantitas[$i]));
                    $data_skso[] = [
                        "id_sk" => $sk->id_sk,
                        "id_so_po" => $id_so_po,
                        "kuantitas" => Helper::decimal($request->kuantitas[$i]),
                        "created_by" => Auth::user()->id_user
                    ];
                } 
            }
            SKSO::insert($data_skso);

            // insert lampiran
            if($request->is_lampiran == 1){
                $this->LampiranService->store($request, $sk->id_sk, Helper::RemoveSpecialChar($sk->no_sk), "SURAT KUASA");
            } 

            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'Tambah surat kuasa berhasil']); 
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
        $id_sk = Helper::decodex($id);
        $info["sk"] = SuratKuasa::with('Supir', 'Gudang')->findOrFail($id_sk);   
        $info["skso"] = SKSO::where("id_sk", $id_sk)->get();
        $info["riwayat_email"] = RiwayatEmail::with('UpdatedBy')->where("id_reference", $id_sk)->where("kategori", "surat kuasa")->first();

        return response()->json(['html' => view('surat_kuasa.detail', compact('info', 'id'))->render()]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $id_sk = Helper::decodex($id);
        $info["sk"] = SuratKuasa::with("SO")->findOrFail($id_sk);
        $info["sk_so"] = SKSO::where("id_sk", $id_sk)->get(); 
        $info["gudang"] = Gudang::where("is_aktif", 1)->get();
        $info["supir"] = Supir::where("is_aktif", 1)->get();

        return view('surat_kuasa.edit', compact('info', 'id'));
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
        $id_sk = Helper::decodex($id);

        $rules = [
            'nomor_sk'      => 'required|unique:tr_sk,no_sk,'.$id_sk.',id_sk',
            'supir'         => 'required|exists:ms_supir,id_supir',
            'gudang'        => 'required|exists:ms_gudang,id_gudang',
            'id_so_po.*'    => 'required',
            'kuantitas.*'   => 'required'
        ];
 
        $messages = [
            'nomor_sk.required' => 'Nomor surat kuasa wajib diisi', 
            'nomor_sk.unique'   => 'Nomor surat kuasa sudah pernah terdaftar pilih nomor surat kuasa yang lain',
            'supir.required'    => 'Supir wajib diisi',
            'supir.exists'      => 'Supir tidak valid',
            'gudang.required'   => 'Gudang wajib diisi', 
            'gudang.exists'     => 'Gudang tidak valid',
            'id_so_po.*.required'   => 'ID produk tidak boleh kosong',
            'kuantitas.*.required'  => 'kuantitas produk twajib diisi'
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
            return response()->json(['status' => 'error', 'message' => $validator->errors()->all()]); 
        } 

        DB::beginTransaction();
        try {
            $sk = SuratKuasa::findOrFail($id_sk); 
            $sk->no_sk = $request->nomor_sk;
            $sk->id_supir = $request->supir;
            $sk->id_gudang = $request->gudang;
            $sk->updated_by = Auth::user()->id_user;
            $sk->save();
 
            for ($i=0; $i < count($request->id_sk_so) ; $i++) 
            {       
                if($request->kuantitas[$i] != 0)
                {
                    $id_sk_so = Helper::decodex($request->id_sk_so[$i]);

                    $skso = SKSO::findOrFail($id_sk_so);
                    $this->SuratKuasaService->validateMaxKuantitasSO($skso->id_so_po, Helper::decimal($request->kuantitas[$i]), $id_sk_so);
                    $skso->kuantitas = Helper::decimal($request->kuantitas[$i]);
                    $skso->updated_by = Auth::user()->id_user;
                    $skso->save();
                     
                } 
            } 

            // lampiran
            $nama_file = Helper::RemoveSpecialChar($this->SuratKuasaService->nomor($id_sk));
            $this->LampiranService->call($request, $id_sk, $nama_file, "SURAT KUASA"); 

            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'Update surat kuasa berhasil']); 
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
            SuratKuasa::findOrFail(Helper::decodex($id))->delete();
            return response()->json(['status' => 'success', 'message' => 'Hapus surat kuasa berhasil']); 
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]); 
        }
    }

    /**
     * Surat kuasa.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function surat_kuasa($id)
    {
        $id = Helper::decodex($id); 
        $pdf = $this->SuratKuasaService->suratKuasa($id);

        return $pdf["pdf"]->setPaper('a4')->stream(Helper::RemoveSpecialChar($pdf["info"]["surat_kuasa"]->no_sk).'.pdf');
    }


    /**
     * Send email surat kuasa.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function send_email($id)
    {
        DB::beginTransaction();
        try {
            $id_sk = Helper::decodex($id);
            $sk = SuratKuasa::findOrFail($id_sk);
            $email_tujuan = $sk->SO->SKPP->Customer->email;

            $lampiran = [];
            if($sk->Lampiran != null && count($sk->Lampiran) > 0){
                foreach ($sk->Lampiran as $value) {
                    $x["name_file"] = $value->file;
                    $x["url_file"] = asset('lampiran/'.$value->file);
                    $lampiran[] = $x;
                }  
            }

            $pdf = $this->SuratKuasaService->suratKuasa($id_sk); 
            Mail::to($email_tujuan)->send(new SendEmail("SURAT KUASA", $pdf["pdf"], $lampiran)); 
            $this->AppService->storeRiwayatEmail($id_sk, "surat kuasa");

            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'Kirim email ke '.$email_tujuan.' berhasil']); 
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]); 
        }
    } 
}

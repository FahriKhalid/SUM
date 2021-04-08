<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables; 
use App\Services\SuratKuasaService;
use App\Mail\SendEmail;
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

    public function __construct(SuratKuasaService $SuratKuasaService){
        $this->SuratKuasaService = $SuratKuasaService;
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
            return $data->totalKuantitasPO();            
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
                    $this->SuratKuasaService->validateMaxKuantitasSO($id_so_po, $request->kuantitas[$i]);

                    $data_skso[] = [
                        "id_sk" => $sk->id_sk,
                        "id_so_po" => $id_so_po,
                        "kuantitas" => $request->kuantitas[$i],
                        "created_by" => Auth::user()->id_user
                    ];
                } 
            }

            SKSO::insert($data_skso);

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
                    $this->SuratKuasaService->validateMaxKuantitasSO($skso->id_so_po, $request->kuantitas[$i], $id_sk_so);
                    $skso->kuantitas = $request->kuantitas[$i];
                    $skso->updated_by = Auth::user()->id_user;
                    $skso->save();
                     
                } 
            } 

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

        return $pdf->setPaper('a4')->stream();
    }


    /**
     * Send email surat kuasa.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function send_email($id)
    {
        try {
            $id_sk = Helper::decodex($id);
            $sk = SuratKuasa::findOrFail($id_sk);
            $email_tujuan = $sk->SO->SKPP->Customer->email;

            $pdf = $this->SuratKuasaService->suratKuasa($id_sk); 
            Mail::to($email_tujuan)->send(new SendEmail("SURAT KUASA", $pdf)); 

            return response()->json(['status' => 'success', 'message' => 'Kirim email ke '.$email_tujuan.' berhasil']); 
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]); 
        }
    }

}

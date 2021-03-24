<?php

namespace App\Http\Controllers\SKPP;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Yajra\Datatables\Datatables; 
use App\Services\PembayaranService;
use App\Services\LampiranService;
use App\Services\SkppService;
use App\Services\BarangService;
use App\Mail\SendEmail;
use App\Pembayaran;
use App\PreOrder;
use App\Customer;
use App\Lampiran;
use App\Produk;
use App\SKPP;
use App\Atm;
use App\Barang;
use Validator;
use Helper;
use Auth;
use PDF;
use DB;

class SkppPembelianController extends Controller
{

    protected $PembayaranService, $SkppService;

    public function __construct(
        PembayaranService $PembayaranService,
        SkppService $SkppService
    ){  
        $this->PembayaranService = $PembayaranService;
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
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
            'id_pre_order'          => 'required', 
            'no_skpp'               => 'required|unique:tr_skpp,no_skpp', 
            'file_skpp'             => 'required|max:2000|mimes:pdf',
            'total_pembayaran'      => 'required',
            'terakhir_pembayaran'   => 'required', 
        ]; 
 
        $messages = [
            'id_pre_order.required'         => 'Pre order wajib diisi', 
            'no_skpp.required'              => 'Nomor SKPP wajib diisi',
            'no_skpp.unique'                => 'Nomor SKPP sudah pernah terdaftar pilih nomor SKPP yang lain',
            'file_skpp.required'            => 'File SKPP pembayaran wajib diisi', 
            'file_skpp.max'                 => 'Ukuran file terlalu besar, maks 2 Mb',
            'file_skpp.mimes'               => 'Ekstensi file yang diizinkan hanya PDF',
            'terakhir_pembayaran.required'  => 'Terakhir pembayaran wajib diisi',
        ];
 
        $validator = Validator::make($request->all(), $rules, $messages);
 
        if($validator->fails()){ 
            return response()->json(['status' => 'error_validate', 'message' => $validator->errors()->all()]); 
        }  


        try {
            $data = new SKPP;
            $data->kategori = "pembelian";
            $data->id_pre_order = Helper::decodex($request->id_pre_order); 
            $data->no_skpp = $request->no_skpp;
            $data->total_pembayaran = Helper::decimal($request->total_pembayaran);
            $data->terakhir_pembayaran = Helper::dateFormat($request->terakhir_pembayaran, true, 'Y-m-d');
            $data->created_by = Auth::user()->id_user;

            $namafile = 'SKPP-'.Str::random(4).'.'.$request->file_skpp->getClientOriginalExtension();
            $data->file_skpp = $namafile;
            $request->file_skpp->move('file_skpp', $namafile);

            $data->save();
            return response()->json(['status' => 'success', 'message' => 'Tambah SKPP berhasil']);
        } catch (\Exception $e) {
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
        $info["total_pembayaran"] = $this->SkppService->totalPembayaranSkppPembelianIncludePPN(null, $id_pre_order);
        
        $info["skpp"] = SKPP::with('PreOrder')->where("id_pre_order", $id_pre_order)->first();
        if($info["skpp"])
        {
            $info["pembayaran"] = Pembayaran::with('CreatedBy')->where("id_skpp", $info["skpp"]->id_skpp)->get(); 
            $info["last_record"] = $this->PembayaranService->lastRecord($info["skpp"]->id_skpp);
            $info["piutang"] = $this->PembayaranService->sisaHutang("pembelian", $info["skpp"]->id_skpp);  
        } 

        return view("skpp.pembelian.show", compact("info", "id"));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $id = Helper::decodex($id);
        $data = SKPP::findOrFail($id);
        $data->id_skpp = Helper::encodex($data->id_skpp);
        $data->total_pembayaran = Helper::currency($data->total_pembayaran);
        return response()->json($data);
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
        $id = Helper::decodex($request->id);

        $rules = [
            'id'                    => 'required',
            'id_pre_order'          => 'required', 
            'no_skpp'               => 'required|unique:tr_skpp,no_skpp,'.$id.',id_skpp',
            'file_skpp'             => 'nullable|max:2000|mimes:pdf',
            'total_pembayaran'      => 'required',
            'terakhir_pembayaran'   => 'required', 
        ]; 
 
        $messages = [
            'id.required'                   => 'Id booking wajib diisi',
            'id_pre_order.required'         => 'Pre order wajib diisi', 
            'no_skpp.required'              => 'Nomor SKPP wajib diisi',
            'no_skpp.unique'                => 'Nomor SKPP sudah pernah terdaftar pilih nomor SKPP yang lain',
            'file_skpp.required'            => 'File SKPP pembayaran wajib diisi', 
            'file_skpp.max'                 => 'Ukuran file terlalu besar, maks 2 Mb',
            'file_skpp.mimes'               => 'Ekstensi file yang diizinkan hanya PDF',
            'terakhir_pembayaran.required'  => 'Terakhir pembayaran wajib diisi',
        ];
 
        $validator = Validator::make($request->all(), $rules, $messages);
 
        if($validator->fails()){ 
            return response()->json(['status' => 'error_validate', 'message' => $validator->errors()->all()]); 
        }  


        try {
            $data = SKPP::findOrFail($id);
            $this->SkppService->validateUpdateHarga($data, Helper::decimal($request->total_pembayaran));

            $data->id_pre_order = Helper::decodex($request->id_pre_order); 
            $data->no_skpp = $request->no_skpp;  
            $data->total_pembayaran = Helper::decimal($request->total_pembayaran);
            $data->terakhir_pembayaran = Helper::dateFormat($request->terakhir_pembayaran, true, 'Y-m-d');
            $data->updated_by = Auth::user()->id_user;

            if($request->has('file_skpp')){
                $namafile = 'SKPP-'.Str::random(4).'.'.$request->file_skpp->getClientOriginalExtension();
                $data->file_skpp = $namafile;
                $request->file_skpp->move('file_skpp', $namafile);
            }

            $data->save();
            return response()->json(['status' => 'success', 'message' => 'Update SKPP berhasil']);
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
        //
    }

    public function sisa_pembayaran($id)
    {
        $id = Helper::decodex($id); 
        $info["sisa_pembayaran"] = $this->PembayaranService->sisaHutang("pembelian", $id); 

        return response()->json($info["sisa_pembayaran"]);
    }



    public function sisa_barang($id)
    {
        $id = Helper::decodex($id);  
        $info["barang"] = Barang::with('Produk')->where("id_pre_order", $id)->get();

        return response()->json([
            'html' => view('skpp.pembelian.table_barang', compact('info', 'id'))->render()
        ]);
    }
}

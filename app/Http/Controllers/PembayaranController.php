<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\Datatables\Datatables; 
use Illuminate\Support\Str; 
use App\Services\BarangService;
use App\Services\PembayaranService;
use App\Pembayaran; 
use App\Booking;
use App\SKPP; 
use Validator;
use Auth;
use Helper;

class PembayaranPenjualanController extends Controller
{

    protected $BarangService, $PembayaranService;

    public function __construct(BarangService $BarangService, PembayaranService $PembayaranService){ 
        $this->BarangService = $BarangService;
        $this->PembayaranService = $PembayaranService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function data(Pembayaran $pembayaran, Request $request, $id)
    {
        $id = Helper::decodex($id);

        $kategori = "penjualan";

        $last_record = $this->PembayaranService->lastRecord($kategori, $id);

        $data = $pembayaran->query()->where("id_skpp", $id)->with('CreatedBy', 'Status');

        return Datatables::of($data)->addIndexColumn()->addColumn('action', function ($data) use ($last_record, $kategori){

            $option = $last_record != $data->id_pembayaran ? 'disabled' : '';

            return '<a href="javascript:void(0)" did="'.Helper::encodex($data->id_pembayaran).'" class="btn btn-sm btn-primary detail-pembayaran"><i class="fa fa-search"></i></a>
            <button url="/pembayaran/destroy/'.Helper::encodex($data->id_pembayaran).'/'.$kategori.'/'.Helper::encodex($data->id_skpp).'" '.$option.' class="btn btn-sm btn-danger hapus"><i class="fa fa-trash"></i>  </button>';

        })->addColumn('bukti_pembayaran', function($data){ 

            return '<div class="layout-overlay"><img class="img-overlay" src="/bukti_pembayaran/'.$data->file_bukti_pembayaran.'" width="100%"></div>';
            
        })->addColumn('jumlah_pembayaran', function($data){ 

            return Helper::currency($data->jumlah_pembayaran);
            
        })->addColumn('sisa_hutang', function($data){ 

            return Helper::currency($data->sisa_hutang);
            
        })->addColumn('status', function($data){ 

            return $data->Status->status;
            
        })->addColumn('created_by', function($data){ 

            return $data->CreatedBy->nama;
            
        })->rawColumns(['action','bukti_pembayaran'])->make(true);
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
    public function store(Request $request, $kategori, $id)
    {

        $id_header = Helper::decodex($id);

        $sisa_hutang = 0;

        $rules = [
            'file'          => 'required|max:2000|mimes:png,jpg,jpeg',
            'keterangan'    => 'nullable|string|max:500', 
        ]; 
 
        $messages = [
            'file.required'             => 'File tanda bukti pembayaran wajib diisi', 
            'file.max'                  => 'Ukuran file terlalu besar, maks 2 Mb',
            'file.mimes'                => 'Ekstensi file yang diizinkan hanya jpg, jpeg dan png',
            'keterangan.string'         => 'keterangan tidak valid',
            'keterangan.max'            => 'Keterangan tidak boleh lebih dari 500 karakter',
            'jumlah_pembayaran.required' => 'Jumlah pembayaran wajib diisi'
        ];

        if($request->has('kode_booking')){
            $new_rule = [
                'kode_booking' => 'required|unique:tr_pembayaran,kode_booking',
            ];

            $rules = array_merge($rules, $new_rule); 
        }

        if($request->has('is_parsial')){
            $rule_pembayaran = [
                'jumlah_pembayaran' => 'required',
            ];

            $rules = array_merge($rules, $rule_pembayaran); 
        }
 
        $validator = Validator::make($request->all(), $rules, $messages);
 
        if($validator->fails()){ 
            return response()->json(['status' => 'error_validate', 'message' => $validator->errors()->all()]); 
        }  
    

        if($this->PembayaranService->isLunas($kategori, $id_header))
        {
            try {
                $store = new Pembayaran; 
                $namafile = 'bukti-pembayaran-'.Str::random(8).'.'.$request->file->getClientOriginalExtension();
                $store->kode_booking = $request->kode_booking;
                $store->file_bukti_pembayaran = $namafile;
                $store->keterangan = $request->keterangan;
                $store->id_skpp = $id_header;
                // if($kategori == "penjualan"){
                //     $store->id_skpp = $id_header;
                // } else {
                //     $store->id_booking = Helper::decodex($request->id_booking);
                // }
                
                if($request->has('is_parsial')){ 
                    $total = $this->PembayaranService->sisaHutang($kategori, $id_header);
                    $sisa_hutang = $total - Helper::decimal($request->jumlah_pembayaran); 
                    $store->jumlah_pembayaran = Helper::decimal($request->jumlah_pembayaran);
                    $store->sisa_hutang = $sisa_hutang;
                    $store->is_parsial = 1;
                }else{
                    $pembayaran = $this->PembayaranService->sisaHutang($kategori, $id_header);
                    $store->jumlah_pembayaran = $pembayaran;
                    $store->sisa_hutang = 0;
                }

                $store->created_by = Auth::user()->id_user; 
                $store->save();

                $request->file->move('bukti_pembayaran', $namafile);

                if ($kategori == "penjualan") {
                    return response()->json([
                        'status' => 'success', 
                        'message' => 'Tambah pembayaran berhasil', 
                        'data' => $sisa_hutang
                    ]); 
                } else {
                    $info["pembayaran"] = Pembayaran::where("id_skpp", $id_header)->get(); 
                    $info["last_record"] = $this->PembayaranService->lastRecord("pembelian", $id_header);
                    $info["piutang"] = $this->PembayaranService->sisaHutang("pembelian", $id_header); 
                    $info["skpp"] = SKPP::findOrFail($id_header); 

                    return response()->json([
                        'status' => 'success', 
                        'message' => 'Tambah pembayaran berhasil', 
                        'data' => $sisa_hutang, 
                        'html' => view('skpp.pembelian.table_pembayaran', compact('info'))->render()
                    ]); 
                }

            } catch (\Exception $e) {
                return response()->json(['status' => 'error', 'message' => $e->getMessage()]); 
            }
        }
        else
        {
            return response()->json(['status' => 'error', 'message' => 'Pembayaran sudah lunas']); 
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

        $info["kategori"] = "penjualan";
        $info["skpp"] = SKPP::with('Customer','Status')->findOrFail($id_skpp);
        $info["pembayaran"] = Pembayaran::with('CreatedBy')->where("id_skpp", $id_skpp)->get();
        $info["piutang"] = $this->PembayaranService->sisaHutang($info["kategori"], $id_skpp);  
        return view('pembayaran.penjualan.show', compact('info', 'id')); 
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function detail($id)
    {
        $id = Helper::decodex($id); 
        $data = Pembayaran::findOrFail($id);
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id, $kategori, $id_header)
    {
        $id = Helper::decodex($id);
        $id_header = Helper::decodex($id_header);

        try {
            $hapus = Pembayaran::findOrFail($id)->delete();

            if($hapus){
                $sisa_hutang = $this->PembayaranService->sisaHutang($kategori, $id_header);

                if ($kategori == "penjualan") 
                {
                    return response()->json(['status' => 'success', 'message' => 'Hapus pembayaran berhasil', 'data' => $sisa_hutang]); 
                } 
                elseif ($kategori == "pembelian") 
                {
                    $info["pembayaran"] = Pembayaran::where("id_skpp", $id_header)->get(); 
                    $info["last_record"] = $this->PembayaranService->lastRecord("pembelian", $id_header);
                    $info["piutang"] = $this->PembayaranService->sisaHutang("pembelian", $id_header);  
                    $info["skpp"] = SKPP::findOrFail($id_header); 
                    
                    return response()->json([
                        'status' => 'success', 
                        'message' => 'Hapus pembayaran berhasil', 
                        'data' => $sisa_hutang, 
                        'html' => view('skpp.pembelian.table_pembayaran', compact('info'))->render()
                    ]); 
                }
            }
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]); 
        } 
    }
}

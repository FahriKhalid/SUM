<?php

namespace App\Http\Controllers\Pembayaran;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables; 
use Illuminate\Support\Str; 
use App\Services\SkppService;
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
    protected $BarangService, $PembayaranService, $SkppService;

    public function __construct(
        BarangService $BarangService, 
        PembayaranService $PembayaranService,
        SkppService $SkppService
    ){ 
        $this->BarangService = $BarangService;
        $this->PembayaranService = $PembayaranService;
        $this->SkppService = $SkppService;
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
        $last_record = $this->PembayaranService->lastRecord("penjualan", $id);
        $data = $pembayaran->query()->where("id_skpp", $id)->with('CreatedBy', 'Status');
        return Datatables::of($data)->addIndexColumn()->addColumn('action', function ($data) use ($last_record, $kategori){
            $option = $last_record != $data->id_pembayaran ? 'disabled' : '';
            return '<a href="javascript:void(0)" attachment="'.asset('bukti_pembayaran/'.$data->file_bukti_pembayaran).'" class="btn btn-sm btn-primary detail-pembayaran"><i class="fa fa-search"></i></a>

            <button url="'.url('penjualan/pembayaran/destroy/'.Helper::encodex($data->id_pembayaran).'/'.Helper::encodex($data->id_skpp)).'" '.$option.' class="btn btn-sm btn-danger hapus"><i class="fa fa-trash"></i>  </button>';

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
    public function store(Request $request, $id)
    {  
        return $this->PembayaranService->store($request, $id, "penjualan"); 
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
        
        //$info["total_pembayaran"] = $this->SkppService->totalPembayaranSkppPembelianIncludePPN($id_skpp, null);
         
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
    public function destroy($id, $id_header)
    {
        $id = Helper::decodex($id);
        $id_header = Helper::decodex($id_header);

        try {
            $hapus = Pembayaran::findOrFail($id)->delete();
            if($hapus){ 
                $sisa_hutang = $this->PembayaranService->sisaHutang("penjualan", $id_header);
                return response()->json(['status' => 'success', 'message' => 'Hapus pembayaran berhasil', 'data' => $sisa_hutang]);  
            }
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]); 
        } 
    }
}

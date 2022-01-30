<?php

namespace App\Http\Controllers\Pembayaran;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables; 
use Illuminate\Support\Str; 
use App\Services\BarangService;
use App\Services\PembayaranService;
use App\Pembayaran; 
use App\Booking;
use App\SKPP; 
use Validator;
use Helper;
use Auth;
use DB;

class PembayaranPembelianController extends Controller
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
    public function index()
    {
        //
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function data(Pembayaran $pembayaran, Request $request, $id)
    {
        $id = Helper::decodex($id); 
        $last_record = $this->PembayaranService->lastRecord("pembelian", $id);
        $data = $pembayaran->query()->where("id_skpp", $id)->with('CreatedBy', 'Status');
        return Datatables::of($data)->addIndexColumn()->addColumn('action', function ($data) use ($last_record){
            $option = $last_record != $data->id_pembayaran ? 'disabled' : '';
            return '<a href="javascript:void(0)" attachment="'.asset('bukti_pembayaran/'.$data->file_bukti_pembayaran).'" class="btn btn-sm btn-primary detail-pembayaran"><i class="fa fa-search"></i></a>

            <button url="'.url('pembelian/pembayaran/destroy/'.Helper::encodex($data->id_pembayaran).'/'.Helper::encodex($data->id_skpp)).'" '.$option.' class="btn btn-sm btn-danger hapus-pembayaran"><i class="fa fa-trash"></i>  </button>';

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
        return $this->PembayaranService->store($request, $id, "pembelian");
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
    public function destroy($id_pre_order, $id_pembayaran)
    {
        $id = $id_pre_order;
        $id_pembayaran = Helper::decodex($id_pembayaran);
        $id_pre_order = Helper::decodex($id_pre_order);

        DB::beginTransaction();
        try {
            
            Pembayaran::findOrFail($id_pembayaran)->delete();
            $sisa_hutang = $this->PembayaranService->sisaHutang("pembelian", $id_pre_order); 
            $info["pembayaran"] = Pembayaran::where("id_pre_order", $id_pre_order)->get(); 
            $info["last_record"] = $this->PembayaranService->lastRecord("pembelian", $id_pre_order);
            $info["piutang"] = $this->PembayaranService->sisaHutang("pembelian", $id_pre_order); 
            
            DB::commit();
            return response()->json([
                'status' => 'success', 
                'message' => 'Hapus pembayaran berhasil', 
                'data' => $sisa_hutang, 
                'html' => view('booking.table_pembayaran', compact('info', 'id'))->render()
            ]); 
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]); 
        } 
    }
}

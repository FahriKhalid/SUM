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
use Auth;
use Helper;

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
    public function destroy($id, $id_skpp)
    {
        $id = Helper::decodex($id);
        $id_skpp = Helper::decodex($id_skpp);

        try {
            $hapus = Pembayaran::findOrFail($id)->delete();

            if($hapus){
                $sisa_hutang = $this->PembayaranService->sisaHutang("penjualan", $id_skpp);
                 
                $info["pembayaran"] = Pembayaran::where("id_skpp", $id_skpp)->get(); 
                $info["last_record"] = $this->PembayaranService->lastRecord($id_skpp);
                $info["piutang"] = $this->PembayaranService->sisaHutang("pembelian", $id_skpp); 
                $info["skpp"] = SKPP::findOrFail($id_skpp); 

                return response()->json([
                    'status' => 'success', 
                    'message' => 'Hapus pembayaran berhasil', 
                    'data' => $sisa_hutang, 
                    'html' => view('skpp.pembelian.table_pembayaran', compact('info'))->render()
                ]); 
            }
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]); 
        } 
    }
}

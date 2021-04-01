<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\DashboardService;
use Helper;
use Auth;
use DB;

class DashboardController extends Controller
{
    protected $DashboardService;

    public function __construct(DashboardService $DashboardService){
        $this->DashboardService = $DashboardService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $info["total_penjualan"] = $this->DashboardService->totalPenjualan();
        $info["total_pembelian"] = $this->DashboardService->totalPembelian();
        $info["penjualan"] = $this->DashboardService->penjualan();
        $info["pembelian"] = $this->DashboardService->pembelian();
        $info["total_hutang"] = $this->DashboardService->totalHutang(); 
        $info["total_piutang"] = $this->DashboardService->totalPiutang();
        $info["tren"] = $this->DashboardService->dataTrenPenjualanPembelian();
       
        return view('dashboard.index', compact("info"));
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
        //
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
    public function destroy($id)
    {
        //
    }

    public function filter(Request $request)
    {
        $penjualan = $this->DashboardService->penjualan($request->start, $request->end);
        $pembelian = $this->DashboardService->pembelian($request->start, $request->end);
        $data_tren = $this->DashboardService->dataTrenPenjualanPembelian($request->start, $request->end);
        
        return response()->json([
            "penjualan" => Helper::currency($penjualan),
            "pembelian" => Helper::currency($pembelian),
            "tren_penjualan" => $data_tren["penjualan"],
            "tren_pembelian" => $data_tren["pembelian"],
        ]);
    }
}

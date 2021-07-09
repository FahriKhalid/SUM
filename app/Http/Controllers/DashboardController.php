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
        $info["total_penjualan"]    = $this->DashboardService->totalPenjualan();
        $info["total_pembelian"]    = $this->DashboardService->totalPembelian();
        $info["penjualan"]          = $this->DashboardService->penjualan();
        $info["pembelian"]          = $this->DashboardService->pembelian(); 
        $info["penjualan_produk"]   = $this->DashboardService->penjualanProduk();
        $info["pembelian_produk"]   = $this->DashboardService->pembelianProduk(); 
        $info["total_hutang"]       = $this->DashboardService->totalHutang(); 
        $info["total_piutang"]      = $this->DashboardService->totalPiutang();
        $info["tren"]               = $this->DashboardService->dataTrenPenjualanPembelian();
        $info["top_customers"]      = $this->DashboardService->topCustomers();
        $info["top_products"]       = $this->DashboardService->topProducts();
        $info["tren_produk"]        = $this->DashboardService->dataTrenProduk(); 
        $info["stok_produk"]        = $this->DashboardService->stokProduk();
        $info["chart_stok_produk"]  = $this->DashboardService->dataTrenStokProduk();
 
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
        $penjualan_produk = $this->DashboardService->penjualanProduk($request->start, $request->end);
        $pembelian_produk = $this->DashboardService->pembelianProduk($request->start, $request->end);
        $data_tren_produk = $this->DashboardService->dataTrenProduk($request->start, $request->end);
        
        return response()->json([
            "penjualan" => Helper::currency($penjualan),
            "pembelian" => Helper::currency($pembelian),
            "penjualan_produk" => $penjualan_produk,
            "pembelian_produk" => $pembelian_produk,
            // "tren_penjualan" => $data_tren["penjualan"],
            // "tren_pembelian" => $data_tren["pembelian"],
            "tren_penjualan_kumulatif" => $data_tren["penjualan_kumulatif"],
            "tren_pembelian_kumulatif" => $data_tren["pembelian_kumulatif"],
            "tren_penjualan_produk" => $data_tren_produk["penjualan"],
            "tren_pembelian_produk" => $data_tren_produk["pembelian"],
        ]);
    }
}

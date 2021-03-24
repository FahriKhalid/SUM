<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use App\VwStok;
use App\Stok;
use Validator;
use Helper;
use Auth;
use PDF;
use DB;


class StokController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('stok_gudang.index');
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function data(VwStok $VwStok, Request $request)
    { 
        $data = DB::table("vw_stok")->get();

        return Datatables::of($data)
        ->addIndexColumn()
        ->addColumn('is_aktif', function($data){ 

            if($data->is_aktif == 1){
                return '<span class="badge badge-success">Aktif</span>';
            }else{
                return '<span class="badge badge-danger">Tidak aktif</span>';
            }
 
        })->rawColumns(['is_aktif'])->make(true);
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


    /**
    * Jumlah stok per produk
    *
    * @param  int  $id
    * @return \Illuminate\Http\Response
    */
    public function jumlah_stok($id)
    { 
        $jumlah_stok = Stok::where("id_produk", $id)->first();

        if($jumlah_stok) {
            $jumlah_stok = $jumlah_stok->jumlah;
        } else {
            $jumlah_stok = 0;
        }

        return response()->json($jumlah_stok);
    }
}

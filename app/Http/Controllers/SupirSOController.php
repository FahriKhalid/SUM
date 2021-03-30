<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;  
use Yajra\Datatables\Datatables; 
use App\Services\SoService; 
use App\SuratKuasa;
use App\SupirSO;
use App\Supir;
use App\SOPO; 
use App\SO;
use Validator;
use Helper;
use Auth;
use PDF;
use DB;

class SupirSOController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($id)
    {
         
    } 
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($id)
    {
        
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function data(SupirSO $SupirSO, Request $request, $id)
    {
        $id_so = Helper::decodex($id);

        $data = $SupirSO->query()->where("id_so", $id_so)->where("is_aktif", "0")->with('CreatedBy', 'Supir');

         return Datatables::of($data)->addIndexColumn()->addColumn('action', function ($id){ 

            return '
            <button url="/supirso/destroy/'.Helper::encodex($id->id_supir_so).'" class="btn btn-sm btn-danger hapus"><i class="fa fa-trash"></i>  </button>';

        })->addColumn('supir', function($data){ 

            return $data->Supir->nama;
            
        })->addColumn('plat_nomor', function($data){ 

            return $data->Supir->plat_nomor;
            
        })->addColumn('kendaraan', function($data){ 

            return $data->Supir->kendaraan;
            
        })->addColumn('no_telepon', function($data){ 

            return $data->Supir->no_telepon;
            
        })->addColumn('created_by', function($data){ 

            return $data->CreatedBy->nama;
            
        })->rawColumns(['action'])->make(true);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function switch($id, Request $request)
    { 
        $rules = [
        	'id_so'					=> 'required',		
            'supir'               	=> 'required|exists:ms_supir,id_supir',
            'keterangan'            => 'required|string|max:250', 
        ]; 
 
        $messages = [
            'supir.required'     	=> 'Supir wajib diisi', 
            'supir.exists'			=> 'Supir tidak valid',
            'keterangan.max'       	=> 'Karakter keterangan teralu panjang. Maks 250 karakter',
            'keterangan.required'   => 'Keterangan waji diisi',  

        ];

        $validator = Validator::make($request->all(), $rules, $messages);
 
        if($validator->fails()){ 
            return response()->json(['status' => 'error', 'message' => $validator->errors()->all()]); 
        }

        $id_so = Helper::decodex($request->id_so);

        DB::beginTransaction();
        try {
            // update supir lama
            $supir_lama = SupirSO::where("id_so", $id_so)->where("is_aktif", 1)->firstOrFail(); 
            $supir_lama->is_aktif = '0'; 
            $supir_lama->updated_by = Auth::user()->id_user;
            $supir_lama->keterangan = $request->keterangan;
            $supir_lama->save(); 

            // insert supir baru
            $supir_baru = new SupirSO();
            $supir_baru->id_so = $id_so; 
            $supir_baru->id_supir = $request->supir; 
            $supir_baru->created_by = Auth::user()->id_user;
            $supir_baru->save();

            $sk = SuratKuasa::where("id_so", $id_so)->firstOrFail();
            $sk->id_supir = $request->supir; 
            $sk->save();

            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'Ganti supir berhasil']); 

        } catch (\Exception $e) { 
            DB::rollback();
            return response()->json(['status' => 'error', 'message' => 'Error. '. $e->getMessage()]); 
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
        $id_so = Helper::decodex($id);

        $info["so"] = SO::with("SupirAktif")->findOrFail($id_so);
 
        $info["sopo"] = SOPO::with('SO','Barang')->where("id_so", $id_so)->get();  

        $info["supir"] = Supir::where("id_supir", "!=", $info["so"]->SupirAktif[0]->id_supir)->get();

        return view('salesorder.show', compact('id', 'info'));
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
        $id = Helper::decodex($id); 
        try { 
            SupirSO::findOrFail($id)->delete();
            return response()->json(['status' => 'success', 'message' => 'Hapus riwayat supir berhasil']); 
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]); 
        } 
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use App\Supir;
use Validator;
use Auth;
use Helper;

class SupirController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('supir.index');
    }

    public function data(Supir $supir, Request $request)
    {
        $data = $supir->query()->with('CreatedBy');

        return Datatables::of($data)->addIndexColumn()->addColumn('action', function ($id){

            return '<a href="javascript:void(0);" did="'.Helper::encodex($id->id_supir).'" class="btn btn-sm btn-primary edit"><i class="fa fa-edit"></i></a>
            <a href="javascript:void(0);" url="/supir/destroy/'.Helper::encodex($id->id_supir).'" class="btn btn-sm btn-danger hapus"><i class="fa fa-trash"></i></a>';

        })->addColumn('is_aktif', function($data){ 

            if($data->is_aktif == 1){
                return '<span class="badge badge-success">Aktif</span>';
            }else{
                return '<span class="badge badge-danger">Tidak aktif</span>';
            }
 
        })->addColumn('created_by', function($data){ 

            return $data->CreatedBy->nama;
            
        })->addColumn('created_on', function($data){ 

            return $data->created_on;
            
        })->rawColumns(['action', 'is_aktif'])->make(true);
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
            'nama_supir'            => 'required',
            'telepon'               => 'required|numeric',
            'plat_nomor'            => 'required',
            'kendaraan'             => 'required|max:50',
        ];
 
        $messages = [
            'nama_supir.required'   => 'Nama produk wajib diisi', 
            'telepon.required'      => 'Nomor telepon wajib diisi',
            'telepon.numeric'       => 'Nomor telepon harus berupa angka', 
            'plat_nomor.required'   => 'Plat nomor wajib diisi',
            'kendaraan.required'    => 'kendaraan wajib diisi',
            'kendaraan.max'         => 'Karakter kendaraan terlalu panjang. Maks 50 karakter',
        ];
 
        $validator = Validator::make($request->all(), $rules, $messages);
 
        if($validator->fails()){ 
            return response()->json(['status' => 'error', 'message' => $validator->errors()->all()]); 
        }

        try {
            $data = new Supir;
            $data->nama = $request->nama_supir;
            $data->no_telepon = $request->telepon;
            $data->plat_nomor = $request->plat_nomor;
            $data->kendaraan = $request->kendaraan;
            $data->is_aktif = $request->status; 
            $data->created_by = Auth::user()->id_user; 
            $data->save();
            return response()->json(['status' => 'success', 'message' => 'Tambah supir berhasil']); 
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
        $data = Supir::findOrFail(Helper::decodex($id));
        $data->id_supir = $id;
        return response()->json($data);
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
        $rules = [
            'nama_supir'            => 'required',
            'telepon'               => 'required|numeric',
            'plat_nomor'            => 'required',
            'kendaraan'             => 'required|max:50',
        ];
 
        $messages = [
            'nama_supir.required'   => 'Nama produk wajib diisi', 
            'telepon.required'      => 'Nomor telepon wajib diisi',
            'telepon.numeric'       => 'Nomor telepon harus berupa angka', 
            'plat_nomor.required'   => 'Plat nomor wajib diisi',
            'kendaraan.required'    => 'kendaraan wajib diisi',
            'kendaraan.max'         => 'Karakter kendaraan terlalu panjang. Maks 50 karakter',
        ];
 
        $validator = Validator::make($request->all(), $rules, $messages);
 
        if($validator->fails()){ 
            return response()->json(['status' => 'error', 'message' => $validator->errors()->all()]); 
        }

        try {
            $data = Supir::findOrFail(Helper::decodex($id));
            $data->nama = $request->nama_supir;
            $data->no_telepon = $request->telepon;
            $data->plat_nomor = $request->plat_nomor;
            $data->kendaraan = $request->kendaraan;
            $data->is_aktif = $request->status; 
            $data->updated_by = Auth::user()->id_user; 
            $data->save();
            return response()->json(['status' => 'success', 'message' => 'Update supir berhasil']); 
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
        try {
            Supir::findOrFail(Helper::decodex($id))->delete();
            return response()->json(['status' => 'success', 'message' => 'Hapus supir berhasil']); 
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]); 
        }
    }
}

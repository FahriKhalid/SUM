<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use App\Gudang;
use Validator;
use Helper;
use Auth;

class GudangController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('gudang.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function data(Gudang $gudang, Request $request)
    {
        $data = $gudang->query()->with('CreatedBy');

        return Datatables::of($data)->addIndexColumn()->addColumn('action', function ($id){

            return '<a href="javascript:void(0);" did="'.Helper::encodex($id->id_gudang).'" class="btn btn-sm btn-primary edit"><i class="fa fa-edit"></i></a>
            <a href="javascript:void(0);" url="/gudang/destroy/'.Helper::encodex($id->id_gudang).'" class="btn btn-sm btn-danger hapus"><i class="fa fa-trash"></i></a>';

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
            'nama'          => 'required',   
            'alamat'         => 'required',  
            'status'        => 'required'
        ];
 
        $messages = [ 
            'nama.required'         => 'Nama gudang wajib diisi',
            'alamat.required'       => 'Alamat wajib diisi', 
            'status.required'       => 'Status gudang wajib diisi', 
        ];
 
        $validator = Validator::make($request->all(), $rules, $messages);
 
        if($validator->fails()){ 
            return response()->json(['status' => 'error', 'message' => $validator->errors()->all()]); 
        }

        try {
            $data = new Gudang();
            $data->nama = $request->nama; 
            $data->alamat = $request->alamat;
            $data->is_aktif = $request->status;
            $data->created_by = Auth::user()->id_user;
            $data->save();
            return response()->json(['status' => 'success', 'message' => 'Tambah gudang berhasil']); 
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
        $data = Gudang::findOrFail(Helper::decodex($id));
        $data->id_gudang = $id;
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
            'nama'          => 'required',   
            'alamat'        => 'required',  
            'status'        => 'required'
        ];
 
        $messages = [ 
            'nama.required'         => 'Nama gudang wajib diisi', 
            'alamat.required'       => 'Alamat wajib diisi', 
            'status.required'       => 'Status gudang wajib diisi', 
        ];
 
        $validator = Validator::make($request->all(), $rules, $messages);
 
        if($validator->fails()){ 
            return response()->json(['status' => 'error', 'message' => $validator->errors()->all()]); 
        }

        try {
            $data = Gudang::findOrFail(Helper::decodex($id));
            $data->nama = $request->nama; 
            $data->alamat = $request->alamat;
            $data->is_aktif = $request->status;
            $data->updated_by = Auth::user()->id_user;
            $data->save();
            return response()->json(['status' => 'success', 'message' => 'Update gudang berhasil']); 
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
            Gudang::findOrFail(Helper::decodex($id))->delete();
            return response()->json(['status' => 'success', 'message' => 'Hapus gudang berhasil']); 
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]); 
        }
    }
}

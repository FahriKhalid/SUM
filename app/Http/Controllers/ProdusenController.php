<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use App\Produsen;
use Validator;
use Auth;
use Helper;

class ProdusenController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('produsen.index');
    }

    public function data(Produsen $produsen, Request $request)
    {
        $data = $produsen->query();

        return Datatables::of($data)->addIndexColumn()->addColumn('action', function ($id){

            return '<a href="javascript:void(0);" did="'.Helper::encodex($id->id_produsen).'" class="btn btn-sm btn-primary edit"><i class="fa fa-edit"></i></a>
            <a href="javascript:void(0);" url="/produsen/destroy/'.Helper::encodex($id->id_produsen).'" class="btn btn-sm btn-danger hapus"><i class="fa fa-trash"></i></a>';

        })->orderColumn('name', function ($query, $order) {

             $query->orderBy('id_produsen', $order);

        })->addColumn('created_by', function($data){ 

            return $data->CreatedBy->nama;
            
        })->addColumn('created_on', function($data){ 

            return $data->created_on;
            
        })->rawColumns(['action'])->make(true);
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
            'nama_perusahaan'       => 'required', 
            'nama_produsen'         => 'required',
            'email'                 => 'required|email'
        ];
 
        $messages = [
            'nama_perusahaan.required'  => 'Nama perusahaan wajib diisi',  
            'nama_produsen.required'    => 'Nama produsen wajib diisi', 
            'email.required'            => 'Email wajib diisi',
            'email.email'               => 'Format email tidak valid',
        ];
 
        $validator = Validator::make($request->all(), $rules, $messages);
 
        if($validator->fails()){ 
            return response()->json(['status' => 'error', 'message' => $validator->errors()->all()]); 
        }

        try {
            $data = new Produsen;
            $data->nama = $request->nama_produsen;
            $data->perusahaan = $request->nama_perusahaan;
            $data->email = $request->email; 
            $data->created_by = Auth::user()->id_user; 
            $data->save();
            return response()->json(['status' => 'success', 'message' => 'Tambah produsen berhasil']); 
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
        $data = Produsen::findOrFail(Helper::decodex($id));
        $data->id_produsen = $id;
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
            'nama_perusahaan'       => 'required', 
            'nama_produsen'         => 'required',
            'email'                 => 'required|email', 
        ];
 
        $messages = [ 
            'nama_perusahaan.required'  => 'Nama perusahaan wajib diisi',  
            'nama_produsen.required'    => 'Nama produsen wajib diisi',  
            'email.required'            => 'Email wajib diisi',
            'email.email'               => 'Format email tidak valid',
        ];
 
        $validator = Validator::make($request->all(), $rules, $messages);
 
        if($validator->fails()){ 
            return response()->json(['status' => 'error', 'message' => $validator->errors()->all()]); 
        }

        try {
            $data = Produsen::findOrFail(Helper::decodex($id));
            $data->nama = $request->nama_produsen;
            $data->perusahaan = $request->nama_perusahaan;
            $data->email = $request->email;  
            $data->updated_by = Auth::user()->id_user; 
            $data->save();
            return response()->json(['status' => 'success', 'message' => 'Update produsen berhasil']); 
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
            Produsen::findOrFail(Helper::decodex($id))->delete();
            return response()->json(['status' => 'success', 'message' => 'Hapus produsen berhasil']); 
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]); 
        }
    }
}

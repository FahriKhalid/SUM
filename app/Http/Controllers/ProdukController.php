<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Yajra\Datatables\Datatables;
use App\Produk;
use Validator;
use Auth;
use Helper;

class ProdukController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('produk.index');
    }

    public function data(Produk $produk, Request $request)
    {
        $data = $produk->query()->with('CreatedBy');

        return Datatables::of($data)->addIndexColumn()->addColumn('action', function ($id){

            return '<a href="javascript:void(0);" did="'.Helper::encodex($id->id_produk).'" class="btn btn-sm btn-primary edit"><i class="fa fa-edit"></i></a>
            <a href="javascript:void(0);" url="/produk/destroy/'.Helper::encodex($id->id_produk).'" class="btn btn-sm btn-danger hapus"><i class="fa fa-trash"></i></a>';

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
            'nama_produk'           => 'required',
            'status'                => 'required',
            'spesifikasi'           => 'nullable|string|max:50'
        ];
 
        $messages = [
            'nama_produk.required'  => 'Nama produk wajib diisi', 
            'status.required'       => 'Status wajib diisi',
            'spesifikasi.string'    => 'Spesifikasi tidak valid',
            'spesifikasi.max'       => 'Karakter spesifikasi terlalu panjang. Maks 50 karakter',
        ];
 
        $validator = Validator::make($request->all(), $rules, $messages);
 
        if($validator->fails()){ 
            return response()->json(['status' => 'error', 'message' => $validator->errors()->all()]); 
        }

        try {
            $data = new Produk;
            $data->nama = $request->nama_produk;
            $data->spesifikasi = $request->spesifikasi; 
            $data->is_aktif = $request->status; 
            $data->created_by = Auth::user()->id_user; 
            $data->save();
            return response()->json(['status' => 'success', 'message' => 'Tambah produk berhasil']); 
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
        $data = Produk::findOrFail(Helper::decodex($id));
        $data->id_produk = $id;
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
            'nama_produk'           => 'required',
            'status'                => 'required',
            'spesifikasi'           => 'nullable|string|max:50'
        ];
 
        $messages = [
            'nama_produk.required'  => 'Nama produk wajib diisi', 
            'status.required'       => 'Status wajib diisi',
            'spesifikasi.string'    => 'Spesifikasi tidak valid',
            'spesifikasi.max'       => 'Karakter spesifikasi terlalu panjang. Maks 50 karakter',
        ];
 
        $validator = Validator::make($request->all(), $rules, $messages);
 
        if($validator->fails()){ 
            return response()->json(['status' => 'error', 'message' => $validator->errors()->all()]); 
        }

        try {
            $data = Produk::findOrFail(Helper::decodex($id));
            $data->nama = $request->nama_produk;
            $data->spesifikasi = $request->spesifikasi; 
            $data->is_aktif = $request->status; 
            $data->updated_by = Auth::user()->id_user; 
            $data->save();
            return response()->json(['status' => 'success', 'message' => 'Update produk berhasil']); 
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
            $produk = Produk::findOrFail(Helper::decodex($id)); 
            $produk->update(['deleted_by' => Auth::user()->id_user]);
            $produk->delete();
            return response()->json(['status' => 'success', 'message' => 'Hapus produk berhasil']); 
        } catch (QueryException $e) {
            if($e->getCode() == "23000"){
                return response()->json(['status' => 'error', 'message' => "Produk tidak dapat dihapus<br>Aksi di tolak"]); 
            } else {
                return response()->json(['status' => 'error', 'message' => $e->getCode()]); 
            } 
        }
    }
}

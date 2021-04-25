<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use App\Customer;
use Validator;
use Auth;
use Helper;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('customer.index');
    }

    public function data(Customer $customer, Request $request)
    {
        $data = $customer->query();

        return Datatables::of($data)->addIndexColumn()->addColumn('action', function ($id){
            return '<a href="javascript:void(0);" did="'.Helper::encodex($id->id_customer).'" class="btn btn-sm btn-primary edit"><i class="fa fa-edit"></i></a>
            <a href="javascript:void(0);" url="/customer/destroy/'.Helper::encodex($id->id_customer).'" class="btn btn-sm btn-danger hapus"><i class="fa fa-trash"></i></a>';
        })->addColumn('perusahaan', function ($query) {
            return $query->perusahaan == null ? '-' : $query->perusahaan;
        })->addColumn('no_npwp', function ($query) {
            return $query->no_npwp == null ? '-' : $query->no_npwp;
        })->addColumn('email', function ($query) {
            return $query->email == null ? '-' : $query->email;
        })->orderColumn('name', function ($query, $order) {
             $query->orderBy('id_customer', $order);
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
            'nama_perusahaan'       => 'nullable',
            'nomor_npwp'            => 'nullable',
            'nama_customer'         => 'required',
            'email'                 => 'nullable|email',
            'telpon'                => 'required',
            'alamat'                => 'required'
        ];
 
        $messages = [
            'nama_perusahaan.required'  => 'Nama perusahaan wajib diisi', 
            'nomor_npwp.required'       => 'Nomor NPWP wajib diisi',
            'nama_customer.required'    => 'Nama customer wajib diisi',
            'telpon.required'           => 'Nomor telpon wajib diisi',
            'alamat.required'           => 'Alamat wajib diisi',
            'email.required'            => 'Email wajib diisi',
            'email.email'               => 'Format email tidak valid',
        ];
 
        $validator = Validator::make($request->all(), $rules, $messages);
 
        if($validator->fails()){ 
            return response()->json(['status' => 'error_validate', 'message' => $validator->errors()->all()]); 
        }

        try {
            $data = new Customer;
            $data->nama = $request->nama_customer;
            $data->perusahaan = $request->nama_perusahaan;
            $data->email = $request->email;
            $data->telpon = $request->telpon;
            $data->alamat = $request->alamat;
            $data->no_npwp = $request->nomor_npwp; 
            $data->created_by = Auth::user()->id_user; 
            $data->save();
            return response()->json(['status' => 'success', 'message' => 'Tambah customer berhasil']); 
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
        $data = Customer::findOrFail(Helper::decodex($id));
        $data->id_customer = $id;
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
            'nama_perusahaan'       => 'nullable',
            'nomor_npwp'            => 'nullable',
            'nama_customer'         => 'required',
            'email'                 => 'nullable|email',
            'telpon'                => 'required',
            'alamat'                => 'required'
        ];
 
        $messages = [ 
            'nama_perusahaan.required'  => 'Nama perusahaan wajib diisi', 
            'nomor_npwp.required'       => 'Nomor NPWP wajib diisi',
            'nama_customer.required'    => 'Nama customer wajib diisi',
            'telpon.required'           => 'Nomor telpon wajib diisi',
            'alamat.required'           => 'Alamat wajib diisi',
            'email.required'            => 'Email wajib diisi',
            'email.email'               => 'Format email tidak valid',
        ];
 
        $validator = Validator::make($request->all(), $rules, $messages);
 
        if($validator->fails()){ 
            return response()->json(['status' => 'error_validate', 'message' => $validator->errors()->all()]); 
        }

        try {
            $data = Customer::findOrFail(Helper::decodex($id));
            $data->nama = $request->nama_customer;
            $data->perusahaan = $request->nama_perusahaan;
            $data->email = $request->email;
            $data->telpon = $request->telpon;
            $data->alamat = $request->alamat;
            $data->no_npwp = $request->nomor_npwp; 
            $data->updated_by = Auth::user()->id_user; 
            $data->save();
            return response()->json(['status' => 'success', 'message' => 'Update customer berhasil']); 
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
            Customer::findOrFail(Helper::decodex($id))->delete();
            return response()->json(['status' => 'success', 'message' => 'Hapus customer berhasil']); 
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]); 
        }
    }
}

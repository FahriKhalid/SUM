<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use App\User;
use App\Role;
use Validator;
use Helper;
use Auth;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $info["role"] = Role::get();

        return view('user.index', compact("info"));
    }

    public function data(User $user, Request $request)
    {
        $data = $user->query();

        return Datatables::of($data)->addIndexColumn()->addColumn('action', function ($id){

            return '<a href="javascript:void(0);" did="'.Helper::encodex($id->id_user).'" class="btn btn-sm btn-primary edit"><i class="fa fa-edit"></i></a>
            <a href="javascript:void(0);" url="/user/destroy/'.Helper::encodex($id->id_user).'" class="btn btn-sm btn-danger hapus"><i class="fa fa-trash"></i></a>';

        })->addColumn('is_aktif', function($data){ 

            if($data->is_aktif == 1){
                return '<span class="badge badge-success">Aktif</span>';
            }else{
                return '<span class="badge badge-danger">Tidak aktif</span>';
            }
 
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
            'nama'            => 'required|string|max:50',
            'email'           => 'required|unique:ms_user,email|max:50',
            'role'            => 'required|exists:ms_role,id_role', 
            'status'          => 'required|numeric',
            'username'        => 'required|unique:ms_user,username|max:50',
            'password'        => 'required|confirmed|min:4',
        ];
 
        $messages = [
            'nama.required'         => 'Nama wajib diisi', 
            'nama.max'              => 'Panjang nama tidak boleh dari 50 karakter', 
            'nama.string'           => 'Nama harus berupa huruf',
            'email.required'        => 'Email wajib diisi',
            'email.email'           => 'Email tidak valid',
            'email.max'             => 'Panjang email tidak boleh dari 50 karakte',
            'email.unique'          => 'Email sudah pernah terdaftar pilih email yang lain',
            'username.required'     => 'Username wajib diisi', 
            'username.max'          => 'Panjang username tidak boleh dari 50 karakte',
            'username.unique'       => 'Username sudah pernah terdaftar pilih username yang lain',
            'role.required'         => 'Role wajib diisi',
            'role.exists'           => 'Role tidak valid',
            'status.required'       => 'Status wajib diisi',
            'status.numeric'        => 'Status harus berupa angka',    
            'password.required'     => 'Password wajib diisi',
            'password.confirmed'    => 'Konfirmasi password tidak sama dengan password',
            'password.min'          => 'Panjang password tidak boleh kurang dari 4 karakter'
        ];
 
        $validator = Validator::make($request->all(), $rules, $messages);
 
        if($validator->fails()){ 
            return response()->json(['status' => 'error', 'message' => $validator->errors()->all()]); 
        }

        try {
            $data = new User;
            $data->nama = $request->nama;
            $data->email = $request->email;
            $data->username = $request->username;
            $data->password = bcrypt($request->password);
            $data->is_aktif = $request->status; 
            $data->id_role = $request->role; 
            $data->created_by = Auth::user()->id_user; 
            $data->save();
            return response()->json(['status' => 'success', 'message' => 'Tambah user berhasil']); 
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
        $data = User::findOrFail(Helper::decodex($id));
        $data->id_user = $id;
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
        $id =  Helper::decodex($id);

        $rules = [
            'nama'            => 'required|string|max:50',
            'email'           => 'required|unique:ms_user,email,'.$id.',id_user|max:50',
            'role'            => 'required|exists:ms_role,id_role', 
            'status'          => 'required|numeric',
            'username'        => 'required|unique:ms_user,username,'.$id.',id_user|max:50', 
        ];
        
        if($request->is_change_password == 1){
            $rule_password = ['password' => 'required|confirmed|min:4'];
            $rules = array_merge($rules, $rule_password);
        }

        $messages = [
            'nama.required'         => 'Nama wajib diisi', 
            'nama.max'              => 'Panjang nama tidak boleh dari 50 karakter', 
            'nama.string'           => 'Nama harus berupa huruf',
            'email.required'        => 'Email wajib diisi',
            'email.email'           => 'Email tidak valid',
            'email.max'             => 'Panjang email tidak boleh dari 50 karakte',
            'email.unique'          => 'Email sudah pernah terdaftar pilih email yang lain',
            'username.required'     => 'Username wajib diisi', 
            'username.max'          => 'Panjang username tidak boleh dari 50 karakte',
            'username.unique'       => 'Username sudah pernah terdaftar pilih username yang lain',
            'role.required'         => 'Role wajib diisi',
            'role.exists'           => 'Role tidak valid',
            'status.required'       => 'Status wajib diisi',
            'status.numeric'        => 'Status harus berupa angka',     
            'password.required'     => 'Password wajib diisi',
            'password.confirmed'    => 'Konfirmasi password tidak sama dengan password',
            'password.min'          => 'Panjang password tidak boleh kurang dari 4 karakter'
        ];
 
        $validator = Validator::make($request->all(), $rules, $messages);
 
        if($validator->fails()){ 
            return response()->json(['status' => 'error', 'message' => $validator->errors()->all()]); 
        }

        try {
            $data = User::findOrFail($id);
            $data->nama = $request->nama;
            $data->email = $request->email;
            $data->username = $request->username;
            $data->is_aktif = $request->status; 
            $data->id_role = $request->role; 
            $data->updated_by = Auth::user()->id_user; 

            if($request->is_change_password == 1 && $request->password != null){
                $data->password = bcrypt($request->password);
            }

            $data->save();
            return response()->json(['status' => 'success', 'message' => 'Update user berhasil']); 
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
            $user = User::findOrFail(Helper::decodex($id));
            $user->update(['deleted_by' => Auth::user()->id_user]);
            $user->delete();
            return response()->json(['status' => 'success', 'message' => 'Hapus user berhasil']); 
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]); 
        }
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request; 
use App\User;
use Validator;
use Session; 
use Helper;
use Hash;
use Auth;

class AuthController extends Controller
{
    public function index()
    {
    	return view('login.index');
    }

    public function authenticate(Request $request)
    {
    	$rules = [
            'username'              => 'required',
            'password'              => 'required'
        ];
 
        $messages = [
            'username.required'     => 'Username wajib diisi', 
            'password.required'     => 'Password wajib diisi'
        ];
 
        $validator = Validator::make($request->all(), $rules, $messages);
 
        if($validator->fails()){ 
            return response()->json(['status' => 'error', 'message' => $validator->errors()->all()]); 
        }
 
        $data = [
            'username'  => $request->input('username'),
            'password'  => $request->input('password'),
        ];

        $remember = $request->remember ? true : false;
 
        Auth::attempt($data, $remember);
 
        if (Auth::check()) {  
            return response()->json(['status' => 'success', 'message' => 'Autentifikasi berhasil']); 
        } else { 
            return response()->json(['status' => 'error', 'message' => 'Email atau password anda salah']); 
        }
    }

    public function logout(){
        Auth::logout();
        return redirect()->to('/');
    }

    public function profil()
    {
        return view('profil.index');
    }

    public function updateProfil(Request $request, $id)
    {
        $id =  Helper::decodex($id);

        $rules = [
            'nama'            => 'required|string|max:50',
            'email'           => 'required|unique:ms_user,email,'.$id.',id_user|max:50', 
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
}

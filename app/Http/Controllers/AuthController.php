<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request; 
use App\User;
use Validator;
use Hash;
use Session; 
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
 
        Auth::attempt($data);
 
        if (Auth::check()) { 
            //Login Success 
            return response()->json(['status' => 'success', 'message' => 'Autentifikasi berhasil']); 
 
        } else { // false
 
            return response()->json(['status' => 'error', 'message' => 'Email atau password anda salah']); 
        }
    }

    public function logout(){
        Auth::logout();
        return redirect()->to('/');
    }
}

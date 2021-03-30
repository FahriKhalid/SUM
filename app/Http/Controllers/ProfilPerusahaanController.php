<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\ProfilPerusahaan;
use App\ATM;
use Validator;
use Helper;
use Auth;
use DB;

class ProfilPerusahaanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $info["data"] = ProfilPerusahaan::first();
        $info["atm"]  = ATM::where("is_aktif", 1)->get();
        return view('profil_perusahaan.index', compact('info'));
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
    public function edit()
    { 
        $info["data"] = ProfilPerusahaan::first();
        $info["atm"] = ATM::get();

        return view('profil_perusahaan.edit', compact("info"));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $rules = [
            'nama'                  => 'required',
            'direktur'              => 'required', 
            'telepon'               => 'required', 
            'alamat'                => 'required',
        ];
 
        $messages = [
            'nama.required'         => 'Nama perusahaan wajib diisi',  
            'direktur.required'     => 'Nama direktur wajib diisi',  
            'telepon.required'      => 'Nomor telepon wajib diisi',   
            'alamat.required'       => 'Alamat wajib diisi'  
        ];
        
        if($request->has('new_atm')){
            $rules_a = [
                'new_atm.*'           => 'required',
                'new_no_atm.*'        => 'required', 
                'new_status.*'        => 'required',  
            ];
     
            $messages_a = [
                'new_atm.*.required'        => 'Nama ATM wajib diisi',  
                'new_no_atm.*.required'     => 'Nomor ATM wajib diisi',  
                'new_status.*.required'     => 'Status wajib diisi',   
            ];

            $rules = array_merge($rules, $rules_a);
            $messages = array_merge($messages, $messages_a);
        }

        if($request->has('atm')){
            $rules_b = [
                'atm.*'           => 'required',
                'no_atm.*'        => 'required', 
                'status.*'        => 'required',  
            ];
     
            $messages_b = [
                'atm.*.required'        => 'Nama ATM wajib diisi',  
                'no_atm.*.required'     => 'Nomor ATM wajib diisi',  
                'status.*.required'     => 'Status wajib diisi',   
            ];

            $rules = array_merge($rules, $rules_b);
            $messages = array_merge($messages, $messages_b);
        }


        $validator = Validator::make($request->all(), $rules, $messages);
    
        if($validator->fails()){ 
            return response()->json(['status' => 'error', 'message' => $validator->errors()->all()]); 
        } 

        DB::beginTransaction();
        try {
            $data = ProfilPerusahaan::first();
            $data->nama = $request->nama;
            $data->direktur = $request->direktur;
            $data->telepon = $request->telepon;
            $data->fax = $request->fax;
            $data->alamat = $request->alamat;
            $data->save();

            if($request->has('new_atm')){
                for ($i=0; $i < count($request->new_atm); $i++) { 
                    $data_atm[] = [
                        "nama" => $request->new_atm[$i],
                        "nomor" => $request->new_no_atm[$i],
                        "is_aktif" => $request->new_status[$i],
                        "created_by" => Auth::user()->id_user
                    ];
                }

                ATM::insert($data_atm);
            }

            if($request->has('atm')){
                for ($i=0; $i < count($request->atm); $i++) { 
                    $update_atm = [
                        "nama" => $request->atm[$i],
                        "nomor" => $request->no_atm[$i],
                        "is_aktif" => $request->status[$i],
                        "updated_by" => Auth::user()->id_user
                    ];

                    ATM::findOrFail(Helper::decodex($request->id_atm[$i]))->update($update_atm);
                }
            }

            DB::commit();

            $info["atm"] = ATM::get();

            return response()->json([
                'status' => 'success', 
                'message' => 'Update profil perusahaan berhasil',
                'list_atm' => view('profil_perusahaan.list_atm', compact('info'))->render()
            ]); 
        } catch (\Exception $e) { 
            DB::rollback();
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
        $id_atm = Helper::decodex($id);

        try {
            ATM::findOrFail($id_atm)->delete();
            $info["atm"] = ATM::get();
            return response()->json([
                'status' => 'success', 
                'message' => 'Hapus ATM berhasil',
                'list_atm' => view('profil_perusahaan.list_atm', compact('info'))->render()
            ]); 

        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]); 
        }
    }
}

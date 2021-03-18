<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Support\Str; 
use Yajra\Datatables\Datatables;
use App\Services\PembayaranService;
use App\Services\BookingService;
use App\Pembayaran;
use App\PreOrder;
use App\Booking;
use App\Barang;
use Validator;
use Helper;
use Auth;
use DB;

class BookingController extends Controller
{
    protected $PembayaranService, $BookingService;

    public function __construct(PembayaranService $PembayaranService, BookingService $BookingService){  
        $this->PembayaranService = $PembayaranService;
        $this->BookingService = $BookingService;
    }

    public function show($id)
    {
    	$id_pre_order = Helper::decodex($id); 

        $info["booking"] = Booking::with('PreOrder')->where("id_pre_order", $id_pre_order)->first();

        if($info["booking"])
        {
            $info["pembayaran"] = Pembayaran::with('CreatedBy')->where("id_booking", $info["booking"]->id_booking)->get(); 

            $info["last_record"] = $this->PembayaranService->lastRecord("pembelian", $info["booking"]->id_booking);

            $info["piutang"] = $this->PembayaranService->sisaHutang("pembelian", $info["booking"]->id_booking);  
        }

    	return view("booking.show", compact("info", "id"));
    }

    public function data(Booking $booking, Request $request, $id)
    {
    	$id = Helper::decodex($id);

    	$data = $booking->query()->with('CreatedBy')->where("id_pre_order", $id);

        return Datatables::of($data)->addIndexColumn()->addColumn('action', function ($data){ 

            return '<div class="btn-group btn-group-sm" role="group">
                    <button id="btnGroupDrop1" type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                      Aksi
                    </button>
                    <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                        <a class="dropdown-item detail" id_booking="'.Helper::encodex($data->id_booking).'" url="'.url('booking/detail/'.Helper::encodex($data->id_booking)).'"><i class="fa fa-search"></i> Detail</a> 
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item edit" url="'.url("booking/edit/".Helper::encodex($data->id_booking)).'"><i class="fa fa-edit"></i> Edit</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item hapus" url="'.url('booking/destroy/'.Helper::encodex($data->id_booking)).'"  href="javascript:void(0);"><i class="fa fa-trash"></i> Hapus</a>
                    </div>
                  </div>';

        })->addColumn('total_pembayaran', function($data){

        	return '<div class="d-flex justify-content-between">
        	<div>Rp</div>
        	<div>'.Helper::currency($data->total_pembayaran).'</div>
        	</div>';

        })->addColumn('file_skpp', function($data){

        	return '<a target="_blank" href="'.asset("skpp/".$data->file_skpp).'">'.$data->file_skpp.'</a>';

        })->addColumn('created_by', function($data){ 

            return $data->CreatedBy->nama;
            
        })->addColumn('terakhir_pembayaran', function($data){ 

            return Helper::dateIndo($data->terakhir_pembayaran);
            
        })->addColumn('created_on', function($data){ 

            return $data->created_on;
            
        })->rawColumns(['action', 'total_pembayaran', 'file_skpp'])->make(true);
    }

    public function store(Request $request, $id)
    {
    	$id_booking = Helper::decodex($id);

        $rules = [
        	'id_pre_order'			=> 'required',
        	'no_skpp'				=> 'required',
            'file_skpp'          	=> 'required|max:2000|mimes:pdf',
        	'total_pembayaran'		=> 'required',
            'terakhir_pembayaran'   => 'required', 
        ]; 
 
        $messages = [
        	'id_pre_order.required'			=> 'Pre order wajib diisi', 
        	'no_skpp.required' 				=> 'Nomor SKPP wajib diisi',
            'file_skpp.required'  				=> 'File SKPP pembayaran wajib diisi', 
            'file_skpp.max'       				=> 'Ukuran file terlalu besar, maks 2 Mb',
            'file_skpp.mimes'     				=> 'Ekstensi file yang diizinkan hanya PDF',
        	'terakhir_pembayaran.required' 	=> 'Terakhir pembayaran wajib diisi',
        ];
 
        $validator = Validator::make($request->all(), $rules, $messages);
 
        if($validator->fails()){ 
            return response()->json(['status' => 'error_validate', 'message' => $validator->errors()->all()]); 
        }  


        try {
        	$data = new Booking;
            $data->id_pre_order = Helper::decodex($request->id_pre_order); 
            $data->no_skpp = $request->no_skpp;
            $data->total_pembayaran = Helper::decimal($request->total_pembayaran);
            $data->terakhir_pembayaran = Helper::dateFormat($request->terakhir_pembayaran, true, 'Y-m-d');
            $data->created_by = Auth::user()->id_user;

            $namafile = 'SKPP-'.$request->no_skpp.'-'.Str::random(4).'.'.$request->file_skpp->getClientOriginalExtension();
            $data->file_skpp = $namafile;
            $request->file_skpp->move('file_skpp', $namafile);

            $data->save();
            return response()->json(['status' => 'success', 'message' => 'Tambah booking berhasil']);
        } catch (\Exception $e) {
        	return response()->json(['status' => 'error', 'message' => $e->getMessage()]); 
        }
    }

    public function edit($id)
    {
    	$id = Helper::decodex($id);

    	$data = Booking::findOrFail($id);
    	$data->id_booking = Helper::encodex($data->id_booking);
    	$data->total_pembayaran = Helper::currency($data->total_pembayaran);
    	return response()->json($data);
    }


    public function update(Request $request)
    {
    	$id = Helper::decodex($request->id);

    	$rules = [
    		'id'					=> 'required',
        	'id_pre_order'			=> 'required', 
        	'no_skpp'				=> 'required',
           	'file_skpp'          	=> 'nullable|max:2000|mimes:pdf',
        	'total_pembayaran'		=> 'required',
            'terakhir_pembayaran'   => 'required', 
        ]; 
 
        $messages = [
        	'id.required'					=> 'Id booking wajib diisi',
        	'id_pre_order.required'			=> 'Pre order wajib diisi', 
        	'no_skpp.required' 				=> 'Nomor SKPP wajib diisi',
            'file_skpp.required'  			=> 'File SKPP pembayaran wajib diisi', 
            'file_skpp.max'       			=> 'Ukuran file terlalu besar, maks 2 Mb',
            'file_skpp.mimes'     			=> 'Ekstensi file yang diizinkan hanya PDF',
        	'terakhir_pembayaran.required' 	=> 'Terakhir pembayaran wajib diisi',
        ];
 
        $validator = Validator::make($request->all(), $rules, $messages);
 
        if($validator->fails()){ 
            return response()->json(['status' => 'error_validate', 'message' => $validator->errors()->all()]); 
        }  


        try {
        	$data = Booking::findOrFail($id);
            $this->BookingService->validateUpdateHarga($data, Helper::decimal($request->total_pembayaran));

            $data->id_pre_order = Helper::decodex($request->id_pre_order);
            $data->no_booking = $request->no_booking;
            $data->no_skpp = $request->no_skpp;  
            $data->total_pembayaran = Helper::decimal($request->total_pembayaran);
            $data->terakhir_pembayaran = Helper::dateFormat($request->terakhir_pembayaran, true, 'Y-m-d');
            $data->updated_by = Auth::user()->id_user;

            if($request->has('file_skpp')){
            	$namafile = 'SKPP-'.$request->no_skpp.'-'.Str::random(4).'.'.$request->file_skpp->getClientOriginalExtension();
	            $data->file_skpp = $namafile;
	            $request->file_skpp->move('file_skpp', $namafile);
            }

            $data->save();
            return response()->json(['status' => 'success', 'message' => 'Update booking berhasil']);
        } catch (\Exception $e) {
        	return response()->json(['status' => 'error', 'message' => $e->getMessage()]); 
        }
    }


    // public function detail($id)
    // {
    //     $id = Helper::decodex($id);

    //     $info["booking"] = Booking::with('PreOrder')->findOrFail($id); 
    //     $info["id_pre_order"] = $info["booking"]->id_pre_order;
    //     $info["pembayaran"] = Pembayaran::with('CreatedBy')->where("id_booking", $id)->get(); 
    //     $info["last_record"] = $this->PembayaranService->lastRecord("pembelian", $id);
    //     $info["piutang"] = $this->PembayaranService->sisaHutang("pembelian", $id); 

    //     return response()->json([ 
    //         'html' => view('booking.detail_booking', compact('info', 'id'))->render()
    //     ]); 
    // }


    public function destroy($id)
    {
        $id = Helper::decodex($id);
        try {
            Booking::findOrFail($id)->delete();
            return response()->json(['status' => 'success', 'message' => 'Hapus SKPP berhasil']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function sisa_pembayaran($id)
    {
        $id = Helper::decodex($id); 
        $info["sisa_pembayaran"] = $this->PembayaranService->sisaHutang("pembelian", $id); 
        return response()->json($info["sisa_pembayaran"]);
    }


    public function sisa_barang($id)
    {
        $id = Helper::decodex($id);  

        $info["barang"] = Barang::with('Produk')->where("id_pre_order", $id)->get();

        return response()->json([
            'html' => view('booking.table_barang', compact('info', 'id'))->render()
        ]);
    }

}
















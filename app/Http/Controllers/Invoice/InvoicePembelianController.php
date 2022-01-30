<?php

namespace App\Http\Controllers\Invoice;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use App\Invoice;
use App\SKPP;
use App\SO;
use Validator;
use Helper;
use Auth;
use PDF;
use DB;

class InvoicePembelianController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($id)
    {
        $id_pre_order = Helper::decodex($id);
        $info["skpp"] = SKPP::where("id_pre_order", $id_pre_order)->first();  
        return view('invoice.pembelian.index', compact('info','id'));
    }


    /**
     * Datatable invoice
     *
     * @return \Illuminate\Http\Response
     */
    public function data(Invoice $invoice, Request $request, $id = null)
    {  
        $data = $invoice->query()->where(function($query) use ($id){
            if($id != null){
                $query->where("id_pre_order", Helper::decodex($id));
            }
        });

        return Datatables::of($data)->addIndexColumn()->addColumn('action', function ($data){
            return '<div class="btn-group btn-group-sm" role="group">
                    <button id="btnGroupDrop1" type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                      Aksi
                    </button>
                    <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                        <a class="dropdown-item detail" url="'.url('pembelian/invoice/show/'.Helper::encodex($data->id_invoice)).'"><i class="fa fa-search"></i> Detail</a> 
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item edit" url="'.url("pembelian/invoice/edit/".Helper::encodex($data->id_invoice)).'"><i class="fa fa-edit"></i> Edit</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item hapus" url="'.url('pembelian/invoice/destroy/'.Helper::encodex($data->id_invoice)).'"  href="javascript:void(0);"><i class="fa fa-trash"></i> Hapus</a>
                    </div>
                </div>';

        })

        ->addColumn('created_by', function($data){ 
 
            return $data->CreatedBy->nama;
            
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
            'nomor_tagihan'              => 'required|unique:tr_invoice,no_tagihan',
            'file_faktur_pajak'          => 'required|max:2000|mimes:pdf',
            'file_invoice'               => 'required|max:2000|mimes:pdf', 
        ]; 
 
        $messages = [
            'nomor_tagihan.required'        => 'Nomor tagihan wajib diisi', 
            'nomor_tagihan.unique'          => 'Nomor tagihan sudah ada, pilih no tagihan lainnya', 
            'file_faktur_pajak.required'    => 'File faktur pajak wajib diisi',
            'file_faktur_pajak.max'         => 'Ukuran file faktur pajak terlalu besar. Maks 2 Mb',
            'file_faktur_pajak.mimes'       => 'Ekstensi file faktur pajak tidak valid',
            'file_invoice.required'         => 'File invoice wajib diisi',
            'file_invoice.max'              => 'Ukuran file invoice terlalu besar. Maks 2 Mb',
            'file_invoice.mimes'            => 'Ekstensi file invoice tidak valid'
        ];

        $validator = Validator::make($request->all(), $rules, $messages);
 
        if($validator->fails()){ 
            return response()->json(['status' => 'error_validate', 'message' => $validator->errors()->all()]); 
        }

        try {
            $invoice = new Invoice(); 
            $invoice->id_pre_order = Helper::decodex($request->id_pre_order);
            $invoice->no_tagihan = $request->nomor_tagihan;
            $file_faktur_pajak = Helper::fileUpload($request->file_faktur_pajak, "faktur_pajak");
            $file_invoice = Helper::fileUpload($request->file_invoice, "faktur_pajak", Helper::RemoveSpecialChar($request->nomor_tagihan));
            $invoice->file_faktur_pajak = $file_faktur_pajak;
            $invoice->file_invoice = $file_invoice;
            $invoice->created_by = Auth::user()->id_user;
            $invoice->save();

            return response()->json(["status" => "success", "message" => "Tambah invoice berhasil"]);   
        } catch (\Exception $e) {
            return response()->json(["status" => "error", "message" => $e->getMessage()]);   
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
        $id_invoice = Helper::decodex($id);
        $info["invoice"] = Invoice::findOrFail($id_invoice);

        return response()->json([ 
            'html' => view('invoice.pembelian.detail_invoice', compact('info', 'id'))->render()
        ]); 
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $id_invoice = Helper::decodex($id);
        $info["invoice"] = Invoice::findOrFail($id_invoice);
        $info["invoice"]->id_invoice = Helper::encodex($info["invoice"]->id_invoice);
        return response()->json($info);
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
        $id_invoice = Helper::decodex($id);

        $rules = [ 
            'nomor_tagihan'              => 'required|unique:tr_invoice,no_tagihan,'.$id_invoice.',id_invoice',
            'file_faktur_pajak'          => 'nullable|max:2000|mimes:pdf',
            'file_invoice'               => 'nullable|max:2000|mimes:pdf', 
        ]; 
 
        $messages = [
            'nomor_tagihan.required'        => 'Nomor tagihan wajib diisi', 
            'nomor_tagihan.unique'          => 'Nomor tagihan sudah ada, pilih no tagihan lainnya',  
            'file_faktur_pajak.max'         => 'Ukuran file faktur pajak terlalu besar. Maks 2 Mb',
            'file_faktur_pajak.mimes'       => 'Ekstensi file faktur pajak tidak valid', 
            'file_invoice.max'              => 'Ukuran file invoice terlalu besar. Maks 2 Mb',
            'file_invoice.mimes'            => 'Ekstensi file invoice tidak valid'
        ];

        $validator = Validator::make($request->all(), $rules, $messages);
 
        if($validator->fails()){ 
            return response()->json(['status' => 'error_validate', 'message' => $validator->errors()->all()]); 
        }

        try {

            $invoice = Invoice::findOrFail($id_invoice); 
            $invoice->no_tagihan = $request->nomor_tagihan;

            if($request->has('file_faktur_pajak')){
                Helper::removeFile('faktur_pajak', $invoice->file_faktur_pajak);
                $file_faktur_pajak = Helper::fileUpload($request->file_faktur_pajak, "faktur_pajak");
                $invoice->file_faktur_pajak = $file_faktur_pajak;
            }

            if($request->has('file_invoice')){
                Helper::removeFile('faktur_pajak', $invoice->file_invoice);
                $file_invoice = Helper::fileUpload($request->file_invoice, "faktur_pajak", Helper::RemoveSpecialChar($request->nomor_tagihan));
                $invoice->file_invoice = $file_invoice;
            }

            $invoice->updated_by = Auth::user()->id_user;
            $invoice->save();

            return response()->json(["status" => "success", "message" => "Update invoice berhasil"]);   
        } catch (\Exception $e) {
            return response()->json(["status" => "error", "message" => $e->getMessage()]);   
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
        $id = Helper::decodex($id); 

        try {
            Invoice::findOrFail($id)->delete(); 
            return response()->json(['status' => 'success', 'message' => 'Hapus invoice berhasil']); 
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]); 
        } 
    }
}

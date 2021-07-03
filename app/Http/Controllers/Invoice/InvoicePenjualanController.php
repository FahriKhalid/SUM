<?php

namespace App\Http\Controllers\Invoice;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use App\Services\SalesOrderPenjualanService;
use App\Invoice;
use App\Barang;
use App\SKPP;
use App\SO;
use Validator;
use Helper;
use Auth;
use PDF;
use DB;

class InvoicePenjualanController extends Controller
{
    protected $status_delivered = 5;
    protected $SalesOrderPenjualanService;

    public function __construct(SalesOrderPenjualanService $SalesOrderPenjualanService)
    {   
        $this->SalesOrderPenjualanService = $SalesOrderPenjualanService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($id)
    {
        $info["skpp"] = SKPP::findOrFail(Helper::decodex($id));

        return view('invoice.penjualan.index', compact('info', 'id')); 
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
                $query->where("id_skpp", Helper::decodex($id));
            }
        })->with('SKPP');

        return Datatables::of($data)->addIndexColumn()->addColumn('action', function ($data){
            return '<div class="btn-group btn-group-sm" role="group">
                    <button id="btnGroupDrop1" type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                      Aksi
                    </button>
                    <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                        <a class="dropdown-item detail" url="'.url('penjualan/invoice/show/'.Helper::encodex($data->id_invoice)).'"><i class="fa fa-search"></i> Detail</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" target="_blank" href="'.url('penjualan/invoice/surat/'.Helper::encodex($data->id_invoice)).'"><i class="fa fa-download"></i> Download</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="'.url("penjualan/invoice/edit/".Helper::encodex($data->id_invoice)).'"><i class="fa fa-edit"></i> Edit</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item hapus" url="'.url('penjualan/invoice/destroy/'.Helper::encodex($data->id_invoice)).'"  href="javascript:void(0);"><i class="fa fa-trash"></i> Hapus</a>
                    </div>
                </div>';

        })->addColumn('no_tagihan', function($data){ 
            return $data->no_tagihan == null ? '-' : $data->no_tagihan;
        })->addColumn('no_faktur_pajak', function($data){ 
            return $data->no_faktur_pajak == null ? '-' : $data->no_faktur_pajak;
        })->addColumn('customer', function($data){ 
            return $data->SKPP->Customer->nama;
        })->addColumn('no_skpp', function($data){ 
            return $data->SKPP->no_skpp;
        })->addColumn('no_so', function($data){ 
            return $data->SO->no_so == null ? '-' : $data->SO->no_so;
        })->addColumn('total', function($data){ 
            return '<div class="d-flex justify-content-between">
                <div>IDR</div>
                <div>'.Helper::currency($data->total).'</div>
            </div>';
        })->rawColumns(['action','total'])->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($id)
    {
        $id_skpp = Helper::decodex($id);
        $info["skpp"] = SKPP::selectRaw("*, left(no_skpp, 4) as no_dokumen")->findOrFail($id_skpp);   
        $info["so"] = SO::where("id_skpp", $id_skpp)->get();
        $info["po"] = Barang::with('Produk')->where("id_skpp", $id_skpp)->get();

        return view('invoice.penjualan.create', compact('info', 'id'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $id)
    { 
        $rules = [
            'tanggal'               => 'required',
            'nomor_tagihan'         => 'required|string|max:4|min:4',
            'nomor_faktur_pajak'    => 'required|unique:tr_invoice,no_faktur_pajak',
            'so'                    => 'required',
            'nomor_resi'            => 'nullable',
            'sub_total'             => 'required',  
            'ppn'                   => 'required',  
            'total'                 => 'required', 
            'file_faktur_pajak'     => 'required|max:2000|mimes:png,jpg,jpeg,pdf', 
        ]; 
 
        $messages = [
            'tanggal.required'              => 'Tanggal wajib diisi',
            'nomor_tagihan.required'        => 'Nomor tagihan wajib diisi', 
            'nomor_tagihan.max'             => 'Karakter nomor tagihan terlalu panjang. Maks 4 karakter',
            'nomor_tagihan.min'             => 'Karakter nomor tagihan terlalu pendek. Min 4 karakter',
            'nomor_faktur_pajak.required'   => 'Nomor faktur pajak wajib diisi', 
            'so.required'                   => 'Sales order wajib diisi',
            'nomor_resi.required'           => 'Nomor resi wajib diisi',  
            'sub_total.required'            => 'Sub total wajib diisi', 
            'ppn.required'                  => 'PPN wajib diisi', 
            'id_po.required'                => 'PO wajib diisi', 
            'file_faktur_pajak.required'    => 'File faktur pajak wajib diisi',
            'file_faktur_pajak.max'         => 'Ukuran file faktur terlalu besar. Maks 2 Mb',
            'file_faktur_pajak.mimes'       => 'Ekstensi file faktur tidak valid'
        ];

        $validator = Validator::make($request->all(), $rules, $messages);
 
        if($validator->fails()){ 
            return response()->json(['status' => 'error_validate', 'message' => $validator->errors()->all()]); 
        }

        DB::beginTransaction();
        try {
            $id_skpp = Helper::decodex($id);
            SKPP::findOrFail($id_skpp);

            $file = $request->file('file_faktur_pajak');
            $tujuan_upload = 'faktur_pajak';
            $namafile = 'faktur-pajak-'.$request->nomor_faktur_pajak.'.'.$file->getClientOriginalExtension();

            $invoice = new Invoice();
            $invoice->tanggal = Helper::dateFormat($request->tanggal, true, 'Y-m-d');
            $invoice->id_skpp = $id_skpp;
            $invoice->id_so = Helper::decodex($request->so);
            $invoice->no_tagihan = $request->nomor_tagihan;
            $invoice->no_faktur_pajak = $request->nomor_faktur_pajak;
            $invoice->no_resi = $request->nomor_resi;
            $invoice->ppn = Helper::decimal($request->ppn);
            $invoice->total = Helper::decimal($request->total);
            $invoice->created_by = Auth::user()->id_user; 
            $invoice->file_faktur_pajak = $namafile;
            $invoice->save();

            $file->move($tujuan_upload, $namafile);

            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'Tambah invoice berhasil']); 
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]); 
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store_sementara(Request $request, $id)
    { 
        $id_skpp = Helper::decodex($id);

        $rules = [ 
            'tanggal'       => 'required',
            'id_po.*'       => 'required',
            'kuantitas.*'   => 'required|min:1',
            'harga_jual.*'  => 'required',
            'nilai.*'       => 'nullable',
            'sub_total'     => 'required',  
            'ppn'           => 'required',  
            'total'         => 'required',  
        ]; 
 
        $messages = [ 
            'tanggal.required'          => 'Tanggal tidak boleh kosong',           
            'id_po.*.required'          => 'Id PO wajib diisi',
            'kuantitas.*.required'      => 'Kuantitas wajib diisi',
            'kuantitas.*.min'           => 'Kuantitas tidak boleh 0',
            'harga_jual.*.required'     => 'Harga jual wajib diisi',  
            'nilai.*.required'          => 'Nilai wajib diisi',
            'sub_total.required'        => 'Sub total wajib diisi', 
            'ppn.required'              => 'PPN wajib diisi', 
            'id_po.required'            => 'PO wajib diisi',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);
 
        if($validator->fails()){ 
            return response()->json(['status' => 'error_validate', 'message' => $validator->errors()->all()]); 
        }

        DB::beginTransaction();
        try {
            
            $result = $this->SalesOrderPenjualanService->storeSoSementara($request, $id_skpp);

            $invoice = new Invoice();
            $invoice->tanggal = Helper::dateFormat($request->tanggal, true, 'Y-m-d');
            $invoice->id_skpp = $id_skpp;
            $invoice->id_so = $result["id_so"];
            $invoice->ppn = Helper::decimal($request->ppn);
            $invoice->total = Helper::decimal($request->total);
            $invoice->created_by = Auth::user()->id_user;  
            $invoice->save();

            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'Tambah invoice berhasil']); 
        } catch (\Exception $e) {
            DB::rollback();
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
        $id_invoice = Helper::decodex($id);

        $info["invoice"] = Invoice::findOrFail($id_invoice);

        return response()->json([ 
            'html' => view('invoice.penjualan.detail_invoice', compact('info', 'id'))->render()
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
        $info["invoice"] = Invoice::with('SO')->findOrFail($id_invoice);
        
        return view('invoice.penjualan.edit', compact('info', 'id'));
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
            'nomor_tagihan'         => 'required|string|max:4|min:4',
            'nomor_faktur_pajak'    => 'required|unique:tr_invoice,no_faktur_pajak,'.$id_invoice.',id_invoice', 
            'sub_total'             => 'required',  
            'ppn'                   => 'required',  
            'nomor_resi'            => 'nullable',
            'total'                 => 'required', 
            'file_faktur_pajak'     => 'nullable|max:2000|mimes:png,jpg,jpeg,pdf', 
        ]; 
 
        $messages = [
            'nomor_tagihan.required'        => 'Nomor tagihan wajib diisi',  
            'nomor_tagihan.max'             => 'Karakter nomor tagihan terlalu panjang. Maks 4 karakter',
            'nomor_tagihan.min'             => 'Karakter nomor tagihan terlalu pendek. Min 4 karakter',
            'nomor_faktur_pajak.required'   => 'Nomor faktur pajak wajib diisi', 
            'nomor_resi.required'                   => 'Nomor resi wajib diisi',  
            'nomor_faktur_pajak.unique'     => 'Nomor faktur pajak sudah pernah terdaftar pilih nomor faktur pajak yang lain',   
            'sub_total.required'            => 'Sub total wajib diisi', 
            'ppn.required'                  => 'PPN wajib diisi', 
            'id_po.required'                => 'PO wajib diisi', 
            'file_faktur_pajak.required'    => 'File faktur pajak wajib diisi',
            'file_faktur_pajak.max'         => 'Ukuran file faktur terlalu besar. Maks 2 Mb',
            'file_faktur_pajak.mimes'       => 'Ekstensi file faktur tidak valid'
        ];

        $validator = Validator::make($request->all(), $rules, $messages);
 
        if($validator->fails()){ 
            return response()->json(['status' => 'error_validate', 'message' => $validator->errors()->all()]); 
        }


        DB::beginTransaction();
        try {  

            $invoice = Invoice::findOrFail($id_invoice); 
            $invoice->no_tagihan = $request->nomor_tagihan;
            $invoice->no_faktur_pajak = $request->nomor_faktur_pajak;
            $invoice->no_resi = $request->nomor_resi;
            $invoice->ppn = Helper::decimal($request->ppn);
            $invoice->total = Helper::decimal($request->total);
            $invoice->updated_by = Auth::user()->id_user; 
            
            if($request->has('file_faktur_pajak'))
            { 
                if(file_exists('faktur_pajak/'.$invoice->file_faktur_pajak)){
                    unlink('faktur_pajak/'.$invoice->file_faktur_pajak);
                }

                $file = $request->file('file_faktur_pajak');
                $tujuan_upload = 'faktur_pajak';
                $namafile = 'faktur-pajak-'.$request->nomor_faktur_pajak.'.'.$file->getClientOriginalExtension();

                $invoice->file_faktur_pajak = $namafile;
                $file->move($tujuan_upload, $namafile);
            }

            $invoice->save(); 

            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'Update invoice berhasil']); 
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
        $id = Helper::decodex($id); 

        try {
            Invoice::findOrFail($id)->delete(); 
            return response()->json(['status' => 'success', 'message' => 'Hapus invoice berhasil']); 
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]); 
        } 
    }

    public function surat($id)
    {
        $id_invoice = Helper::decodex($id);  

        $info["invoice"] = Invoice::with("SKPP")->findOrFail($id_invoice);  
        $info["profil_perusahaan"]  = DB::table("ms_profil_perusahaan")->first();
        $pdf = PDF::loadview('surat.penjualan.surat_invoice', compact('info')); 

        if ($info["invoice"]->SO->is_sementara == 1) {
            $file = 'Invoice '.date('Y-m-d H:i:s');
        } else {
            $file = Helper::RemoveSpecialChar($info["invoice"]->no_tagihan);
        }
        
        return $pdf->setPaper('a4')->stream($file.'.pdf');  
    }

    public function reset_po($id)
    {
        $id_skpp = Helper::decodex($id);
        $info["po"] = Barang::with('Produk')->where("id_skpp", $id_skpp)->get();

        return response()->json(['html' => view('invoice.penjualan.form_po', compact('info'))->render()]);
    }
}

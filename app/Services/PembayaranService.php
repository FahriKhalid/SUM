<?php

namespace App\Services;
use Illuminate\Support\Str; 
use App\Services\BarangService;
use App\Pembayaran;
use App\SKPP;
use Validator;
use Auth;
use Helper;

class PembayaranService 
{  
	public static function sisaHutang($kategori, $id)
	{
		$hutang = Pembayaran::when($kategori == "penjualan", function($query) use ($id){
                                $query->where("id_skpp", $id);
                            })
                            ->when($kategori == "pembelian", function($query) use ($id){
                                $query->where("id_pre_order", $id);
                            })
                            ->latest()
                            ->first(); 	
        
		if($hutang){
			return $hutang->sisa_hutang;
		}else{ 
			$barang = new BarangService();
			return $barang->total_pembayaran($kategori, $id); 
		}
	} 

	public static function isLunas($kategori, $id)
    {
		$sisa_hutang = self::sisaHutang($kategori, $id);

		if($sisa_hutang == "0.00"){
			return false;
		} else {
			return true;
		}
	}

    public static function isBayar($id)
    {   
        $skpp = SKPP::with("Pembayaran")->findOrFail($id);

        if ($skpp->Pembayaran->sisa_hutang == null) {
            return true;
        } elseif($skpp->Pembayaran->sisa_hutang != null && $skpp->Pembayaran->sisa_hutang > 0){
            return false;
        } 
    }

	public function lastRecord($kategori, $id)
	{
		$data = Pembayaran::when($kategori == "penjualan", function($query) use ($id){
                                $query->where("id_skpp", $id);
                            })
                            ->when($kategori == "pembelian", function($query) use ($id){
                                $query->where("id_pre_order", $id);
                            })
                            ->orderBy("id_pembayaran", "desc")->first(); 
		
		if($data){
			return $data->id_pembayaran;
		}else{
			return null;
		}
	}  

	public function store($request, $id, $kategori)
	{
		$id_header = Helper::decodex($id); 
        $sisa_hutang = 0;

        $rules = [
            'file'          => 'required|max:2000|mimes:png,jpg,jpeg,pdf',
            'keterangan'    => 'nullable|string|max:500', 
        ]; 
 
        $messages = [
            'file.required'  => 'File tanda bukti pembayaran wajib diisi', 
            'file.max'       => 'Ukuran file terlalu besar, maks 2 Mb',
            'file.mimes'     => 'Ekstensi file yang diizinkan hanya jpg, jpeg dan png',
            'keterangan.string' => 'keterangan tidak valid',
            'keterangan.max'    => 'Keterangan tidak boleh lebih dari 500 karakter',
            'jumlah_pembayaran.required' => 'Jumlah pembayaran wajib diisi'
        ];

        if($request->has('kode_booking')){
            $new_rule = [
                'kode_booking' => 'required|unique:tr_pembayaran,kode_booking',
            ];

            $new_message = [
                'kode_booking.unique' => 'Kode booking sudah ada, pilih kode booking yang lain',
            ];

            $rules = array_merge($rules, $new_rule);
            $messages = array_merge($messages, $new_message); 
        }

        if($request->has('is_parsial')){
            $rule_pembayaran = [
                'jumlah_pembayaran' => 'required',
            ];

            $rules = array_merge($rules, $rule_pembayaran); 
        }
 
        $validator = Validator::make($request->all(), $rules, $messages);
 
        if($validator->fails()){ 
            return response()->json(['status' => 'error_validate', 'message' => $validator->errors()->all()]); 
        }  
    
        if(self::isLunas($kategori, $id_header))
        {
            try {
                $store = new Pembayaran;   
                $store->kode_booking = $request->kode_booking == null ? null : $request->kode_booking;
                $store->file_bukti_pembayaran = Helper::fileUpload($request->file, 'bukti_pembayaran');
                $store->keterangan = $request->keterangan; 
                
                if ($kategori == "penjualan") {
                    $store->id_skpp = $id_header;
                } elseif ($kategori == "pembelian") {
                    $store->id_pre_order = $id_header;
                }

                if($request->has('is_parsial')){ 
                    $total = self::sisaHutang($kategori, $id_header);
                    $sisa_hutang = $total - Helper::decimal($request->jumlah_pembayaran); 
                    $store->jumlah_pembayaran = Helper::decimal($request->jumlah_pembayaran);
                    $store->sisa_hutang = $sisa_hutang;
                    $store->is_parsial = 1;
                }else{
                    $pembayaran = self::sisaHutang($kategori, $id_header);
                    $store->jumlah_pembayaran = $pembayaran;
                    $store->sisa_hutang = 0;
                }

                $store->created_by = Auth::user()->id_user; 
                $store->save(); 

                if ($kategori == "penjualan") {
                    return response()->json([
                        'status' => 'success', 
                        'message' => 'Tambah pembayaran berhasil', 
                        'data' => $sisa_hutang
                    ]); 
                } else {
                    $info["pembayaran"] = Pembayaran::where("id_pre_order", $id_header)->get(); 
                    $info["last_record"] = self::lastRecord($kategori, $id_header);
                    $info["piutang"] = self::sisaHutang($kategori, $id_header); 

                    return response()->json([
                        'status' => 'success', 
                        'message' => 'Tambah pembayaran berhasil', 
                        'data' => $sisa_hutang, 
                        'html' => view('booking.table_pembayaran', compact('info', 'id'))->render()
                    ]); 
                }

            } catch (\Exception $e) {
                return response()->json(['status' => 'error', 'message' => $e->getMessage()]); 
            }
        }
        else
        {
            return response()->json(['status' => 'error', 'message' => 'Pembayaran sudah lunas']); 
        }
	}
}

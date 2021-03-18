<?php

namespace App\Services;
use App\Services\StokService; 
use App\LogPenjualan;
use App\Pembayaran;
use App\PreOrder;
use App\Barang;
use App\SKPP;
use Helper;
use Auth;

class SkppService 
{
	public $StokService;

	public function __construct(StokService $StokService){
		$this->StokService = $StokService;
	}

	public function lastKodeSkpp()
	{
		$data = SKPP::withTrashed()->selectRaw('left(no_skpp, 4) as last')->where("kategori", "penjualan")->orderBy('last', 'desc')->first();
		
		if($data){
			$last_number = sprintf("%04d", ((int)substr($data->last, 0, 4) + 1));
			$nomor_skpp = $last_number.'/SUM-SKPP/'.Helper::integerToRoman(date('m')).'.'.date('Y');
		} else {
			$nomor_skpp = '0001/SUM-SKPP/'.Helper::integerToRoman(date('m')).'.'.date('Y');
		}

		return $nomor_skpp;
	}

	public function validateUpdateHarga($data, $total_pembayaran)
	{
		$data = Pembayaran::where("id_skpp", $data->id_skpp)->get();
        if(count($data) > 0 && $total_pembayaran != $data->total_pembayaran){
            throw new \Exception("Total pembayaran tidak dapat di update, hapus semua data pembayaran terlebih dahulu.", 1); 
        }
	} 

	public function LogPenjulalan($id_skpp, $from, $to, $revisi = null)
	{
		LogPenjualan::insert([
			"id_skpp" => $id_skpp,
			"status_from" => $from,
			"status_to" => $to,
			"keterangan" => $revisi,
			"created_by" => Auth::user()->id_user
		]);
	}

	public function totalPembayaranSkppPembelian($id)
	{
		$data = PreOrder::with("Barang")->findOrFail($id);

		$total = 0;
		foreach ($data->Barang as $value) {
			$nilai = $value->nilai;
			$ppn = $nilai * 0.1;
			$nilai = $nilai + $ppn;
			$total += $nilai;
		}

		return $total;
	}

	public function minusStok($id_skpp)
	{
		$barang = Barang::where("id_skpp", $id_skpp)->get();

		foreach ($barang as $value) {
			$this->StokService->minusStok($value->id_produk, $value->kuantitas);
		}
	}

	public function addStok($id_skpp)
	{
		$barang = Barang::where("id_skpp", $id_skpp)->get();

		foreach ($barang as $value) {
			$this->StokService->addStok($value->id_produk, $value->kuantitas);
		}
	}
 
}

















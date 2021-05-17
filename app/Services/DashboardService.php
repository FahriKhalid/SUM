<?php

namespace App\Services; 
use App\Services\BarangService;
use App\Pembayaran;
use App\Barang;
use App\SKPP;
use Carbon\Carbon;
use Helper;
use Auth;
use DB;

class DashboardService 
{ 
	public function totalPenjualan()
	{
		return Pembayaran::whereHas("SKPP", function($query){
			$query->where("kategori", "penjualan");
		})->sum("jumlah_pembayaran"); 
	}


	public function totalPembelian()
	{
		return Pembayaran::whereHas("SKPP", function($query){
			$query->where("kategori", "pembelian");
		})->sum("jumlah_pembayaran"); 
	}

	public function totalPiutang()
	{
		// return Pembayaran::whereHas("SKPP", function($query){
		// 	$query->where("kategori", "penjualan");
		// })->sum("sisa_hutang");  
		return (float) self::piutang() + (float)self::belumBayarPenjualan();
	}

	public function totalHutang()
	{
		// return Pembayaran::whereHas("SKPP", function($query){
		// 	$query->where("kategori", "pembelian");
		// })->sum("sisa_hutang");  
		return (float)self::hutang() + (float)self::belumBayarPembelian();
	}

	public function penjualan($from = null, $to = null)
	{
		if($from == null || $to == null){
			$from = Carbon::now()->startOfMonth();
			$to = Carbon::now()->endOfMonth();
		}

		return Pembayaran::whereHas("SKPP", function($query){
			$query->where("kategori", "penjualan");
		})
		->whereBetween("created_at", [$from, $to]) 
		->sum("jumlah_pembayaran"); 
	}


	public function pembelian($from = null, $to = null)
	{
		if($from == null || $to == null){
			$from = Carbon::now()->startOfMonth();
			$to = Carbon::now()->endOfMonth();
		}

		return Pembayaran::whereHas("SKPP", function($query){
			$query->where("kategori", "pembelian");
		})
		->whereBetween("created_at", [$from, $to]) 
		->sum("jumlah_pembayaran"); 
	}


	public function queryPenjualanPembelian($start = null, $end = null)
	{
		if($start == null || $end == null){
			$start = Carbon::now()->startOfMonth();
			$end = Carbon::now()->endOfMonth();
		}
		return DB::select(DB::raw("(
			SELECT *, @running_total:=@running_total + sales AS penjualan_kumulatif
			, @running_total2:=@running_total2 + purchase AS pembelian_kumulatif FROM 
			(
				SELECT *, IFNULL(penjualan, 0) AS sales, IFNULL(pembelian, 0) AS purchase FROM 
				(
					SELECT * FROM 
					(
						SELECT * FROM 
						(SELECT adddate('1970-01-01',t4.i*10000 + t3.i*1000 + t2.i*100 + t1.i*10 + t0.i) tanggal FROM 
				 		(SELECT 0 i union SELECT 1 union SELECT 2 union SELECT 3 union SELECT 4 union SELECT 5 union SELECT 6 union SELECT 7 union SELECT 8 union SELECT 9) t0,
						(SELECT 0 i union SELECT 1 union SELECT 2 union SELECT 3 union SELECT 4 union SELECT 5 union SELECT 6 union SELECT 7 union SELECT 8 union SELECT 9) t1,
						(SELECT 0 i union SELECT 1 union SELECT 2 union SELECT 3 union SELECT 4 union SELECT 5 union SELECT 6 union SELECT 7 union SELECT 8 union SELECT 9) t2,
						(SELECT 0 i union SELECT 1 union SELECT 2 union SELECT 3 union SELECT 4 union SELECT 5 union SELECT 6 union SELECT 7 union SELECT 8 union SELECT 9) t3,
						(SELECT 0 i union SELECT 1 union SELECT 2 union SELECT 3 union SELECT 4 union SELECT 5 union SELECT 6 union SELECT 7 union SELECT 8 union SELECT 9) t4) v
						WHERE tanggal BETWEEN DATE('".$start."') AND DATE('".$end."')
					) AS A
					LEFT JOIN (
					    SELECT p.created_at,
					       SUM(CASE WHEN s.kategori = 'penjualan' THEN jumlah_pembayaran ELSE 0 END) AS penjualan,
					       SUM(CASE WHEN s.kategori = 'pembelian' THEN jumlah_pembayaran ELSE 0 END) AS pembelian
					    FROM tr_pembayaran as p 
					    JOIN tr_skpp as s ON p.id_skpp = s.id_skpp
					 	GROUP BY DATE(p.created_at)
					) 
					AS B ON A.tanggal = DATE(B.created_at)  
				)
				AS C
				CROSS JOIN (SELECT @running_total := 0) r
				CROSS JOIN (SELECT @running_total2 := 0) q
				ORDER BY tanggal
			)  
			AS D
		)"));
	}

	 
	public function dataTrenPenjualanPembelian($start = null, $end = null)
	{
		$data = $this->queryPenjualanPembelian($start, $end);
 
		$penjualan = [];
		$pembelian = [];
		$penjualan_kumulatif = [];
		$pembelian_kumulatif = [];
		foreach (array_chunk($data, 100) as $item) {
			foreach ($item as $value) {
				if ($value->penjualan != null) {
					array_push($penjualan, $value->penjualan);
				} else {
					array_push($penjualan, 0);
				} 
			} 

			foreach ($item as $value) {
				if ($value->pembelian != null) {
					array_push($pembelian, $value->pembelian);
				} else {
					array_push($pembelian, 0);
				} 
			} 

			foreach ($item as $value) {
				if ($value->penjualan_kumulatif != null) {
					array_push($penjualan_kumulatif, $value->penjualan_kumulatif);
				} else {
					array_push($penjualan_kumulatif, 0);
				} 
			} 

			foreach ($item as $value) {
				if ($value->pembelian_kumulatif != null) {
					array_push($pembelian_kumulatif, $value->pembelian_kumulatif);
				} else {
					array_push($pembelian_kumulatif, 0);
				} 
			} 
		} 
		
		return array(
			"penjualan" => $penjualan,
			"pembelian" => $pembelian,
			"penjualan_kumulatif" => $penjualan_kumulatif,
			"pembelian_kumulatif" => $pembelian_kumulatif
		);
	}

	public function bayar_penjualan()
	{
		return Barang::whereNotNull("id_skpp")->sum('nilai');
	}

	public function belumBayarPenjualan()
	{
		return SKPP::whereDoesntHave("Pembayaran")->where("kategori", "penjualan")->sum("total_pembayaran");
	}

	public function belumBayarPembelian()
	{
		return SKPP::whereDoesntHave("Pembayaran")->where("kategori", "pembelian")->sum("total_pembayaran");
	}

	public function piutang()
	{
		$piutang = DB::select(
			DB::raw("SELECT SUM(sisa_hutang) as total_hutang 
			FROM tr_pembayaran 
			JOIN tr_skpp ON tr_skpp.id_skpp = tr_pembayaran.id_skpp
			where id_pembayaran in (SELECT MAX(id_pembayaran) FROM tr_pembayaran GROUP BY id_skpp)
			AND tr_skpp.kategori = 'penjualan'")
		);

		if($piutang){
			return $piutang[0]->total_hutang;
		} else {
			return 0;
		}
	}

	public function hutang()
	{
		$hutang = DB::select(
			DB::raw("SELECT SUM(sisa_hutang) as total_piutang 
			FROM tr_pembayaran 
			JOIN tr_skpp ON tr_skpp.id_skpp = tr_pembayaran.id_skpp
			where id_pembayaran in (SELECT max(id_pembayaran) FROM tr_pembayaran GROUP BY id_skpp)
			AND tr_skpp.kategori = 'pembelian'")
		);

		if($hutang){
			return $hutang[0]->total_piutang;
		} else {
			return 0;
		} 
	}
}










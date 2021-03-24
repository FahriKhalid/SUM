<?php

namespace App\Services; 
use App\Pembayaran;
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
		return Pembayaran::whereHas("SKPP", function($query){
			$query->where("kategori", "pembelian");
		})->sum("sisa_hutang"); 
	}

	public function totalHutang()
	{
		return Pembayaran::whereHas("SKPP", function($query){
			$query->where("kategori", "penjualan");
		})->sum("sisa_hutang"); 
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
			    SELECT SUM(jumlah_pembayaran) as pembayaran, p.created_at, s.kategori
			    FROM tr_pembayaran as p 
			    JOIN tr_skpp as s ON p.id_skpp = s.id_skpp
			    GROUP BY s.kategori
			) as B ON A.tanggal = DATE(B.created_at)

			GROUP BY tanggal
		)"));
	}

	public function dataTrenPenjualanPembelian($start = null, $end = null)
	{
		$data = $this->queryPenjualanPembelian($start, $end);
		$penjualan = [];
		$pembelian = [];
		foreach (array_chunk($data, 100) as $item) {
			foreach ($item as $value) {
				if ($value->kategori == "penjualan") {
					array_push($penjualan, $value->pembayaran);
				} else {
					array_push($penjualan, 0);
				} 
			} 

			foreach ($item as $value) {
				if ($value->kategori == "pembelian") {
					array_push($pembelian, $value->pembayaran);
				} else {
					array_push($pembelian, 0);
				} 
			} 
		}

		return array(
			"penjualan" => $penjualan,
			"pembelian" => $pembelian
		);
	}
}










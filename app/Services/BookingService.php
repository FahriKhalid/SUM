<?php

namespace App\Services; 
use App\Pembayaran;
use App\PreOrder;
use App\Booking;
use App\Barang;
use Helper;
use Auth;

class BookingService 
{
	

	public function sisaBarang($id)
	{
		$pre_order = Barang::where("id_pre_order", $id)->sum("kuantitas");

		if($pre_order){

			//$pre_order - 
		}

		return $pre_order;
	}
}

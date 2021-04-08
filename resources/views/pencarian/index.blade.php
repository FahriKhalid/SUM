@extends('layout.index')

@section('title', 'SUM - Pencarian')
 
@section('content')

@include('produk.form_modal')
 
	<div class="container-fluid mt-4">
		@if(count($info["data"]) > 0 && strlen($info["pencarian"]) > 2)
		<div class="row">
	        <div class="col-md-12 d-flex justify-content-between">
	        	<h6 class="m-0 font-weight-bold text-primary">Hasil pencarian, {{ count($info["data"]) }} data ditemukan.</h6>   
	        </div>  
	    </div>
		<div class="mt-3 mb-4 shadow-sm">
			@foreach($info["data"]->chunk(100) as $value)
				@foreach($value as $item)

					@if($item->table_name == "SKPP")
						@php($color = "border-left-danger")
						@if(strtoupper($item->kategori) == "PENJUALAN")
							@php($url = url("penjualan/skpp/show/".Helper::encodex($item->id)))
						@else
							@php($url = url("pembelian/skpp/show/".Helper::encodex($item->id)))
						@endif
					@elseif($item->table_name == "PRE ORDER")
						@php($color = "border-left-warning")
						@php($url = url("pembelian/pre_order/show/".Helper::encodex($item->id)))
					@elseif($item->table_name == "SALES ORDER")
						@php($color = "border-left-primary")
						@if(strtoupper($item->kategori) == "PENJUALAN")
							@php($url = url("penjualan/salesorder/show/".Helper::encodex($item->id)))
						@else
							@php($url = url("pembelian/salesorder/show/".Helper::encodex($item->reference_id)."?url=".Helper::encodex(url('pembelian/salesorder/detail/'.Helper::encodex($item->id)))))
						@endif
					@elseif($item->table_name == "PENGAJUAN SO")
						@php($color = "border-left-success")
						@php($url = url("pembelian/skpp/show/".Helper::encodex($item->reference_id)."?url=".Helper::encodex(url('pembelian/pengajuan_so/detail/'.Helper::encodex($item->id))))) 
					@elseif($item->table_name == "SURAT KUASA")
						@php($color = "border-left-secondary") 
						@php($url = url("surat_kuasa/index/".Helper::encodex($item->reference_id)."?url=".Helper::encodex(url('surat_kuasa/show/'.Helper::encodex($item->id))))) 
					@elseif($item->table_name == "INVOICE")
						@php($color = "border-left-info")
						@if(strtoupper($item->kategori) == "PENJUALAN")
							@php($url = url("penjualan/invoice/index/".Helper::encodex($item->reference_id)."?url=".Helper::encodex(url('penjualan/invoice/show/'.Helper::encodex($item->id)))))
						@else
							@php($url = url("pembelian/invoice/index/".Helper::encodex($item->reference_id)))
						@endif
					@endif

					<div class="bg-white {{ $color }} border-bottom">
						<div class="card-bod p-3">
							<div class="font-weight-bold">
								<a target="_blank" href="{{ $url }}">{{ $item->nomor }} </a>
							</div>

							<div class="mt-2">
								<span class="badge rounded-pill border custom-pill"> 
							  		{{ $item->table_name }}
								</span>
								<span class="badge rounded-pill border custom-pill"> 
							  		{{ strtoupper($item->kategori) }}  
								</span>
								<span class="badge rounded-pill border custom-pill"> 
									{{ strtoupper($item->CreatedBy->nama) }}
								</span>
								<span class="badge rounded-pill border custom-pill"> 
							  		{{ $item->created_at }}
								</span>
							</div>
						</div>
					</div>
				@endforeach
			@endforeach
		</div> 
		@elseif(!empty($info["data"]) && count($info["data"]) < 1)

		    <div class="flex justify-content-center text-center align-items-center center-position">   
		        <p>Hasil pencarian <b>{{ $info["pencarian"] }}</b> tidak ditemukan, <br> coba dengan keyword yang lain.</p>
		    </div>

		@else
		    
		    <div class="flex justify-content-center text-center align-items-center center-position">   
		        <p>Inputan pencarian harus lebih dari 2 karakter.</p>
		    </div>

		@endif
	</div>

@endsection
 
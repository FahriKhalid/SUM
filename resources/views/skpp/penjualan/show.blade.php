@extends('layout.index')

@section('title', 'SUM - Detail SKPP')

@section('css')
	<link rel="stylesheet" type="text/css" href="{{asset('vendor/datatables/dataTables.bootstrap4.min.css')}}"> 
@endsection
 
@section('content')
 
@include('layout.header_penjualan')
@include('layout.modal_email')
@include("skpp.modal_konfirmasi_skpp")
@include("skpp.modal_approve_skpp")
@include("skpp.modal_unapprove_skpp")
@include("skpp.modal_revisi_skpp")
@include("skpp.modal_cancel_skpp")
@include("skpp.modal_view_lampiran")

<div class="container-fluid mb-4 mt-4">

    <div class="row">
        <div class="col-md-12 d-flex justify-content-between">
        	<h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-file-upload"></i> SKPP</h6>
            <a href="{{ url("penjualan/skpp") }}" class="text-muted"><i class="fa fa-arrow-left"></i> Kembali</a>   
        </div>  
    </div> 

    @if($info["skpp"]->catatan_revisi != null && in_array($info["skpp"]->id_status, [DRAFT, CANCEL]))
	    <div class="alert alert-danger mt-3">
	    	<h4 class="alert-heading"><i class="fa fa-exclamation-circle"></i> {{ $info["skpp"]->id_status == DRAFT ? 'Catatan revisi' : 'Catatan pembatalan penjualan' }}</h4>
	    	<span>{{ $info["skpp"]->catatan_revisi }}</span>
	    </div>
    @endif

	<div class="card mt-3 ">
		<div class="card-header bg-white d-flex justify-content-between"> 
			<div>
				<a target="_blank" href="{{ url('penjualan/skpp/preview/'.Helper::encodex($info["skpp"]->id_skpp)) }}" class="btn btn-success"><i class="fa fa-download"></i> SKPP</a>

				@if($info["skpp"]->id_status == 1)
					<a href="{{ url('penjualan/skpp/edit/'.Helper::encodex($info["skpp"]->id_skpp)) }}" class="btn btn-primary"><i class="fa fa-edit"></i> Edit</a>
					<button onclick="confirm('{{ url('penjualan/skpp/confirm/'.Helper::encodex($info["skpp"]->id_skpp)) }}')" class="btn btn-success"><i class="fa fa-check"></i> Confirm</button>
				@elseif($info["skpp"]->id_status == 2)
					<button class="btn btn-success" onclick="show_form_email('dokumen SKPP', '{{ url('penjualan/skpp/send_email/'.$id) }}')"><i class="fas fa-paper-plane"></i> Kirim email ({{ isset($info["riwayat_email"]) ? $info["riwayat_email"]->jumlah : '0' }})</button>  
					<button onclick="revisi('{{ url('penjualan/skpp/revisi/'.Helper::encodex($info["skpp"]->id_skpp)) }}')" class="btn btn-warning"><i class="fa fa-edit"></i> Revisi</button>
					<button onclick="cancel('{{ url('penjualan/skpp/cancel/'.Helper::encodex($info["skpp"]->id_skpp)) }}')" class="btn btn-danger"><i class="fa fa-times"></i> Batalkan penjualan</button>
				@elseif($info["skpp"]->id_status == CANCEL)
					<button onclick="destroy('{{ url('penjualan/skpp/destroy/'.Helper::encodex($info["skpp"]->id_skpp)) }}')" class="btn btn-danger"><i class="fa fa-trash"></i> Hapus</button>
				@endif
			</div>
			<div>
				@if($info["skpp"]->id_status == DRAFT)
					<span class="badge badge-secondary">DRAFT</span>
				@elseif($info["skpp"]->id_status == CONFIRM)
					<span class="badge badge-warning">CONFIRM</span>
				@elseif($info["skpp"]->id_status == CANCEL)
					<span class="badge badge-danger">CANCEL</span>
				@endif
			</div>
		</div> 
		<div class="card-body"> 
			<table class="table table-borderless table-sm">
				<tr>
					<th width="20%">Nomor SKPP</th>
					<th width="1%">:</th>
					<td>{{ $info["skpp"]->no_skpp }}</td>
				</tr>
				<tr>
					<th>Customer</th>
					<th>:</th>
					<td>{{ $info["skpp"]->Customer->perusahaan == null ? '' : $info["skpp"]->Customer->perusahaan .' -'}} {{ $info["skpp"]->Customer->nama }} </td>
				</tr>
				<tr>
					<th>Syarat penyerahan</th>
					<th>:</th>
					<td>{{ $info["skpp"]->syarat_penyerahan == null ? '-' : $info["skpp"]->syarat_penyerahan }}</td>
				</tr>
				<tr>
					<th>Batas akhir pembayaran</th>
					<th>:</th>
					<td>{{ $info["skpp"]->terakhir_pembayaran == null ? '-' : Helper::dateIndo($info["skpp"]->terakhir_pembayaran) }}</td>
				</tr>
				<tr>
					<th>Batas akhir pengambilan</th>
					<th>:</th>
					<td>{{ $info["skpp"]->batas_akhir_pengambilan == null ? '-' : Helper::dateIndo($info["skpp"]->batas_akhir_pengambilan) }}</td>
				</tr>
				<tr>
					<th>ATM</th>
					<th>:</th>
					<td>
						@if(count($info["skpp"]->SKPPATM) > 1)
						<ul style="margin-bottom: -5px; margin-left: -23px;">
							@foreach($info["skpp"]->SKPPATM as $atm)
							<li>{{ $atm->ATM->nama .' ('. $atm->ATM->nomor.')' }}</li>
							@endforeach
						</ul>
						@elseif(count($info["skpp"]->SKPPATM) == 1)
							{{ $info["skpp"]->SKPPATM[0]->ATM->nama .' ('. $info["skpp"]->SKPPATM[0]->ATM->nomor.')' }}
						@else
						-
						@endif
					</td>
				</tr>
				<tr>
					<th>Lampiran</th>
					<th>:</th>
					<td>
						@if(count($info["skpp"]->Lampiran) > 0) 
							@foreach($info["skpp"]->Lampiran as $lampiran)  
							  	<span class="badge rounded-pill border custom-pill"> 
						  			{{ $lampiran->nama }} . {{ Helper::getExtensionFromString($lampiran->file) }} 
						  			@if(Helper::getExtensionFromString($lampiran->file) == "PDF")
						  			<a href="javascript:void(0)" onclick="view_lampiran('{{ $lampiran->nama }}','{{ asset('lampiran/'.$lampiran->file) }}')">Lihat dokumen</a>
						  			@else
						  			<a href="{{ asset('lampiran/'.$lampiran->file) }}" download>Download</a>
						  			@endif
    							</span>
							@endforeach 
						@else
						-
						@endif
					</td>
				</tr> 
			</table>
 
			<table class="table table-bordered">
				<thead>
					<tr>
						<th width="1px">No</th>
						<th>Produk</th>
						<th>Incoterm</th>
						<th width="170px">Kuantitas</th>
						<th>Harga jual</th>
						<th>Nilai</th> 
					</tr>
				</thead>	
				<tbody>
					@php 
						$total = 0;
					@endphp
					@foreach($info["po"] as $po)
					<tr>
						<td>{{ $loop->iteration }}</td>
						<td>{{ $po->Produk->nama }}</td>
						<td>{{ $po->incoterm }}</td>
						<td>{{ Helper::comma($po->kuantitas) }} MT</td>
						<td>
							<div class="d-flex justify-content-between">
								<div>Rp</div>
								<div>{{ Helper::currency($po->harga_jual) }}</div>		
							</div>
						</td>
						<td>
							<div class="d-flex justify-content-between">
								<div>Rp</div>
								<div>{{ Helper::currency($po->nilai) }}</div>		
							</div> 
						</td>

						@php 
							$total += floatval($po->nilai);
						@endphp
					</tr>
					@endforeach
				</tbody>
				<tfoot>
					<tr>
						<td colspan="5" align="right"><b>TOTAL</b></td> 
						<td align="right">
							<div class="d-flex justify-content-between">
								<div>Rp</div>
								<div id="total-harga">{{ Helper::currency(Helper::toFixed($total, 2)) }}</div>		
							</div>
						</td>
					</tr>
				</tfoot>			
			</table>
 
		</div>
	</div> 
</div>

@endsection

@section('footer')
 

<script type="text/javascript">

	/*
	|--------------------------------------------------------------------------
	| Konfirmasi
	|--------------------------------------------------------------------------
	*/

	function confirm(url) {
		$("#confirm-skpp").attr("action", url); 
		$("#modal-konfirmasi-skpp").modal("show");
	}	

	$("body").delegate("#confirm-skpp", "click", function(){
		$.ajax({
	        url : $(this).attr("action"),
	        type : 'GET',
	        data : { "_token" : $('meta[name="csrf-token"]').attr('content') },  
			dataType : "json", 
			beforeSend: function(resp){
				loader(".modal-content", true);
			},
	        success : function(resp){ 
	            if (resp.status == 'success') {
	                toastr.success(resp.message, { "closeButton": true });     
	                $("#modal-konfirmasi-skpp").modal("hide");
	                location.reload(); 

	            } else {
	                toastr.error(resp.message, { "closeButton": true });
	            }

	            loader(".modal-content", false);
	        },
	        error : function (jqXHR, exception) {
	            errorHandling(jqXHR.status, exception); 
	            loader(".modal-content", false);
	        }
	    });
	});


	/*
	|--------------------------------------------------------------------------
	| Revisi
	|--------------------------------------------------------------------------
	*/

	function revisi(url){
		$("#form-revisi-skpp").attr("action", url); 
		$("#modal-revisi-skpp").modal({"backdrop" : "static", "keyboard" : false});
	}

	$(document).on("submit", "#form-revisi-skpp", function(e){
	    e.preventDefault();

	    $.ajax({
	        url : $(this).attr("action"),
	        type : 'POST',
	        data : new FormData(this),
	        processData : false,
	        contentType : false, 
			dataType : "json", 
			beforeSend: function(resp){
				loader(".modal-content", true);
			},
	        success : function(resp){ 
	            if (resp.status == 'success') {
	                toastr.success(resp.message, { "closeButton": true });     
	                $("#modal-revisi-skpp").modal("hide"); 
	                location.reload(); 

	            } else {
	                toastr.error(resp.message, { "closeButton": true });
	            }
	            loader(".modal-content", false);
	        },
	        error : function (jqXHR, exception) {
	            errorHandling(jqXHR.status, exception); 
	            loader(".modal-content", false);
	        }
	    });
	});


	/*
	|--------------------------------------------------------------------------
	| Cancel
	|--------------------------------------------------------------------------
	*/

	function cancel(url)
	{
		$("#form-cancel-skpp").attr("action", url); 
		$("#modal-cancel-skpp").modal({"backdrop" : "static", "keyboard" : false});
	}

	$(document).on("submit", "#form-cancel-skpp", function(e){
	    e.preventDefault();

	    $.ajax({
	        url : $(this).attr("action"),
	        type : 'POST',
	        data : new FormData(this),
	        processData : false,
	        contentType : false, 
			dataType : "json", 
			beforeSend: function(resp){
				loader(".modal-content", true);
			},
	        success : function(resp){ 
	            if (resp.status == 'success') {
	                toastr.success(resp.message, { "closeButton": true });     
	                $("#modal-cancel-skpp").modal("hide"); 
	                location.reload(); 
	            } else {
	                toastr.error(resp.message, { "closeButton": true });
	            }
	            loader(".modal-content", false);
	        },
	        error : function (jqXHR, exception) {
	            errorHandling(jqXHR.status, exception); 
	            loader(".modal-content", false);
	        }
	    });
	});
 

	/*
	|--------------------------------------------------------------------------
	| delete
	|--------------------------------------------------------------------------
	*/

	function destroy(url)
	{
		$("#form-hapus").attr("action", url); 
    	$("#modal-konfirmasi-hapus").modal("show");
	}

	$(document).on("submit", "#form-hapus", function(e){
	    e.preventDefault();

	    $.ajax({
	        url : $(this).attr("action"),
	        type : 'DELETE',
	        data : { "_token" : $('meta[name="csrf-token"]').attr('content') },  
			dataType : "json", 
			beforeSend: function(resp){
				loader(".modal-content", true);
			},
	        success : function(resp)
	        { 
	            if (resp.status == 'success') {
	                toastr.success(resp.message, { "closeButton": true });  
	                $("#modal-konfirmasi-hapus").modal("hide");
	                location.href = '{{ url('penjualan/skpp') }}'

	            } else {
	                toastr.error(resp.message, { "closeButton": true });
	            }
	            loader(".modal-content", false);
	        },
	        error : function (jqXHR, exception) {
	            errorHandling(jqXHR.status, exception); 
	            loader(".modal-content", false);
	        }
	    })
	})

	function view_lampiran(nama, url)
	{
		$("#modal-view-lampiran").modal("show");
		$("#title-modal-lampiran").html(nama);
		$("#view-file-lampiran").attr("data", url+"#view=FitH");
	}
</script>

@endsection
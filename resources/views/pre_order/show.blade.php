@extends('layout.index')

@section('title', 'SUM - Detail Pre Order') 
 
@section('content') 

@include('layout.header_pembelian')
@include("skpp.modal_konfirmasi_skpp")
@include('layout.modal_email')
@include("skpp.modal_view_lampiran")

<div class="container-fluid mb-4 mt-4">

    <div class="row">
        <div class="col-md-12 d-flex justify-content-between">
        	<h6 class="m-0 font-weight-bold text-primary"><i class="fa fa-search"></i> Detail Pre Order</h6>
            <a href="{{ url("pembelian/pre_order") }}" class="text-muted"><i class="fa fa-arrow-left"></i> Kembali</a>   
        </div>  
    </div> 

    @if($info["pre_order"]->catatan_revisi != null && $info["pre_order"]->id_status == 1)
	    <div class="alert alert-danger mt-3">
	    	<h4 class="alert-heading"><i class="fa fa-exclamation-circle"></i> Catatan Revisi!</h4>
	    	<span>{{ $info["pre_order"]->catatan_revisi }}</span>
	    </div>
    @endif

	<div class="card mt-3 ">
		<div class="card-header bg-white d-flex justify-content-between"> 
			<div>
				<a href="{{ url('pembelian/pre_order/edit/'.Helper::encodex($info["pre_order"]->id_pre_order)) }}" class="btn btn-primary"><i class="fa fa-edit"></i> Edit</a>
				<a target="_blank" href="{{ url('pembelian/pre_order/surat_po/'.Helper::encodex($info["pre_order"]->id_pre_order)) }}" class="btn btn-warning"><i class="fa fa-download"></i> Download</a>
				<button class="btn btn-warning"onclick="show_form_email('dokumen Pre Order', '{{ url('pembelian/pre_order/send_email/'.$id) }}')"><i class="fas fa-paper-plane"></i> Kirim email ({{ isset($info["riwayat_email"]) ? $info["riwayat_email"]->jumlah : '0' }})</button>
			</div>
			<div>
				@if($info["pre_order"]->id_status == 1)
					<span class="badge badge-secondary">DRAFT</span>
				@elseif($info["pre_order"]->id_status == 2)
					<span class="badge badge-warning">CONFIRM</span>
				@else
					<span class="badge badge-success">APPROVE</span>
				@endif
			</div>
		</div> 
		<div class="card-body"> 
			<table class="table table-sm table-borderless">
				<tr>
					<th width="20%">Nomor Pre Order</th>
					<th width="1%">:</th>
					<td>{{ $info["pre_order"]->no_po }}</td>
				</tr>
				<tr>
					<th>Produsen</th>
					<th>:</th>
					<td>{{ $info["pre_order"]->Produsen->perusahaan == null ? '' : $info["pre_order"]->Produsen->perusahaan .' -'}} {{ $info["pre_order"]->Produsen->nama }}</td>
				</tr> 
				<tr>
					<th>Lampiran</th>
					<th>:</th>
					<td>
						@if(count($info["pre_order"]->Lampiran) > 0) 
							@foreach($info["pre_order"]->Lampiran as $lampiran)  
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
	| Approve
	|--------------------------------------------------------------------------
	*/

	function approve(url){
		$("#form-approve-skpp").attr("action", url); 
		$("#modal-approve-skpp").modal({"backdrop" : "static", "keyboard" : false});
	}

	$(document).on("submit", "#form-approve-skpp", function(e){
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
	                $("#modal-approve-skpp").modal("hide");
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
	| Approve
	|--------------------------------------------------------------------------
	*/

	function unapprove(url){
		$("#form-unapprove-skpp").attr("action", url); 
		$("#modal-unapprove-skpp").modal({"backdrop" : "static", "keyboard" : false});
	}

	$(document).on("submit", "#form-unapprove-skpp", function(e){
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
	                $("#modal-unapprove-skpp").modal("hide");
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

	function view_lampiran(nama, url)
	{
		$("#modal-view-lampiran").modal("show");
		$("#title-modal-lampiran").html(nama);
		$("#view-file-lampiran").attr("data", url+"#view=FitH");
	}
</script>

@endsection
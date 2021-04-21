@extends('layout.index')

@section('title', 'SUM - Detail SKPP')

@section('css')
	<link rel="stylesheet" type="text/css" href="{{asset('vendor/datatables/dataTables.bootstrap4.min.css')}}"> 
@endsection
 
@section('content')
 
@include('layout.header_penjualan') 
@include('salesorder.penjualan.modal_update_status') 

<div class="container-fluid mb-4 mt-4">

    <div class="row">
        <div class="col-md-12 d-flex justify-content-between">
        	<h6 class="m-0 font-weight-bold text-primary"><i class="fa fa-truck-moving"></i> Sales Order</h6>
            <a href="{{ url("penjualan/skpp") }}" class="text-muted"><i class="fa fa-arrow-left"></i> Kembali</a>   
        </div>  
    </div>  
    @if(PembayaranService::isBayar("penjualan", Helper::decodex($id)))
    <div class="alert alert-warning mt-3">
    	<h4 class="alert-heading"><i class="fa fa-exclamation-circle"></i> Warning</h4>
        Pembayaran belum ada. Tidak dapat menambahkan data sales order!
    </div>
    @endif
	<div class="card mt-3 ">
		@if(!PembayaranService::isBayar(Helper::decodex($id)))
		<div class="card-body alert-info">
			<table class="table table-borderless table-sm">
				<thead>
					<tr>
						<th width="1px">No</th>
						<th width="200px">Produk</th>
						<th>Sisa Kuantitas</th>
					</tr>
				</thead>
				<tbody>
					@foreach($info["po"] as $po)
						<tr>
							<td>{{ $loop->iteration }}.</td>
							<td>{{ $po->Produk->nama }}</td>
							<td>{{ (int)$po->kuantitas - (int)$po->totalKuantitasPO() }} MT</td>
							 
						</tr>
					@endforeach
				</tbody>
			</table>
		</div>
		@endif
		<div class="card-body bg-white"> 
			@if(!PembayaranService::isBayar("penjualan", Helper::decodex($id)))
			<a class="btn btn-success" href="{{ url('penjualan/salesorder/'.$id.'/create') }}"><i class="fa fa-plus"></i> Tambah</a>
			@endif

			<div class="btn-group" role="group" aria-label="Basic example">
			  	<button disabled class="btn btn-warning action" onclick="update_status('Hold', '{{ Helper::encodex(7) }}')"><i class="fas fa-box"></i> Hold</button>
				<button disabled class="btn btn-warning action" onclick="update_status('On Process', '{{ Helper::encodex(6) }}')"><i class="fas fa-truck-moving"></i> On process</button>
				<button disabled class="btn btn-warning action" onclick="update_status('Delivered', '{{ Helper::encodex(5) }}')"><i class="fas fa-truck-loading"></i> Delivered</button>
			</div>
		</div> 
		<div class="card-body"> 
			<table class="table table-bordered" id="tabel-so" style="width: 100%">
				<thead>
					<tr>
						<th width="1px">No</th>
						<th>Nomor SO</th> 
						<th>Total Kuantitas</th>
						<th>Penanggung jawab</th>
						<th>Alat angkut</th> 
						<th>Tujuan</th> 
						<th>Status</th> 
						<th width="70px">Aksi</th>
					</tr>
				</thead> 
			</table>
		</div>
	</div>
</div>

@endsection

@section('footer')

<script type="text/javascript" src="{{asset('vendor/datatables/jquery.dataTables.min.js')}}"></script>
<script type="text/javascript" src="{{asset('vendor/datatables/dataTables.bootstrap4.min.js')}}"></script> 
<script type="text/javascript" src="{{asset('vendor/datatables/dataTables.responsive.min.js')}}"></script>  

<script type="text/javascript">

	/*
    |--------------------------------------------------------------------------
    | Table 
    |--------------------------------------------------------------------------
    */ 

	var data_table = [
        {data: 'check', 	  name: 'check', orderable: false, searchable: false}, 
        {data: 'no_so',       name: 'no_so'}, 
        {data: 'kuantitas',   name: 'kuantitas'},  
        {data: 'supir',       name: 'supir'},  
        {data: 'alat_angkut', name: 'alat_angkut'}, 
        {data: 'tujuan',      name: 'tujuan'},
        {data: 'status',      name: 'status'}, 
        {data: 'action',      name: 'action', orderable: false,},
    ];
    
    var table_so = table('#tabel-so', '{{url('penjualan/salesorder/data')}}/'+'{{$id}}', data_table);
	

	/*
	|--------------------------------------------------------------------------
	| hapus data
	|--------------------------------------------------------------------------
	*/

	$("body").delegate(".hapus", "click", function(e){
    	e.preventDefault();  
    	$("#form-hapus").attr("action", $(this).attr("url")); 
    	$("#modal-konfirmasi-hapus").modal("show");
    });

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
	                refresh_table("#tabel-so");
	                $("#modal-konfirmasi-hapus").modal("hide");
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
	});

	var no_so = null;
	$("body").delegate(".check-so", "click", function(){
		var id = $(this).val();

		var jumlah = $("#tabel-so").find("input[type=checkbox]:checked").length;
		no_so = $(this).attr("no_so")
		if(jumlah > 0){
			$('.action').prop("disabled", false);
		} else {
			$('.action').prop("disabled", true);
		}
	});

	function update_status(action, id_action){
		$("#title-update").html(action);
		$("#no-so").html(no_so);
		$("input[name=status]").val(id_action); 
		$("#modal-konfirmasi-update").modal("show");
	}

	$(document).on("submit", "#form-update-status", function(e){
		e.preventDefault();

		id = []; 
		$("#tabel-so").find("input[type=checkbox]:checked").each(function(){
		 	id.push($(this).val());
		}); 

		$.ajax({
	        url : '{{ url('penjualan/salesorder/update_status') }}',
	        type : 'POST',
	        data : { _token: "{{ csrf_token() }}", status : $("input[name=status]").val(), id : id }, 
			dataType : "json", 
			beforeSend: function(resp){
				loader(".modal-content", true);
			},
	        success : function(resp)
	        { 
	            if (resp.status == 'success') {
	                toastr.success(resp.message, { "closeButton": true });    
	                refresh_table("#tabel-so");
	                $('.action').prop("disabled", true);
	                $("#modal-konfirmasi-update").modal("hide");
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
	
</script>

@endsection
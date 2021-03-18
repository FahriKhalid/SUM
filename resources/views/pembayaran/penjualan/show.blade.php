@extends('layout.index')

@section('title', 'SUM - Detail SKPP')

@section('css')
	<link rel="stylesheet" type="text/css" href="{{asset('vendor/datatables/dataTables.bootstrap4.min.css')}}"> 
@endsection
 
@section('content')
 
@if($info["kategori"] == "penjualan")
	@include('layout.header_penjualan')
	@include('pembayaran.penjualan.modal_form_pembayaran')
@else
	@include('layout.header_pembelian')
	@include('pembayaran.penjualan.modal_form_pembayaran_pembelian')
@endif


@include('pembayaran.penjualan.modal_show_pembayaran')

<div class="container-fluid mb-4 mt-4">

    <div class="row">
        <div class="col-md-12 d-flex justify-content-between">
        	<h6 class="m-0 font-weight-bold text-primary"><i class="fa fa-wallet"></i> Pembayaran</h6>
            <a href="{{ url("skpp") }}" class="text-muted"><i class="fa fa-arrow-left"></i> Kembali</a>   
        </div>  
    </div>  
 
	<div class="card mt-3 ">
		<div class="card-header alert-warning text-center">
			<div class="mt-2">
		    	<div id="status-piutang">
		    		@if($info["piutang"] == 0)
		    			<h3 id="title-sisa-hutang" class="text-dark">Rp {{ Helper::currency($info["skpp"]->total_pembayaran) }}</h3>
		    			<span class="badge-success font-15 badge">LUNAS</span>
			    	@else
			    		<h3 id="title-sisa-hutang" class="text-dark">Rp {{ Helper::currency($info["piutang"]) }}</h3>
			    		<span class="badge-warning font-15 badge">BELUM TERBAYARKAN</span>
			    	@endif
		    	</div> 
		    </div>
		</div>
		<div class="card-body bg-white d-flex justify-content-between"> 
			<button class="btn btn-success" onclick="showModalPembayaran()"><i class="fa fa-plus"></i> Tambah</button>
		</div> 
		<div class="card-body">
			<table class="table table-sm table-bordered" id="tabel-pembayaran" width="100%">
				<thead>
					<tr>
						<th width="1px">No</th>
						<th width="200px">Bukti pembayaran</th>
						<th>Jumlah pembayaran</th> 
						<th>Sisa hutang</th>
						<th>Keterangan</th>
						<th>Status</th>
						<th>Created at</th>
						<th>Created by</th>
						<th>Aksi</th>
					</tr>
				</thead>
				<tbody>
					 
				</tbody>
			</table>
		</div>
	</div>
  
</div>

@endsection

@section('footer')

<script type="text/javascript" src="{{asset('vendor/datatables/jquery.dataTables.min.js')}}"></script>
<script type="text/javascript" src="{{asset('vendor/datatables/dataTables.bootstrap4.min.js')}}"></script> 
<script src="https://cdn.datatables.net/responsive/2.2.3/js/dataTables.responsive.min.js" type="text/javascript"></script>  

<script type="text/javascript">
	function showModalPembayaran(){
		$("#title-modal-form-pembayaran").html("Form Upload tanda bukti pembayaran");
		$("#form-parsial").addClass("d-none");
		$("#form-is-parsial").prop("checked", false);
		$("#form-pembayaran")[0].reset();
		$("#modal-form-pembayaran").modal("show");
	}

	/*
    |--------------------------------------------------------------------------
    | Table 
    |--------------------------------------------------------------------------
    */ 

	var data_table = [
        {data: 'DT_RowIndex', 		name: 'DT_RowIndex', orderable: false, searchable: false}, 
        {data: 'bukti_pembayaran',  name: 'bukti_pembayaran'},
        {data: 'jumlah_pembayaran', name: 'jumlah_pembayaran'}, 
        {data: 'sisa_hutang',      	name: 'sisa_hutang'}, 
        {data: 'keterangan',  		name: 'keterangan'},
        {data: 'status',  			name: 'status'},
        {data: 'created_by',  		name: 'created_by'},
        {data: 'created_at',  		name: 'created_at'}, 
        {data: 'action',      		name: 'action', orderable: false,},
    ];
    
    table('#tabel-pembayaran', '{{url('penjualan/pembayaran/data')}}/'+'{{ $id }}', data_table);

    /*
    |--------------------------------------------------------------------------
    | Checkbox parsial 
    |--------------------------------------------------------------------------
    */

	$("body").delegate("#form-is-parsial", "click", function(){
		if($(this).is(":checked")){
			$("#form-parsial").removeClass("d-none"); 
		}else{
			$("#form-parsial").addClass("d-none"); 
		}
	});


	/*
    |--------------------------------------------------------------------------
    | Form submit pembayaran 
    |--------------------------------------------------------------------------
    */

	$(document).on("submit", "#form-pembayaran", function(e){
		e.preventDefault();
		
		const validate = false;

		if($("#form-is-parsial").is(":checked")){
			var jumlah_pembayaran = $("input[name=jumlah_pembayaran]").val();
			var piutang = {{  $info["piutang"] }};
	 
			if(piutang <= convertNumeric(jumlah_pembayaran)){
				alert('Jumlah pembayaran harus lebih kecil dari total yang harus dibayar');
				validate = true;
				return false;
			} 
		} 

		if(validate === false){
			$.ajax({
				url : '{{ url('penjualan/pembayaran/store') }}/'+'{{ $id }}',
				method : "POST",
				data : new FormData(this),
				contentType : false,
				processData : false,
				dataType : "json",
				beforeSend : function(){
	                loader(".modal-content", true);
				},
				success : function(resp){
	               	if (resp.status == "error_validate"){
	               		for (var i = 0; i < resp.message.length; i++) {
	               			toastr.error(resp.message[i],{ "closeButton": true });
	               		}  
	                } else if (resp.status == "error"){
	               		toastr.error(resp.message, { "closeButton": true }); 
	                } else {

	                	if (resp.data == 0) {
	                		$("#status-piutang").html('<h3 class="text-success"><i class="fa fa-check"></i> LUNAS</h3>');
	                	} else {
	                		$("#title-sisa-hutang").html("Rp " + formatNumber(resp.data,2));
	                	}

	                	$("input[name=total_pembayaran]").val(formatNumber(resp.data,2));

	                	$("#modal-form-pembayaran").modal("hide");
	               		refresh_table("#tabel-pembayaran");
	               		toastr.success(resp.message, { "closeButton": true });  
	               		
	                }
					
	                loader(".modal-content", false);
				},
				error : function(jqXHR, exception){
					errorHandling(jqXHR.status, exception);
	                loader(".modal-content", false);
				}
			});
		}
	});


	/*
    |--------------------------------------------------------------------------
    | validasi pembayaran 
    |--------------------------------------------------------------------------
    */
	
	function validate_pembayaran(){
		if($("#form-is-parsial").is(":checked")){
			var jumlah_pembayaran = $("input[name=jumlah_pembayaran]").val();
			var piutang = {{  $info["piutang"] }};
	 
			if(piutang <= convertNumeric(jumlah_pembayaran)){
				alert('Jumlah pembayaran harus lebih kecil dari total yang harus dibayar');
				return false;
			} 
		}
	}


	/*
	|--------------------------------------------------------------------------
	| hapus pembayaran
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

	            	if (resp.data == 0) {
                		$("#status-piutang").html('<h3 class="text-success"><i class="fa fa-check"></i> LUNAS</h3>');
                	} else {
                		$("#status-piutang").html('<h3 id="title-sisa-hutang" class="text-dark">Rp '+ formatNumber(resp.data,2) +' </h3>\
			    		<span class="badge-warning badge">SISA HUTANG</span>');
                	}

                	$("input[name=total_pembayaran]").val(formatNumber(resp.data,2));

	                toastr.success(resp.message, { "closeButton": true });    
	                refresh_table("#tabel-pembayaran");
	                $("#modal-konfirmasi-hapus").modal("hide");
	            } else {
	                toastr.error(resp.message, { "closeButton": true });
	            }
	            loader(".modal-content", false);
	        },
	        error : function (jqXHR, exception) {
	        	loader(".modal-content", false);
	            errorHandling(jqXHR.status, exception); 
	        }
	    });
	});

	$("body").delegate(".detail-pembayaran", "click", function(e){
		e.preventDefault();
		var id = $(this).attr("did");

		$.ajax({
	        url : '{{ url('penjualan/pembayaran/detail') }}/'+id,
	        type : 'GET', 
			dataType : "json", 
			beforeSend: function(resp){
				loader(".modal-content", true);
			},
	        success : function(resp)
	        { 
	            $("#foto-bukti-pembayaran").attr("src", '{{ asset('bukti_pembayaran') }}/'+resp.file_bukti_pembayaran);
	            $("#modal-show-pembayaran").modal("show");
	            loader(".modal-content", false);
	        },
	        error : function (jqXHR, exception) {
	        	loader(".modal-content", false);
	            errorHandling(jqXHR.status, exception); 
	        }
	    });

		
	});

</script>

@endsection
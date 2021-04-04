@extends('layout.index')

@section('title', 'SUM - Invoice')

@section('css')
	<link rel="stylesheet" type="text/css" href="{{asset('vendor/datatables/dataTables.bootstrap4.min.css')}}"> 
@endsection
 
@section('content')
 
@include('layout.header_pembelian') 
@include('invoice.pembelian.form_modal') 
@include('invoice.pembelian.modal_detail') 

<div class="container-fluid mb-4 mt-4">

    <div class="row">
        <div class="col-md-12 d-flex justify-content-between">
        	<h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-file-invoice-dollar"></i> Invoice</h6>
            <a href="{{ url("pembelian/pre_order") }}" class="text-muted"><i class="fa fa-arrow-left"></i> Kembali</a>   
        </div>  
    </div>  
    
    @if($info["skpp"] == null)
    <div class="alert alert-warning mt-3">
        <h4 class="alert-heading"><i class="fa fa-exclamation-circle"></i> Warning</h4>
        SKPP belum ada, tambah SKPP terlebih dahulu.
    </div>
    @endif

	<div class="card mt-3 "> 
		
		@if($info["skpp"] != null)
        <div class="card-body bg-white d-flex justify-content-between"> 
            <a class="btn btn-success" href="javascript:void(0)" onclick="show_modal()"><i class="fa fa-plus"></i> Tambah</a>
        </div> 
        @endif
		<div class="card-body">
			<table class="table table-bordered" id="tabel-invoice" width="100%">
				<thead>
					<tr>
						<th width="1px">No</th>
						<th>No Invoice</th>   
						<th>Created by</th>   
						<th>Created at</th>   
						<th width="1px">Aksi</th>
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

	let aksi = "add";

	/*
	|--------------------------------------------------------------------------
	| modal form invoice
	|--------------------------------------------------------------------------
	*/

    function show_modal()
    {
    	aksi = "add";
    	$("#form-invoice")[0].reset();
    	$("#title-modal-form-invoice").html("Form tambah invoice");
    	$("#modal-form-invoice").modal("show");
    }


	/*
    |--------------------------------------------------------------------------
    | Table 
    |--------------------------------------------------------------------------
    */ 

	var data_table = [
        {data: 'DT_RowIndex',		name: 'DT_RowIndex', orderable: false, searchable: false}, 
        {data: 'no_tagihan',    	name: 'no_tagihan'},   
        {data: 'created_by',    	name: 'created_by'},   
        {data: 'created_at',    	name: 'created_at'},   
        {data: 'action',      		name: 'action', orderable: false,},
    ];
    
    table('#tabel-invoice', '{{url('pembelian/invoice/data')}}/'+'{{ Helper::encodex($info["skpp"]->id_skpp) }}', data_table);

    /*
	|--------------------------------------------------------------------------
	| detail invoice
	|--------------------------------------------------------------------------
	*/

	$("body").delegate(".detail", "click", function(){
    	var url = $(this).attr("url");
    	
    	$.ajax({
    		url : url,
    		type : "GET",
    		dataType : "json",
    		beforeSend : function(){
    			loader(".modal-content", true);
    			$("#modal-detail").modal("show");
    		},
    		success : function(resp){
    			$("#detail-invoice").html(resp.html);
    			loader(".modal-content", false);
    		},
    		error : function (jqXHR, exception){
    			loader(".modal-content", false);
	            errorHandling(jqXHR.status, exception); 
    		}
    	});
    });


    /*
	|--------------------------------------------------------------------------
	| Edit invoice
	|--------------------------------------------------------------------------
	*/

	$("body").delegate(".edit", "click", function(){
    	var url = $(this).attr("url");
    	
    	$.ajax({
    		url : url,
    		type : "GET",
    		dataType : "json",
    		beforeSend : function(){
    			aksi = "edit";
    			loader(".modal-content", true);
    			$("#form-invoice")[0].reset();
    			$("#title-modal-form-invoice").html("Form edit invoice");
    			$("#modal-form-invoice").modal("show");
    		},
    		success : function(resp){
    			$("input[name=id]").val(resp.invoice.id_invoice);
    			$("input[name=nomor_tagihan]").val(resp.invoice.no_tagihan);
    			loader(".modal-content", false);
    		},
    		error : function (jqXHR, exception){
    			loader(".modal-content", false);
	            errorHandling(jqXHR.status, exception); 
    		}
    	});
    });
 

    /*
	|--------------------------------------------------------------------------
	| form submit invoice
	|--------------------------------------------------------------------------
	*/

    $(document).on("submit", "#form-invoice", function(e){
    	e.preventDefault();
    	let url;
    	if (aksi == "add") {
    		url = "{{ url("pembelian/invoice/store") }}/"+"{{ $id }}";
    	} else {
    		let id = $("input[name=id]").val();
    		url = "{{ url("pembelian/invoice/update") }}/"+id;
    	}

    	$.ajax({
    		url : url,
    		type : "POST",
    		data : new FormData(this),
    		processData : false,
    		contentType : false,
    		dataType : "json",
    		beforeSend : function()
    		{
    			loader(".modal-content", true);
    		},
    		success : function(resp)
    		{
    			if(resp.status == "error_validate") {
    				for (var i = 0; i < resp.message.length; i++) {
    					toastr.error(resp.message[i], { "closeButton": true }); 
    				}
    			} else if (resp.status == "error"){
    				toastr.error(resp.message, { "closeButton": true }); 
    			} else {
    				toastr.success(resp.message, { "closeButton": true }); 
    				$("#modal-form-invoice").modal("hide");
    				refresh_table("#tabel-invoice");
    			}

    			loader(".modal-content", false);
    		},
    		error : function (jqXHR, exception) {
	        	loader(".modal-content", false);
	            errorHandling(jqXHR.status, exception); 
	        }
    	});
    });



    /*
	|--------------------------------------------------------------------------
	| hapus invoice
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
	                refresh_table("#tabel-invoice");
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
</script>

@endsection
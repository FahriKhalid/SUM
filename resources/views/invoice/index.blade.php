@extends('layout.index')

@section('title', 'SUM - Invoice '.$info["skpp"]->no_skpp)

@section('css')
	<link rel="stylesheet" type="text/css" href="{{asset('vendor/datatables/dataTables.bootstrap4.min.css')}}"> 
@endsection
 
@section('content')
 
@include('layout.header_penjualan') 
@include('invoice.modal_detail') 

<div class="container-fluid mb-4 mt-4">

    <div class="row">
        <div class="col-md-12 d-flex justify-content-between">
        	<h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-file-invoice-dollar"></i> Invoice</h6>
            <a href="{{ url("skpp") }}" class="text-muted"><i class="fa fa-arrow-left"></i> Kembali</a>   
        </div>  
    </div>  
 
	<div class="card mt-3 "> 
		<div class="card-body bg-white d-flex justify-content-between"> 
			<a class="btn btn-success" href="{{ url('invoice/create/'.$id) }}"><i class="fa fa-plus"></i> Tambah</a>
		</div> 
		<div class="card-body">
			<table class="table table-bordered" id="tabel-invoice" width="100%">
				<thead>
					<tr>
						<th width="1px">No</th>
						<th width="100px">No Invoice</th> 

						<th>Customer</th>
						<th>No SKPP</th>
						<th>No SO</th> 
						<th>No Faktur Pajak</th> 
						<th>Nominal</th>
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
	/*
    |--------------------------------------------------------------------------
    | Table 
    |--------------------------------------------------------------------------
    */ 

	var data_table = [
        {data: 'DT_RowIndex',		name: 'DT_RowIndex', orderable: false, searchable: false}, 
        {data: 'no_tagihan',    	name: 'no_tagihan'}, 
        {data: 'customer',  		name: 'customer'},  
        {data: 'no_skpp',       	name: 'no_skpp'}, 
        {data: 'no_so',       		name: 'no_so'},  
        {data: 'no_faktur_pajak',	name: 'no_faktur_pajak'},
        {data: 'total', 			name: 'total'},  
        {data: 'action',      		name: 'action', orderable: false,},
    ];
    
    var table_invoice = table('#tabel-invoice', '{{url('invoice/data')}}/'+'{{$id}}', data_table);


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
    		error : function(){
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
</script>

@endsection
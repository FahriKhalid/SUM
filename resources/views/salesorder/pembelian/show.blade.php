@extends('layout.index')

@section('title', 'SUM - Detail SKPP')

@section('css')
	<link rel="stylesheet" type="text/css" href="{{asset('vendor/datatables/dataTables.bootstrap4.min.css')}}"> 
@endsection
 
@section('content')
 
@include('layout.header_pembelian') 
@include('salesorder.pembelian.modal_detail') 

<div class="container-fluid mb-4 mt-4">

    <div class="row">
        <div class="col-md-12 d-flex justify-content-between">
        	<h6 class="m-0 font-weight-bold text-primary"><i class="fa fa-truck-moving"></i> Sales Order</h6>
            <a href="{{ url("pembelian/pre_order") }}" class="text-muted"><i class="fa fa-arrow-left"></i> Kembali</a>   
        </div>  
    </div>    
	

    @if($info["skpp"] == null)
    <div class="text-center">
		<div>
			<img src="{{asset('img/add_bg.png')}}" class="mt-4 mb-4" width="200px">

			<p>SKPP masih kosong <br> Silahkan tambah SKPP terlebih dahulu</p>
			<a href="{{ url('pembelian/skpp/show/'.$id) }}" class="btn btn-success">SKPP</a> 
		</div> 
	</div> 
	@else
	<div class="card mt-3 "> 
		<div class="card-body bg-white">   
			@if($info["skpp"] != null)
			<a class="btn btn-success" href="{{ url('pembelian/salesorder/'.$id.'/create') }}"><i class="fa fa-plus"></i> Tambah</a>
 			@endif
		</div> 
		<div class="card-body"> 
			<table class="table table-bordered" id="tabel-so" style="width: 100%">
				<thead>
					<tr>
						<th width="1px">No</th>
						<th>Nomor SO</th> 
						<th>Nomor SKPP</th>
						<th>Total Kuantitas</th> 
						<th>Created by</th> 
						<th>Created at</th> 
						<th width="70px">Aksi</th>
					</tr>
				</thead> 
			</table>
		</div>
	</div>
    @endif  
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
        {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false}, 
        {data: 'no_so',       name: 'no_so'}, 
        {data: 'no_skpp',  	  name: 'no_skpp'},   
        {data: 'kuantitas',   name: 'kuantitas'},   
        {data: 'created_by',  name: 'created_by'}, 
        {data: 'created_at',  name: 'created_at'}, 
        {data: 'action',      name: 'action', orderable: false,},
    ];
    
    @if($info["skpp"] != null)
    var table_so = table('#tabel-so', '{{url('pembelian/salesorder/data')}}/'+'{{Helper::encodex($info["skpp"]->id_skpp)}}', data_table);
     
    @endif
	

	/*
	|--------------------------------------------------------------------------
	| modal hapus data sales order
	|--------------------------------------------------------------------------
	*/

	$("body").delegate(".hapus", "click", function(e){
    	e.preventDefault();  
    	let url = $(this).attr("show"); 
    	let url_delete = $(this).attr("url");

    	let parent = $("#modal-konfirmasi-hapus-so_pembelian");
    	$.ajax({
	        url : url,
	        type : 'GET',  
			dataType : "json", 
			beforeSend: function(resp){
				$("#stok-minus").prop("checked", false);
				$("#table-show-produk").addClass("d-none"); 
				parent.find("#form-hapus-so_pembelian").attr("action", url_delete); 
    			parent.modal("show");
			},
	        success : function(resp)
	        {   
	        	$("#table-show-produk").html(resp.html); 
	        },
	        error : function (jqXHR, exception) {
	            errorHandling(jqXHR.status, exception); 
	        }
	    });
    });


	/*
	|--------------------------------------------------------------------------
	| checkbox stok minus
	|--------------------------------------------------------------------------
	*/

    $("#stok-minus").on("click", function(){
    	if($(this).is(":checked")){
    		$("#table-show-produk").removeClass("d-none");
    	} else {
    		$("#table-show-produk").addClass("d-none");
    	}
    });


    /*
	|--------------------------------------------------------------------------
	| hapus data sales order
	|--------------------------------------------------------------------------
	*/

	$(document).on("submit", "#form-hapus-so_pembelian", function(e){
	    e.preventDefault();

	    $.ajax({
	        url : $(this).attr("action"),
	        type : 'GET',
	        data : { "_token" : $('meta[name="csrf-token"]').attr('content'), "stok_minus" : $("input[type=checkbox][name=stok_minus]:checked").val() },  
			dataType : "json", 
			beforeSend: function(resp){
				loader(".modal-content", true);
			},
	        success : function(resp)
	        { 
	            if (resp.status == 'success') {
	                toastr.success(resp.message, { "closeButton": true });    
	                refresh_table("#tabel-so");
	                $("#modal-konfirmasi-hapus-so_pembelian").modal("hide");
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
	| detail sales order
	|--------------------------------------------------------------------------
	*/

	$("body").delegate(".detail", "click", function(){
		let url = $(this).attr("url");

		detail(url);
	});

	function detail(url)
	{
		$.ajax({
	        url : url,
	        type : 'GET',  
			dataType : "json", 
			beforeSend: function(resp){
				$("#modal-detail").modal("show");
				loader(".modal-content", true);
			},
	        success : function(resp)
	        {   
	        	$("#detail-sales-order").html(resp.html);
	            loader(".modal-content", false);
	        },
	        error : function (jqXHR, exception) {
	            errorHandling(jqXHR.status, exception); 
	            loader(".modal-content", false);
	        }
	    });
	}

</script>

@if(isset($_GET["url"]) && $_GET["url"] != "")
<script type="text/javascript">
	detail('{{ Helper::decodex($_GET["url"]) }}');
</script>
@endif

@endsection
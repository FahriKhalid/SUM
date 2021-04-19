@extends('layout.index')

@section('title', 'SUM - SKPP')

@section('css')
	<link rel="stylesheet" type="text/css" href="{{asset('vendor/datatables/dataTables.bootstrap4.min.css')}}"> 
@endsection
 
@section('content')
 
 
<div class="container-fluid mt-4">
	<div class="row">
        <div class="col-md-12 d-flex justify-content-between">
        	<h6 class="m-0 font-weight-bold text-primary">SKPP</h6>   
        </div>  
    </div> 
	
	<div class="card mt-3">
        <div class="card-header bg-white">
            <a href="{{ url('penjualan/skpp/create') }}" class="btn btn-success"><i class="fa fa-plus"></i> Tambah</a>
        </div>
		<div class="card-body"> 
			<div class="table-responsive">
                <table class="table table-bordered" id="tabel-skpp" style="width:100%">
                    <thead>
                        <tr>
                            <th width="1px">No</th>
                            <th>Nomor SKPP</th>  
                            <th>Customer</th> 
                            <th>Terakhir pembayaran</th> 
                            <th>Pembayaran</th>
                            <th>Status</th>
                            <th>Created by</th>
                            <th width="130px">Created at</th>
                            <th width="70px">Aksi</th>
                        </tr>
                    </thead> 
                    <thead>
                        <tr>
                            <td class="p-1"></td>
                            <td class="p-1">
                            	<input type="text" class="form-control" id="filter-no-skpp" placeholder="Cari" name="">
                            </td>  
                            <td class="p-1">
                            	<select class="form-control select2" id="filter-customer" title="customer">
                            		<option value="">Semua</option>
                            		@foreach($info["customer"] as $customer)
                            		<option value="{{ Helper::encodex($customer->id_customer) }}">{{ $customer->perusahaan }}</option>
                            		@endforeach
                            	</select>
                            </td> 
                            <td class="p-1">
                            	<input type="text" class="form-control datepicker-table" id="filter-terkahir-pembayaran" placeholder="Cari" name="">
                            </td> 
                            <td class="p-1">
                            	<select class="form-control select2" id="filter-pembayaran">
                            		<option value="">Semua</option> 
                            		@foreach($info["status_pembayaran"] as $status)
                            		<option value="{{ Helper::encodex($status->id_status) }}">{{ $status->status }}</option>
                            		@endforeach
                            	</select>
                            </td>
                            <td class="p-1">
                            	<select class="form-control select2" id="filter-status" title="status">
                            		<option value="">Semua</option>
                            		@foreach($info["status_skpp"] as $status)
                            		<option value="{{ Helper::encodex($status->id_status) }}">{{ $status->status }}</option>
                            		@endforeach
                            	</select>
                            </td>
                            <td class="p-1">
                            	<input type="text" class="form-control" id="filter-created-by" placeholder="Cari" name="">
                            </td>
                            <td class="p-1">
                            	<input type="text" class="form-control datepicker-table" id="filter-created-at" placeholder="Cari" name="">
                            </td>
                            <td class="p-1"></td>
                        </tr>
                    </thead> 
                </table>
            </div>

            <div>
				<div class="legend bg-warning"></div> Jatuh tempo kurang dari 10 hari
			</div>
			<div>
				<div class="legend bg-red"></div> Jatuh tempo
			</div>
		</div>
	</div>
</div>

@endsection

@section('footer')

<script type="text/javascript" src="{{asset('vendor/datatables/jquery.dataTables.min.js')}}"></script>
<script type="text/javascript" src="{{asset('vendor/datatables/dataTables.bootstrap4.min.js')}}"></script> 
<script src="https://cdn.datatables.net/responsive/2.2.3/js/dataTables.responsive.min.js" type="text/javascript"></script>  
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js" integrity="sha512-qTXRIMyZIFb8iQcfjXWCO8+M5Tbc38Qi5WzdPOYZHIlZpzBHG3L3by84BBBOiRGiEb7KKtAOAs5qYdUiZiQNNQ==" crossorigin="anonymous"></script>

<script type="text/javascript">

	var add = true;

	function show_modal(){
		add = true;
		$("#title-modal-form-skpp").html("Form tambah skpp");
		$("#form-skpp")[0].reset();
		$("#modal-form-skpp").modal("show");
	}

	/*
    |--------------------------------------------------------------------------
    | Table 
    |--------------------------------------------------------------------------
    */ 

	var columns = [
        {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false}, 
        {data: 'no_skpp',     name: 'no_skpp'},
        {data: 'customer',    name: 'customer'}, 
        {data: 'terakhir_pembayaran',  name: 'terakhir_pembayaran'}, 
        {data: 'pembayaran',  name: 'pembayaran'},  
        {data: 'status',  	  name: 'status'},   
        {data: 'created_by',  name: 'created_by'},
        {data: 'created_at',  name: 'created_at'}, 
        {data: 'action',      name: 'action', orderable: false,},
    ];

    $('#tabel-skpp').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url : '{{url('penjualan/skpp/data')}}',
            data: function (d) {
                d.no_skpp = $("#filter-no-skpp").val();
                d.customer = $("#filter-customer").val();
                d.terakhir_pembayaran = $("#filter-terkahir-pembayaran").val();
                d.pembayaran = $("#filter-pembayaran").val();
                d.status = $("#filter-status").val();
                d.created_by = $("#filter-created-by").val(); 
                d.created_at = $("#filter-created-at").val(); 
            }
        },
        columns: columns,
        responsive: true,
        colReorder: true, 
        columnDefs:[{
        	targets:3, render:function(data){
		      	if(data != '-'){
		      		return moment(data).format('DD/MM/yyyy');
		     	} else {
		     		return '-';
		     	}
		    }
		}],
        createdRow: function ( row, data, index ) { 
        	if(data['pembayaran'] != 'Lunas'){
        		$(row).addClass(data['status_terakhir_pembayaran']);
        	}
		},
		order: [[ 7, "desc" ]]
    });  


    /*
	|--------------------------------------------------------------------------
	| Function filter Datatable
	|--------------------------------------------------------------------------
	*/   
	filterDatatable("#tabel-skpp");
 

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
	                refresh_table("#tabel-pre-order");
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
	})

</script>

@endsection
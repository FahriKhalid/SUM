@extends('layout.index')

@section('title', 'SUM - Pre Order')

@section('css')
	<link rel="stylesheet" type="text/css" href="{{asset('vendor/datatables/dataTables.bootstrap4.min.css')}}"> 
@endsection
 
@section('content')
 
 
<div class="container-fluid mt-4">
	<div class="row">
        <div class="col-md-12 d-flex justify-content-between">
        	<h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-bullseye"></i> Pre order</h6>   
        </div>  
    </div> 
	
	<div class="card mt-3">
		<div class="card-body">
			<a href="{{ url('pembelian/pre_order/create') }}" class="btn btn-success mb-3"><i class="fa fa-plus"></i> Tambah</a>
			<div class="table-responsive">
                <table class="table table-bordered" id="tabel-pre-order" style="width:100%">
                    <thead>
                        <tr>
                            <th width="1px">No</th>
                            <th>Nomor PO</th>  
                            <th>Produsen</th> 
                            <th>Nomor SKPP</th>
                            <th>Terakhir pembayaran</th> 
                            <th>Pembayaran</th>
                            <th>Created by</th>
                            <th width="130px">Created at</th>
                            <th width="70px">Aksi</th>
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
        {data: 'no_po',       name: 'no_po'},
        {data: 'produsen',    name: 'produsen'}, 
        {data: 'skpp',    	  name: 'skpp'},   
        {data: 'terakhir_pembayaran',  name: 'terakhir_pembayaran'}, 
        {data: 'pembayaran',  name: 'pembayaran'},  
        {data: 'created_by',  name: 'created_by'},
        {data: 'created_at',  name: 'created_at'}, 
        {data: 'action',      name: 'action', orderable: false,},
    ];

    $('#tabel-pre-order').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url : '{{url('pembelian/pre_order/data')}}'
        },
        columns: columns,
        responsive: true,
        colReorder: true, 
        columnDefs:[{
        	targets:4, render:function(data){
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
		}
    }); 


    /*
    |--------------------------------------------------------------------------
    | form aksi tambha dan edit
    |--------------------------------------------------------------------------
    */ 

	$(document).on("submit", "#form-skpp", function(e){
		e.preventDefault();

		if (add === true) {
			var url = '{{ url('pembelian/skpp/store') }}';
		} else {
			var id = $("input[name=id]").val();
			var url = '{{ url('pembelian/skpp/update') }}/'+id;
		}

		$.ajax({
			url : url,
			method : "POST",
			data : new FormData(this),
			contentType : false,
			processData : false,
			dataType : "json",
			beforeSend : function(){
                loader(".modal-content", true);
			},
			success : function(resp){
               	if (resp.status == "error"){
               		for (var i = 0; i < resp.message.length; i++) {
               			toastr.error(resp.message[i],{ "closeButton": true });
               		} 
                } else {
               		toastr.success(resp.message, { "closeButton": true }); 
               		refresh_table("#tabel-pre-order"); 
                }
				
                loader(".modal-content", false);
			},
			error : function(jqXHR, exception){
				errorHandling(jqXHR.status, exception);
                loader(".modal-content", false);
			}
		});
	});
 

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
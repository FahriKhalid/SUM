@extends('layout.index')

@section('title', 'SUM - SKPP')

@section('css')
	<link rel="stylesheet" type="text/css" href="{{asset('vendor/datatables/dataTables.bootstrap4.min.css')}}"> 
@endsection
 
@section('content')
 
 
<div class="container-fluid mt-4">
	<div class="row">
        <div class="col-md-12 d-flex justify-content-between">
        	<h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-bullseye"></i> SKPP</h6>   
        </div>  
    </div> 
	
	<div class="card mt-3">
		<div class="card-body">
			<a href="{{ url('penjualan/skpp/create') }}" class="btn btn-success mb-3"><i class="fa fa-plus"></i> Tambah</a>
			<div class="table-responsive">
                <table class="table table-bordered" id="tabel-skpp" style="width:100%">
                    <thead>
                        <tr>
                            <th width="1px">No</th>
                            <th>Nomor SKPP</th>  
                            <th>Customer</th>
                            <th>Status</th>
                            <th>Pembayaran</th>
                            <th>Created by</th>
                            <th width="130px">Created at</th>
                            <th width="70px">Aksi</th>
                        </tr>
                    </thead> 
                </table>
            </div>
		</div>
	</div>
</div>

@endsection

@section('footer')

<script type="text/javascript" src="{{asset('vendor/datatables/jquery.dataTables.min.js')}}"></script>
<script type="text/javascript" src="{{asset('vendor/datatables/dataTables.bootstrap4.min.js')}}"></script> 
<script src="https://cdn.datatables.net/responsive/2.2.3/js/dataTables.responsive.min.js" type="text/javascript"></script>  

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

	var data_table = [
        {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false}, 
        {data: 'no_skpp',     name: 'no_skpp'},
        {data: 'customer',    name: 'customer'}, 
        {data: 'status',      name: 'status'},
        {data: 'pembayaran',  name: 'pembayaran'}, 
        {data: 'created_by',  name: 'created_by'},
        {data: 'created_at',  name: 'created_at'}, 
        {data: 'action',      name: 'action', orderable: false,},
    ];
    
    var table_pengguna = table('#tabel-skpp', '{{url('penjualan/skpp/data')}}', data_table);


    /*
    |--------------------------------------------------------------------------
    | form aksi tambha dan edit
    |--------------------------------------------------------------------------
    */ 

	$(document).on("submit", "#form-skpp", function(e){
		e.preventDefault();

		if (add === true) {
			var url = '{{ url('penjualan/skpp/store') }}';
		} else {
			var id = $("input[name=id]").val();
			var url = '{{ url('penjualan/skpp/update') }}/'+id;
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
               		refresh_table("#tabel-skpp");
               		$("#modal-form-skpp").modal("hide"); 
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
    | show data
    |--------------------------------------------------------------------------
    */

    $("body").delegate(".edit", "click", function(e){
    	e.preventDefault();

    	var id = $(this).attr("did");

    	$.ajax({
			url : '{{url('penjualan/skpp/show')}}/'+id, 
			type: 'GET',
			data : { id : id }, 
			dataType : "json",
			beforeSend : function(){
				add = false;
				$("#title-modal-form-skpp").html("Form edit skpp");
				$("#modal-form-skpp").modal("show");
                loader(".modal-content", true);
			},
			success : function(resp){
				$("input[name=id]").val(resp.id_skpp);
               	$("input[name=nama_skpp]").val(resp.nama); 
               	$("input[type=radio][name=status][value="+resp.is_aktif+"]").prop("checked", true);
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
	                refresh_table("#tabel-skpp");
	                $("#modal-konfirmasi-hapus").modal("hide");
	            } else {
	                toastr.error(resp.message, { "closeButton": true });
	            }
	            loader(".modal-content", false);
	        },
	        error : function (jqXHR, exception) {
	            errorHandling(jqXHR.status, exception); 
	        }
	    })
	})

</script>

@endsection
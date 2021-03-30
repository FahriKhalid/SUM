@extends('layout.index')

@section('title', 'SUM - Gudang')

@section('css')
	<link rel="stylesheet" type="text/css" href="{{asset('vendor/datatables/dataTables.bootstrap4.min.css')}}"> 
@endsection
 
@section('content')

@include('gudang.form_modal')
 
	<div class="container-fluid mt-4">
		
		<div class="row">
	        <div class="col-md-12 d-flex justify-content-between">
	        	<h6 class="m-0 font-weight-bold text-primary">GUDANG</h6>   
	        </div>  
	    </div>

		<div class="card mt-3">
			<div class="card-body">

				<button onclick="show_modal()" class="btn btn-success"><i class="fa fa-plus"></i> Tambah</button>

				<div class="table-responsive mt-3">
                    <table class="table table-sm table-bordered" id="tabel-gudang" style="width:100%">
                        <thead>
                            <tr>
                                <th width="1px">No</th>
                                <th>Nama gudang</th>  
                                <th>Alamat</th>
                                <th>Status</th>
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
		$("#title-modal-form-gudang").html("Form tambah gudang");
		$("#form-gudang")[0].reset();
		$("#modal-form-gudang").modal("show");
	}

	/*
    |--------------------------------------------------------------------------
    | Table 
    |--------------------------------------------------------------------------
    */ 

	var data_table = [
        {data: 'DT_RowIndex', 	name: 'DT_RowIndex', orderable: false, searchable: false}, 
        {data: 'nama',		  	name: 'nama'}, 
        {data: 'alamat',		name: 'alamat'}, 
        {data: 'is_aktif',    	name: 'is_aktif'}, 
        {data: 'created_by',  	name: 'created_by'},
        {data: 'created_at',  	name: 'created_at'}, 
        {data: 'action',      	name: 'action', orderable: false,},
    ];
    
    var table_pengguna = table('#tabel-gudang', '{{url('gudang/data')}}', data_table);


    /*
    |--------------------------------------------------------------------------
    | form aksi tambha dan edit
    |--------------------------------------------------------------------------
    */ 

	$(document).on("submit", "#form-gudang", function(e){
		e.preventDefault();

		if (add === true) {
			var url = '{{ url('gudang/store') }}';
		} else {
			var id = $("input[name=id]").val();
			var url = '{{ url('gudang/update') }}/'+id;
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
               		refresh_table("#tabel-gudang");
               		$("#modal-form-gudang").modal("hide"); 
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
			url : '{{url('gudang/show')}}/'+id, 
			type: 'GET',
			data : { id : id }, 
			dataType : "json",
			beforeSend : function(){
				add = false;
				$("#title-modal-form-gudang").html("Form edit gudang");
				$("#modal-form-gudang").modal("show");
                loader(".modal-content", true);
			},
			success : function(resp){
				$("input[name=id]").val(resp.id_gudang);
               	$("input[name=nama]").val(resp.nama); 
               	$("input[name=alamat]").val(resp.alamat); 
               	$("input[name=kepala_gudang]").val(resp.kepala_gudang);  
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
	                refresh_table("#tabel-gudang");
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
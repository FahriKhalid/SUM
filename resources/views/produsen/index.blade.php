@extends('layout.index')

@section('title', 'Produsen')

@section('css')
	<link rel="stylesheet" type="text/css" href="{{asset('vendor/datatables/dataTables.bootstrap4.min.css')}}">
	<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/fixedcolumns/3.3.2/css/fixedColumns.dataTables.min.css">
@endsection
 
@section('content')

@include('produsen.form_modal')
 
	<div class="container-fluid mt-4">
		<button onclick="show_modal()" class="btn btn-success"><i class="fa fa-plus"></i> Tambah</button>
		<div class="card mt-3">
			<div class="card-body">
				<div class="table-responsive">
                    <table class="table table-sm table-bordered" id="tabel-produsen" style="width:100%">
                        <thead>
                            <tr>
                                <th width="1px">No</th>
                                <th>Nama perusahaan</th>  
                                <th>Nama</th>
                                <th>Email</th> 
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
<script type="text/javascript" src="https://cdn.datatables.net/fixedcolumns/3.3.2/js/dataTables.fixedColumns.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.3/js/dataTables.responsive.min.js" type="text/javascript"></script>  

<script type="text/javascript">

	var add = true;

	function show_modal(){
		add = true;
		$("#title-modal-form-produsen").html("Form tambah produsen");
		$("#form-produsen")[0].reset();
		$("#modal-form-produsen").modal("show");
	}

	/*
    |--------------------------------------------------------------------------
    | Table 
    |--------------------------------------------------------------------------
    */ 

	var data_table = [
        {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
        {data: 'perusahaan',  name: 'perusahaan'}, 
        {data: 'nama',		  name: 'nama'},
        {data: 'email',    	  name: 'email'}, 
        {data: 'action',      name: 'action', orderable: false,},
    ];
    
    var table_pengguna = table('#tabel-produsen', '{{url('produsen/data')}}', data_table);


    /*
    |--------------------------------------------------------------------------
    | form aksi tambha dan edit
    |--------------------------------------------------------------------------
    */ 

	$(document).on("submit", "#form-produsen", function(e){
		e.preventDefault();

		if (add === true) {
			var url = '{{ url('produsen/store') }}';
		} else {
			var id = $("input[name=id]").val();
			var url = '{{ url('produsen/update') }}/'+id;
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
               		refresh_table("#tabel-produsen");
               		$("#modal-form-produsen").modal("hide"); 
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
			url : '{{url('produsen/show')}}/'+id, 
			type: 'GET',
			data : { id : id }, 
			dataType : "json",
			beforeSend : function(){
				add = false;
				$("#title-modal-form-produsen").html("Form edit produsen");
				$("#modal-form-produsen").modal("show");
                loader(".modal-content", true);
			},
			success : function(resp){
				$("input[name=id]").val(resp.id_produsen);
               	$("input[name=nama_perusahaan]").val(resp.perusahaan);
               	$("input[name=nomor_npwp]").val(resp.no_npwp);
               	$("input[name=nama_produsen]").val(resp.nama);
               	$("input[name=email]").val(resp.email);
               	$("input[name=telpon]").val(resp.telpon);
               	$("textarea[name=alamat]").val(resp.alamat);
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
	                refresh_table("#tabel-produsen");
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
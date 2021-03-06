@extends('layout.index')

@section('title', 'SUM - Stok Gudang')

@section('css')
	<link rel="stylesheet" type="text/css" href="{{asset('vendor/datatables/dataTables.bootstrap4.min.css')}}"> 
@endsection
 
@section('content')
 
	<div class="container-fluid mt-4">
		<div class="row">
            <div class="col-md-12 d-flex justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">STOK GUDANG</h6>   
            </div>  
        </div>
		<div class="card mt-3">
			<div class="card-body">
				<div class="table-responsive">
                    <table class="table table-sm table-bordered" id="tabel-stok" style="width:100%">
                        <thead>
                            <tr>
                            	<th width="1px">No</th> 
                                <th>Nama</th>  
                                <th>Spesifikasi</th> 
                                <th>Jumlah</th>
                                <th width="70px">Status</th>
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
		$("#title-modal-form-user").html("Form tambah user");
		$("#form-user")[0].reset();
		$("#form-password").removeClass("d-none");
		$("#checkbox-password").addClass("d-none");
		$("#change-password").prop("checked", false);
		$("#modal-form-user").modal("show");
	}

	$("body").delegate("#change-password", "click", function(){
		if($(this).is(":checked")){
            $("#form-password").removeClass("d-none"); 
        }else{
            $("#form-password").addClass("d-none");
        }
	})

	/*
    |--------------------------------------------------------------------------
    | Table 
    |--------------------------------------------------------------------------
    */ 

	var columns = [  
		{data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false}, 
        {data: 'nama',	      name: 'nama'},
        {data: 'spesifikasi', name: 'spesifikasi'}, 
        {data: 'jumlah',	  name: 'jumlah'},
        {data: 'is_aktif',    name: 'is_aktif'},   
    ];

     $('#tabel-stok').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url : '{{url('stok/data')}}'
        },
        columns: columns,
        responsive: true,
        colReorder: true, 
        columnDefs:[{
        	targets: 3, render: function(data){
		      	return data + ' MT';
		    }
		}], 
    }); 
 


    /*
    |--------------------------------------------------------------------------
    | form aksi tambha dan edit
    |--------------------------------------------------------------------------
    */ 

	$(document).on("submit", "#form-user", function(e){
		e.preventDefault();

		if (add === true) {
			var url = '{{ url('user/store') }}';
		} else {
			var id = $("input[name=id]").val();
			var url = '{{ url('user/update') }}/'+id;
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
               		refresh_table("#tabel-stok");
               		$("#modal-form-user").modal("hide"); 
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
			url : '{{url('user/show')}}/'+id, 
			type: 'GET',
			data : { id : id }, 
			dataType : "json",
			beforeSend : function(){
				add = false;
				$("#title-modal-form-user").html("Form edit user");
				$("#modal-form-user").modal("show");
				$("#form-password").addClass("d-none");
				$("#checkbox-password").removeClass("d-none");
				$("#change-password").prop("checked", false);
                loader(".modal-content", true);
			},
			success : function(resp){
				$("input[name=id]").val(resp.id_user);
               	$("input[name=nama]").val(resp.nama); 
               	$("input[name=email]").val(resp.email); 
               	$("select[name=role]").val(resp.id_role); 
               	$("input[name=username]").val(resp.username);
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
	                refresh_table("#tabel-stok");
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
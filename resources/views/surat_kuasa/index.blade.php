@extends('layout.index')

@section('title', 'SUM - Surat Kuasa')
@section('css')
<link rel="stylesheet" type="text/css" href="{{asset('vendor/datatables/dataTables.bootstrap4.min.css')}}"> 
@endsection
@section('content') 

@include('salesorder.penjualan.header_salesorder') 
@include('surat_kuasa.modal_detail')
@include('layout.modal_email')

<div class="container-fluid mb-4 mt-4">

	<div class="row">
		<div class="col-md-12 d-flex justify-content-between">
			<h6 class="m-0 font-weight-bold text-primary"><i class="fa fa-truck-moving"></i> Surat kuasa</h6>
			<a href="{{ url('penjualan/salesorder/index/'.Helper::encodex($info["so"]->SKPP->id_skpp)) }}" class="text-muted"><i class="fa fa-arrow-left"></i> Kembali</a>   
		</div>  
	</div>    

	<div class="card mt-3"> 
		<div class="card-header bg-white">
			<a href="{{ url('surat_kuasa/'.Helper::encodex($info["so"]->id_so).'/create') }}" class="btn btn-success"><i class="fa fa-plus"></i> Tambah</a>
		</div>
		<div class="card-body">
			<div class="table-responsive"> 
				<table class="table table-bordered" id="tabel-surat-kuasa" width="100%">
					<thead>
						<tr> 
							<th>Nomor surat kuasa</th>
							<th>Supir</th> 
							<th>Kuantitas</th>
							<th>Gudang</th> 
							<th>Created by</th>
							<th>Created at</th>
							<th width="1px;">Aksi</th>
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
<script type="text/javascript" src="{{asset('vendor/datatables/dataTables.responsive.min.js')}}"></script>  

<script type="text/javascript">
	function modal_ganti_supir() {
		$("#modal-ganti-supir").modal("show");
		$("#form-ganti-supir")[0].reset();
	}

	/*
    |--------------------------------------------------------------------------
    | Table 
    |--------------------------------------------------------------------------
    */ 

    var columns = [ 
    {data: 'no_sk',       name: 'no_sk'},  
    {data: 'supir',       name: 'supir', orderable: false},   
    {data: 'kuantitas',   name: 'kuantitas'},
    {data: 'gudang',  	  name: 'gudang'},
    {data: 'created_by',  name: 'created_by'}, 
    {data: 'created_at',  name: 'created_at'}, 
    {data: 'action',      name: 'action', orderable: false},
    ];
     
    $('#tabel-surat-kuasa').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url : '{{url('surat_kuasa/data')}}/'+'{{$id}}'
        },
        columns: columns,
        responsive: true,
        colReorder: true, 
        columnDefs:[{
        	targets:2, render:function(data){
		      	return data + " MT";
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
	| detail data
	|--------------------------------------------------------------------------
	*/

	$("body").delegate(".detail", "click", function(){
		var url = $(this).attr("url");
		detail(url);
	});

	function detail(url)
	{
		$.ajax({
			url : url,
			type : 'GET',
			dataType : 'json',
			beforeSend : function(){
				$("#modal-detail").modal("show");
				loader(".modal-content", true);
			},
			success : function(resp){
				$("#detail-surat-kuasa").html(resp.html);
				loader(".modal-content", false);
			},
			error : function(jqXHR, exception){
				errorHandling(jqXHR.status, exception);
				loader(".modal-content", false);
			}
		});
	}


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
					refresh_table("#tabel-surat-kuasa");
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
	});
</script>

@if(isset($_GET["url"]) && $_GET["url"] != "")
<script type="text/javascript">
	detail('{{ Helper::decodex($_GET["url"]) }}');
</script>
@endif
@endsection
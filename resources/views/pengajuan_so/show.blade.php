@extends('layout.index')

@section('title', 'SUM - Detail SKPP Pembelian')

@section('css')
<link rel="stylesheet" type="text/css" href="{{asset('vendor/datatables/dataTables.bootstrap4.min.css')}}"> 
@endsection

@section('content')
@include('layout.header_pembelian')
{{-- @include('skpp.pembelian.modal_form_booking')
@include('skpp.pembelian.modal_detail_booking') 
@include('skpp.pembelian.modal_form_pembayaran') --}}
@include('pengajuan_so.modal_detail') 
{{-- @include('pembayaran.penjualan.modal_show_pembayaran')
@include('layout.modal_email')  --}}

<div class="container-fluid mb-4 mt-4">

	<div class="row">
		<div class="col-md-12 d-flex justify-content-between">
			<h6 class="m-0 font-weight-bold text-primary"><i class="fa fa-wallet"></i> Pengajuan SO</h6>
			<a href="{{ url("pembelian/pre_order") }}" class="text-muted"><i class="fa fa-arrow-left"></i> Kembali</a>   
		</div>  
	</div>  

	<div id="layout-booking">
		 
		 


		<div class="card mt-3 ">
			<div class="card-body">
				<a href="{{ url('pembelian/pengajuan_so/create/'.$id) }}" class="btn btn-success">
					<i class="fa fa-plus"></i> Tambah 
				</a>
				<div>
					@include('pengajuan_so.table_pengajuan_so')
				</div>

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
 

	/*
    |--------------------------------------------------------------------------
    |
    | table pengajuan so
    |
    |--------------------------------------------------------------------------
    */ 
    
    var kolom_pso = [
    {data: 'DT_RowIndex', 			name: 'DT_RowIndex', orderable: false, searchable: false},  
    {data: 'no_pengajuan_so', 		name: 'no_pengajuan_so'}, 
    {data: 'kuantitas',             name: 'kuantitas'}, 
    {data: 'created_by',  			name: 'created_by'},
    {data: 'created_at',  			name: 'created_at'}, 
    {data: 'action',      			name: 'action', orderable: false}
    ];

    table('#tabel-pengajuan-so', '{{url('pembelian/pengajuan_so/data')}}/'+'{{ $id }}', kolom_pso);
 
    /*
    |--------------------------------------------------------------------------
    | Pengajuan SO
    | 1. detail  
    | 2. hapus
    |--------------------------------------------------------------------------
    */

    $("body").delegate(".detail-pengajuan-so", "click", function(){
    	let url = $(this).attr("url");
    	detail_pengajuan_so(url);
    });

    function detail_pengajuan_so(url)
    {
        $.ajax({
            url : url,
            type : "GET",
            dataType : "json",
            beforeSend: function()
            {
                $("#modal-detail-pengajuan-so").modal("show");
                loader('.modal-content', true);
            },
            success : function(resp)
            {
                $("#detail-pengajuan-so").html(resp);
                loader('.modal-content', false);
            },
            error : function(jqXHR, exception){
                errorHandling(jqXHR.status, exception);
                loader(".modal-content", false);
            }
        });
    }

    $("body").delegate(".delete-pengajuan-so", "click", function(){
    	let url = $(this).attr("url"); 
		$("#form-hapus").attr("action", url); 
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
					refresh_table("#tabel-pengajuan-so"); 
					$("#modal-konfirmasi-hapus").modal("hide");    
					$("#modal-detail-pengajuan-so").modal("hide"); 
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

@if(isset($_GET["url"]) && $_GET["url"] != "")
<script type="text/javascript">
    detail_pengajuan_so('{{ Helper::decodex($_GET["url"]) }}');
</script>
@endif
@endsection
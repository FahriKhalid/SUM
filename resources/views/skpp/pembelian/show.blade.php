@extends('layout.index')

@section('title', 'SUM - Detail SKPP Pembelian')

@section('css')
<link rel="stylesheet" type="text/css" href="{{asset('vendor/datatables/dataTables.bootstrap4.min.css')}}"> 
@endsection

@section('content')
@include('layout.header_pembelian')
@include('skpp.pembelian.modal_form_booking')
@include('skpp.pembelian.modal_detail_booking') 
@include('skpp.pembelian.modal_form_pembayaran')
@include('pengajuan_so.modal_detail') 
@include('pembayaran.penjualan.modal_show_pembayaran')
@include('layout.modal_email') 

<div class="container-fluid mb-4 mt-4">

	<div class="row">
		<div class="col-md-12 d-flex justify-content-between">
			<h6 class="m-0 font-weight-bold text-primary"><i class="fa fa-wallet"></i> SKPP</h6>
			<a href="{{ url("pembelian/pre_order") }}" class="text-muted"><i class="fa fa-arrow-left"></i> Kembali</a>   
		</div>  
	</div>  

	<div id="layout-booking">
		@if($info["skpp"] == null)
		<div class="text-center">
			<div>
				<img src="{{ asset('img/add_bg.png') }}" class="mt-4 mb-4" width="200px">

				<p>SKPP masih kosong <br> Klik tombol tambah untuk menambahkan data SKPP</p>
				<button class="btn btn-success" onclick="showModalBooking()"><i class="fa fa-plus"></i> Tambah</button> 
			</div> 
		</div>
		@else

		

		<div class="card mt-3">
			<div class="card-header bg-white">
				<button class="btn btn-primary" onclick="showEditSkpp('{{ url('pembelian/skpp/edit/'.Helper::encodex($info["skpp"]->id_skpp)) }}')">
					<i class="fa fa-edit"></i> Edit
				</button> 
			</div>
			<div class="card-body">
				<table class="table table-borderless" style="margin-left: -10px">  
				<tr>
					<th width="220px">Nomor SKPP</th>
					<th width="1px">:</th>
					<td>{{ $info["skpp"]->no_skpp }}</td>
				</tr>
				<tr>
					<th>Total pembayaran</th>
					<th>:</th>
					<td>Rp {{ Helper::currency($info["skpp"]->total_pembayaran) }}</td>
				</tr>
				<tr>
					<th>Terakhir pembayaran</th>
					<th>:</th>
					<td>{{ Helper::dateIndo($info["skpp"]->terakhir_pembayaran) }}</td>
				</tr> 
			</table> 
			</div>
		</div>

		<div class="card mt-3 ">  
			<nav class="alert-primary">
				<div class="nav nav-tabs nav-justified" id="nav-tab" role="tablist">
					<a class="nav-item text-dark nav-link active" id="nav-home-tab" data-toggle="tab" href="#nav-1" role="tab" aria-controls="nav-home" aria-selected="true">Pengajuan SO</a>
					<a class="nav-item text-dark nav-link" id="nav-home-tab" data-toggle="tab" href="#nav-2" role="tab" aria-controls="nav-home" aria-selected="true">Booking & pembayaran</a>
					<a class="nav-item text-dark nav-link" id="nav-agt-keluarga-tab" data-toggle="tab" href="#nav-3" role="tab" aria-controls="nav-agt-keluarga" aria-selected="true">SKPP</a> 
				</div>
			</nav>

			
			<div class="tab-content">
				<div class="tab-pane fade show active" id="nav-1">
					<div class="card-body">
						<a href="{{ url('pembelian/pengajuan_so/create/'.$id) }}" class="btn btn-success">
							<i class="fa fa-plus"></i> Tambah 
						</a>
						<div>
							@include('pengajuan_so.table_pengajuan_so')
						</div>

					</div> 
				</div>
				<div class="tab-pane fade" id="nav-2">
					<div class="card-body">
						<button class="btn btn-success" onclick="showFormPembayaran('{{ Helper::encodex($info["skpp"]->id_skpp) }}')">
							<i class="fa fa-plus"></i> Tambah 
						</button>
						<div id="layout-table-pembayaran">
							@include('skpp.pembelian.table_pembayaran')
						</div>

					</div> 
				</div>
				<div class="tab-pane fade" id="nav-3">
					<object data="{{ asset('file_skpp/'.$info["skpp"]->file_skpp) }}#view=FitH" type="application/pdf" width="100%" height="800px">
						<div class="text-center">
							<p>File SKPP kosong!</p>
						</div>
					</object>
				</div>
			</div>
		</div>
		@endif
	</div> 

</div>

@endsection

@section('footer')

<script type="text/javascript" src="{{asset('vendor/datatables/jquery.dataTables.min.js')}}"></script>
<script type="text/javascript" src="{{asset('vendor/datatables/dataTables.bootstrap4.min.js')}}"></script> 
<script src="https://cdn.datatables.net/responsive/2.2.3/js/dataTables.responsive.min.js" type="text/javascript"></script>  

<script type="text/javascript">

	let aksi = "add";
	let jumlah_bayar;
	let hapus_pembayaran = false;
	let id_booking = null;

	function showModalBooking(){
		aksi = "add";
		$("#title-modal-form-booking").html("Form tambah SKPP");
		$("#form-booking")[0].reset();
		$("#modal-form-booking").modal("show");
	}

	/*
    |--------------------------------------------------------------------------
    | Table 
    | 1. table booking & pembayaran
    | 2. table pengajuan so
    | 3. table pembayaran
    |--------------------------------------------------------------------------
    */ 

    var kolom_booking = [
    {data: 'DT_RowIndex', 			name: 'DT_RowIndex', orderable: false, searchable: false},  
    {data: 'no_skpp', 				name: 'no_skpp'},  
    {data: 'total_pembayaran', 		name: 'total_pembayaran'}, 
    {data: 'terakhir_pembayaran',   name: 'terakhir_pembayaran'},  
    {data: 'created_by',  			name: 'created_by'},
    {data: 'created_at',  			name: 'created_at'}, 
    {data: 'action',      			name: 'action', orderable: false}
    ];
    
    table('#tabel-booking', '{{url('booking/data')}}/'+'{{ $id }}', kolom_booking);

    
    var kolom_pso = [
    {data: 'DT_RowIndex', 			name: 'DT_RowIndex', orderable: false, searchable: false},  
    {data: 'no_pengajuan_so', 		name: 'no_pengajuan_so'}, 
    {data: 'kuantitas',             name: 'kuantitas'}, 
    {data: 'created_by',  			name: 'created_by'},
    {data: 'created_at',  			name: 'created_at'}, 
    {data: 'action',      			name: 'action', orderable: false}
    ];

    table('#tabel-pengajuan-so', '{{url('pembelian/pengajuan_so/data')}}/'+'{{ $id }}', kolom_pso);


    var kolom_pembayaran = [
        {data: 'DT_RowIndex',       name: 'DT_RowIndex', orderable: false, searchable: false},  
        {data: 'kode_booking', name: 'kode_booking'}, 
        {data: 'jumlah_pembayaran', name: 'jumlah_pembayaran'}, 
        {data: 'sisa_hutang',       name: 'sisa_hutang'}, 
        {data: 'keterangan',        name: 'keterangan'}, 
        {data: 'created_by',        name: 'created_by'},
        {data: 'created_at',        name: 'created_at'}, 
        {data: 'action',            name: 'action', orderable: false,},
    ];

    @if($info["skpp"] != null)
    table('#tabel-pembayaran', '{{url('pembelian/pembayaran/data')}}/'+'{{ Helper::encodex($info["skpp"]->id_skpp) }}', kolom_pembayaran);
    @endif

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

    /*
    |--------------------------------------------------------------------------
    | Checkbox parsial 
    |--------------------------------------------------------------------------
    */

    $("body").delegate("#form-is-parsial", "click", function(){
    	if($(this).is(":checked")){
    		$("#form-parsial").removeClass("d-none"); 
    	}else{
    		$("#form-parsial").addClass("d-none"); 
    	}
    });


	/*
    |--------------------------------------------------------------------------
    | Form submit booking 
    |--------------------------------------------------------------------------
    */

    $(document).on("submit", "#form-booking", function(e){
    	e.preventDefault();
    	let url;
    	if(aksi == "add"){
    		url = '{{ url('pembelian/skpp/store') }}';
    	} else {
    		url = '{{ url('pembelian/skpp/update') }}/'+'{{ $id }}';
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
    			if (resp.status == "error_validate"){
    				for (var i = 0; i < resp.message.length; i++) {
    					toastr.error(resp.message[i],{ "closeButton": true });
    				}  
    			} else if (resp.status == "error"){
    				toastr.error(resp.message, { "closeButton": true }); 
    			} else { 
    				$("#modal-form-booking").modal("hide");
    				location.reload();
    				toastr.success(resp.message, { "closeButton": true });   
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
 	| Edit data
 	|--------------------------------------------------------------------------
 	*/

 	function showEditSkpp(url){
 		aksi = "apdate"; 

 		$.ajax({
 			url : url,
 			type : 'GET', 
 			dataType : "json", 
 			beforeSend: function(resp){
 				loader(".modal-content", true);
 				$("#title-modal-form-booking").html("Form edit SKPP");
 				$("#modal-form-booking").modal("show");
 			},
 			success : function(resp)
 			{ 
 				$("input[name=id]").val(resp.id_skpp); 
 				$("input[name=no_skpp]").val(resp.no_skpp);
 				$("input[name=total_pembayaran]").val(resp.total_pembayaran);
 				$("input[name=terakhir_pembayaran]").val(resp.terakhir_pembayaran.split("-").reverse().join("/"));
 				loader(".modal-content", false);
 			},
 			error : function (jqXHR, exception) {
 				loader(".modal-content", false);
 				errorHandling(jqXHR.status, exception); 
 			}
 		});
 	}


	/*
 	|--------------------------------------------------------------------------
 	| Detail data
 	|--------------------------------------------------------------------------
 	*/


 	$("body").delegate(".detail", "click", function(e){
 		e.preventDefault();
 		aksi = "apdate";
 		let url = $(this).attr("url"); 

 		$.ajax({
 			url : url,
 			type : 'GET', 
 			dataType : "json", 
 			beforeSend: function(resp){
 				loader(".modal-content", true); 
 				$("#modal-detail").modal("show");
 			},
 			success : function(resp)
 			{ 
 				$("#detail-booking").html(resp.html)
 				loader(".modal-content", false);
 			},
 			error : function (jqXHR, exception) {
 				loader(".modal-content", false);
 				errorHandling(jqXHR.status, exception); 
 			}
 		});
 	});


 	/*
 	|--------------------------------------------------------------------------
 	| show modal pembayaran
 	|--------------------------------------------------------------------------
 	*/

 	function showFormPembayaran(id)
 	{
 		let form = $("#form-pembayaran"); 
 		id_booking = id;
 		form.find("input[name=kode_booking]").val("");
 		form.find("input[name=file]").val("");
 		form.find("textarea[name=keterangan]").val("");
 		form.find("input[name=jumlah_pembayaran]").val("");
 		form.find("input[name=id_booking]").val(id);
 		form.find("#form-is-parsial").prop("checked", false);
 		form.find("#form-parsial").addClass("d-none");

 		$("#title-modal-form-pembayaran").html("Form tambah pembayaran");
 		$("#modal-form-pembayaran").modal("show");

 		$.ajax({
 			url : '{{ url('pembelian/skpp/sisa_pembayaran') }}/'+id,
 			type : 'GET',
 			dataType : 'json',
 			beforeSend : function(){

 			},
 			success : function(resp){
 				$("#form-parsial").find("input[name=total_pembayaran]").val(formatNumber(resp, 2));
 			},
 			error : function(){
 				errorHandling(jqXHR.status, exception);
 				loader(".modal-content", false);
 			}
 		}); 
 	}
 	

	/*
    |--------------------------------------------------------------------------
    | Form submit pembayaran 
    |--------------------------------------------------------------------------
    */

    $(document).on("submit", "#form-pembayaran", function(e){
    	e.preventDefault();

    	const validate = false;

    	if($("#form-is-parsial").is(":checked")){
    		var jumlah_pembayaran = $("input[name=jumlah_pembayaran]").val();
    		var piutang = $("#form-parsial").find("input[name=total_pembayaran]").val();

    		if(convertNumeric(piutang) <= convertNumeric(jumlah_pembayaran)){
    			alert('Jumlah pembayaran harus lebih kecil dari total yang harus dibayar');
    			validate = true;
    			return false;
    		} 
    	} 

    	if(validate === false && id_booking !== null){
    		$.ajax({
    			url : '{{ url('pembelian/pembayaran/store') }}/'+id_booking,
    			method : "POST",
    			data : new FormData(this),
    			contentType : false,
    			processData : false,
    			dataType : "json",
    			beforeSend : function(){
    				loader(".modal-content", true);
    			},
    			success : function(resp){
    				if (resp.status == "error_validate"){
    					for (var i = 0; i < resp.message.length; i++) {
    						toastr.error(resp.message[i],{ "closeButton": true });
    					}  
    				} else if (resp.status == "error"){
    					toastr.error(resp.message, { "closeButton": true }); 
    				} else {
    					$("input[name=total_pembayaran]").val(formatNumber(resp.data,2));
    					$("#modal-form-pembayaran").modal("hide");
                        refresh_table("#tabel-pembayaran");
    					toastr.success(resp.message, { "closeButton": true });  	
    				}

    				loader(".modal-content", false);
    			},
    			error : function(jqXHR, exception){
    				errorHandling(jqXHR.status, exception);
    				loader(".modal-content", false);
    			}
    		});
    	}
    });


	/*
	|--------------------------------------------------------------------------
	| hapus data pembayaran
	|--------------------------------------------------------------------------
	*/

	$("body").delegate(".hapus-pembayaran", "click", function(e){
		e.preventDefault();  
		$("#form-hapus").attr("action", $(this).attr("url")); 
		$("#modal-konfirmasi-hapus").modal("show");
		hapus_pembayaran = true;
		jumlah_bayar = $(this).attr("jumlah_bayar");
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
					refresh_table("#tabel-booking");
					refresh_table("#tabel-pengajuan-so");
					$("#layout-table-pembayaran").html(resp.html); 
					$("#modal-konfirmasi-hapus").modal("hide");    
					$("#modal-detail-pengajuan-so").modal("hide");

					if(hapus_pembayaran == true){
						let total_pembayaran = $("#form-parsial").find("input[name=total_pembayaran]").val();
						let jumlah = formatNumber(parseFloat(jumlah_bayar) + convertNumeric(total_pembayaran),2);
						$("#form-parsial").find("input[name=total_pembayaran]").val(jumlah);
					}

				} else {
					toastr.error(resp.message, { "closeButton": true });
				}

				hapus_pembayaran = false;
				loader(".modal-content", false);
			},
			error : function (jqXHR, exception) {
				loader(".modal-content", false);
				errorHandling(jqXHR.status, exception); 
			}
		});
	});

    /*
    |--------------------------------------------------------------------------
    | detail pembayaran
    |--------------------------------------------------------------------------
    */

    $("body").delegate(".detail-pembayaran", "click", function(e){
        e.preventDefault(); 

        let url = $(this).attr("attachment");
        $("#view-file-lampiran").attr("data", url+"#view=FitH");
        $("#modal-show-pembayaran").modal("show");
    });

</script>

@if(isset($_GET["url"]) && $_GET["url"] != "")
<script type="text/javascript">
    detail_pengajuan_so('{{ Helper::decodex($_GET["url"]) }}');
</script>
@endif
@endsection
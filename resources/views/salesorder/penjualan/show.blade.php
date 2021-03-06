@extends('layout.index')

@section('title', 'SUM - Sales Order')

@section('css')
<link rel="stylesheet" type="text/css" href="{{asset('vendor/datatables/dataTables.bootstrap4.min.css')}}"> 
@endsection

@section('content')
@include('salesorder.penjualan.header_salesorder')
@include('salesorder.penjualan.modal_update_status') 
@include('layout.modal_email') 
@if($info["so"]->is_sementara != 1)
	@include('salesorder.penjualan.modal_ganti_supir')
@endif

<div class="container-fluid mb-4 mt-4">
	<div class="row">
		<div class="col-md-12 d-flex justify-content-between">
			<h6 class="m-0 font-weight-bold text-primary"><i class="fa fa-truck-moving"></i> Detail Sales Order</h6>
			<a href="{{ url("penjualan/salesorder/index/".Helper::encodex($info["so"]->id_skpp)) }}" class="text-muted"><i class="fa fa-arrow-left"></i> Kembali</a>   
		</div>  
	</div>  
	@if($info["so"]->is_sementara == 1)
		<div class="alert alert-warning mt-3">
	    	<h4 class="alert-heading"><i class="fa fa-exclamation-circle"></i> Warning</h4> 
		    Sales order masih bersifat sementara!
	    </div>
	@endif
	<div class="card mt-3"> 
		<div class="card-header bg-white d-flex justify-content-between align-items-center">
			<div>
				<a href="{{ url('penjualan/salesorder/edit/'.$id) }}"  class="btn btn-primary"><i class="fa fa-edit"></i> Edit</a>
				<a href="{{ url('penjualan/salesorder/surat_so/'.$id) }}" target="_blank" class="btn btn-warning"><i class="fa fa-download"></i> Surat SO</a>

				@if($info["so"]->is_sementara != 1)
				<button class="btn btn-warning" onclick="modal_ganti_supir()"><i class="fa fa-sync"></i> Ganti supir</button>

				<div class="btn-group" role="group" aria-label="Basic example">
				  	<button class="btn btn-warning action" {{ $info["so"]->Status->id_status == 7 ? 'disabled' : '' }} onclick="update_status('Hold', '{{ Helper::encodex(7) }}')"><i class="fas fa-box"></i> Hold</button>
					<button class="btn btn-warning action" {{ $info["so"]->Status->id_status == 6 ? 'disabled' : '' }} onclick="update_status('On Process', '{{ Helper::encodex(6) }}')"><i class="fas fa-truck-moving"></i> On process</button>
					<button class="btn btn-warning action" {{ $info["so"]->Status->id_status == 5 ? 'disabled' : '' }} onclick="update_status('Delivered', '{{ Helper::encodex(5) }}')"><i class="fas fa-truck-loading"></i> Delivered</button>
				</div> 
				@endif

				<button class="btn btn-warning"onclick="show_form_email('dokumen Sales Order', '{{ url('penjualan/salesorder/send_email/'.$id) }}')"><i class="fas fa-paper-plane"></i> Kirim email ({{ isset($info["riwayat_email"]) ? $info["riwayat_email"]->jumlah : '0' }})
				</button>  
			</div>
			<div>
				@if($info["so"]->is_sementara != 1)
					@if($info["so"]->Status->id_status == 7)
						@php
							$color = 'badge-secondary';
						@endphp
					@elseif($info["so"]->Status->id_status == 6) 
						@php
							$color = 'badge-warning';
						@endphp
					@elseif($info["so"]->Status->id_status == 5)
						@php
							$color = 'badge-success';
						@endphp
					@else
						@php
							$color = 'badge-danger';
						@endphp
					@endif 
					<span class="badge {{ $color }} font-15">{{ $info["so"]->Status->status }}</span>
				@endif
			</div>
		</div> 
		
		<div class="card-body">
			<div class="row">
				<div class="col-md-12">
					<table class="table table-sm table-borderless">
						<tbody>
							<tr>
								<th width="20%">Nomor SKPP</th>
								<th width="1%">:</th>
								<td> {{ $info["so"]->SKPP->no_skpp }}</td>
							</tr> 
							<tr>
			                    <th>Tanggal</th>
			                    <th>:</th>
			                    <td>{{ $info["so"]->tanggal == null ? '-' : Helper::dateFormat($info["so"]->tanggal, true, 'd/m/Y') }}</td>
			                </tr>
							<tr>
								<th>Nomor SO</th>
								<th>:</th>
								<td>{{ $info["so"]->no_so }}</td>
							</tr>
							<tr>
								<th>Nomor SO pengambilan</th>
								<th>:</th>
								<td>{{ $info["so"]->no_so_pengambilan == null ? '-' : $info["so"]->no_so_pengambilan }}</td>
							</tr> 
							<tr>
								<th>Alat angkut</th>
								<th>:</th>
								<td>{{ $info["so"]->is_sementara == 1 ? '-' : $info["so"]->SupirAktif[0]->Supir->kendaraan }}</td>
							</tr>
							<tr>
								<th>Tujuan</th>
								<th>:</th>
								<td>{{ $info["so"]->is_sementara == 1 ? '-' : $info["so"]->tujuan }}</td>
							</tr>
							<tr>
								<th>Nama supir</th>
								<th>:</th>
								<td id="nama-supir">{{ $info["so"]->is_sementara == 1 ? '-' : ($info["so"]->SupirAktif[0]->Supir->nama == null ? "-" : $info["so"]->SupirAktif[0]->Supir->nama) }}</td>
							</tr>
							<tr>
								<th>No truck</th>
								<th>:</th>
								<td>{{ $info["so"]->is_sementara == 1 ? '-' : ($info["so"]->SupirAktif[0]->Supir->plat_nomor == null ? "-" : $info["so"]->SupirAktif[0]->Supir->plat_nomor) }}</td>
							</tr>
							<tr>
								<th>No HP</th>
								<th>:</th>
								<td>{{ $info["so"]->is_sementara == 1 ? '-' : ($info["so"]->SupirAktif[0]->Supir->no_telepon == null ? "-" : $info["so"]->SupirAktif[0]->Supir->no_telepon) }}</td>
							</tr> 
							<tr>
								<th>Lampiran</th>
								<th>:</th>
								<td>
									@if(count($info["so"]->Lampiran) > 0) 
										@foreach($info["so"]->Lampiran as $lampiran)  
										  	<span class="badge rounded-pill border custom-pill"> 
									  			{{ $lampiran->nama }} . {{ Helper::getExtensionFromString($lampiran->file) }} 
									  			@if(Helper::getExtensionFromString($lampiran->file) == "PDF")
									  			<a href="javascript:void(0)" onclick="view_lampiran('{{ $lampiran->nama }}','{{ asset('lampiran/'.$lampiran->file) }}')">Lihat dokumen</a>
									  			@else
									  			<a href="{{ asset('lampiran/'.$lampiran->file) }}" download>Download</a>
									  			@endif
			    							</span>
										@endforeach 
									@else
									-
									@endif
								</td>
							</tr> 
						</tbody>
					</table>
				</div> 
			</div>

			<table class="table table-bordered">
				<thead>
					<tr>
						<th width="1px">No</th>
						<th>Produk</th>
						<th>Spesifikasi</th>
						<th width="150px">Kuantitas</th> 
						<th>Incoterm</th>
						<th>Dokumen</th> 
					</tr>
				</thead>	
				<tbody id="tbody-po"> 
					@php
					$total = 0;
					@endphp
					@foreach($info["sopo"] as $sopo)

					@php
					$total += $sopo->kuantitas; 
					@endphp

					<tr>
						<td>{{ $loop->iteration }}</td>
						<td>{{ $sopo->Barang->Produk->nama }}</td>
						<td>{{ $sopo->Barang->Produk->spesifikasi }}</td>
						<td>{{ Helper::comma($sopo->kuantitas) }} MT</td> 
						<td>{{ $sopo->Barang->incoterm }}</td>
						<td>{{ substr($sopo->SO->SKPP->no_skpp, 0, 4) }}</td> 
					</tr>
					@endforeach
				</tbody>
				<tfoot>
					<tr>
						<td colspan="3" align="right"><b>TOTAL</b></td> 
						<td> <span id="total-kuantitas">{{ Helper::comma($total) }}</span> MT</td>
						<td class="border-none"></td>
						<td class="border-none"></td>
					</tr>
				</tfoot>			
			</table>
		</div> 
	</div>

	<br>
	<div class="row mt-3">
		<div class="col-md-12 d-flex justify-content-between">
			<h6 class="m-0 font-weight-bold text-primary"><i class="fa fa-truck-moving"></i> Riwayat supir</h6> 
		</div>  
	</div>

	<div class="card mt-3"> 
		<div class="card-body">
			<div class="table-responsive"> 
				<table class="table table-bordered" id="tabel-riwayat-supir" width="100%">
					<thead>
						<tr> 
							<th>Supir</th> 
							<th>Plat nomor</th>
							<th>Alat angkut</th>
							<th>Keterangan</th>
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

    var data_table = [ 
    {data: 'supir',       name: 'supir', orderable: false}, 
    {data: 'plat_nomor',  name: 'plat_nomor'},  
    {data: 'kendaraan',   name: 'kendaraan'}, 
    {data: 'keterangan',  name: 'keterangan'},
    {data: 'created_by',  name: 'created_by'}, 
    {data: 'created_at',  name: 'created_at'}, 
    {data: 'action',      name: 'action', orderable: false},
    ];
    
    var table_riwayat_supir = table('#tabel-riwayat-supir', '{{url('supirso/riwayat_supir')}}/'+'{{$id}}', data_table);

	/*
	|--------------------------------------------------------------------------
	| switch supir
	|--------------------------------------------------------------------------
	*/

	$(document).on("submit", "#form-ganti-supir", function(e){
		e.preventDefault();

		var nama_supir = $("select[name=supir]").find(":selected").attr("supir");

		$.ajax({
			url : '{{ url('supirso/switch_supir') }}/'+'{{$id}}',
			type : 'POST',
			data : new FormData(this),
			contentType : false,
			processData : false,
			dataType : 'json',
			beforeSend : function(){
				loader(".modal-content", true);
			},
			success : function(resp){
				if (resp.status == 'success') {
					toastr.success(resp.message, { "closeButton": true });    
					refresh_table("#tabel-riwayat-supir");
					$("#modal-ganti-supir").modal("hide");
					$("#nama-supir").html(nama_supir);
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
					refresh_table("#tabel-riwayat-supir");
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


	/*
	|--------------------------------------------------------------------------
	| update status
	|--------------------------------------------------------------------------
	*/

	function update_status(action, id_action){
		$("#title-update").html(action);
		$("#no-so").html('{{$info["so"]->no_so}}');
		$("input[name=status]").val(id_action); 
		$("#modal-konfirmasi-update").modal("show");
	}

	$(document).on("submit", "#form-update-status", function(e){
		e.preventDefault();

		id = ['{{ $id }}']; 
		 

		$.ajax({
	        url : '{{ url('penjualan/salesorder/update_status') }}',
	        type : 'POST',
	        data : { _token: "{{ csrf_token() }}", status : $("input[name=status]").val(), id : id }, 
			dataType : "json", 
			beforeSend: function(resp){
				loader(".modal-content", true);
			},
	        success : function(resp)
	        { 
	            if (resp.status == 'success') {
	                toastr.success(resp.message, { "closeButton": true });     
	                location.reload();
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

</script>

@endsection
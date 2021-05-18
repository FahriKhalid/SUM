@extends('layout.index')
@section('title', 'SUM - Create SKPP')
@section('content')

	<div class="container-fluid mt-4 mb-4">
	    <div class="row">
	        <div class="col-md-12 d-flex justify-content-between">
	        	<h6 class="m-0 font-weight-bold text-primary"><i class="fa fa-plus-circle"></i> FORM TAMBAH SALES ORDER </h6>
	            <a href="{{ url("penjualan/salesorder/index/".$id) }}" class="text-muted"><i class="fa fa-arrow-left"></i> Kembali</a>   
	        </div>  
	    </div> 
	    {{-- @if(PembayaranService::isBayar(Helper::decodex($id)))
	    <div class="alert alert-warning mt-3">
	    	<h4 class="alert-heading"><i class="fa fa-exclamation-circle"></i> Warning</h4>
	        Pembayaran belum lunas. Tidak dapat menambahkan data sales order!
	    </div>
	    @endif --}}
		<div class="card mt-3">  
			<form id="form-so" enctype="multipart/form-data"> 
				<div class="card-body" id="layout-parent"> 

					<div class="form-row ">
						<div class="form-group col-md-4">
	                        <label>Nomor SKPP <span class="text-danger">*</span></label>
	                        <div class="form-group"> 
	                            <input class="form-control" disabled value="{{ $info["skpp"]->no_skpp }}" placeholder="Wajib di isi"> 
	                        </div>
	                    </div> 
						<div class="form-group col-md-4">
	                        <label>Nomor Sales Order <span class="text-danger">*</span></label>
	                        <div class="form-group"> 
	                            <input class="form-control" name="nomor_so" value="{{ $info["no_so"] }}" placeholder="Wajib di isi"> 
	                        </div>
	                    </div>  
	                    <div class="form-group col-md-4">
	                        <label>Nomor Sales Order Pengambilan <span class="text-danger">*</span></label>
	                        <div class="form-group"> 
	                            <input class="form-control" name="nomor_so_pengambilan" placeholder="Wajib di isi"> 
	                        </div>
	                    </div>  
					</div>

					
					<div class="form-row">
		                <div class="form-group col-md-6">
		                    <label>Penanggung jawab <span class="text-danger">*</span></label>
		                    <div class="form-group"> 
		                        <select class="form-control select2" name="supir">
		                        	@foreach($info["supir"] as $supir)
		                        		<option value="{{ $supir->id_supir }}">{{ $supir->nama }} - {{ $supir->plat_nomor }} - {{ $supir->kendaraan }}</option>
		                        	@endforeach
		                        </select>
		                    </div> 
		                </div> 
	                    <div class="form-group col-md-6">
		                    <label>Tujuan <span class="text-danger">*</span></label>
	                        <div class="form-group"> 
	                            <input class="form-control" name="tujuan" placeholder="Wajib di isi"> 
	                        </div>
		                </div>  
					</div>

					<div class="table-responsive">
						<table class="table table-bordered">
							<thead>
								<tr>
									<th width="1px">No</th>
									<th>Produk</th>
									<th>Spesifikasi</th>
									<th width="300px">Kuantitas <i class="text-danger">*</i></th> 
									<th>Incoterm</th>
									<th>Dokumen</th>
									{{-- <th width="1px">#</th> --}}
								</tr>
							</thead>	
							<tbody id="tbody-po"> 
								@php
								$total = 0;
								@endphp
								@foreach($info["po"] as $barang)

								@php($total += \App\Services\SoService::sisaKuantitasPO($barang->id_barang))

								<tr class="{{ \App\Services\SoService::sisaKuantitasPO($barang->id_barang) == 0 ? 'bg-red' : '' }}">
									<td>{{ $loop->iteration }}.</td>
									<td>{{ $barang->Produk->nama }}</td>
									<td>{{ $barang->Produk->spesifikasi }}</td>
									<td class="p-1">
										<input type="hidden" name="id_po[]" value="{{ Helper::encodex($barang->id_barang) }}">
										<div class="d-flex">
											<div class="input-group">
												<input type="text" disabled class="form-control sisa_kuantitas float" value="{{ \App\Services\SoService::sisaKuantitasPO($barang->id_barang) }}">
												<div class="input-group-append">
													<span class="input-group-text">MT</span>
												</div>
											</div>

											<div class="ml-2 mr-2 align-self-center">
												<i class="fa fa-arrow-right"></i>
											</div>

											<div class="input-group">
												<input type="text" name="kuantitas[]" autocomplete="off" class="form-control float" value="{{ \App\Services\SoService::sisaKuantitasPO($barang->id_barang) }}">
												<div class="input-group-append">
													<span class="input-group-text">MT</span>
												</div>
											</div>
										</div>
										
									</td> 
									<td>{{ $barang->incoterm }}</td>
									<td>{{ $info["skpp"]->no_dokumen }}</td> 
								</tr> 
								@endforeach
							</tbody>
							<tfoot>
								<tr>
									<td colspan="3" align="right"><b>TOTAL</b></td> 
									<td class="align-right"> <span id="total-kuantitas">{{ $total }}</span> MT</td>
									<td class="border-none"></td>
									<td class="border-none"></td>
								</tr>
							</tfoot>			
						</table>
					</div>

					<div class="form-group mt-3">
	                    <label>Status <span class="text-danger">*</span></label>
                    	{{-- <div class="form-group">
							@foreach($info["status"] as $status)
	                    		<div class="custom-control custom-radio custom-control-inline">
								  	<input type="radio" value="{{ $status->id_status }}" id="{{ $status->status }}" {{ $loop->iteration == 1 ? 'checked' : '' }} name="status" class="custom-control-input">
								  	<label class="custom-control-label" for="{{ $status->status }}">{{ $status->status }}</label>
								</div>
                    		@endforeach
                        </div> --}} 


                        <div class="row">
                        	<div class="col-md-6">
                        		<div class="btn-group btn-block btn-group-toggle" data-toggle="buttons">
                        			@foreach($info["status"] as $status) 
										<label class="btn btn-outline-primary mr-2 active" style="border-radius: 10px">
										    <input type="radio" value="{{ $status->id_status }}" id="{{ $status->status }}" {{ $loop->iteration == 1 ? 'checked' : '' }} name="status"> 
										    @if(strtolower($status->status) == 'hold')
										    	<i class="fas fz-20 fa-box"></i>
										    @elseif(strtolower($status->status) == 'on process')
										    	<i class="fas fz-20 fa-truck-moving"></i>
										    @else
										    	<i class="fas fz-20 fa-truck-loading"></i>
										    @endif
										    <div>{{ $status->status }}</div>
										 </label>
		                    		@endforeach 
								</div>
                        	</div>
                        </div>
	                </div>  

	                @include('layout.form_tambah_lampiran')
				</div>   

				<div class="card-body border-top d-flex justify-content-between">  
					<div>
						<div class="legend bg-red"></div> Stok Habis
					</div>
					{{-- @if(!PembayaranService::isBayar(Helper::decodex($id)))
					@csrf
					<input type="hidden" name="id_skpp" value="{{ $id }}">
					<button class="btn btn-primary"><i class="fa fa-save"></i> Simpan</button> 
					@endif --}}
					@csrf
					<input type="hidden" name="id_skpp" value="{{ $id }}">
					<button class="btn btn-primary"><i class="fa fa-save"></i> Simpan</button> 
				</div>
			</form>
		</div>
	</div>

@endsection

@section('footer')
<script type="text/javascript" src="{{ asset('js/lampiran.js') }}"></script>
<script type="text/javascript"> 
	$(document).on("submit", "#form-so", function(e){
		e.preventDefault();

		$.ajax({
			url : '{{ url('penjualan/salesorder/store') }}/'+ '{{ $id }}',
			type : 'POST',
			data : new FormData(this),
			processData : false,
			contentType : false,
			dataType : 'json',
			beforeSend: function(){
				loader(".card", true);
			},
			success : function(resp){
				if(resp.status == 'error'){
					toastr.error(resp.message,{ "closeButton": true });
				} else {
					toastr.success(resp.message, { "closeButton": true });  
               		location.href = '{{ url('penjualan/salesorder/index') }}/' + '{{ $id }}';
				}

				loader(".card", false);
			},
			error : function(){
				loader(".card", false);
			}
		})
	});

	$("body").delegate(".remove", "click", function(){

		const jumlah_baris = $("#tbody-po").find("tr").length;
		if(jumlah_baris < 2){
			$("#tbody-po").find("button").prop("disabled", true);
		} else {
			$("#tbody-po").find("button").prop("disabled", false);
			$(this).closest("tr").remove();
			totalKuantitas();
		} 
	});

	function totalKuantitas()
	{	
		var jumlah = 0;
		$("#tbody-po").find("tr").each(function(){
			jumlah += parseFloat($(this).find("input[name='kuantitas[]']").val());
		}); 
		$("#total-kuantitas").html(jumlah);
	}


	$("body").delegate("input[name='kuantitas[]']", "keyup", function(){
		totalKuantitas();
 		var jumlah = $(this).val();
 		var sisa = parseInt($(this).closest("td").find(".sisa_kuantitas").val());

 		if(jumlah > sisa){
 			$(this).val(sisa);
 			totalKuantitas();
 			alert("Jumlah kuantitas tidak boleh lebih dari sisa kuantitas");
 		}
 		
	});
</script>

@endsection
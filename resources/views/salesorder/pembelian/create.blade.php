@extends('layout.index')
@section('title', 'SUM - Buat Sales Order Pembelian')
@section('content')

	<div class="container-fluid mt-4 mb-4">
	    <div class="row">
	        <div class="col-md-12 d-flex justify-content-between">
	        	<h6 class="m-0 font-weight-bold text-primary"><i class="fa fa-plus-circle"></i> FORM TAMBAH SALES ORDER </h6>
	            <a href="{{ url("pembelian/salesorder/show/".$id) }}" class="text-muted"><i class="fa fa-arrow-left"></i> Kembali</a>   
	        </div>  
	    </div> 
{{-- 	    @if(PembayaranService::isLunas("penjualan", Helper::decodex($id)))
	    <div class="alert alert-warning mt-3">
	    	<h4 class="alert-heading"><i class="fa fa-exclamation-circle"></i> Warning</h4>
	        Pembayaran belum lunas. Tidak dapat menambahkan data sales order!
	    </div>
	    @endif --}}

	{{--     @if($info["skpp"]->total_pembayaran == $info["piutang"])
		<div class="alert alert-warning mt-3">
	    	<h4 class="alert-heading"><i class="fa fa-exclamation-circle"></i> Warning</h4>
	        Lakukan pembayarn terlebih dahulu, untuk menambahkan sales order.
	    </div>
	    @endif --}}

		<div class="card mt-3"> 
			<form id="form-so" enctype="multipart/form-data"> 
				@csrf
				<div class="card-body" id="layout-parent"> 
					<input type="hidden" name="id_pre_order" value="{{ $id }}">
					<div class="form-row ">
						<div class="form-group col-md-6"> 
	                        <label>Tanggal <span class="text-danger">*</span></label>
	                        <div class="form-group"> 
	                            <input type="text" class="form-control datepicker" value="{{ date('d/m/Y') }}" name="tanggal" placeholder="Wajib di isi"> 
	                        </div>
	                    </div>
						<div class="form-group col-md-6">
	                        <label>Nomor SKPP</label>
	                        <div class="form-group"> 
	                            <input class="form-control" placeholder="Optional"> 
	                        </div>
	                    </div> 
						<div class="form-group col-md-6">
	                        <label>Nomor Sales Order <span class="text-danger">*</span></label>
	                        <div class="form-group"> 
	                            <input class="form-control" name="nomor_so" placeholder="Wajib di isi"> 
	                        </div>
	                    </div> 
	                    <div class="form-group col-md-6">
	                        <label>File <span class="text-danger">*</span></label>
	                        <div class="form-group"> 
	                            <input type="file" accept="application/pdf" class="form-control" name="file" placeholder="Wajib di isi"> 
	                        </div>
	                    </div>  
					</div> 

					<div class="form-group">
						<label>Menggunakan Pengajuan Sales Order <span class="text-danger">*</span></label>
                    	<div class="form-group">
							<div class="custom-control custom-radio custom-control-inline">
							  	<input type="radio" value="1" id="YA" name="is_pengajuan_so" class="custom-control-input">
							  	<label class="custom-control-label" for="YA">YA</label>
							</div>
							<div class="custom-control custom-radio custom-control-inline">
							  	<input type="radio" value="0" id="TIDAK" checked name="is_pengajuan_so" class="custom-control-input">
							  	<label class="custom-control-label" for="TIDAK">TIDAK</label>
							</div>
                        </div> 
					</div> 

				    <div id="form-pengajuan-so" class="collapse">
				    	<div class="form-group">
	                        <label>Nomor Pengajuan Sales Order <span class="text-danger">*</span></label>
	                        <div class="form-group"> 
	                            <select class="form-control select2" name="id_pengajuan_so">
	                            	<option value="">-- Pilih Nomor Pengajuan Sales Order --</option>
	                            	@foreach($info["pengajuan_so"] as $pengajuan_so)
	                            		<option value="{{ Helper::encodex($pengajuan_so->id_pengajuan_so) }}">{{ $pengajuan_so->no_pengajuan_so }}</option>
	                            	@endforeach
	                            </select>
	                        </div>
	                    </div> 

	                    <div id="table-pengajuan-so" class="collapse">
	                    	
	                    </div>
				    </div>

					<div id="table-barang" class="table-responsive">
						<table class="table table-bordered">
							<thead>
								<tr>
									<th width="1px">No</th>
									<th>Produk</th>
									<th>Spesifikasi</th>
									<th width="300px">Kuantitas</th> 
									<th>Incoterm</th>
									{{-- <th>Dokumen</th> --}}
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
										<input type="hidden" name="id_produk[]" value="{{ Helper::encodex($barang->Produk->id_produk) }}">
										<input type="hidden" name="id_barang[]" value="{{ Helper::encodex($barang->id_barang) }}">
										<div class="d-flex">
											<div class="input-group">
												<input type="text" disabled class="form-control float sisa_kuantitas" value="{{ Helper::currency(\App\Services\SoService::sisaKuantitasPO($barang->id_barang)) }}">
												<div class="input-group-append">
													<span class="input-group-text">MT</span>
												</div>
											</div>

											<div class="ml-2 mr-2 align-self-center">
												<i class="fa fa-arrow-right"></i>
											</div>

											<div class="input-group">
												<input type="text" name="kuantitas[]" autocomplete="off" class="form-control float" value="{{ Helper::currency(\App\Services\SoService::sisaKuantitasPO($barang->id_barang)) }}">
												<div class="input-group-append">
													<span class="input-group-text">MT</span>
												</div>
											</div>
										</div>
										
									</td> 
									<td>{{ $barang->incoterm }}</td>
									{{-- <td>x</td>  --}}
								</tr> 
								@endforeach
							</tbody>
							<tfoot>
								<tr>
									<td colspan="3" align="right"><b>TOTAL</b></td> 
									<td class="align-right"> <span id="total-kuantitas">{{ Helper::comma($total) }}</span> MT</td>
									<td class="border-none"></td>
									{{-- <td class="border-none"></td> --}}
								</tr>
							</tfoot>			
						</table>
					</div>  
					
					{{-- <div class="form-group mt-3">
	                    <label>Status <span class="text-danger">*</span></label>
                    	<div class="form-group">
							@foreach($info["status"] as $status)
	                    		<div class="custom-control custom-radio custom-control-inline">
								  	<input type="radio" set="{{ $status->status }}" value="{{ $status->id_status }}" id="{{ $status->status }}" {{ $loop->iteration == 1 ? 'checked' : '' }} name="status" class="custom-control-input">
								  	<label class="custom-control-label" for="{{ $status->status }}">{{ $status->status }}</label>
								</div>
                    		@endforeach
                        </div> 
	                </div>  --}}
				</div>   

				<div class="card-body border-top d-flex justify-content-between">    
					<button class="btn btn-primary"><i class="fa fa-save"></i> Simpan</button>  
				</div>
			</form>
		</div>
	</div>

@endsection

@section('footer')
 
<script type="text/javascript"> 
	$(document).on("submit", "#form-so", function(e){
		e.preventDefault();
		
		var data = new FormData(this);

		$.ajax({
			url : '{{ url('pembelian/salesorder/store') }}/'+ '{{ $id }}',
			type : 'POST',
			data : data,
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
					Swal.fire('Berhasil',resp.message,'success');
               		location.href = '{{ url('pembelian/salesorder/show') }}/' + '{{ $id }}';
				}

				loader(".card", false);
			},
			error : function(jqXHR, exception){
				errorHandling(jqXHR.status, exception);
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
			jumlah += convertNumeric($(this).find("input[name='kuantitas[]']").val());
		}); 
		$("#total-kuantitas").html(jumlah);
	}


	$("body").delegate("input[name='kuantitas[]']", "keyup", function(){
		totalKuantitas();
 		var jumlah = convertNumeric($(this).val());
 		var sisa = convertNumeric($(this).closest("td").find(".sisa_kuantitas").val());

 		if(jumlah > sisa){
 			$(this).val(convertDecimal(sisa));
 			totalKuantitas();
 			alert("Jumlah kuantitas tidak boleh lebih dari sisa kuantitas");
 		}
	});


	$("body").delegate("input:radio[name=is_pengajuan_so]", "change", function(){
		if(this.checked && this.value == "1"){
            $("#form-pengajuan-so").collapse("show");
            $("#table-barang").addClass("d-none");
        }else{ 
            $("#form-pengajuan-so").collapse("hide");
            $("#table-barang").removeClass("d-none");
        }
	});


	$("body").delegate("select[name=id_pengajuan_so]", "change", function(){
		var id = $(this).val();

		if(id == ""){
			$("#table-pengajuan-so").html("").collapse("hide");
		} else {
			$.ajax({
				url : '{{ url('pembelian/pengajuan_so/table_view') }}/'+id,
				type : 'GET',
				dataType : 'json',
				beforeSend : function()
				{
					loader(".card", true);
				},
				success : function(resp)
				{
					$("#table-pengajuan-so").html(resp).collapse("show");
					loader(".card", false);
				},
				error : function(jqXHR, exception){
					errorHandling(jqXHR.status, exception);
	                loader(".card", false);
				}
			});
		}
	});
</script>

@endsection
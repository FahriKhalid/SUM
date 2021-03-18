@extends('layout.index')

@section('title', 'SUM - Edit Surat Kuasa')
 
@section('content')

	<div class="container-fluid mt-4 mb-4">

	    <div class="row">
	        <div class="col-md-12 d-flex justify-content-between">
	        	<h6 class="m-0 font-weight-bold text-primary"><i class="fa fa-plus-circle"></i> Form tambah surat kuasa </h6>
	            <a href="{{ url("surat_kuasa/index/".Helper::encodex($info["sk"]->SO->id_so)) }}" class="text-muted"><i class="fa fa-arrow-left"></i> Kembali</a>   
	        </div>  
	    </div> 

		<div class="card mt-3"> 
			<form id="form-so" enctype="multipart/form-data">
				@csrf
				<input type="hidden" name="id_skso" value="{{ $id }}">
				<div class="card-body" id="layout-parent"> 

					<div class="form-row ">
	                    <div class="form-group col-md-6">
		                    <label>Nomor Sales Order <span class="text-danger">*</span></label>
	                        <div class="form-group"> 
	                            <input class="form-control" disabled value="{{ $info["sk"]->SO->no_so }}" placeholder="Wajib di isi"> 
	                        </div>
		                </div> 

						<div class="form-group col-md-6">
	                        <label>Nomor Surat Kuasa <span class="text-danger">*</span></label>
	                        <div class="form-group"> 
	                            <input class="form-control" name="nomor_sk" value="{{ $info["sk"]->no_sk }}" placeholder="Wajib di isi"> 
	                        </div>
	                    </div> 
					</div>

					
					<div class="form-row"> 
		                <div class="form-group col-md-6">
		                    <label>Supir <span class="text-danger">*</span></label>
		                    <div class="form-group"> 
		                        <select class="form-control select2" name="supir">
		                        	@foreach($info["supir"] as $supir)
		                        		<option value="{{ $supir->id_supir }}">{{ $supir->nama }} - {{ $supir->plat_nomor }} - {{ $supir->kendaraan }}</option>
		                        	@endforeach
		                        </select>
		                    </div> 
		                </div> 
	                    <div class="form-group col-md-6">
		                    <label>Gudang <span class="text-danger">*</span></label>
	                        <div class="form-group"> 
	                            <select class="form-control select2" name="gudang">
		                        	@foreach($info["gudang"] as $gudang)
		                        		<option value="{{ $gudang->id_gudang }}">{{ $gudang->nama }}</option>
		                        	@endforeach
		                        </select>
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
									<th width="300px">Kuantitas</th> 
									{{-- <th>Gudang</th>  --}}
									{{-- <th width="1px">#</th> --}}
								</tr>
							</thead>	
							<tbody id="tbody-po"> 
								@php
								$total = 0;
								@endphp
								@foreach($info["sk_so"] as $sk_so)


								@php($total += $sk_so->kuantitas)

								<tr>
									<td>{{ $loop->iteration }}.</td>
									<td>{{ $sk_so->SOPO->Barang->Produk->nama }}</td>
									<td>{{ $sk_so->SOPO->Barang->Produk->spesifikasi }}</td>
									<td class="p-1">
										<input type="hidden" name="id_sk_so[]" value="{{ Helper::encodex($sk_so->id_sk_so) }}">
										<div class="d-flex">
											<div class="input-group">
												<input type="text" disabled class="form-control number sisa_kuantitas" value="{{ SuratKuasaService::sisaKuantitasSOPO($sk_so->id_so_po, $sk_so->id_sk_so) }}">
												<div class="input-group-append">
													<span class="input-group-text">MT</span>
												</div>
											</div>

											<div class="ml-2 mr-2 align-self-center">
												<i class="fa fa-arrow-right"></i>
											</div>

											<div class="input-group">
												<input type="text" name="kuantitas[]" autocomplete="off" class="form-control number" value="{{ $sk_so->kuantitas }}">
												<div class="input-group-append">
													<span class="input-group-text">MT</span>
												</div>
											</div>
										</div>
										
									</td>  
								</tr> 
								@endforeach
							</tbody>
							<tfoot>
								<tr>
									<td colspan="3" align="right"><b>TOTAL</b></td> 
									<td class="align-right"> <span id="total-kuantitas">{{ $total }}</span> MT</td> 
								</tr>
							</tfoot>			
						</table>
					</div>

					<div>
						<div class="legend bg-red"></div>
						<div class="text-dark">Stok Habis</div>
					</div>
				</div>  

				<div id="layout-child">
					
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

		$.ajax({
			url : '{{ url('surat_kuasa/update') }}/'+ '{{ $id }}',
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
               		location.href = '{{ url('surat_kuasa/index') }}/' + '{{ Helper::encodex($info["sk"]->SO->id_so) }}';
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
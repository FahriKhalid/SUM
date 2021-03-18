@extends('layout.index')

@section('title', 'SUM - Edit sales order')

@section('content')

	<div class="container-fluid mt-4 mb-4">

	    <div class="row">
	        <div class="col-md-12 d-flex justify-content-between">
	        	<h6 class="m-0 font-weight-bold text-primary"><i class="fa fa-plus-circle"></i> Form edit sales order </h6>
	            <a href="{{ url("penjualan/salesorder/show/".$id) }}" class="text-muted"><i class="fa fa-arrow-left"></i> Kembali</a>
	        </div>  
	    </div> 
	    @if($info["so"]->Status->status == 'Delivered')
	    <div class="alert alert-warning mt-3">
	    	<h4 class="alert-heading"><i class="fa fa-exclamation-circle"></i> Warning</h4>
	    	Status delivered. Sales order tidak dapat di edit!
	    </div>
	    @endif
		<div class="card mt-3"> 
			<form id="form-so" enctype="multipart/form-data">
				@csrf 
				<div class="card-body" id="layout-parent"> 

					<div class="form-row ">
						<div class="form-group col-md-6">
	                        <label>Nomor SKPP <span class="text-danger">*</span></label>
	                        <div class="form-group"> 
	                            <input class="form-control" disabled value="{{ $info["so"]->SKPP->no_skpp }}" placeholder="Wajib di isi"> 
	                        </div>
	                    </div> 
						<div class="form-group col-md-6">
	                        <label>Nomor Sales Order <span class="text-danger">*</span></label>
	                        <div class="form-group"> 
	                            <input class="form-control" name="nomor_so" value="{{ $info["so"]->no_so }}" placeholder="Wajib di isi"> 
	                        </div>
	                    </div> 
	                     
					</div>

					
					<div class="form-row">
		                <div class="form-group col-md-6">
		                    <label>Penanggung jawab <span class="text-danger">*</span></label>
		                    <div class="form-group"> 
								<input type="hidden" value="{{ Helper::encodex($info["so"]->SupirAktif[0]->id_supir_so) }}" name="id_supir_so">
		                        <select class="form-control select2" name="supir">
		                        	@foreach($info["supir"] as $supir)
		                        		<option {{ $info["so"]->SupirAktif[0]->Supir->id_supir == $supir->id_supir ? "selected" : "" }} value="{{ $supir->id_supir }}">{{ $supir->nama }} - {{ $supir->plat_nomor }} - {{ $supir->kendaraan }}</option>
		                        	@endforeach
		                        </select>
		                    </div> 
		                </div> 
	                    <div class="form-group col-md-6">
		                    <label>Tujuan <span class="text-danger">*</span></label>
	                        <div class="form-group"> 
	                            <input class="form-control" name="tujuan" value="{{ $info["so"]->tujuan }}" placeholder="Wajib di isi"> 
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
									<th>Incoterm</th>
									<th>Dokumen</th>
									{{-- <th width="1px">#</th> --}}
								</tr>
							</thead>	
							<tbody id="tbody-po"> 
								@php
								$total = 0;
								@endphp
								@foreach($info["so_po"] as $so_po) 

								@php($total += $so_po->kuantitas)

								<tr>
									<td>{{ $loop->iteration }}.</td>
									<td>{{ $so_po->Barang->Produk->nama }}</td>
									<td>{{ $so_po->Barang->Produk->spesifikasi }}</td>
									<td class="p-1">
										<input type="hidden" name="id_so_po[]" value="{{ Helper::encodex($so_po->id_so_po) }}">
										<div class="d-flex">
											<div class="input-group">
												<input type="text" disabled class="form-control number sisa_kuantitas" value="{{ \App\Services\SoService::sisaKuantitasPO($so_po->id_barang, $so_po->id_so_po) }}">
												<div class="input-group-append">
													<span class="input-group-text">MT</span>
												</div>
											</div>

											<div class="ml-2 mr-2 align-self-center">
												<i class="fa fa-arrow-right"></i>
											</div>

											<div class="input-group">
												<input type="text" name="kuantitas[]" autocomplete="off" class="form-control number" value="{{ $so_po->kuantitas }}">
												<div class="input-group-append">
													<span class="input-group-text">MT</span>
												</div>
											</div>
										</div>
										
									</td> 
									<td>{{ $so_po->Barang->incoterm }}</td>
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
				</div>   

				@if($info["so"]->Status->status != 'Delivered')
			    <div class="card-body border-top d-flex justify-content-between"> 
					<button class="btn btn-primary"><i class="fa fa-save"></i> Simpan</button> 
				</div>
			    @endif
				
			</form>
		</div>
	</div>

@endsection

@section('footer')
 
<script type="text/javascript"> 
	$(document).on("submit", "#form-so", function(e){
		e.preventDefault();

		$.ajax({
			url : '{{ url('penjualan/salesorder/update') }}/'+ '{{ $id }}',
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
               		location.href = '{{ url('penjualan/salesorder/show') }}/' + '{{ $id }}';
				}

				loader(".card", false);
			},
			error : function(){
				loader(".card", false);
			}
		})
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
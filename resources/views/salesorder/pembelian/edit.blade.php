@extends('layout.index')

@section('title', 'SUM - Edit sales order')

@section('content')

	<div class="container-fluid mt-4 mb-4">

	    <div class="row">
	        <div class="col-md-12 d-flex justify-content-between">
	        	<h6 class="m-0 font-weight-bold text-primary"><i class="fa fa-edit"></i> FORM EDIT SALES ORDER </h6>
	            <a href="{{ url("pembelian/salesorder/show/".Helper::encodex($info['so']->SKPP->PreOrder->id_pre_order)) }}" class="text-muted"><i class="fa fa-arrow-left"></i> Kembali</a>
	        </div>  
	    </div>  
	    <div class="alert alert-warning mt-3">
	    	<h4 class="alert-heading"><i class="fa fa-exclamation-circle"></i> Warning</h4>
	    	Tidak dapat melakuakn edit kuantitas pada produk.
	    </div> 
		<div class="card mt-3"> 
			<form id="form-so" enctype="multipart/form-data">
				@csrf 
				<div class="card-body" id="layout-parent"> 

					<div class="form-row ">
						<div class="form-group col-md-6"> 
	                        <label>Tanggal <span class="text-danger">*</span></label>
	                        <div class="form-group"> 
	                            <input type="text" class="form-control datepicker" value="{{ Helper::dateFormat($info["so"]->tanggal, false, 'd/m/Y') }}" name="tanggal" placeholder="Wajib di isi"> 
	                        </div>
	                    </div>
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
	                    <div class="form-group col-md-6">
	                        <label>File <span class="text-danger">*</span></label>
	                        <div class="form-group">  
	                            <div class="input-group"> 
									<input type="file" class="lampiran form-control" name="file" width="200px">
									<div class="input-group-append">
										<a href="{{ asset('file_so/'.$info["so"]->file) }}" target="_blank" class="btn btn-outline-primary">
											<i class="fa fa-search"></i>
										</a>
									</div>
								</div> 
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
									<td>{{ $so_po->kuantitas }}</td> 
									<td>{{ $so_po->Barang->incoterm }}</td>
									<td>{{ $info["skpp"]->no_dokumen }}</td> 
								</tr> 
								@endforeach
							</tbody>
							<tfoot>
								<tr>
									<td colspan="3" align="right"><b>TOTAL</b></td> 
									<td> <span id="total-kuantitas">{{ $total }}</span> MT</td>
									<td class="border-none"></td>
									<td class="border-none"></td>
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
	                </div>   --}}
				</div>   
 
			    <div class="card-body border-top d-flex justify-content-between"> 
			    	<div></div>
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
			url : '{{ url('pembelian/salesorder/update') }}/'+ '{{ $id }}',
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
               		location.href = '{{ url('pembelian/salesorder/show') }}/' + '{{ Helper::encodex($info["skpp"]->PreOrder->id_pre_order) }}';
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
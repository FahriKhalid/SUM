@extends('layout.index')
@section('title', 'SUM - Edit Invoice')
@section('content')
	@php($sub_total = 0)
	<div class="container-fluid mt-4 mb-4">
	    <div class="row">
	        <div class="col-md-12 d-flex justify-content-between">
	        	<h6 class="m-0 font-weight-bold text-primary"><i class="fa fa-plus-circle"></i> Form edit invoice </h6>
	            <a href="{{ url("penjualan/invoice/index/".Helper::encodex($info["invoice"]->id_skpp)) }}" class="text-muted"><i class="fa fa-arrow-left"></i> Kembali</a>   
	        </div>  
	    </div> 
	    @if(PembayaranService::isLunas("penjualan", Helper::decodex($id)))
	    <div class="alert alert-warning mt-3">
	    	<h4 class="alert-heading"><i class="fa fa-exclamation-circle"></i> Warning</h4>
	        Pembayaran belum lunas. Tidak dapat edit invoice!
	    </div>
	    @endif
		<div class="card mt-3"> 
			<form id="form-so" enctype="multipart/form-data"> 
				<div class="card-body" id="layout-parent">  
					<div class="form-row ">
						<div class="form-group col-md-6">
	                        <div class="form-group"> 
	                        	<label>Customer <span class="text-danger">*</span></label>
	                            <input class="form-control" disabled value="{{ $info["invoice"]->SKPP->Customer->perusahaan }}" placeholder="Wajib di isi"> 
	                        </div> 
	                    </div> 
						<div class="form-group col-md-6"> 
	                        <div class="form-group">  
	                        	<label>Alamat <span class="text-danger">*</span></label>
	                            <input class="form-control" disabled value="{{ $info["invoice"]->SKPP->Customer->alamat }}" placeholder="Wajib diisi"> 
	                        </div>
	                    </div> 

						<div class="form-group col-md-6">
	                        <div class="form-group"> 
	                        	<label>Nomor Tagihan <span class="text-danger">*</span></label>
	                            <input class="form-control" name="nomor_tagihan" value="{{ $info["invoice"]->no_tagihan }}" placeholder="Wajib diisi"> 
	                        </div>
	                    </div>

	                    <div class="form-group col-md-6">
	                        <div class="form-group"> 
	                        	<label>Nomor Resi </label> 
	                            <input class="form-control" autocomplete="off" name="nomor_resi" value="{{ $info["invoice"]->no_resi }}" placeholder="Wajib diisi"> 
	                        </div>
	                    </div>
	                    
	                    <div class="form-group col-md-6"> 
	                        <div class="form-group"> 
	                        	<label>Nomor Faktur pajak <span class="text-danger">*</span></label>
	                            <input class="form-control" autocomplete="off" name="nomor_faktur_pajak" value="{{ $info["invoice"]->no_faktur_pajak }}" placeholder="Wajib diisi"> 
	                        </div> 
	                    </div> 
	                    <div class="form-group col-md-6"> 
	                        <div class="form-group"> 
	                        	<label>File Faktur pajak <span class="text-danger">*</span></label>
	                            <input type="file" class="form-control" name="file_faktur_pajak"> 
	                        </div> 
	                    </div> 

	                    <div class="form-group col-md-12"> 
	                        <div class="form-group"> 
	                        	<label>Nomor Sales Order <span class="text-danger">*</span></label> 
	                        	<input type="text" disabled class="form-control" value="{{ $info["invoice"]->SO->no_so }}">
	                        </div>
	                    </div> 
					</div>
					
					<table class="table table-bordered">
						<thead>
							<tr>
								<th width="1px">NO</th>
								<th>PRODUK</th>
								<th>KUANTITAS</th>
								<th>INCOTERM</th>
								<th>HARGA JUAL</th>
								<th>JUMLAH</th>
							</tr>
						</thead>
						<tbody> 
							@php($sub_total = 0)
							@foreach($info["invoice"]->SO->SOPO as $sopo)
							@php($sub_total += floor(($sopo->Barang->harga_jual / 1.1) * $sopo->kuantitas) )
							<tr>
								<td>{{ $loop->iteration }}.</td>
								<td>{{ $sopo->Barang->Produk->nama }}</td>
								<td>{{ $sopo->kuantitas }} MT</td>
								<td>{{ $sopo->Barang->incoterm }}</td>
								<td> 
									<div class="d-flex justify-content-between">
										<div>IDR</div>
										<div>{{ Helper::currency(Helper::toFixed(floor(($sopo->Barang->harga_jual / 1.1)), 2)) }}</div>	
									</div>
								</td>
								<td width="250px">
									<div class="d-flex justify-content-between">
										<div>IDR</div>
										<div>{{ Helper::currency(Helper::toFixed(floor((($sopo->Barang->harga_jual / 1.1) * $sopo->kuantitas)), 2))  }}</div>
									</div>
								</td>
							</tr>
							@endforeach 
							<tr>
								<td colspan="5" align="right"><b>SUB TOTAL</b></td> 
								{{-- <td>
									<div class="d-flex justify-content-between">
										<div>IDR</div>
										<div>{{ Helper::currency(floor($sub_total)) }}</div>
									</div>
									<input type="hidden" class="form-control" value="{{ floor($sub_total) }}" name="sub_total">
								</td> --}}
								<td class="p-1">
									<div class="input-group">
										<div class="input-group-prepend">
											<span class="input-group-text">IDR</span>
										</div> 
										<input type="text" class="form-control numeric" value="{{ Helper::currency($sub_total) }}" name="sub_total">
									</div>
								</td>
							</tr>
							<tr>
								<td colspan="5" align="right"><b>PPN</b></td> 
								<td class="p-1">
									<div class="input-group">
										<div class="input-group-prepend">
											<span class="input-group-text">IDR</span>
										</div> 
										<input type="text" class="form-control numeric" value="{{ Helper::currency(floor($info["invoice"]->ppn)) }}" name="ppn">
									</div>
								</td>
							</tr>
							<tr> 
								<td colspan="5" align="right"><b>TOTAL</b></td> 
								<td class="p-1"> 
									<div class="input-group">
										<div class="input-group-prepend">
											<span class="input-group-text">IDR</span>
										</div> 
										<input type="text" readonly class="form-control numeric" value="{{ Helper::currency($info["invoice"]->total) }}" name="total">
									</div>
								</td>
							</tr>
						</tbody> 
					</table>
					
				</div>   
				<div class="card-body border-top d-flex justify-content-between"> 
					<div>
						<small>
							<span class="text-danger font-italic">
								<div>Note : </div>
								<div>- Extensi file yang diperbolehkan hanya JPG, JPEG, PNG dan PDF.</div>
								<div>- Maksimal ukuran file 2 Mb.</div> 
							</span>
						</small>
					</div>
					@if(!PembayaranService::isLunas("penjualan", Helper::decodex($id)))
					<div>
						@csrf
						<input type="hidden" name="id_skpp" value="{{ $id }}">
						<button class="btn btn-primary"><i class="fa fa-save"></i> Simpan</button> 
					</div>
					@endif
				</div>
			</form>
		</div>
	</div>

@endsection

@section('footer')
 
<script type="text/javascript"> 

	/*
    |--------------------------------------------------------------------------
    | submit form
    |--------------------------------------------------------------------------
    */

	$(document).on("submit", "#form-so", function(e){
		e.preventDefault();

		$.ajax({
			url : '{{ url('penjualan/invoice/update') }}/'+ '{{ $id }}',
			type : 'POST',
			data : new FormData(this),
			processData : false,
			contentType : false,
			dataType : 'json',
			beforeSend: function(){
				loader(".card", true);
			},
			success : function(resp){
				if(resp.status == 'error_validate'){
					for (var i = 0; i < resp.message.length; i++) {
                 		toastr.error(resp.message[i],{ "closeButton": true });
                	} 
				} else {
					toastr.success(resp.message, { "closeButton": true });  
               		location.href = '{{ url('penjualan/invoice/index') }}/'+'{{ Helper::encodex($info["invoice"]->SKPP->id_skpp) }}';
				}

				loader(".card", false);
			},
			error : function(jqXHR, exception){
				errorHandling(jqXHR.status, exception);
                loader(".card", false);
			}
		})
	});


	/*
    |--------------------------------------------------------------------------
    | total calculate 
    |--------------------------------------------------------------------------
    */

    $("body").delegate("input[name=ppn]", "keyup", function(){
    	var ppn = $(this).val();
    	var sub_total = $("input[name=sub_total]").val();
    	var total = convertNumeric(ppn) + convertNumeric(sub_total);

    	$("input[name=total]").val(formatNumber(total.toFixed(2), 2));
    });

    $("body").delegate("input[name=sub_total]", "keyup", function(){
    	var sub_total = $(this).val();
    	var ppn = $("input[name=ppn]").val();
    	var total = convertNumeric(sub_total) + convertNumeric(ppn);

    	$("input[name=total]").val(formatNumber(total.toFixed(2), 2));
    });


</script>

@endsection
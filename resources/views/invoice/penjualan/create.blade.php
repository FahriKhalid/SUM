@extends('layout.index')
@section('title', 'SUM - Create Invoice')
@section('content')
	@php($sub_total = 0)
	<div class="container-fluid mt-4 mb-4">
	    <div class="row">
	        <div class="col-md-12 d-flex justify-content-between">
	        	<h6 class="m-0 font-weight-bold text-primary"><i class="fa fa-plus-circle"></i> Form tambah invoice </h6>
	            <a href="{{ url("penjualan/invoice/index/".$id) }}" class="text-muted"><i class="fa fa-arrow-left"></i> Kembali</a>   
	        </div>  
	    </div> 
	    @if(PembayaranService::isBayar("penjualan", Helper::decodex($id)))
	    <div class="alert alert-warning mt-3">
	    	<h4 class="alert-heading"><i class="fa fa-exclamation-circle"></i> Warning</h4>
	        Pembayaran belum ada. Tidak dapat menambahkan invoice!
	    </div>
	    @endif
		<div class="card mt-3"> 
			<form id="form-so" enctype="multipart/form-data"> 
				<div class="card-body" id="layout-parent">  
					<div class="form-row ">
						<div class="form-group col-md-6">
	                        <div class="form-group"> 
	                        	<label>Customer <span class="text-danger">*</span></label>
	                            <input class="form-control" disabled value="{{ $info["skpp"]->Customer->perusahaan }}" placeholder="Wajib di isi"> 
	                        </div> 
	                    </div> 
						<div class="form-group col-md-6"> 
	                        <div class="form-group">  
	                        	<label>Alamat <span class="text-danger">*</span></label>
	                            <input class="form-control" disabled value="{{ $info["skpp"]->Customer->alamat }}" placeholder="Wajib diisi"> 
	                        </div>
	                    </div> 

						<div class="form-group col-md-6">
	                        <div class="form-group"> 
	                        	<label>Nomor Tagihan <span class="text-danger">*</span></label>
	                            <input class="form-control" name="nomor_tagihan" value="{{ $info["skpp"]->no_dokumen }}" placeholder="Wajib diisi"> 
	                        </div>
	                    </div>

	                    <div class="form-group col-md-6">
	                        <div class="form-group"> 
	                        	<label>Nomor Resi </label>
	                            <input class="form-control" autocomplete="off" name="nomor_resi" value="" placeholder="Opsional"> 
	                        </div>
	                    </div>
	                    
	                    <div class="form-group col-md-6"> 
	                        <div class="form-group"> 
	                        	<label>Nomor Faktur pajak <span class="text-danger">*</span></label>
	                            <input class="form-control" autocomplete="off" name="nomor_faktur_pajak" value="" placeholder="Wajib diisi"> 
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
	                        		<select name="so" class="form-control select2">
		                        		<option value="">-- Pilih --</option> 
			                        		@forelse($info["so"] as $so)
			                        			@if($so->Invoice == null)
			                        			<option value="{{ Helper::encodex($so->id_so) }}">{{ $so->no_so }}</option>
			                        			@endif
			                        		@empty
			                        		@endforelse 
		                        	</select>   
	                        </div>
	                    </div> 
					</div>
					<div id="table-sopo" class="collapse"></div>
					
				</div>   
				<div class="card-body border-top d-flex justify-content-between"> 
					<div>
						<span class="text-danger font-italic">
							<div>- Extensi file yang diperbolehkan hanya JPG, JPEG, PNG dan PDF.</div>
							<div>- Maksimal ukuran file 2 Mb.</div>
						</span>
					</div>
					@if(!PembayaranService::isBayar("penjualan", Helper::decodex($id)))
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
			url : '{{ url('penjualan/invoice/store') }}/'+ '{{ $id }}',
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
               		location.href = '{{ url('penjualan/invoice/index') }}/'+'{{ $id }}';
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
    | get data by no SO
    |--------------------------------------------------------------------------
    */

	$("body").delegate("select[name=so]", "change", function(){
		var id = $(this).val();

		if(id == ""){
			$.when($('#table-sopo').collapse('hide')).then(function(){
				$('#table-sopo').html('');
			});
		} else {
			$.ajax({
				url : '{{ url('penjualan/salesorder/sopo') }}/'+id,
				type : 'GET',
				dataType : 'json',
				beforeSend : function(){
					loader(".card", true);
				},
				success : function(resp){
					$.when($("#table-sopo").html(resp.html),
							$('[data-toggle="tooltip"]').tooltip(),
							$("input.numeric:text").inputmask('numeric', {
					            groupSeparator: '.',
					            radixPoint : ',',
					            autoGroup: true,
					            digits: 2,
					            digitsOptional: false,
					            placeholder: '0,00',
					        }),
					        loader(".card", false)
					 )
					.then(function(){   
						setTimeout(function() { 
					        $('#table-sopo').collapse('show');
					        
					    }, 100);
			        });

			        $(document).on('shown.bs.collapse', function(event){
					    $("html, body").animate({ scrollTop: document.body.scrollHeight }, "slow");
				    });
				},
				error : function(jqXHR, exception){
					errorHandling(jqXHR.status, exception);
	                loader(".card", false);
				}
			});
		}  
	});


	/*
    |--------------------------------------------------------------------------
    | total calculate 
    |--------------------------------------------------------------------------
    */

    $("body").delegate("input[name=ppn]", "keyup", function(){
    	var ppn = $(this).val();
    	var sub_total = $("input[name=sub_total]").val();
    	var total = convertNumeric(ppn) + parseFloat(sub_total);

    	$("input[name=total]").val(formatNumber(total, 0));
    });


    /*
    |--------------------------------------------------------------------------
    | Reset PPN
    |--------------------------------------------------------------------------
    */

    function reset_ppn()
    {
    	var sub_total = $("input[name=sub_total]").val();
    	var ppn = convertNumeric(sub_total) / 10;
    	var total = ppn + parseFloat(sub_total);

    	$("input[name=ppn]").val(formatNumber(ppn, 0));
    	$("input[name=total]").val(formatNumber(total, 0));
    }

</script>

@endsection
@extends('layout.index')


@section('title', 'SUM - Create Pre Order')
 

@section('content')
 	
	<div class="container-fluid mt-4">

	    <div class="row">
	        <div class="col-md-12 d-flex justify-content-between">
	        	<h6 class="m-0 font-weight-bold text-primary"><i class="fa fa-plus-circle"></i> FORM TAMBAH PRE ORDER</h6>
	            <a href="{{ url("pembelian/pre_order") }}" class="text-muted"><i class="fa fa-arrow-left"></i> Kembali</a>   
	        </div>  
	    </div> 

		<div class="card mt-3 mb-4"> 
			 
				@csrf
				<div class="card-body">
					<div class="form-row">
						<div class="form-group col-md-6">
	                        <label>Nomor Pre Order <i class="text-danger">*</i></label>
	                        <div class="form-group"> 
	                            <input type="text" class="form-control" name="no_po" value="{{ $info["no_po"] }}">
	                        </div> 
	                    </div> 
						<div class="form-group col-md-6">
	                        <label>Produsen <i class="text-danger">*</i></label>
	                        <div class="form-group"> 
	                            <select class="form-control select2" name="produsen">
	                            	@foreach($info["produsen"] as $produsen)
	                            		<option value="{{ $produsen->id_produsen }}">{{ $produsen->perusahaan }} - {{ $produsen->nama }}</option>
	                            	@endforeach
	                            </select>
	                        </div> 
	                    </div> 
					</div>

					{{-- Form Purchase Order --}}

					<label>Purchase Order</label>
					<table class="table table-sm table-bordered">
						<thead>
							<tr> 
								<th>Produk <i class="text-danger">*</i></th>
								<th>Incoterm <i class="text-danger">*</i></th>
								<th width="170px">Kuantitas <i class="text-danger">*</i></th>
								<th>Harga beli <i class="text-danger">*</i></th> 
								<th>Nilai <i class="text-danger">*</i></th>
								<th width="1px"></th>
							</tr>
						</thead>
						<tbody id="form-parent-po">
							<tr> 
								<td> 
									<select class="form-control select2 select-produk" title="Pilih Produk" name="new_produk[]">
										<option value="">-- Pilih Barang --</option>
										@foreach($info["produk"] as $produk)
											<option value="{{ $produk->id_produk }}">{{ $produk->nama.' ('.$produk->spesifikasi.')' }}</option>
										@endforeach
									</select>
								</td>
								<td>
									<input type="text" class="form-control" name="new_incoterm[]">
								</td>
								<td>
									<div class="input-group"> 
		                                <input type="text" value="1" class="form-control kuantitas number" name="new_kuantitas[]">
		                                <div class="input-group-append">
		                                    <span class="input-group-text">MT</span>
		                                </div>
		                            </div> 
								</td>
								<td>
									<div class="input-group"> 
										<div class="input-group-prepend">
		                                    <span class="input-group-text">Rp</span>
		                                </div>
		                                <input type="text" class="form-control numeric harga-beli" name="new_harga_jual[]">
		                            </div> 
								</td> 
								<td>
									<div class="input-group"> 
										<div class="input-group-prepend">
		                                    <span class="input-group-text">Rp</span>
		                                </div>
		                                <input type="text" readonly class="form-control nilai align-right" name="new_nilai[]">
		                            </div> 
									
								</td>
								<td><button type="button" class="btn btn-success btn-sm" onclick="addRowPO()" data-toggle="tooltip" data-placement="top" title="Tambah data"><i class="fa fa-plus"></i></button></td>
							</tr> 
						</tbody> 
						<tfoot> 
							<tr>
								<td colspan="4" align="right"><b>TOTAL</b></td> 
								<td align="right">
									<div class="d-flex justify-content-between">
										<div>Rp</div>
										<div id="total-harga">0,00</div>		
									</div>
								</td>
							</tr>
						</tfoot>
					</table>
	 				@include('layout.form_tambah_lampiran')
				</div>
				<div class="card-body border-top d-flex justify-content-between">  
					<div></div>
					<div> 
						<button class="btn btn-primary" type="button" onclick="save(2)" ><i class="fa fa-check"></i> Submit</button>
					</div>
				</div> 
		</div>
	</div>

@endsection

@section('footer')
<script type="text/javascript" src="{{ asset('js/lampiran.js') }}"></script>
<script type="text/javascript">
	function addRowPO()
	{ 
		var html = $("#form-parent-po").find("tr:last"); 
		html.find("select").select2("destroy");

		var clone = html.clone();
		let length = $("#form-parent-po tr").length

		clone.find('button:last').addClass("remove-row-po").removeClass("btn-success")
				.addClass("btn-danger")
				.attr("onclick", "")
				.find('i').removeClass("fa-plus").addClass("fa-minus"); 
		clone.find("input").val(""); 
		clone.find(".kuantitas").val(1);  

		var append = $("#form-parent-po").append(clone); 
		append.find("select").select2({
		    theme : 'bootstrap4',
		    width : '100%'
		});

		input_numeric();
		input_number();
		total_harga();
	}

	function addRowLampiran(){
		var clone = $("#form-parent-lampiran").find("tr:last").clone();	

		clone.find('button:last').addClass("remove-row-po").removeClass("btn-success")
				.addClass("btn-danger")
				.attr("onclick", "")
				.find('i').removeClass("fa-plus").addClass("fa-minus");

		clone.find("input").val("");
		clone.find("textarea").val("");

		$("#form-parent-lampiran").append(clone);
	}

	$("body").delegate(".remove-row-po", "click", function(){
		$(this).closest("tr").remove();
		total_harga();
	});


	$("body").delegate(".harga-beli", "keyup", function(){ 
		var harga_jual = $(this).val(); 
		var closest = $(this).closest("tr"); 
		var kuantitas = closest.find(".kuantitas").val();  
		if (harga_jual != "" && harga_jual != "0,00") {
			var hasil = convertNumeric(harga_jual) * parseInt(kuantitas);

			// let ppn = hasil * 0.1;
			// hasil = hasil + ppn; 

			closest.find(".nilai").val(formatNumber(hasil.toFixed(2), 2));
		} else {
			closest.find(".nilai").val("0,00");
		} 
 		
 		total_harga();
	});  

	$("body").delegate(".kuantitas", "keyup", function(){ 
		var kuantitas = $(this).val(); 
		var closest = $(this).closest("tr"); 
		var harga_jual = closest.find(".harga-beli").val(); 
		if (harga_jual != "" && harga_jual != "0,00") {
			var hasil = convertNumeric(harga_jual) * parseInt(kuantitas);

			// let ppn = hasil * 0.1;
			// hasil = hasil + ppn; 
			closest.find(".nilai").val(formatNumber(hasil.toFixed(2), 2));
		} else {
			closest.find(".nilai").val("0,00");
		} 

		total_harga();
	}); 

	function total_harga(){
		var total = 0;
		$("#form-parent-po").find("tr").each(function(){
			var nilai = $(this).find(".nilai").val();
			if(nilai != "" && nilai != "0,00"){
				total += convertNumeric($(this).find(".nilai").val());
			}
		});	 

		$("#total-harga").html(formatNumber(total, 2));
	}

	function save(option)
	{
		var data = new FormData();	
		data.append("_token", "{{ csrf_token() }}");
		data.append("status", option); 
		data.append("no_po", $("input[name=no_po]").val());
		data.append("produsen", $("select[name=produsen] option:selected").val());
		data.append("is_lampiran", $("input[name=is_lampiran]:checked").val())

		$.each($(".select-produk option:selected"), function(){            
            data.append("new_produk[]", $(this).val()); 
        });
        $.each($("input[name='new_incoterm[]'"), function(){
        	data.append("new_incoterm[]", $(this).val())
        })
        $.each($("input[name='new_kuantitas[]'"), function(){
        	data.append("new_kuantitas[]", $(this).val()); 
        });
        $.each($("input[name='new_harga_jual[]'"), function(){
        	data.append("new_harga_jual[]", $(this).val()); 
        }); 
		$.each($("input[name='new_nilai[]'"), function(){
			data.append("new_nilai[]", $(this).val()); 
		});
		$.each($("input[name='new_file[]']"), function(i, value){
		    data.append('new_file['+i+']', value.files[0]);
		});
		$.each($("input[name='new_nama_file[]']"), function(){
			data.append("new_nama_file[]", $(this).val()); 
		});
		$.each($("textarea[name='new_keterangan_file[]']"), function(){
			data.append("new_keterangan_file[]", $(this).val()); 
		});

		$.ajax({
			url : '{{ url('pembelian/pre_order/store') }}',
			method : "POST",
			data : data,
			contentType : false,
			processData : false,
			dataType : "json",
			beforeSend : function(){
                loader(".card", true);
			},
			success : function(resp){
               	if (resp.status == "error_validate"){
               		for (var i = 0; i < resp.message.length; i++) {
               			toastr.error(resp.message[i],{ "closeButton": true });
               		} 
                } else if(resp.status == "error"){
                	toastr.error(resp.message, { "closeButton": true });
                } else {
               		toastr.success(resp.message, { "closeButton": true });  
               		location.href = '{{url("pembelian/pre_order/show")}}/'+resp.id_pre_order;
                }
				
                loader(".card", false);
			},
			error : function(jqXHR, exception){
				errorHandling(jqXHR.status, exception);
                loader(".card", false);
			}
		});
	}
 
	$("body").delegate(".select-produk", "change", function(){ 
		var array = [];
		$("#form-parent-po").find("tr").each(function(){
			var val = $(this).find("select").val();
			if(val != ""){
				array.push(val);
			} 
		}); 

		if(checkIfDuplicateExists(array) === true){
			$(this).val("").change(); 
			alert("Produk duplikat, silahkan pilih produk lainnya");
		}

	});
 
</script>

@endsection
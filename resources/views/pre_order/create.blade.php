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
									<select class="form-control select2 select-produk" title="Pilih Produk" name="produk[]">
										<option value="">-- Pilih Barang --</option>
										@foreach($info["produk"] as $produk)
											<option value="{{ $produk->id_produk }}">{{ $produk->nama.' ('.$produk->spesifikasi.')' }}</option>
										@endforeach
									</select>
								</td>
								<td>
									<input type="text" class="form-control" name="incoterm[]">
								</td>
								<td>
									<div class="input-group"> 
		                                <input type="text" value="1" class="form-control kuantitas number" name="kuantitas[]">
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
		                                <input type="text" class="form-control numeric harga-beli" name="harga_beli[]">
		                            </div> 
								</td> 
								<td>
									<div class="input-group"> 
										<div class="input-group-prepend">
		                                    <span class="input-group-text">Rp</span>
		                                </div>
		                                <input type="text" readonly class="form-control nilai align-right" name="nilai[]">
		                            </div> 
									
								</td>
								<td><button type="button" class="btn btn-success btn-sm" onclick="addRowPO()"><i class="fa fa-plus"></i></button></td>
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
	 				

	 				{{-- Form Lampiran --}}

					<div class="custom-control custom-checkbox mt-4">
				        <input type="checkbox" class="custom-control-input" name="is_lampiran" value="1" id="show-form-lampiran">
				        <label class="custom-control-label" for="show-form-lampiran">Centang jika ada lampiran</label>
				    </div>
					<div class="d-none" id="form-lampiran">
						<table class="table table-sm table-bordered">
							<thead>
								<tr> 
									<th width="200px">File <i class="text-danger">*</i></th> 
									<th>Nama <i class="text-danger">*</i></th>
									<th>Keterangan</th>
									<th width="1px"></th> 
								</tr>
							</thead>
							<tbody id="form-parent-lampiran">
								<tr> 
									<td> 
										<input type="file" class="lampiran" name="file[]">
									</td> 
									<td>
										<input type="text" class="form-control lampiran" name="nama_file[][]">
									</td>
									<td>
										<textarea style="height: 38px" class="form-control lampiran" rows="1" name="keterangan_file[]"></textarea>
									</td> 
									<td><button type="button" class="btn btn-success btn-sm" onclick="addRowLampiran()"><i class="fa fa-plus"></i></button></td>
								</tr> 
							</tbody>
						</table>
						<small>
							<span class="text-danger font-italic">
								<div>Note : </div>
								<div>- Extensi file lampiran yang diperbolehkan hanya DOC, DOCX, dan PDF.</div>
								<div>- Maksimal ukuran file 2 Mb.</div> 
							</span>
						</small>
					</div>
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
 
<script type="text/javascript">
	$("body").delegate("#show-form-lampiran", "click", function(){
		if($(this).is(":checked")){
            $("#form-lampiran").removeClass("d-none"); 
        }else{
            $("#form-lampiran").addClass("d-none");
        }
	});

	$("body").delegate("#show-form-ongkir", "click", function(){
		if($(this).is(":checked")){
            $("#form-ongkir").removeClass("d-none"); 
        }else{
            $("#form-ongkir").addClass("d-none");
        }
	});

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
            data.append("produk[]", $(this).val()); 
        });
        $.each($("input[name='incoterm[]'"), function(){
        	data.append("incoterm[]", $(this).val())
        })
        $.each($(".kuantitas"), function(){
        	data.append("kuantitas[]", $(this).val()); 
        });
        $.each($(".harga-beli"), function(){
        	data.append("harga_beli[]", $(this).val()); 
        }); 
		$.each($(".nilai"), function(){
			data.append("nilai[]", $(this).val()); 
		});
		$.each($('input[type=file]'), function(i, value){
		    data.append('file['+i+']', value.files[0]);
		});
		$.each($("input[name='nama_file[]']"), function(){
			data.append("nama_file[]", $(this).val()); 
		});
		$.each($("input[name='keterangan_file[]']"), function(){
			data.append("keterangan_file[]", $(this).val()); 
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
               	if (resp.status == "error"){
               		for (var i = 0; i < resp.message.length; i++) {
               			toastr.error(resp.message[i],{ "closeButton": true });
               		} 
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
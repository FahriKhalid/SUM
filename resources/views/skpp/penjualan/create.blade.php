@extends('layout.index') 
@section('title', 'SUM - Create SKPP') 
@section('content')
 	
	<div class="container-fluid mt-4">

	    <div class="row">
	        <div class="col-md-12 d-flex justify-content-between">
	        	<h6 class="m-0 font-weight-bold text-primary"><i class="fa fa-plus-circle"></i> FORM TAMBAH SKPP</h6>
	            <a href="{{ url("penjualan/skpp") }}" class="text-muted"><i class="fa fa-arrow-left"></i> Kembali</a>   
	        </div>  
	    </div> 

		<div class="card mt-3 mb-4"> 
			<form id="form-skpp" method="post" enctype="multipart/form-data">
				@csrf
				<div class="card-body">
					<div class="form-row"> 
						<div class="form-group col-md-6">
	                        <label>Nomor SKPP <i class="text-danger">*</i></label>
	                        <div class="form-group"> 
	                            <input class="form-control" name="nomor_skpp" placeholder="Wajib diisi" value="{{ $info["no_skpp"] }}"> 
	                        </div>
	                    </div> 

						<div class="form-group col-md-6">
	                        <label>Customer <i class="text-danger">*</i></label>
	                        <div class="form-group"> 
	                            <select class="form-control select2" name="customer">
	                            	@foreach($info["customer"] as $customer)
	                            		<option value="{{ $customer->id_customer }}">{{ $customer->perusahaan }} - {{ $customer->nama }}</option>
	                            	@endforeach
	                            </select>
	                        </div> 
	                    </div> 

	                    <div class="form-group col-md-6">
	                        <label>Gudang pengambilan <i class="text-danger">*</i></label>
	                        <div class="form-group"> 
	                            <input class="form-control" name="syarat_penyerahan" placeholder="Wajib diisi"> 
	                        </div>
	                    </div> 

	                    <div class="form-group col-md-6">
	                        <label>Batas akhir pengambilan <i class="text-danger">*</i></label>
	                        <div class="form-group"> 
	                            <input type="text" class="form-control" autocomplete="off" name="batas_akhir_pengambilan" placeholder="Wajib diisi"> 
	                        </div>
	                    </div> 

	                    <div class="form-group col-md-6">
	                        <label>Batas akhir pembayaran <i class="text-danger">*</i></label>
	                        <div class="form-group"> 
	                            <input type="text" class="form-control" autocomplete="off" name="batas_akhir_pembayaran" placeholder="Wajib diisi"> 
	                        </div>
	                    </div> 
	                	
						<div class="form-group col-md-6">
	                        <label>Atm <i class="text-danger">*</i></label>
	                        <div class="form-group"> 
	                        	 <select class="form-control select2" multiple name="atm[]">
	                        	 	@foreach($info["atm"] as $atm)
	                        	 		<option value="{{ Helper::encodex($atm->id_atm) }}">{{ $atm->nama.' - '.$atm->nomor }}</option>
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
								<th width="300px">Kuantitas <i class="text-danger">*</i></th>
								<th>Harga jual <i class="text-danger">*</i></th>
								<th>Nilai <i class="text-danger">*</i></th>
								<th width="1px"></th>
							</tr>
						</thead>
						<tbody id="form-parent-po">
							<tr> 
								<td> 
									<select class="form-control select2 select-produk" title="Pilih Produk" name="produk[]">
										<option value="">-- Pilih Produk --</option>
										@foreach($info["produk"] as $produk)
											<option value="{{ $produk->id_produk }}">{{ $produk->nama.' ('.$produk->spesifikasi.')' }}</option>
										@endforeach
									</select>
								</td>
								<td>
									<input type="text" class="form-control" name="incoterm[]">
								</td>
								<td> 
									<div class="d-flex">
										<div class="input-group">
											<input type="text" disabled class="form-control float sisa_kuantitas" value="0" im-insert="true" style="text-align: right;">
											<div class="input-group-append">
												<span class="input-group-text">MT</span>
											</div>
										</div> 
										<div class="ml-2 mr-2 align-self-center">
											<i class="fa fa-arrow-right"></i>
										</div> 
										<div class="input-group"> 
			                                <input type="text" value="0" class="form-control float kuantitas" name="kuantitas[]">
			                                <div class="input-group-append">
			                                    <span class="input-group-text">MT</span>
			                                </div>
			                            </div> 
									</div> 
								</td>
								<td>
									<div class="input-group"> 
										<div class="input-group-prepend">
		                                    <span class="input-group-text">Rp</span>
		                                </div>
		                                <input type="text" class="form-control numeric harga-jual" name="harga_jual[]">
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
								<td><button type="button" class="btn btn-success btn-sm" onclick="addRowPO()" data-toggle="tooltip" data-placement="top" title="Tambah data"><i class="fa fa-plus"></i></button></td>
							</tr> 
						</tbody> 
						<tfoot>
							{{-- <tr>
								<td colspan="5" align="right"><b>PPN 10%</b></td> 
								<td align="right">
									<div class="d-flex justify-content-between">
										<div>Rp</div>
										<div id="total-ppn">0,00</div>		
									</div>
								</td>
							</tr> --}}
							<tr>
								<td colspan="4" align="right"><b>TOTAL</b></td> 
								<td align="right">
									<div class="d-flex justify-content-between">
										<div>Rp</div>
										<div id="total-harga">0,00</div>		
									</div>
								</td>
								<td></td>
							</tr>
						</tfoot>
					</table>
					@include('layout.form_tambah_lampiran')
				</div> 
				<div class="card-body border-top d-flex justify-content-between">  
					<div>
						<div class="legend bg-red"></div> Stok Habis
					</div>
					
					<div>
						<button class="btn btn-secondary" type="button" onclick="save(1)"><i class="fa fa-save"></i> Draft</button>
						<button class="btn btn-primary" type="button" onclick="save(2)" ><i class="fa fa-check"></i> Submit</button>
					</div>
				</div> 
			</form>
		</div>
	</div>

@endsection

@section('footer')
<script type="text/javascript" src="{{ asset('js/lampiran.js') }}"></script>
<script type="text/javascript">

	$('input[name=batas_akhir_pembayaran]').datepicker({
	    showOtherMonths: true,
	    uiLibrary: 'bootstrap4',
	    format: 'dd/mm/yyyy'
	});

	$('input[name=batas_akhir_pengambilan]').datepicker({
	    showOtherMonths: true,
	    uiLibrary: 'bootstrap4',
	    format: 'dd/mm/yyyy'
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
		clone.find('button:last').addClass("remove-row-po").removeClass("btn-success")
				.addClass("btn-danger")
				.attr("onclick", "")
				.find('i').removeClass("fa-plus").addClass("fa-minus");
		clone.removeClass("bg-red");
		clone.find("input").val("");
		clone.find(".sisa_kuantitas").val("0,0").attr("sisa", "0");
		clone.find(".kuantitas").val(0);
		
		var append = $("#form-parent-po").append(clone);

		append.find("select").select2({
		    theme : 'bootstrap4',
		    width : '100%'
		});

		input_numeric();
		input_float();
		total_harga();
	}
 

	$("body").delegate(".remove-row-po", "click", function(){
		$(this).closest("tr").remove();
		total_harga();
	});


	$("body").delegate(".harga-jual", "keyup", function(){
		var harga_jual = $(this).val();
		var closest = $(this).closest("tr");
		var kuantitas = closest.find(".kuantitas").val().replace(",",".");

		if (harga_jual != "" && harga_jual != "0,00") {
			var hasil = convertNumeric(harga_jual) * parseFloat(kuantitas);
			closest.find(".nilai").val(formatNumber(hasil.toFixed(2), 2));
		} else {
			closest.find(".nilai").val("0,00");
		} 
 		
 		total_harga();
	});  

	$("body").delegate(".kuantitas", "keyup", function(e){

		let kuantitas = $(this).val().replace(",", "."); 
		let sisa_kuantitas = $(this).closest("tr").find(".sisa_kuantitas")
		let jumlah_stok = sisa_kuantitas.attr("sisa");
		let hasil = parseFloat(jumlah_stok) - parseFloat(kuantitas); 

		if(e.keyCode == 8) {
			if(kuantitas == ""){
				sisa_kuantitas.val(jumlah_stok.replace(".", ","));
			} else { 
				sisa_kuantitas.val(hasil.toString().replace(".", ","));
			}
		} else {	  
			if(kuantitas == ""){
				sisa_kuantitas.val(jumlah_stok.replace(".", ","));
			} else {
				if(parseFloat(kuantitas) > parseFloat(jumlah_stok)){
					alert("Kuantitas tidak boleh melebihi dari jumlah stok");
					$(this).val(jumlah_stok)
					sisa_kuantitas.val(0);
				} else {  
					sisa_kuantitas.val(hasil.toString().replace(".", ","));
				}
				
			}
		}
		

		let closest = $(this).closest("tr");
		
		let harga_jual = closest.find(".harga-jual").val();

		if (harga_jual != "" && harga_jual != "0,00") {
			let hasil = 0;
			if(parseFloat(kuantitas) > parseFloat(parseFloat)){
				hasil = convertNumeric(harga_jual) * parseFloat(jumlah_stok);
			} else {
				hasil = convertNumeric(harga_jual) * parseFloat(kuantitas);
			}

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

		// var ppn = total * 10 / 100;
		// var total_harga = total - ppn; 
		//$("#total-ppn").html(formatNumber(ppn, 2));

		$("#total-harga").html(formatNumber(total, 2));
	}
 	
 	$("body").delegate(".select-produk", "change", function(){
 		let val = $(this).val();
		let tr = $(this).closest("tr");
		let sisa_kuantitas = tr.find(".sisa_kuantitas");

		if(val == ""){
			tr.removeClass("bg-red");
		}

		var array = [];
		$("#form-parent-po").find("tr").each(function(){
			var val = $(this).find("select").val();
			if(val != ""){
				array.push(val);
			} 
		}); 

		if(checkIfDuplicateExists(array) === true){
			$(this).val("").change(); 
			tr.removeClass("bg-red");
			alert("Produk duplikat, silahkan pilih produk lainnya");
		} else { 
			if(val != "") {
				$.ajax({
					url : '{{ url('stok/jumlah_stok') }}/'+ val,
					type : 'GET',
					dataType : 'json',
					beforeSend : function()
					{
						loader(".card", true); 
					},
					success : function(resp)
					{
						if(resp > 0){
							sisa_kuantitas.val(resp.replace(".", ","));
							sisa_kuantitas.attr("sisa", resp);
						} else {
							sisa_kuantitas.val("0,0");
							sisa_kuantitas.attr("sisa", 0.0);
						}

						if(parseInt(resp) < 1){  
							tr.addClass("bg-red");  
						} else {
							tr.removeClass("bg-red");
						}

						loader(".card", false);
					},
					error : function(jqXHR, exception){
						errorHandling(jqXHR.status, exception);
		                loader(".card", false);
					}
				});
			}
		}
	});

	function save(option)
	{
		var data = new FormData();	
		data.append("_token", "{{ csrf_token() }}");
		data.append("status", option); 
		data.append("nomor_skpp", $("input[name=nomor_skpp]").val());
		data.append("customer", $("select[name=customer] option:selected").val());
		data.append("syarat_penyerahan", $("input[name=syarat_penyerahan]").val());
		data.append("jadwal_penyerahan", $("select[name=jadwal_penyerahan] option:selected").val());
		data.append("batas_akhir_pengambilan", $("input[name=batas_akhir_pengambilan]").val());
		data.append("batas_akhir_pembayaran", $("input[name=batas_akhir_pembayaran]").val());
		data.append("is_lampiran", $("input[name=is_lampiran]:checked").val());

		$.each($("select[name='atm[]'] option:selected"), function(){            
            data.append("atm[]", $(this).val()); 
        });
		$.each($(".select-produk option:selected"), function(){            
            data.append("new_produk[]", $(this).val()); 
        });
        $.each($("input[name='incoterm[]'"), function(){
        	data.append("new_incoterm[]", $(this).val())
        })
        $.each($(".kuantitas"), function(){
        	data.append("new_kuantitas[]", $(this).val()); 
        });
        $.each($(".harga-jual"), function(){
        	data.append("new_harga_jual[]", $(this).val()); 
        }); 
		$.each($(".nilai"), function(){
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
			url : '{{ url('penjualan/skpp/store') }}',
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
                } else if(resp.status == "error") {
                	toastr.error(resp.message,{ "closeButton": true });
                } else {
               		toastr.success(resp.message, { "closeButton": true });  
               		location.href = '{{url("penjualan/skpp/show")}}/'+resp.id_skpp;
                }
				
                loader(".card", false);
			},
			error : function(jqXHR, exception){
				errorHandling(jqXHR.status, exception);
                loader(".card", false);
			}
		});
	}
 
</script>

@endsection
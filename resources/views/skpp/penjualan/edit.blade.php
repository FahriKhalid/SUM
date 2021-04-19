@extends('layout.index')

@section('title', 'SUM - Edit SKPP')

@section('content')
 	
	<div class="container-fluid mt-4">

	    <div class="row">
	        <div class="col-md-12 d-flex justify-content-between">
	        	<h6 class="m-0 font-weight-bold text-primary"><i class="fa fa-edit"></i> FORM EDIT SKPP</h6>
	            <a href="{{ url("penjualan/skpp/show/".Helper::encodex($info["skpp"]->id_skpp)) }}" class="text-muted"><i class="fa fa-arrow-left"></i> Kembali</a>   
	        </div>  
	    </div> 

        @if($info["skpp"]->catatan_revisi != null && $info["skpp"]->id_status == 1)
		    <div class="alert alert-danger mt-3">
		    	<h4 class="alert-heading"><i class="fa fa-exclamation-circle"></i> Catatan Revisi!</h4>
		    	<span>{{ $info["skpp"]->catatan_revisi }}</span>
		    </div>
	    @endif


		<div class="card mt-3 mb-4"> 
			<form id="form-skpp" enctype="multipart/form-data">
				@csrf
				<div class="card-body">
					<div class="form-row"> 
						<div class="form-group col-md-6">
	                        <label>Nomor SKPP <span class="text-danger">*</span></label>
	                        <div class="form-group"> 
	                            <input class="form-control" name="nomor_skpp" value="{{ $info["skpp"]->no_skpp }}" placeholder="Wajib di isi"> 
	                        </div>
	                    </div> 

						<div class="form-group col-md-6">
	                        <label>Customer <span class="text-danger">*</span></label>
	                        <div class="form-group"> 
	                            <select class="form-control select2" name="customer">
	                            	@foreach($info["customer"] as $customer)
	                            		<option {{ $info["skpp"]->id_customer == $customer->id_customer ? "selected" : "" }} value="{{ $customer->id_customer }}">{{ $customer->perusahaan }} - {{ $customer->nama }}</option>
	                            	@endforeach
	                            </select>
	                        </div> 
	                    </div> 

	                    <div class="form-group col-md-6">
	                        <label>Gudang pegambilan <span class="text-danger">*</span></label>
	                        <div class="form-group"> 
	                            <input class="form-control" name="syarat_penyerahan" value="{{ $info["skpp"]->syarat_penyerahan }}" placeholder="Wajib di isi"> 
	                        </div>
	                    </div>

	                    <div class="form-group col-md-6">
	                        <label>Batas akhir pengambilan <i class="text-danger">*</i></label> 
	                        <div class="form-group"> 
	                            <input type="text" class="form-control" autocomplete="off" name="batas_akhir_pengambilan" value="{{ Helper::dateFormat($info["skpp"]->batas_akhir_pengambilan, false, 'd/m/Y') }}" placeholder="Wajib diisi"> 
	                        </div>
	                    </div> 
	                    
	                    <div class="form-group col-md-6">
	                        <label>Batas akhir pembayaran <i class="text-danger">*</i></label> 
	                        <div class="form-group"> 
	                            <input type="text" class="form-control" autocomplete="off" name="batas_akhir_pembayaran" value="{{ Helper::dateFormat($info["skpp"]->terakhir_pembayaran, false, 'd/m/Y') }}" placeholder="Wajib diisi"> 
	                        </div>
	                    </div> 

						<div class="form-group col-md-6">
	                        <label>Atm <i class="text-danger">*</i></label> 
	                        <div class="form-group"> 
	                        	 <select class="form-control select2" multiple name="atm[]">
	                        	 	@foreach($info["atm"] as $atm)
	                        	 		<option value="{{ Helper::encodex($atm->id_atm) }}" {{ in_array($atm->id_atm, $info["id_atm"]) ? "selected" : "" }}>{{ $atm->nama.' - '.$atm->nomor }}</option>
	                        	 	@endforeach
	                        	 </select>
	                        </div>
	                    </div> 
					</div>

					{{-- Form Purchase Order --}}
					<label>Purchase Order</label>
					<div id="table-po">
						@include('skpp.penjualan.form_edit_po')
					</div>
	 				

	 				{{-- Form Lampiran --}}
					@include('layout.form_edit_lampiran', ["info_lampiran" => $info["skpp"]->Lampiran])

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

	function addRowPO(){ 
		var html = $("#form-parent-po").find("tr:last");

		html.find("select").select2("destroy");

		var clone = html.clone();

		clone.find('button:last')
				.prop("disabled", false)
				.removeClass("btn-dark")
				.removeClass("delete-po")
				.addClass("btn-danger")
				.addClass("remove-row-po")
				.attr("onclick", "")
				.find('i').removeClass("fa-trash").addClass("fa-minus");

		clone.find("select[name='produk[]']").attr("name", "new_produk[]");
		clone.find("input[name='incoterm[]']").attr("name", "new_incoterm[]");
		clone.find("input[name='kuantitas[]']").attr("name", "new_kuantitas[]");
		clone.find("input[name='harga_jual[]']").attr("name", "new_harga_jual[]");
		clone.find("input[name='nilai[]']").attr("name", "new_nilai[]");

		clone.find("input").val("");
		clone.find("select").val("");
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
 
	$("body").delegate(".remove-row-po", "click", function(){
		$(this).closest("tr").remove();
	});

	$("body").delegate(".remove-row-lampiran", "click", function(){
		var jumlah_baris = $("#form-parent-lampiran").find("tr").length;

		if(jumlah_baris == 1){
			$("#show-form-lampiran").prop("checked", false);
			$("#form-lampiran").addClass("d-none");
		}else{
			$(this).closest("tr").remove();
		}
	});


	$("body").delegate(".harga-jual", "keyup", function(){
		var harga_jual = $(this).val();

		var closest = $(this).closest("tr");
		
		var kuantitas = closest.find(".kuantitas").val();

		if (harga_jual != "" && harga_jual != "0,00") {
			var hasil = convertNumeric(harga_jual) * parseInt(kuantitas);
			closest.find(".nilai").val(formatNumber(hasil.toFixed(2), 2));
		} else {
			closest.find(".nilai").val("0,00");
		} 
 		
 		total_harga();
	});  

	$("body").delegate(".kuantitas", "keyup", function(){
		var kuantitas = $(this).val();
		var closest = $(this).closest("tr");
		var harga_jual = closest.find(".harga-jual").val();

		if (harga_jual != "" && harga_jual != "0,00") {
			var hasil = convertNumeric(harga_jual) * parseInt(kuantitas);
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

		var ppn = total * 10 / 100;
		var total_harga = total - ppn; 
		$("#total-ppn").html(formatNumber(ppn, 2));
		$("#total-harga").html(formatNumber(total_harga, 2));
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

	var row_po_remove = null;

	$("body").delegate(".delete-po", "click", function(e){ 
		e.preventDefault();  
    	$("#form-hapus").attr("action", $(this).attr("url")); 
    	$("#modal-konfirmasi-hapus").modal("show");

    	row_po_remove = $(this).closest("tr");
	});


	var row_lampiran_remove = null;

	$("body").delegate(".delete-lampiran", "click", function(e){ 
		e.preventDefault();  
    	$("#form-hapus").attr("action", $(this).attr("url")); 
    	$("#modal-konfirmasi-hapus").modal("show"); 

    	row_lampiran_remove = $(this).closest("tr");
	});

	$(document).on("submit", "#form-hapus", function(e){
	    e.preventDefault();

	    $.ajax({
	        url : $(this).attr("action"),
	        type : 'DELETE',
	        data : { "_token" : $('meta[name="csrf-token"]').attr('content') },  
			dataType : "json", 
			beforeSend: function(resp){
				loader(".modal-content", true);
			},
	        success : function(resp)
	        { 
	            if (resp.status == 'success') {
	                toastr.success(resp.message, { "closeButton": true });    
	                
	                if(row_po_remove != null){
	                	row_po_remove.remove();
					    row_po_remove = null;
					    total_harga();
	                }

	                if(row_lampiran_remove != null){
	                	row_lampiran_remove.remove();
	                	row_lampiran_remove = null;
	                }

	                $("#modal-konfirmasi-hapus").modal("hide");
	            } else {
	                toastr.error(resp.message, { "closeButton": true });
	            }
	            loader(".modal-content", false);
	        },
	        error : function (jqXHR, exception) {
	            errorHandling(jqXHR.status, exception); 
	            loader(".modal-content", false);
	        }
	    })
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
		data.append("atm", $("select[name=atm] option:selected").val());
		data.append("is_lampiran", $("input[name=is_lampiran]:checked").val())

		$.each($("input[name='id_po[]'"), function(){
        	data.append("id_po[]", $(this).val())
        });
        $.each($("select[name='atm[]'] option:selected"), function(){
        	data.append("atm[]", $(this).val())
        });
		$.each($("select[name='produk[]'] option:selected"), function(){            
            data.append("produk[]", $(this).val()); 
        });
        $.each($("input[name='incoterm[]'"), function(){
        	data.append("incoterm[]", $(this).val())
        });
        $.each($("input[name='kuantitas[]']"), function(){
        	data.append("kuantitas[]", $(this).val()); 
        });
        $.each($("input[name='harga_jual[]']"), function(){
        	data.append("harga_jual[]", $(this).val()); 
        }); 
		$.each($("input[name='nilai[]']"), function(){
			data.append("nilai[]", $(this).val()); 
		});
		$.each($("select[name='new_produk[]'] option:selected"), function(){            
            data.append("new_produk[]", $(this).val()); 
        });
        $.each($("input[name='new_incoterm[]'"), function(){
        	data.append("new_incoterm[]", $(this).val())
        });
        $.each($("input[name='new_kuantitas[]']"), function(){
        	data.append("new_kuantitas[]", $(this).val()); 
        });
        $.each($("input[name='new_harga_jual[]']"), function(){
        	data.append("new_harga_jual[]", $(this).val()); 
        }); 
		$.each($("input[name='new_nilai[]']"), function(){
			data.append("new_nilai[]", $(this).val()); 
		});
		$.each($("input[name='id_lampiran[]']"), function(){
			data.append("id_lampiran[]", $(this).val()); 
		});
		$.each($("input[name='file[]']"), function(i, value){
		    data.append('file['+i+']', value.files[0]);
		});
		$.each($("input[name='nama_file[]']"), function(){
			data.append("nama_file[]", $(this).val()); 
		});
		$.each($("textarea[name='keterangan_file[]']"), function(){
			data.append("keterangan_file[]", $(this).val()); 
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

		var is_lampiran = {{ count($info["skpp"]->Lampiran) }};

		if(is_lampiran > 0){
			if(!$("#show-form-lampiran").is(":checked")){
				var r = confirm('Konfirmasi hapus semua lampiran\nApakah anda yakin?');
				if(r == false){
					return;
				} 
			}
		}

		$.ajax({
			url : '{{ url('penjualan/skpp/update') }}/'+'{{ Helper::encodex($info["skpp"]->id_skpp) }}',
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
                } else if (resp.status == "error"){
               		toastr.error(resp.message,{ "closeButton": true });
                } else {
               		toastr.success(resp.message, { "closeButton": true });  
               		$("#table-po").html(resp.form_edit_po);
               		$("#form-parent-lampiran").html(resp.form_edit_lampiran); 

               		$(".select2").select2({
					    theme : 'bootstrap4',
					    width : '100%'
					});

					location.href = '{{ url('penjualan/skpp/show/'.$id) }}';
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
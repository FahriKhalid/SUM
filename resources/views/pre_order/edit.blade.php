@extends('layout.index')


@section('title', 'SUM - Edit Pre Order')
 

@section('content')
 	
	<div class="container-fluid mt-4">

	    <div class="row">
	        <div class="col-md-12 d-flex justify-content-between">
	        	<h6 class="m-0 font-weight-bold text-primary"><i class="fa fa-edit"></i> FORM EDIT PRE ORDER</h6>
	            <a href="{{ url("pembelian/pre_order/show/".Helper::encodex($info["pre_order"]->id_pre_order)) }}" class="text-muted"><i class="fa fa-arrow-left"></i> Kembali</a>   
	        </div>  
	    </div>  
	    
	    {{-- @if($info["pre_order"]->SKPP->id_skpp != null && $info["piutang"] == 0)
	    	<div class="alert alert-warning mt-3">
		    	<h4 class="alert-heading"><i class="fa fa-exclamation-circle"></i> Warning</h4>
		        Pembayaran sudah lunas. Tidak dapat edit data pre order !
		    </div>
	    @endif --}}

	    @if($info["pre_order"]->SKPP->id_skpp != null)
	    	<div class="alert alert-warning mt-3">
		    	<h4 class="alert-heading"><i class="fa fa-exclamation-circle"></i> Warning</h4>
		        SKPP sudah di buat. Tidak dapat melakukan edit produk pre order !
		    </div>
	    @endif

		<div class="card mt-3 mb-4"> 
			 
				@csrf
				<div class="card-body">
					<div class="form-row">
						<div class="form-group col-md-6">
	                        <label>Nomor Pre Order <i class="text-danger">*</i></label>
	                        <div class="form-group"> 
	                            <input type="text" class="form-control" name="no_po" value="{{ $info["pre_order"]->no_po }}">
	                        </div> 
	                    </div> 
						<div class="form-group col-md-6">
	                        <label>Produsen <i class="text-danger">*</i></label>
	                        <div class="form-group"> 
	                            <select class="form-control select2" name="produsen">
	                            	@foreach($info["produsen"] as $produsen)
	                            		<option {{ $info["pre_order"]->id_produsen == $produsen->id_produsen ? "selected" : "" }} value="{{ $produsen->id_produsen }}">{{ $produsen->perusahaan }} - {{ $produsen->nama }}</option>
	                            	@endforeach
	                            </select>
	                        </div> 
	                    </div> 
					</div>

					{{-- Form Purchase Order --}}
					<label>Purchase Order</label>
					<div id="table-po">
						@include("pre_order.form_edit_po")
					</div>
	 				
	 				{{-- Form Lampiran --}}
	 				@include("layout.form_edit_lampiran", ["info_lampiran" => $info["pre_order"]->Lampiran])
				</div>

				<div class="card-body border-top d-flex justify-content-between"> 
					
					<div></div>
					<div>
						@if($info["pre_order"]->SKPP->id_skpp == null)
							<button class="btn btn-primary" type="button" onclick="save(2)" ><i class="fa fa-check"></i> Submit</button>
						@elseif($info["piutang"] != 0)
							<button class="btn btn-primary" type="button" onclick="save(2)" ><i class="fa fa-check"></i> Submit</button>
						@endif
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

	function addRowPO()
	{ 
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

		clone.find("select[name='produk[]']").attr("name", "new_produk[]").prop("disabled", false);
		clone.find("input[name='incoterm[]']").attr("name", "new_incoterm[]").prop("disabled", false);
		clone.find("input[name='kuantitas[]']").attr("name", "new_kuantitas[]").prop("disabled", false);
		clone.find("input[name='harga_jual[]']").attr("name", "new_harga_jual[]").prop("disabled", false); 
		clone.find("input[name='nilai[]']").attr("name", "new_nilai[]").prop("disabled", false);

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

	function addRowLampiran(){
		var clone = $("#form-parent-lampiran").find("tr:last").clone();	

		clone.find('button:last').addClass("remove-row-lampiran")
				.removeClass("btn-dark")
				.removeClass("delete-lampiran") 
				.addClass("btn-danger")
				.attr("onclick", "")
				.find('i').removeClass("fa-trash").addClass("fa-minus");

		clone.find("input[name='file[]']").attr("name", "new_file[]")
			 .parent().find(".input-group-append").addClass("d-none");
		clone.find("input[name='nama_file[]']").attr("name", "new_nama_file[]");
		clone.find("textarea[name='keterangan_file[]']").attr("name", "new_keterangan_file[]");

		clone.find("input").val("");
		clone.find("textarea").val("");

		$("#form-parent-lampiran").append(clone);
	}


	$("body").delegate(".remove-row-lampiran", "click", function(){
		var jumlah_baris = $("#form-parent-lampiran").find("tr").length;

		if(jumlah_baris == 1){
			$("#show-form-lampiran").prop("checked", false);
			$("#form-lampiran").addClass("d-none");
		}else{
			$(this).closest("tr").remove();
		}
	});

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

	$("body").delegate(".checkbox-ppn", "click", function(){
 	
		var closest = $(this).closest("tr");
		var harga_jual = closest.find(".harga-beli").val();
		var kuantitas = closest.find(".kuantitas").val(); 

		if (harga_jual != "" && harga_jual != "0,00") {
			var hasil = convertNumeric(harga_jual) * parseInt(kuantitas);

			let ppn = 0;
			if(this.checked) {
			 	ppn = hasil * 0.1;
			 	hasil = hasil + ppn;
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

		$("#total-harga").html(formatNumber(total, 2));
	}

	function save(option)
	{
		var data = new FormData();	
		data.append("_token", "{{ csrf_token() }}");
		data.append("status", option); 
		data.append("no_po", $("input[name=no_po]").val());
		data.append("produsen", $("select[name=produsen] option:selected").val());

		if($("input[name=is_lampiran]").is(":checked")){
			data.append("is_lampiran", $("input[name=is_lampiran]:checked").val())
		}
		$.each($("input[name='id_po[]']"), function(){
			data.append("id_po[]", $(this).val()); 
		});
		$.each($("select[name='produk[]'] option:selected"), function(){            
            data.append("produk[]", $(this).val()); 
        });
        $.each($("input[name='incoterm[]'"), function(){
        	data.append("incoterm[]", $(this).val())
        })
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
        })
        $.each($("input[name='new_kuantitas[]']"), function(){
        	data.append("new_kuantitas[]", $(this).val()); 
        });
        $.each($("input[name='new_harga_jual[]']"), function(){
        	data.append("new_harga_jual[]", $(this).val()); 
        }); 

		$.each($("input[name='new_nilai[]']"), function(){
			data.append("new_nilai[]", $(this).val()); 
		});
		$.each($("input[name='id_lampiran[]']"), function(i, value){
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

		$.ajax({
			url : '{{ url('pembelian/pre_order/update') }}/'+'{{ $id }}',
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
               		toastr.error(resp.message[i],{ "closeButton": true });
                } else {
               		toastr.success(resp.message, { "closeButton": true });  
               		location.href = "{{ url('pembelian/pre_order/show') }}/"+"{{ $id }}"
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
	                location.reload();
	            } else {
	                toastr.error(resp.message, { "closeButton": true });
	            }
	            loader(".modal-content", false);
	        },
	        error : function (jqXHR, exception) {
	            errorHandling(jqXHR.status, exception); 
	            loader(".modal-content", false);
	        }
	    });
	});
 
</script>

@endsection
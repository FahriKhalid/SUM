@extends('layout.index') 

@section('title', 'SUM - Edit Pengajuan Sales Order')
@section('content')

	<div class="container-fluid mt-4 mb-4">
	    <div class="row">
	        <div class="col-md-12 d-flex justify-content-between">
	        	<h6 class="m-0 font-weight-bold text-primary"><i class="fa fa-edit"></i> FORM EDIT PENGAJUAN SALES ORDER </h6>
	            <a href="{{ url("pembelian/pengajuan_so/show/".Helper::encodex($info["pengajuan_so"]->PreOrder->id_pre_order)) }}" class="text-muted"><i class="fa fa-arrow-left"></i> Kembali</a>   
	        </div>  
	    </div>  

		<div class="card mt-3"> 
			<form id="form-pengajuan-so" enctype="multipart/form-data"> 
				@csrf
				<div class="card-body" id="layout-parent"> 
					<div class="form-row ">
						<div class="form-group col-md-12">
	                        <label>Nomor Pengajuan Sales Order <span class="text-danger">*</span></label>
	                        <div class="form-group"> 
	                            <input class="form-control" disabled value="{{ $info["pengajuan_so"]->no_pengajuan_so }}" placeholder="Wajib di isi"> 
	                        </div>
	                    </div>  
					</div> 

					{{-- Purchase Order --}}
					<label>Purchase Order</label>
					<div id="table-po">
						 @include('pengajuan_so.form_table_edit')
					</div>

					{{-- Form Lampiran --}}
					@include('layout.form_edit_lampiran', ["info_lampiran" => $info["pengajuan_so"]->Lampiran])

				</div>  
				<div class="card-body border-top d-flex justify-content-between">  
					<div>
						<div class="legend bg-red"></div> Stok Habis
					</div>
					<button class="btn btn-primary"><i class="fa fa-save"></i> Simpan</button> 
				</div>
 
			</form>
		</div>
	</div>

@endsection

@section('footer')

<script type="text/javascript" src="{{ asset('js/lampiran.js') }}"></script>
<script type="text/javascript"> 

	/*
    |--------------------------------------------------------------------------
    | Function Lampiran
    |--------------------------------------------------------------------------
    */

	$("body").delegate("#show-form-lampiran", "click", function(){
		if($(this).is(":checked")){
            $("#form-lampiran").removeClass("d-none"); 
        }else{
            $("#form-lampiran").addClass("d-none");
        }
	});

	function addRowLampiran(){
		var clone = $("#form-parent-lampiran").find("tr:last").clone();	

		clone.find('button:last').addClass("remove-row-lampiran")
				.removeClass("btn-dark")
				.removeClass("delete-lampiran") 
				.addClass("btn-danger")
				.attr("onclick", "")
				.find('i').removeClass("fa-trash").addClass("fa-minus");

		clone.find("input[name='file[]']").attr("name", "new_file[]");
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
	    });
	});

	/*
    |--------------------------------------------------------------------------
    | Function pengajauan so
    |--------------------------------------------------------------------------
    */

	$(document).on("submit", "#form-pengajuan-so", function(e){
		e.preventDefault();
		var data = new FormData(this);
		submit(data);
	});

	function submit(data)
	{
		$.ajax({
			url : '{{ url('pembelian/pengajuan_so/update') }}/'+ '{{ $id }}',
			type : 'POST',
			data : data,
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
				} else if(resp.status == 'error'){
					toastr.error(resp.message,{ "closeButton": true });
				} else { 
					Swal.fire('Berhasil',resp.message,'success');

               		location.href = '{{ url('pembelian/skpp/show') }}/' + '{{ Helper::encodex($info["pengajuan_so"]->PreOrder->id_pre_order) }}';
				}

				loader(".card", false);
			},
			error : function(jqXHR, exception){
    			errorHandling(jqXHR.status, exception);
    			loader(".card", false);
    		}
		})
	}

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

	function total_harga(){
		var total = 0;
		$("#tbody-po").find("tr").each(function(){
			var nilai = $(this).find(".nilai").val();
			var maks = $(this).find(".sisa_kuantitas").val();
			var kuantitas = $(this).find("input[name='kuantitas[]']").val();

			total += convertNumeric($(this).find(".nilai").val());
		});	 

		$("#total-harga").html(formatNumber(total, 2));
	}

	$("body").delegate("input[name='kuantitas[]']", "keyup", function(){
		totalKuantitas();
 		let kuantitas = convertNumeric($(this).val());
 		let sisa = convertNumeric($(this).closest("td").find(".sisa_kuantitas").val());

 		if(kuantitas > sisa){
 			$(this).val(convertDecimal(sisa));
 			totalKuantitas();
 			alert("Jumlah kuantitas tidak boleh lebih dari sisa kuantitas");
 		}
 		
 		let closest = $(this).closest("tr");
		let harga_beli = closest.find(".harga-beli").val();

		if (harga_beli != "" && harga_beli != "0,00") {
			let hasil = 0;
			if(kuantitas > sisa) {
				hasil = convertNumeric(harga_beli) * sisa;
			} else {
				hasil = convertNumeric(harga_beli) * kuantitas;
			}
			
			// let ppn = hasil * 0.1;
			// hasil = hasil + ppn;
			closest.find(".nilai").val(formatNumber(hasil.toFixed(2), 2));
		} else {
			closest.find(".nilai").val("0,00");
		} 

		total_harga();
	}); 

	$("body").delegate(".remove-row-po", "click", function(){
		$(this).closest("tr").remove();
		total_harga();
	});

</script>

@endsection
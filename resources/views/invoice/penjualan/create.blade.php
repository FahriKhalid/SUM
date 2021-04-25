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
	    @if(PembayaranService::isBayar(Helper::decodex($id)))
	    <div class="alert alert-warning mt-3">
	    	<h4 class="alert-heading"><i class="fa fa-exclamation-circle"></i> Warning</h4>
	        Pembayaran belum ada. Tidak dapat menambahkan invoice!
	    </div>
	    @endif
		<div class="card mt-3"> 
			<nav class="alert-primary">
				<div class="nav nav-tabs nav-justified" id="nav-tab" role="tablist">
					<a class="nav-item text-dark nav-link active" id="nav-home-tab" data-toggle="tab" href="#nav-1" role="tab" aria-controls="nav-home" aria-selected="true">Dengan SO</a>
					<a class="nav-item text-dark nav-link" id="nav-home-tab" data-toggle="tab" href="#nav-2" role="tab" aria-controls="nav-home" aria-selected="true">Tanpa SO</a> 
				</div>
			</nav>
			<div class="tab-content">
				<div class="tab-pane fade show active" id="nav-1">
					@include('invoice.penjualan.form_with_so')
				</div>
				<div class="tab-pane fade" id="nav-2">
					@include('invoice.penjualan.form_without_so')
				</div>
			</div> 
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
		});
	});

	$(document).on("submit", "#form-without-so", function(e){
		e.preventDefault();

		$.ajax({
			url : '{{ url('penjualan/invoice/store_sementara') }}/'+ '{{ $id }}',
			type : 'POST',
			data : new FormData(this),
			processData : false,
			contentType : false,
			dataType : 'json',
			beforeSend: function(){
				loader(".card", true);
			},
			success : function(resp)
			{
				if(resp.status == 'error_validate'){
					for (var i = 0; i < resp.message.length; i++) {
                 		toastr.error(resp.message[i], { "closeButton": true });
                	} 
				} else if(resp.status == 'error'){
					toastr.error(resp.message, { "closeButton": true });
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

    // $("body").delegate("input[name=ppn]", "keyup", function(){
    // 	var ppn = $(this).val();
    // 	var sub_total = $("input[name=sub_total]").val();
    // 	var total = convertNumeric(ppn) + parseFloat(sub_total);

    // 	$("input[name=total]").val(formatNumber(total, 0));
    // });

    
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


    $("#table-po").delegate("input[name=ppn]", "keyup", function(){
    	let parent = $("#table-po");
    	var ppn = $(this).val();
    	var sub_total = parent.find("input[name=sub_total]").val();
    	var total = convertNumeric(ppn) + convertNumeric(sub_total);

    	parent.find("input[name=total]").val(formatNumber(total.toFixed(2), 2));
    });

    $("#table-po").delegate("input[name=sub_total]", "keyup", function(){
    	let parent = $("#table-po");
    	var sub_total = $(this).val();
    	var ppn = parent.find("input[name=ppn]").val();
    	var total = convertNumeric(sub_total) + convertNumeric(ppn);

    	parent.find("input[name=total]").val(formatNumber(total.toFixed(2), 2));
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



    /*
    |--------------------------------------------------------------------------
    | Keyup kuantitas
    |--------------------------------------------------------------------------
    */

    $("body").delegate(".kuantitas", "keyup", function(e){
    	let closest = $(this).closest("tr");
		let kuantitas = $(this).val(); 
		let sisa_kuantitas = $(this).closest("td").find(".sisa_kuantitas")
		let harga_jual = closest.find(".harga-jual").val(); 
		let jumlah_stok = sisa_kuantitas.attr("sisa");

		if(e.keyCode == 8) {
			if(kuantitas == ""){
				sisa_kuantitas.val(jumlah_stok);
			} else {
				sisa_kuantitas.val(parseInt(jumlah_stok) - parseInt(kuantitas));
			}
		} else {	  
			if(kuantitas == ""){
				sisa_kuantitas.val(jumlah_stok);
			} else {
				 
				if(parseInt(kuantitas) > parseInt(jumlah_stok)){
					alert("Kuantitas tidak boleh melebihi dari jumlah stok");
					$(this).val(jumlah_stok)
					sisa_kuantitas.val(0);
				} else {
					sisa_kuantitas.val(parseInt(jumlah_stok) - parseInt(kuantitas));
				}
				
			}
		} 
		
		if (kuantitas != "") {
			if (harga_jual != "" && harga_jual != "0,00") {
				let hasil = 0;
				if(parseInt(kuantitas) > parseInt(jumlah_stok)){
					hasil = convertNumeric(harga_jual) * parseInt(jumlah_stok);
				} else {
					hasil = convertNumeric(harga_jual) * parseInt(kuantitas);
				}
				closest.find(".nilai").html(formatNumber(hasil.toFixed(2), 2));
				closest.find(".nilai").val(formatNumber(hasil.toFixed(2), 2));
			} else {
				closest.find(".nilai").html("0,00");
				closest.find(".nilai").val("0,00");
			} 
		} else {
			closest.find(".nilai").html("0,00");
			closest.find(".nilai").val("0,00");
		}

		total_harga();
	});

	function total_harga(){
		var total = 0;
		let parent = $("#table-po");

		parent.find("tr").each(function(){
			var nilai = $(this).find(".nilai:not(input)").text();
			if(nilai != "" && nilai != "0,00"){
				total += convertNumeric($(this).find(".nilai:not(input)").text());
			}
		});	
 
		parent.find("input[name=sub_total]").val(total)

		let ppn = total * 10 / 100;
		let total_harga = total - ppn; 

		parent.find("input[name=ppn]").val(formatNumber(ppn, 2));
		parent.find("input[name=total]").val(formatNumber(total, 2));
	}
	
	$("#table-po").delegate(".remove", "click", function(){
		$(this).closest("tr").remove();
		total_harga();

		if($("#table-po").find(".remove").length < 2){
			$("#table-po").find(".remove").prop("disabled", true);
		}
	});

	function reset_table_po()
	{
		$.ajax({
			url : '{{ url('penjualan/invoice/reset_po') }}/'+'{{ $id}}',
			type : 'GET',
			dataType : 'json',
			beforeSend : function()
			{
				loader('.card', true);
			},
			success : function(resp)
			{
				$("#tbody-po-without-so").html(resp.html);
				total_harga();
				input_numeric();
				loader('.card', false);
			},
			error : function(jqXHR, exception){
				errorHandling(jqXHR.status, exception);
                loader(".card", false);
			}
		});
	}
</script>

@endsection
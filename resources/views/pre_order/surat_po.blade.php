<!DOCTYPE html>
<html>
<head>
	<title></title>
</head>

<style type="text/css"> 

	.container {
		padding-left: 20px;
		padding-right: 20px;
	}

	.container-fluid {
		padding: 30px; 
	}

	.text-center {
		text-align:center!important
	}

	table {
		width: 100%;
		border-collapse: collapse;
	}
 

	.table th, .table td{
		border: 1px solid black; 
	}	

	.lampiran th, .lampiran td {
		vertical-align: top !important; 
	}

	.table td {
		padding: 5px;
	}

	.mt-25{
		margin-top: 25px;
	}

	.mt-20{
		margin-top: 20px;
	}

	.mt-15{
		margin-top: 15px;
	}

	.mt-10{
		margin-top: 10px;
	}

	.ml-3 {
		margin-left: 3px;
	}

	.border-none {
		border: none !important;
	}

	.text-green-moss {
		color: #103900 !important;
	}

	.verdana {
		font-family: Verdana, sans-serif;
	}

	.text-grey {
		color: #a7a7a7;
	}

	footer {
        position: fixed; 
        bottom: 20px; 
        left: 0px; 
        right: 20px;
        height: 50px; 
        text-align: right;
        color: #a7a7a7;
    }

    .page-break {
	    page-break-after: always;
	} 

	body { 
		background-image: url("img/background_document.png"); 
	  	height: 100%; 
	  	background-position: center;
	  	background-repeat: no-repeat;
	  	background-size: cover;
	  	font-size: 15px;
	}

	@page {
        margin: 0px 0px 0px 0px !important;
        padding: 0px 0px 0px 0px !important;
    }

</style>

<body>
	<div class="container-fluid">
		<div class="text-dark" style="display: flex; align-items: center;">
			<img src="{{ public_path('img/logo_perusahaan_1.png')}}" width="40px">
			<div style="margin-left: 50px">
				PT SETIAGUNG USAHA MANDIRI
			</div>
		</div> 
		<div class="text-center">
			<b><u>SURAT PRE-ORDER</u></b>
			<div>{{ Helper::dateIndo(date('Y-m-d')) }}</div>
		</div>

		<div class="container">
			<table class="mt-15 lampiran">
				<thead>
					<tr>
						<td width="100px">Nomor</td>
						<td width="1px">:</td>
						<td><span style="margin-left: 3px;">{{ $info["pre_order"]->no_po }}</span></td>
					</tr>
					<tr>
						<td>Lampiran</td>
						<td>:</td>
						<td>
							@if(count($info["lampiran"]) > 0 && count($info["lampiran"]) > 1)
							<ol style="margin-left: -20px; margin-bottom: 0px !important; margin-top:0px !important"> 
								@foreach($info["lampiran"] as $lampiran)
								  	<li>{{ $lampiran->nama }}</li>
								@endforeach
							</ol>
							@elseif(count($info["lampiran"]) == 1)
							<span style="margin-left: 3px;">{{ $info["lampiran"][0]->nama }}</span>
							@else
							<span style="margin-left: 3px;">-</span>
							@endif
						</td>
					</tr>
				</thead>
			</table>

			<div class="ml-3 mt-25">
				Kepada Yth.
				<br>
				<b>{{ $info["pre_order"]->Produsen->perusahaan }}</b>
				<br>
				{{ $info["pre_order"]->Produsen->nama }}
			</div>


			<div class="ml-3 mt-20">Dengan hormat</div>

			<div class="ml-3 mt-20" align="justify">
				Menindak lanjuti mengenai penawaran harga, berikut ini PO barang pupuk :
			</div>

			<table class="ml-3 mt-20 table">
				<thead>
					<tr>
						<th align="center">Jenis Pupuk</th>
						<th align="center">Harga per ton</th>
						<th align="center">Kuantum</th>
						<th align="center">Incoterm</th> 
					</tr>
				</thead>
				<tbody>
					@php($total = 0)
					@foreach($info["po"] as $po) 
					{{-- <?php  
					$ppn_harga = $po->harga_jual * 0.1;
					$harga = $po->harga_jual + $ppn_harga
					?> --}}
					<tr>
						<td align="center">{{ $po->Produk->nama }}</td> 
						<td align="center">{{ Helper::currency($po->harga_jual) }}</td>
						<td align="center">{{ $po->kuantitas }} MT</td> 
						<td align="center">{{ $po->incoterm }}</td>  
					</tr> 
					@endforeach
				</tbody> 
			</table>
 

			<p align="justify" class="ml-3">
				Demikian surat ini kami buat dengan harapan terjalin kerjasama yang baik. Atas perhatian dan
				kerjasamanya kami ucapkan terima kasih.
			</p> 
		</div>
	</div>

	<div class="container-fluid" style="position: absolute; bottom: 100px">
		<div class="container ttd verdana ml-3"> 
			<div class="text-green-moss"><b>{{ $info["profil_perusahaan"]->nama }}</b> </div> 
			<br><br><br><br><br><br>
			<div><b><u>{{ $info["profil_perusahaan"]->direktur }}</u></b></div>
			<div><b>Direktur</b></div>
		</div>
	</div>


	<footer>
		<div>{{ $info["profil_perusahaan"]->alamat }}</div>
		<div>{{ $info["profil_perusahaan"]->telepon }}</div>
		<div>{{ $info["profil_perusahaan"]->email }}</div>
	</footer>
</body>
</html>
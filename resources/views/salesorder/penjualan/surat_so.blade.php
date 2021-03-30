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

		
		<div class="text-center" style="margin-top: -20px">
			<b><u>SALES ORDER & DO</u></b>
			<div>{{ Helper::dateIndo(date('Y-m-d')) }}</div>
		</div>

		<div class="container">
			<table class="mt-25">
				<thead>
					<tr>
						<td width="100px">Nomor</td>
						<td width="1px">:</td>
						<td>{{ $info["so"]->no_so }}</td>
					</tr>
					<tr>
						<td>Lampiran</td>
						<td>:</td>
						<td>-</td>
					</tr>
				</thead>
			</table>

			<div class="ml-3 mt-25">
				Kepada Yth.
				<br>
				<b>{{ $info["so"]->SKPP->Customer->perusahaan }}</b>
				<br>
				{{ $info["so"]->SKPP->Customer->nama }}
			</div>


			<div class="ml-3 mt-20">Dengan hormat</div>

			<div class="ml-3 mt-20" align="justify">
				Bersama ini kami sampaikan rincian pembelian sebagai berikut :
			</div>

			<table class="ml-3 mt-20 table">
				<thead>
					<tr>
						<th align="center">JENIS PUPUK</th>
						<th align="center">SPESIFIKASI</th>
						<th align="center">KUANTUM</th>
						<th align="center">INCOTERM</th>
						<th align="center">DOKUMEN</th>
					</tr>
				</thead>
				<tbody>
					@php
					$total = 0;
					@endphp
					@foreach($info["sopo"] as $sopo)

					@php
						$total += $sopo->kuantitas; 
					@endphp

					<tr>
						<td align="center">{{ $sopo->Barang->Produk->nama }}</td>
						<td align="center">{{ $sopo->Barang->Produk->spesifikasi }}</td>
						<td align="center">{{ $sopo->kuantitas }} MT </td> 
						<td align="center">{{ $sopo->Barang->incoterm }}</td>
						<td align="center">{{ substr($sopo->SO->SKPP->no_skpp, 0, 4) }}</td> 
					</tr>

					@endforeach

					<tr>
						<td colspan="2" align="right"><b>TOTAL</b></td> 
						<td align="center"> {{ $total }} MT</td> 
						<td class="border-none"></td>
						<td class="border-none"></td>
					</tr>
				</tbody> 
			</table>
	 

			<table class="ml-3 mt-15">
				<thead>
					<tr>
						<td width="200px">Alat angkut</td>
						<td width="10px">:</td>
						<td>{{ $info["so"]->SupirAktif[0]->Supir->kendaraan }}</td>
					</tr>
					<tr>
						<td>Tujuan</td>
						<td>:</td>
						<td>{{ $info["so"]->tujuan }}</td>
					</tr> 
				</thead>
			</table>
			<div style="margin-left: 5px;">Penanggung jawab</div>
			<table style="margin-left: 80px">
				<thead>
					<tr>
						<td width="1px">-</td>
						<td width="116px">Nama supir</td>
						<td width="10px">:</td>
						<td>{{ $info["so"]->SupirAktif[0]->Supir->nama == null ? "-" : $info["so"]->SupirAktif[0]->Supir->nama }}</td>
					</tr>
					<tr>
						<td>-</td>
						<td>No Truck</td>
						<td>:</td>
						<td>{{ $info["so"]->SupirAktif[0]->Supir->plat_nomor == null ? "-" : $info["so"]->SupirAktif[0]->Supir->plat_nomor }}</td>
					</tr> 
					<tr>
						<td>-</td>
						<td>No HP</td>
						<td>:</td>
						<td>{{ $info["so"]->SupirAktif[0]->Supir->no_telepon == null ? "-" : $info["so"]->SupirAktif[0]->Supir->no_telepon }}</td>
					</tr> 
				</thead>
			</table>


			<p align="justify" class="ml-3">
				Produk tersebut mohon diambil di lokasi yang sudah tertera paling lambat {{ Helper::dateIndo($info["so"]->SKPP->batas_akhir_pengambilan) }}
			</p> 
			<p align="justify" class="ml-3">
				Demikian kami sampaikan, atas perhatiannya diucapkan terima kasih.
			</p> 
		</div> 
	</div> 

	<div class="container-fluid" style="position: absolute; bottom: 100px">
		<div class="container ttd verdana ml-3"> 
			<div class="text-green-moss"><b>{{ $info["profil_perusahaan"]->nama }}</b> </div> 
			<br><br><br><br><br>
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
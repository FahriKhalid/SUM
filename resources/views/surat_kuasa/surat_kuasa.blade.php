<!DOCTYPE html>
<html>
<head>
	<title></title>
</head>

<style type="text/css">
	
	body {
	    font-family: Verdana, sans-serif;
   		font-size: 12px !important;
	}

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
			<b><u>SURAT KUASA</u></b>
			<div>{{ Helper::dateIndo(date('Y-m-d')) }}</div>
		</div>

		<div class="container">
			<table class="mt-25">
				<thead>
					<tr>
						<td width="100px">Nomor</td>
						<td width="1px">:</td>
						<td>{{ $info["surat_kuasa"]->no_sk }}</td>
					</tr>
					<tr>
						<td>Lamp</td>
						<td>:</td>
						<td>-</td>
					</tr>
					<tr>
						<td>Hal</td>
						<td>:</td>
						<td><b><u>Surat Kuasa</u></b></td>
					</tr>
				</thead>
			</table>

			<div class="ml-3 mt-25">
				<div>Kepada Yth,</div>
				<div class="mt-15">
					<b>PT Pupuk Kalimantan Timur</b>
				</div> 
				<div>Kepala Gudang {{ $info["surat_kuasa"]->Gudang->nama }}</div>
				<div>{{ $info["surat_kuasa"]->Gudang->alamat }}</div>
			</div>


			<div class="ml-3 mt-20">Dengan hormat,</div>
			 
			<div style="margin-left: 4px;" class="mt-15">Yang bertanda tangan dibawah ini :</div> 
			<table class="ml-3">
				<thead>
					<tr>
						<td width="200px">Nama</td>
						<td width="10px">:</td>
						<td>{{ Helper::profil_perusahaan()->direktur }}</td>
					</tr>
					<tr>
						<td>Jabatan</td>
						<td>:</td>
						<td>Direktur</td>
					</tr> 
					<tr>
						<td width="200px">Nama Perusahaan</td>
						<td width="10px">:</td>
						<td>{{ Helper::profil_perusahaan()->nama }}</td>
					</tr> 
				</thead>
			</table>


			<div style="margin-left: 4px;" class="mt-15">Dengan ini memberikan Kuasa kepada :</div> 
			<table class="ml-3">
				<thead>
					<tr> 
						<td width="200px">Nama</td>
						<td width="10px">:</td>
						<td>{{ $info["surat_kuasa"]->Supir->nama == null ? "-" : $info["surat_kuasa"]->Supir->nama }}</td>
					</tr>
					<tr> 
						<td>Plat Nomor</td>
						<td>:</td>
						<td>{{ $info["surat_kuasa"]->Supir->plat_nomor == null ? "-" : $info["surat_kuasa"]->Supir->plat_nomor }}</td>
					</tr> 
					<tr> 
						<td>Phone</td>
						<td>:</td>
						<td>{{ $info["surat_kuasa"]->Supir->no_telepon == null ? "-" : $info["surat_kuasa"]->Supir->no_telepon }}</td>
					</tr> 
				</thead>
			</table>
			

			<div style="margin-left: 4px;" class="mt-15">
			 	Bertindak sebagai  penerima Kuasa untuk melakukan pengambilan pupuk di Gudang PT Pupuk Kalimantan Timur dengan ketentuan sebagai berikut:
			</div>

			<table class="ml-3 mt-20 table">
				<thead>
					<tr>
						<th align="center">Nama Barang</th>
						<th align="center">Kuantum</th>
						<th align="center">Nomor SO</th>
						<th align="center">Pengambilan</th> 
						<th align="center">Nomor DO</th>
					</tr>
				</thead>
				<tbody>
					@php($total = 0)
					@foreach($info["skso"] as $skso)
					@php($total += $skso->nilai)
					<tr>
						<td align="center">{{ $skso->SOPO->Barang->Produk->nama }}</td>  
						<td align="center">{{ $skso->kuantitas }} MT</td> 
						<td align="center">{{ $skso->SOPO->SO->no_so }}</td> 
						<td align="center">{{ $info["surat_kuasa"]->Gudang->nama }}</td> 
						<td align="center">{{ $info["surat_kuasa"]->SO->no_so_pengambilan }}</td>
					</tr> 
					@endforeach
				</tbody> 
			</table>
		</div>
	</div>

	<div class="container-fluid" style="position: absolute; bottom: 100px">
		<div class="container ttd verdana ml-3"> 
			<div class="text-green-moss"><b>{{ $info["profil_perusahaan"]->nama }}</b> </div> 
			<br><br>
			<img src="{{ public_path('img/ttd.png') }}" width="120px">
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
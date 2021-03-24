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
				PT. SETIAGUNG USAHA MANDIRI
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
						<td><span style="margin-left: 3px;">{{ $info["pengajuan_so"]->no_pengajuan_so }}</span></td>
					</tr>
					<tr>
						<td>Lampiran</td>
						<td>:</td>
						<td>
							{{-- @if(count($info["lampiran"]) > 0 && count($info["lampiran"]) > 1)
							<ol style="margin-left: -20px; margin-bottom: 0px !important; margin-top:0px !important"> 
								@foreach($info["lampiran"] as $lampiran)
								  	<li>{{ $lampiran->nama }}</li>
								@endforeach
							</ol>
							@elseif(count($info["lampiran"]) == 1)
							<span style="margin-left: 3px;">{{ $info["lampiran"][0]->nama }}</span>
							@else
							<span style="margin-left: 3px;">-</span>
							@endif --}}
							-
						</td>
					</tr>
				</thead>
			</table>

			<div class="ml-3 mt-25">
				Kepada Yth.
				<br>
				<b>{{ $info["pengajuan_so"]->PreOrder->Produsen->perusahaan }}</b>
				<br>
				{{ $info["pengajuan_so"]->PreOrder->Produsen->nama }}
			</div>


			<div class="ml-3 mt-20">Dengan hormat</div>

			<div class="ml-3 mt-20" align="justify">
				Menindak lanjuti mengenai mengenai SKPP dari Pupuk Kaltim dengan nomor : {{ $info["pengajuan_so"]->PreOrder->SKPP->no_skpp }}, kami akan melakukan penebusan barang sebagai berikut:
			</div>

			<table class="ml-3 mt-20 table">
				<thead>
					<tr>
						<th align="center">JENIS PRODUK</th>
						<th align="center">INCOTERM</th> 
						<th align="center">KUANTUM</th>
						<th align="center">HARGA + PPN</th>
					</tr>
				</thead>
				<tbody>
					@php($total = 0)
					@foreach($info["pengajuan_so"]->BarangPengajuanSo as $barang)
					{{-- @php($total += $barang->nilai) --}}
					<?php 
					$nilai = Helper::PPN($barang->harga_jual) * $barang->kuantitas;
					$total += $nilai;
					?>
					
					@if($barang->kuantitas > 0)
					<tr>
						<td align="center">{{ $barang->Produk->nama }}</td> 
						<td align="center">{{ $barang->Barang->incoterm }}</td> 
						<td align="center">{{ $barang->kuantitas }} MT</td> 
						<td align="center">{{ Helper::currency(Helper::toFixed(Helper::PPN($barang->harga_jual), 2)) }}</td>
					</tr>
					@endif

					@endforeach
				</tbody>
				<tfoot>
					<tr>
						<td class="border-none"></td> 
						<td class="border-none"></td> 
						<td align="center"><b>TOTAL</b></td>
						<td align="center">{{ Helper::currency(Helper::toFixed($total, 2)) }}</td>
					</tr>
				</tfoot>
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
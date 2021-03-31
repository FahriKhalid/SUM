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

	.table {
		width: 100%;
		border-collapse: collapse;
	}

	.table-borderless tr, .table-borderless td{
		border: none !important; 
		padding: 0px !important;
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
 	   
	.inline-block {
	  display: inline-block;
	}

	.float-right {
		float: right;
		width: 50%;
	}

	.float-left {
		float: left;
		width: 50%;
	}

	.w-100 {
		width: 100%;
	}

	.table-borderless tr .table-borderless td {
		border: none !important;
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
			<b><u>FAKTUR PENJUALAN</u></b>
			<div>{{ Helper::dateIndo(date('Y-m-d')) }}</div>
		</div>


 
		<div class="container">
			<div class="d-flex" style="width: 100%;">
				<div class="inline-block float-left">
					<div class="ml-3 mt-25">
						Kepada Yth.
						<br>
						<b>{{ $info["invoice"]->SKPP->Customer->perusahaan }}</b>
						<br>
						{{ $info["invoice"]->SKPP->Customer->alamat }}
					</div>
				</div>
 				<div class="inline-block float-right">
 					<table class="mt-25">
						<thead>
							<tr>
								<td width="150px">Tanggal</td>
								<td width="1px">:</td>
								<td>{{ Helper::dateIndo(date('Y-m-d')) }}</td>
							</tr>
							<tr>
								<td>Nomor Tagihan</td>
								<td>:</td>
								<td>{{ $info["invoice"]->no_tagihan }}</td>
							</tr>
							<tr>
								<td>Nomor Faktur Pajak</td>
								<td>:</td>
								<td>{{ $info["invoice"]->no_faktur_pajak == null ? '-' : $info["invoice"]->no_faktur_pajak}}</td>
							</tr>
							<tr>
								<td>Nomor Sales Order</td>
								<td>:</td>
								<td>{{ $info["invoice"]->SO->no_so }}</td>
							</tr>
						</thead>
					</table> 
 				</div> 
			</div>
			<br><br><br><br><br><br>
 
			<table class="ml-3 mt-20 table">
				<thead>
					<tr>
						<th>NO</th>
						<th align="center">PRODUK</th> 
						<th align="center">KUANTUM</th>
						<th align="center">INCOTERM</th>
						<th align="center">HARGA JUAL</th>
						<th align="center">JUMLAH</th>
					</tr>
				</thead>
				<tbody>
					@php
					$sub_total = 0;
					@endphp
					@foreach($info["invoice"]->SO->SOPO as $sopo)

					@php
						$sub_total += (($sopo->Barang->harga_jual / 1.1) * $sopo->kuantitas); 
					@endphp

					<tr>
						<td align="center">{{ $loop->iteration }}</td>
						<td align="center">{{ $sopo->Barang->Produk->nama }}</td> 
						<td align="center">{{ $sopo->kuantitas }} MT </td> 
						<td align="center">{{ $sopo->Barang->incoterm }}</td>
						<td align="center">
							<table class="w-100 table-borderless">
								<tr>
									<td>
										<span>IDR</span>
									</td>
									<td align="right">
										<span>{{ Helper::currency(Helper::toFixed((floor(($sopo->Barang->harga_jual / 1.1))) ,2)) }}</span>
									</td>
								</tr>
							</table>
						</td> 
						<td align="center">
							<table class="w-100 table-borderless">
								<tr>
									<td>
										<span>IDR</span>
									</td>
									<td align="right">
										<span>{{ Helper::currency(Helper::toFixed(floor((($sopo->Barang->harga_jual / 1.1) * $sopo->kuantitas)) ,2)) }}</span>
									</td>
								</tr>
							</table> 
						</td> 
					</tr>

					@endforeach

					<tr>
						<td></td>
						<td colspan="4">
							{{-- <div><b>SUB TOTAL</b></div>
							<div><b>Dasar Pengenaan Pajak (DPP)</b></div>
							<div><b>PPN</b></div>
							<div><b>TOTAL</b></div> --}}
							<div class="w-100">
								<table class="w-100 table-borderless">
									<tr>
										<td>
											<b>SUB TOTAL</b>
										</td> 
									</tr>
									<tr>
										<td>
											<b>Dasar Pengenaan Pajak (DPP)</b>
										</td> 
									</tr>
									<tr>
										<td>
											<b>PPN</b>
										</td> 
									</tr>
									<tr>
										<td>
											<b>TOTAL</b>
										</td> 
									</tr>
								</table>
							</div>
						</td> 
						<td> 
							<div class="w-100">
								<table class="w-100 table-borderless">
									<tr>
										<td>
											<span>IDR</span>
										</td>
										<td align="right">
											<span>{{ Helper::currency(Helper::toFixed(floor($sub_total) ,2)) }}</span>
										</td>
									</tr>
								</table>
							</div>
							<div class="w-100">
								<table class="w-100 table-borderless">
									<tr>
										<td>
											<span>IDR</span>
										</td>
										<td align="right">
											<span>{{ Helper::currency(Helper::toFixed(floor($sub_total) ,2)) }}</span>
										</td>
									</tr>
								</table> 
							</div>
							<div class="w-100">
								<table class="w-100 table-borderless">
									<tr>
										<td>
											<span>IDR</span>
										</td>
										<td align="right">
											<span>{{ Helper::currency(Helper::toFixed(floor($info["invoice"]->ppn) ,2)) }}</span>
										</td>
									</tr>
								</table> 
							</div>
							<div class="w-100">
								<table class="w-100 table-borderless">
									<tr>
										<td>
											<span>IDR</span>
										</td>
										<td align="right">
											<span>{{ Helper::currency(Helper::toFixed(floor($info["invoice"]->total) ,2)) }}</span>
										</td>
									</tr>
								</table>
							</div>
						</td>  
					</tr>
				</tbody> 
			</table>
	  
			<p align="justify">
				Terbilang <b style="margin-left: 50px">#{{ Helper::penyebut(floor($info["invoice"]->total)) }} Rupiah</b>
			</p>  

			<p>Pelunasan ke rekening kami nomor:</p>
			<ol style="margin-top: -10px;">
				@foreach($info["invoice"]->SKPP->SKPPATM as $atm)
					@php($atm = $atm->ATM->nama .', No. '.$atm->ATM->nomor)
				 	<li>{!! $atm !!}</li>
				@endforeach
			</ol>

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
@extends('surat.index')
@section('content')

@section('title')
<b><u>FAKTUR PENJUALAN</u></b>
@endsection 

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
					<div class="w-100">
						<table class="w-100 table-borderless">
							<tr>
								<td><b>SUB TOTAL</b></td> 
							</tr>
							<tr>
								<td><b>Dasar Pengenaan Pajak (DPP)</b></td> 
							</tr>
							<tr>
								<td><b>PPN</b></td> 
							</tr>
							<tr>
								<td><b>TOTAL</b></td> 
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
@endsection
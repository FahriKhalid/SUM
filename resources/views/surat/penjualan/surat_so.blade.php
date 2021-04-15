@extends('surat.index')
@section('content')
 
@section('title')
<b><u>SALES ORDER & DO</u></b>
@endsection

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

@endsection
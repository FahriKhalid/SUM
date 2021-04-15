@extends('surat.index')
@section('content')

@section('title')
<b><u>SURAT KUASA</u></b>
@endsection

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

@endsection
@extends('surat.index')
@section('content')

@section('title')
<b><u>SURAT PENGAJUAN SO</u></b>
@endsection

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
					@if(count($info["pengajuan_so"]->Lampiran) > 0 && count($info["pengajuan_so"]->Lampiran) > 1)
					<ol style="margin-left: -20px; margin-bottom: 0px !important; margin-top:0px !important"> 
						@foreach($info["pengajuan_so"]->Lampiran as $lampiran)
						  	<li>{{ $lampiran->nama }}</li>
						@endforeach
					</ol>
					@elseif(count($info["pengajuan_so"]->Lampiran) == 1)
					<span style="margin-left: 3px;">{{ $info["pengajuan_so"]->Lampiran[0]->nama }}</span>
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
				<th align="center">SPESIFIKASI</th>
				<th align="center">INCOTERM</th> 
				<th align="center">KUANTUM</th>
				<th align="center">HARGA</th>
			</tr>
		</thead>
		<tbody>
			@php($total = 0)
			@foreach($info["pengajuan_so"]->BarangPengajuanSo as $barang)
			{{-- @php($total += $barang->nilai) --}}
			<?php 
			$nilai = $barang->harga_jual * $barang->kuantitas;
			$total += $nilai;
			?>
			
			@if($barang->kuantitas > 0)
			<tr>
				<td align="center">{{ $barang->Produk->nama }}</td> 
				<td align="center">{{ $barang->Produk->spesifikasi }}</td> 
				<td align="center">{{ $barang->Barang->incoterm }}</td> 
				<td align="center">{{ $barang->kuantitas }} MT</td> 
				<td align="center">{{ Helper::currency(Helper::toFixed($barang->harga_jual, 2)) }}</td>
			</tr>
			@endif

			@endforeach
		</tbody>
		<tfoot>
			<tr>
				<td class="border-none"></td> 
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
		
@endsection
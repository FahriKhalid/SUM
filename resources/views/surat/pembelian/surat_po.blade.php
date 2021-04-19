@extends('surat.index')
@section('content')

@section('title')
<b><u>SURAT PRE-ORDER</u></b>
@endsection

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
					@if(count($info["pre_order"]->Lampiran) > 0 && count($info["pre_order"]->Lampiran) > 1)
					<ol style="margin-left: -20px; margin-bottom: 0px !important; margin-top:0px !important"> 
						@foreach($info["pre_order"]->Lampiran as $lampiran)
						  	<li>{{ $lampiran->nama }}</li>
						@endforeach
					</ol>
					@elseif(count($info["pre_order"]->Lampiran) == 1)
					<span style="margin-left: 3px;">{{ $info["pre_order"]->Lampiran[0]->nama }}</span>
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
				<th align="center">JENIS PUPUK</th>
				<th align="center">SPESIFIKASI</th>
				<th align="center">HARGA PER TON</th>
				<th align="center">KUANTUM</th>
				<th align="center">INCOTERM</th> 
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
				<td align="center">{{ $po->Produk->spesifikasi }}</td> 
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
		
@endsection
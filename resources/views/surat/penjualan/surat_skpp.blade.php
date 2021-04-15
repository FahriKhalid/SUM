@extends('surat.index')
@section('content')

@section('title')
<b><u>SURAT KONFIRMASI PERSETUJUAN PEMBELIAN</u></b>
@endsection

<div class="container">
	<table class="mt-15">
		<thead>
			<tr>
				<td width="100px">Nomor</td>
				<td width="1px">:</td>
				<td>{{ $info["skpp"]->no_skpp }}</td>
			</tr>
			<tr>
				<td>Lampiran</td>
				<td>:</td>
				<td>
					@if(count($info["lampiran"]) > 0)
					<ol style="margin-left: -25px;"> 
						@foreach($info["lampiran"] as $lampiran)
						  	<li>{{ $lampiran->nama }}</li>
						@endforeach
					</ol>
					@else
					-
					@endif
				</td>
			</tr>
		</thead>
	</table>

	<div class="ml-3 mt-25">
		Kepada Yth.
		<br>
		<b>{{ $info["skpp"]->Customer->perusahaan }}</b>
		<br>
		{{ $info["skpp"]->Customer->nama }}
	</div>

	<div class="ml-3 mt-20">Dengan hormat</div>

	<div class="ml-3 mt-20" align="justify">
		Sesuai Komitmen tentang permintaan pembelian pupuk non subsidi untuk memenuhi kebutuhan
		kebun sendiri, bersama ini disampaikan bahwa kami dapat menyediakan kebutuhan tersebut
		dengan perincian sebagai berikut
	</div>

	<table class="ml-3 mt-20 table">
		<thead>
			<tr>
				<th align="center">PRODUK</th>
				<th align="center">INCOTERM</th>
				<th align="center">KUANTITAS</th>
				<th align="center">HARGA JUAL</th>
				<th align="center">NILAI</th>
			</tr>
		</thead>
		<tbody>
			@php($total = 0)
			@foreach($info["po"] as $po)
			@php($total += $po->nilai)
			<tr>
				<td align="center">{{ $po->Produk->nama }}</td> 
				<td align="center">{{ $po->incoterm }}</td> 
				<td align="center">{{ $po->kuantitas }} MT</td> 
				<td align="center">{{ Helper::currency($po->harga_jual) }}</td> 
				<td align="center">{{ Helper::currency($po->nilai) }}</td> 
			</tr> 
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

	<table class="ml-3 mt-15">
		<thead>
			<tr>
				<td width="200px">Syarat Penyerahan</td>
				<td width="1px">:</td>
				<td>{{ $info["skpp"]->syarat_penyerahan == null ? '-' : $info["skpp"]->syarat_penyerahan }}</td>
			</tr>
			<tr>
				<td>Batas Akhir Pembayaran</td>
				<td>:</td>
				<td>{{ $info["skpp"]->terakhir_pembayaran == null ? '-' : Helper::dateIndo($info["skpp"]->terakhir_pembayaran) }}</td>
			</tr>
			<tr>
				<td>Batas Akhir Pengambilan</td>
				<td>:</td>
				<td>{{ $info["skpp"]->batas_akhir_pengambilan == null ? '-' : Helper::dateIndo($info["skpp"]->batas_akhir_pengambilan) }}</td>
			</tr>
		</thead>
	</table>

	<p align="justify" class="ml-3">
		Pembayaran secara tunai senilai Rp <b>{{ Helper::currency($total) }},-</b> ({{Helper::penyebut($total)}}) untuk dapat dilaksanakan sebelum pengambilan barang, ke rekening PT Setiagung
		Usaha Mandiri di 
		@foreach($info["skpp"]->SKPPATM as $atm)
		@php($atm = $atm->ATM->nama .' No. <b>'.$atm->ATM->nomor .'</b>/')
		 	@if($loop->last)
		        {!!str_replace("/", "", $atm)!!}
		    @else
		    	{!!$atm!!}
		    @endif 
		@endforeach
		.
	</p>

	<p align="justify" class="ml-3">
		Agar disampaikan berita acara serah terima setelah tiba di gudang penerima. Pupuk ini sama
		sekali tidak diperbolehkan untuk diekspor, pelanggaran terhadap ketentuan ini akan mendapat
		sanksi yang ditetapkan secara sepihak oleh {{ $info["profil_perusahaan"]->nama }} dan atau sanksi hukum
		sesuai undang-undang/peraturan pemerintah yang berlaku yang harus ditanggung oleh Pembeli.
	</p>

	<p align="justify" class="ml-3">
		Ketentuan harga dan tonase dalam surat ini tidak mengikat lagi apabila pembayaran dan
		pengambilan pupuk tidak dilakukan sebelum jadwal penyerahan diatas. Adapun ketentuan lain
		akan diatur dalam Surat Perjanjian / Kontrak Jual Beli antara perusahaan Saudara dengan PT
		Setiagung Usaha Mandiri.
	</p>

	<p align="justify" class="ml-3">
		Demikian surat ini kami buat dengan harapan terjalin kerjasama yang baik. Atas perhatian dan
		kerjasamanya kami ucapkan terima kasih.
	</p> 
</div>

@endsection
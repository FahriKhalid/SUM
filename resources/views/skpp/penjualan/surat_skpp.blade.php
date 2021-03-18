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
			<b><u>SURAT KONFIRMASI PERSETUJUAN PEMBELIAN</u></b>
			<div>{{ Helper::dateIndo(date('Y-m-d')) }}</div>
		</div>

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
						<td align="center">FOT {{ $po->incoterm }}</td> 
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
						<td>Jadwal Penyerahan</td>
						<td>:</td>
						<td>{{ $info["skpp"]->jadwal_penyerahan == null ? '-' : $info["skpp"]->jadwal_penyerahan }}</td>
					</tr>
					<tr>
						<td>Batas AKhir Pengambilan</td>
						<td>:</td>
						<td>{{ $info["skpp"]->batas_akhir_pengambilan == null ? '-' : Helper::dateIndo($info["skpp"]->batas_akhir_pengambilan) }}</td>
					</tr>
				</thead>
			</table>

			<p align="justify" class="ml-3">
				Pembayaran secara tunai senilai Rp <b>{{ Helper::currency($total) }},-</b> ({{Helper::penyebut($total)}}) untuk dapat dilaksanakan sebelum pengambilan barang, ke rekening PT. Setiagung
				Usaha Mandiri di {{ $info["skpp"]->ATM->nama }} No. <b>{{ $info["skpp"]->ATM->nomor }}</b>.
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
				akan diatur dalam Surat Perjanjian / Kontrak Jual Beli antara perusahaan Saudara dengan PT.
				Setiagung Usaha Mandiri.
			</p>

			<p align="justify" class="ml-3">
				Demikian surat ini kami buat dengan harapan terjalin kerjasama yang baik. Atas perhatian dan
				kerjasamanya kami ucapkan terima kasih.
			</p>

			<br>
			<br> 

			<div class="ttd verdana ml-3">
				
				<div class="text-green-moss"><b>{{ $info["profil_perusahaan"]->nama }}</b> </div>

				<br><br><br><br>

				<div><b><u>{{ $info["profil_perusahaan"]->direktur }}</u></b></div>
				<div><b>Direktur</b></div>
			</div>
		</div>
	</div>


	<footer>
		<div>{{ $info["profil_perusahaan"]->alamat }}</div>
		<div>{{ $info["profil_perusahaan"]->telepon }}</div>
		<div>{{ $info["profil_perusahaan"]->email }}</div>
	</footer>
</body>
</html>
@php
$total = 0;
@endphp
@foreach($info["po"] as $barang)
@php($kuantitas = \App\Services\SoService::sisaKuantitasPO($barang->id_barang))
@php($harga_jual = Helper::currency(Helper::toFixed(($barang->harga_jual / 1.1) ,2)))
@php($total += \App\Services\SoService::sisaKuantitasPO($barang->id_barang)) 

<tr class="{{ \App\Services\SoService::sisaKuantitasPO($barang->id_barang) == 0 ? 'bg-red' : '' }}">
	<td>{{ $loop->iteration }}.</td>
	<td>{{ $barang->Produk->nama }}</td>
	<td>{{ $barang->Produk->spesifikasi }}</td>
	<td class="p-1">
		<input type="hidden" name="id_barang[]" value="{{ Helper::encodex($barang->id_barang) }}">
		<div class="d-flex">
			<div class="input-group">
				<input type="text" disabled class="form-control number sisa_kuantitas" sisa="{{ $kuantitas }}" value="{{ $kuantitas }}">
				<div class="input-group-append">
					<span class="input-group-text">MT</span>
				</div>
			</div>

			<div class="ml-2 mr-2 align-self-center">
				<i class="fa fa-arrow-right"></i>
			</div>

			<div class="input-group">
				<input type="text" name="kuantitas[]" autocomplete="off" class="form-control number kuantitas" value="0">
				<div class="input-group-append">
					<span class="input-group-text">MT</span>
				</div>
			</div>
		</div>  
	</td> 
	<td>
		<div class="d-flex justify-content-between">
			<div>IDR</div>
			<div>{{ $harga_jual }}</div>
			<input type="hidden" name="harga_jual[]" class="harga-jual" value="{{ $harga_jual }}">
		</div>
	</td>
	<td>
		<div class="d-flex justify-content-between">
			<div>IDR</div>
			<div class="nilai">0,00</div>
			<input type="hidden" name="nilai[]" class="nilai">
		</div>
	</td> 
	<td>
		<button class="btn btn-danger btn-sm remove">
			<i class="fa fa-minus"></i>
		</button>
	</td>
</tr> 
@endforeach
<tr>
	<td colspan="5" align="right"><b>SUB TOTAL</b></td>  
	<td class="p-1">
		<div class="input-group">
			<div class="input-group-prepend">
				<span class="input-group-text">IDR</span>
			</div>  
			<input type="text" id="sub-total-without-so" class="form-control numeric" value="0" name="sub_total">
		</div>
	</td>
	<td></td>
</tr>
<tr>
	<td colspan="5" align="right"><b>PPN</b></td> 
	<td class="p-1">
		<div class="input-group">
			<div class="input-group-prepend">
				<span class="input-group-text">IDR</span>
			</div>  
			<input type="text" id="ppn-without-so" class="form-control numeric" value="0" name="ppn">
		</div>
	</td>
	<td></td>
</tr>
<tr> 
	<td colspan="5" align="right"><b>TOTAL</b></td> 
	<td class="p-1"> 
		<div class="input-group">
			<div class="input-group-prepend">
				<span class="input-group-text">IDR</span>
			</div> 
			<input type="text" readonly id="total-without-so" class="form-control numeric" value="0" name="total">
		</div>
	</td>
	<td></td>
</tr>
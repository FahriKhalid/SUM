@php($total = 0)
<table class="table table-sm table-bordered">
	<thead>
		<tr> 
			<th>Produk <i class="text-danger">*</i></th>
			<th>Incoterm <i class="text-danger">*</i></th>
			<th width="170px">Kuantitas <i class="text-danger">*</i></th>
			<th>Harga beli <i class="text-danger">*</i></th> 
			<th>Nilai <i class="text-danger">*</i></th>
			<th width="1px">
				<button type="button" class="btn btn-success btn-sm" onclick="addRowPO()" {{ $info["pre_order"]->SKPP->id_skpp != null ? 'disabled' : '' }} data-toggle="tooltip" data-placement="top" title="Tambah data"><i class="fa fa-plus"></i></button>
			</th>
		</tr>
	</thead>
	<tbody id="form-parent-po">
		
		@forelse($info["barang"] as $barang)
		@php($total+=floatval($barang->nilai))
		<tr> 
			<td> 
				<input type="hidden" class="id-po" name="id_po[]" value="{{ Helper::encodex($barang->id_barang) }}">
				<select class="form-control select2 select-produk" {{ $info["pre_order"]->SKPP->id_skpp != null ? 'disabled' : '' }} title="Pilih Produk" name="produk[]">
					<option value="">-- Pilih Produk --</option>

					@foreach($info["produk"] as $produk)
					<option {{ $barang->id_produk == $produk->id_produk ? "selected" : "" }} value="{{ $produk->id_produk }}">
						{{ $produk->nama.' ('.$produk->spesifikasi.')' }}
					</option>
					@endforeach
				</select>
			</td>
			<td>
				<input type="text" value="{{ $barang->incoterm }}" {{ $info["pre_order"]->SKPP->id_skpp != null ? 'disabled' : '' }} class="form-control" name="incoterm[]">
			</td>
			<td>
				<div class="input-group"> 
					<input type="text" value="{{ Helper::currency($barang->kuantitas) }}" {{ $info["pre_order"]->SKPP->id_skpp != null ? 'disabled' : '' }} min="1" class="form-control kuantitas float" name="kuantitas[]">
					<div class="input-group-append">
						<span class="input-group-text">MT</span>
					</div>
				</div> 
			</td>
			<td>
				<div class="input-group"> 
					<div class="input-group-prepend">
						<span class="input-group-text">Rp</span>
					</div>
					<input type="text" value="{{ Helper::currency($barang->harga_jual) }}" {{ $info["pre_order"]->SKPP->id_skpp != null ? 'disabled' : '' }} class="form-control numeric harga-beli" name="harga_jual[]">
				</div> 
			</td> 
			<td>
				<div class="input-group"> 
					<div class="input-group-prepend">
						<span class="input-group-text">Rp</span>
					</div>
					<input type="text" readonly value="{{ Helper::currency($barang->nilai) }}" {{ $info["pre_order"]->SKPP->id_skpp != null ? 'disabled' : '' }} class="form-control nilai align-right" name="nilai[]">
				</div> 

			</td>
			<td>
				@if(count($info["barang"]) == 1)
					<button type="button" disabled class="btn btn-dark btn-sm" data-toggle="tooltip" data-placement="top" title="Hapus baris"><i class="fa fa-trash"></i></button>
				@else
					<button type="button" url="{{ url('barang/destroy/'.Helper::encodex($barang->id_barang)) }}" {{ $info["pre_order"]->SKPP->id_skpp != null ? 'disabled' : '' }} class="btn btn-dark btn-sm delete-po" data-toggle="tooltip" data-placement="top" title="Hapus data">
						<i class="fa fa-trash"></i>
					</button>
				@endif 
			</td>
		</tr> 
		@empty
		<tr> 
			<td> 
				<select class="form-control select2 select-produk" title="Pilih Produk" name="new_produk[]">
					<option value="">-- Pilih Produk --</option>

					@foreach($info["produk"] as $produk)
					<option value="{{ $produk->id_produk }}">{{ $produk->nama }}</option>
					@endforeach
				</select>
			</td>
			<td>
				<input type="text" class="form-control" name="incoterm[]">
			</td>
			<td>
				<div class="input-group"> 
					<input type="text" value="1" min="1" class="form-control kuantitas number" name="new_kuantitas[]">
					<div class="input-group-append">
						<span class="input-group-text">MT</span>
					</div>
				</div> 
			</td>
			<td>
				<div class="input-group"> 
					<div class="input-group-prepend">
						<span class="input-group-text">Rp</span>
					</div>
					<input type="text" class="form-control numeric harga-beli" name="new_harga_jual[]">
				</div> 
			</td> 
			<td>
				<div class="input-group"> 
					<div class="input-group-prepend">
						<span class="input-group-text">Rp</span>
					</div>
					<input type="text" readonly value="{{ Helper::currency($barang->nilai) }}" class="form-control nilai align-right" name="new_nilai[]">
				</div> 

			</td>
			<td><button type="button" class="btn btn-success btn-sm" onclick="addRowPO()"><i class="fa fa-plus"></i></button></td>
		</tr> 
		@endforelse
	</tbody> 
	<tfoot> 
		<tr>
			<td colspan="4" align="right"><b>TOTAL</b></td> 
			<td align="right">
				<div class="d-flex justify-content-between">
					<div>Rp</div> 
					<div id="total-harga">{{ Helper::currency(Helper::toFixed($total, 2)) }}</div>		
				</div>
			</td>
			<td></td>
		</tr>
	</tfoot>
</table>
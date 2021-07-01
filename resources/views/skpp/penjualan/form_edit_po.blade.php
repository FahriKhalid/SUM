@php
	$total = 0;
@endphp

<table class="table table-sm table-bordered">
	<thead>
		<tr> 
			<th>Produk <span class="text-danger">*</span></th>
			<th>Incoterm <span class="text-danger">*</span></th>
			<th width="300px">Kuantitas <span class="text-danger">*</span></th>
			<th>Harga jual <span class="text-danger">*</span></th>
			<th>Nilai <span class="text-danger">*</span></th>
			<th width="1px">
				<button type="button" class="btn btn-success btn-sm" onclick="addRowPO()" data-toggle="tooltip" data-placement="top" title="Tambah data"><i class="fa fa-plus"></i></button>
			</th>
		</tr>
	</thead>
	<tbody id="form-parent-po">
		@forelse($info["po"] as $barang) 
		@php 
			$total += floatval($barang->nilai); 
			$sisa = (float)$barang->Stok->jumlah - (float)$barang->kuantitas;
			$x = (float)$barang->Stok->jumlah;
		@endphp
		<tr newdata="false"> 
			<td> 
				<input type="hidden" class="id-po" name="id_po[]" value="{{ Helper::encodex($barang->id_barang) }}">
				<select class="form-control select2 select-produk" title="Pilih Produk" name="produk[]">
					<option value="">-- Pilih produk --</option>
					@foreach($info["produk"] as $produk)
						<option {{ $barang->id_produk == $produk->id_produk ? "selected" : "" }} value="{{ $produk->id_produk }}">{{ $produk->nama.' ('.$produk->spesifikasi.')' }}</option>
					@endforeach
				</select>
			</td>
			<td>
				<input type="text" class="form-control" value="{{ $barang->incoterm }}" name="incoterm[]">
			</td>
			<td>
				<div class="d-flex">
					<div class="input-group"> 
			            <input type="text" disabled sisa="{{ $x }}" class="form-control sisa_kuantitas float" value="{{ Helper::currency($sisa) }}">
			            <div class="input-group-append">
			                <span class="input-group-text">MT</span>
			            </div>
			        </div> 
			        <div class="ml-2 mr-2 align-self-center">
						<i class="fa fa-arrow-right"></i>
					</div> 
					<div class="input-group">  
			            <input type="text" kuantitas="{{ $barang->kuantitas }}" class="form-control kuantitas float" value="{{ Helper::currency($barang->kuantitas) }}" name="kuantitas[]">
			            <div class="input-group-append">
			                <span class="input-group-text">MT</span>
			            </div>
			        </div> 
			    </div>
			</td>
			<td>
				<div class="input-group"> 
					<div class="input-group-prepend">
		                <span class="input-group-text">Rp</span>
		            </div>
		            <input type="text" class="form-control numeric harga-jual" value="{{ Helper::currency($barang->harga_jual) }}" name="harga_jual[]">
		        </div> 
			</td>
			<td>
				<div class="input-group"> 
					<div class="input-group-prepend">
		                <span class="input-group-text">Rp</span>
		            </div>
		            <input type="text" readonly class="form-control nilai align-right" value="{{ Helper::currency($barang->nilai) }}" name="nilai[]">
		        </div> 
				
			</td>
			<td>  
				@if(count($info["po"]) == 1)
					<button type="button" disabled class="btn btn-dark btn-sm"><i class="fa fa-trash"></i></button>
				@else
					<button type="button" url="{{ url('barang/destroy/'.Helper::encodex($barang->id_barang)) }}" class="btn btn-dark btn-sm delete-po" data-toggle="tooltip" data-placement="top" title="Hapus data"><i class="fa fa-trash"></i></button>
				@endif 
			</td>
		</tr> 
		@empty
		<tr> 
			<td> 

				<select class="form-control select2 select-produk" title="Pilih Produk" name="new_produk[]">
					<option value="">-- Pilih produk --</option>
					@foreach($info["produk"] as $produk)
						<option value="{{ $produk->id_produk }}">{{ $produk->nama }}</option>
					@endforeach
				</select>
			</td>
			<td>
				<input type="text" class="form-control" name="new_incoterm[]">
			</td>
			<td>
				<div class="input-group"> 
		            <input type="text" class="form-control kuantitas number" value="1" name="new_kuantitas[]">
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
		            <input type="text" class="form-control numeric harga-jual" name="new_harga_jual[]">
		        </div> 
			</td>
			<td>
				<div class="input-group"> 
					<div class="input-group-prepend">
		                <span class="input-group-text">Rp</span>
		            </div>
		            <input type="text" readonly class="form-control nilai align-right" name="new_nilai[]">
		        </div> 
				
			</td>
			<td>  
				<button type="button" disabled class="btn btn-danger btn-sm"><i class="fa fa-minus"></i></button>
			</td>
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


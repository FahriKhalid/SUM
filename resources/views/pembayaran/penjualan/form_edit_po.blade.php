@php
	$total = 0;
@endphp

<table class="table table-sm table-bordered">
	<thead>
		<tr> 
			<th>Produk <span class="text-danger">*</span></th>
			<th>Incoterm <span class="text-danger">*</span></th>
			<th width="170px">Kuantitas <span class="text-danger">*</span></th>
			<th>Harga jual <span class="text-danger">*</span></th>
			<th>Nilai <span class="text-danger">*</span></th>
			<th width="1px">
				<button type="button" class="btn btn-success btn-sm" onclick="addRowPO()"><i class="fa fa-plus"></i></button>
			</th>
		</tr>
	</thead>
	<tbody id="form-parent-po">
		@foreach($info["po"] as $po)

		@php 
			$total += floatval($po->nilai);
		@endphp

		<tr> 
			<td> 
				<input type="hidden" class="id-po" name="id_po[]" value="{{ Helper::encodex($po->id_barang) }}">
				<select class="form-control select2 select-produk" title="Pilih Produk" name="produk[]">
					<option value="">-- Pilih produk --</option>
					@foreach($info["produk"] as $produk)
						<option {{ $po->id_produk == $produk->id_produk ? "selected" : "" }} value="{{ $produk->id_produk }}">{{ $produk->nama }}</option>
					@endforeach
				</select>
			</td>
			<td>
				<input type="text" class="form-control" value="{{ $po->incoterm }}" name="incoterm[]">
			</td>
			<td>
				<div class="input-group"> 
		            <input type="text" class="form-control kuantitas number" value="{{ $po->kuantitas }}" name="kuantitas[]">
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
		            <input type="text" class="form-control numeric harga-jual" value="{{ Helper::currency($po->harga_jual) }}" name="harga_jual[]">
		        </div> 
			</td>
			<td>
				<div class="input-group"> 
					<div class="input-group-prepend">
		                <span class="input-group-text">Rp</span>
		            </div>
		            <input type="text" readonly class="form-control nilai align-right" value="{{ Helper::currency($po->nilai) }}" name="nilai[]">
		        </div> 
				
			</td>
			<td>  
				@if(count($info["po"]) == 1)
					<button type="button" disabled class="btn btn-dark btn-sm"><i class="fa fa-trash"></i></button>
				@else
					<button type="button" url="{{ url('barang/destroy/'.Helper::encodex($po->id_barang)) }}" class="btn btn-dark btn-sm delete-po"><i class="fa fa-trash"></i></button>
				@endif 
			</td>
		</tr> 
		@endforeach
	</tbody>
	<tfoot>
		<tr>
			<td colspan="4" align="right"><b>PPN 10%</b></td> 
			<td align="right">
				<div class="d-flex justify-content-between">
					<div>Rp</div>

					@php
						$ppn = Helper::toFixed(($total * 10) / 100, 2);
					@endphp

					<div id="total-ppn">{{ Helper::currency($ppn) }} </div>		
				</div>
			</td>
			<td></td>
		</tr>
		<tr>
			<td colspan="4" align="right"><b>TOTAL</b></td> 
			<td align="right">
				<div class="d-flex justify-content-between">
					<div>Rp</div> 
					<div id="total-harga">{{ Helper::currency(Helper::toFixed($total - $ppn, 2)) }}</div>		
				</div>
			</td>
			<td></td>
		</tr>
	</tfoot>
</table>


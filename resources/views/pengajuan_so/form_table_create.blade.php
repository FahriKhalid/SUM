<div class="table-responsive">
	<table class="table table-bordered table-sm">
		<thead>
			<tr> 
				<th>Produk</th> 
				<th>Incoterm</th>
				<th width="300px">Kuantitas</th> 
				<th width="200px">Harga Beli</th>
				<th width="200px">Nilai</th>
				<th width="1px">
					<button type="button" class="btn btn-success btn-sm" onclick="reset_table_po()"><i class="fa fa-sync"></i></button>
				</th>
			</tr>
		</thead>	
		<tbody id="tbody-po"> 
			@php
			$total = 0;
			$total_harga = 0;
			@endphp
			@foreach($info["po"] as $barang)

			@php($total += \App\Services\PengajuanSoService::sisaKuantitasPO($barang->id_barang))
			@php($total_harga += $barang->nilai)

			<tr class="{{ \App\Services\PengajuanSoService::sisaKuantitasPO($barang->id_barang) == 0 ? 'bg-red' : '' }}"> 
				<td>{{ $barang->Produk->nama }}</td> 
				<td>{{ $barang->incoterm }}</td>
				<td class="p-1">
					<input type="hidden" name="id_produk[]" value="{{ Helper::encodex($barang->Produk->id_produk) }}">
					<input type="hidden" name="id_barang[]" value="{{ Helper::encodex($barang->id_barang) }}">
					<input type="hidden" name="incoterm[]" value="{{ $barang->incoterm }}">
					<div class="d-flex">
						<div class="input-group">
							<input type="text" disabled class="form-control float sisa_kuantitas" value="{{ Helper::currency(\App\Services\PengajuanSoService::sisaKuantitasPO($barang->id_barang)) }}">
							<div class="input-group-append">
								<span class="input-group-text">MT</span>
							</div>
						</div>

						<div class="ml-2 mr-2 align-self-center">
							<i class="fa fa-arrow-right"></i>
						</div>

						<div class="input-group">
							<input type="text" name="kuantitas[]" autocomplete="off" class="form-control float" value="{{ Helper::currency(\App\Services\PengajuanSoService::sisaKuantitasPO($barang->id_barang)) }}">
							<div class="input-group-append">
								<span class="input-group-text">MT</span>
							</div>
						</div>
					</div>
					
				</td> 
				<td class="p-1">
					<div class="input-group"> 
						<div class="input-group-prepend">
                            <span class="input-group-text">Rp</span>
                        </div>
                        <input type="text" readonly="" value="{{ Helper::currency($barang->harga_jual) }}" class="form-control harga-beli align-right" name="harga_beli[]">
                    </div>
				</td> 
				<td class="p-1">
					<div class="input-group"> 
						<div class="input-group-prepend">
                            <span class="input-group-text">Rp</span>
                        </div>
                        <input type="text" readonly="" value="{{ Helper::currency($barang->nilai) }}" class="form-control nilai align-right" name="nilai[]">
                    </div>
				</td> 
				<td>
					<button type="button" class="btn btn-danger btn-sm remove-row-po"><i class="fa fa-minus"></i></button>
				</td>
			</tr> 
			@endforeach
		</tbody>
		<tfoot>
			<tr>
				<td colspan="4" align="right"><b>TOTAL</b></td>  
				<td class="border-none">
					<div class="d-flex justify-content-between">
						<div>Rp</div> 
						<div id="total-harga">{{ Helper::currency(Helper::toFixed($total_harga, 2)) }}</div>		
					</div>
				</td>  
			</tr>
		</tfoot>			
	</table>
</div>  
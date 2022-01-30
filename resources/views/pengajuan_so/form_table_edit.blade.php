
<div class="table-responsive">
	<table class="table table-bordered table-sm">
		<thead>
			<tr> 
				<th>Produk</th> 
				<th>Incoterm</th>
				<th width="300px">Kuantitas</th> 
				<th width="200px">Harga Beli</th>
				<th width="200px">Nilai</th>
				{{-- <th width="1px">
					<button type="button" class="btn btn-success btn-sm" onclick="reset_table_po()"><i class="fa fa-sync"></i></button>
				</th> --}}
			</tr>
		</thead>	
		<tbody id="tbody-po"> 
			@php
			$total = 0;
			$total_harga = 0;
			@endphp
			@foreach($info["pengajuan_so"]->BarangPengajuanSo as $barang)
			@php
			$sisa_barang = \App\Services\PengajuanSoService::sisaKuantitasPO($barang->id_barang, $barang->id_barang_pengajuan_so);
			$total += $sisa_barang;
			$nilai = $barang->harga_jual * $sisa_barang;
			$total_harga += $nilai;
			@endphp

			<tr class="{{ \App\Services\PengajuanSoService::sisaKuantitasPO($barang->id_barang, $barang->id_barang_pengajuan_so) == 0 ? 'bg-red' : '' }}"> 
				<td>{{ $barang->Produk->nama }}</td> 
				<td>{{ $barang->incoterm }}</td>
				<td class="p-1">
					<input type="hidden" name="id_barang_pengajuan_so[]" value="{{ Helper::encodex($barang->id_barang_pengajuan_so) }}">
					<input type="hidden" name="id_produk[]" value="{{ Helper::encodex($barang->Produk->id_produk) }}">
					<input type="hidden" name="id_barang[]" value="{{ Helper::encodex($barang->id_barang) }}">
					<div class="d-flex">
						<div class="input-group">
							<input type="text" disabled class="form-control float sisa_kuantitas" value="{{ Helper::currency(\App\Services\PengajuanSoService::sisaKuantitasPO($barang->id_barang, $barang->id_barang_pengajuan_so)) }}">
							<div class="input-group-append">
								<span class="input-group-text">MT</span>
							</div>
						</div>

						<div class="ml-2 mr-2 align-self-center">
							<i class="fa fa-arrow-right"></i>
						</div>

						<div class="input-group">
							<input type="text" name="kuantitas[]" autocomplete="off" class="form-control float" value="{{ Helper::currency($barang->kuantitas) }}">
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
                        <input type="text" readonly="" value="{{ Helper::currency(Helper::toFixed($nilai, 2)) }}" class="form-control nilai align-right" name="nilai[]">
                    </div>
				</td> 
				{{-- <td class="p-1">
					<button type="button" class="btn btn-danger btn-sm remove-row-po"><i class="fa fa-minus"></i></button>
				</td> --}}
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
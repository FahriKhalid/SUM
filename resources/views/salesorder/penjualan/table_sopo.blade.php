@if($info["so"]->Invoice != null)
<div class="alert alert-warning">
	<h4 class="alert-heading"><i class="fa fa-exclamation-circle"></i> Warning</h4>
	<p>Nomor SO {{ $info["so"]->no_so }} telah memiliki invoice</p>
	<a href="#" target="_blank" class="btn btn-sm btn-primary"><i class="fa fa-search"></i> Lihat invoice</a>
</div>
@endif
<table class="table table-bordered">
	<thead>
		<tr>
			<th width="1px">NO</th>
			<th>PRODUK</th>
			<th>KUANTITAS</th>
			<th>INCOTERM</th>
			<th>HARGA JUAL</th>
			<th>JUMLAH</th>
		</tr>
	</thead>
	<tbody> 
		@php($sub_total = 0)
		@foreach($info["sopo"] as $sopo)
		@php($sub_total += (Helper::toFixed($sopo->Barang->harga_jual / 1.1, 2)) * $sopo->kuantitas)
		<tr>
			<td>{{ $loop->iteration }}.</td>
			<td>{{ $sopo->Barang->Produk->nama }}</td>
			<td>{{ $sopo->kuantitas }} MT</td>
			<td>{{ $sopo->Barang->incoterm }}</td>
			<td> 
				<div class="d-flex justify-content-between">
					<div>IDR</div>
					<div>{{ Helper::currency(Helper::toFixed($sopo->Barang->harga_jual / 1.1, 2)) }}</div>	
				</div>
			</td>
			<td width="250px">
				<div class="d-flex justify-content-between">
					<div>IDR</div>
					<div>{{ Helper::currency((Helper::toFixed($sopo->Barang->harga_jual / 1.1, 2)) * $sopo->kuantitas)  }}</div>
				</div>
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
					<input type="text" class="form-control numeric" value="{{ Helper::currency(Helper::toFixed($sub_total, 2)) }}" name="sub_total">
				</div>
			</td>
		</tr>
		<tr>
			<td colspan="5" align="right"><b>PPN</b></td> 
			<td class="p-1">
				<div class="input-group">
					<div class="input-group-prepend">
						<span class="input-group-text">IDR</span>
					</div> 
					<input type="text" class="form-control numeric" value="{{ Helper::currency(Helper::toFixed($sub_total / 10, 2)) }}" name="ppn">
				</div>
			</td>
		</tr>
		<tr> 
			<td colspan="5" align="right"><b>TOTAL</b></td> 
			<td class="p-1"> 
				<div class="input-group">
					<div class="input-group-prepend">
						<span class="input-group-text">IDR</span>
					</div> 
					<input type="text" readonly class="form-control numeric" value="{{ Helper::currency(Helper::toFixed($sub_total, 2) + (Helper::toFixed($sub_total / 10, 2)), 2) }}" name="total">
				</div>
			</td>
		</tr>
	</tbody> 
</table>
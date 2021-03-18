<table class="table table-bordered">
	<thead>
		<tr>
			<th width="1px">No</th>
			<th>Produk</th> 
			<th width="300px">Kuantitas</th>
		</tr>
	</thead>	
	<tbody>
		@php 
			$total = 0;
		@endphp
		@foreach($info["barang"] as $barang)
		<tr>
			<td>{{ $loop->iteration }}</td>
			<td>{{ $barang->Produk->nama }}</td>
			<td class="p-1">
				<div class="d-flex">
					<div class="input-group">
						<input type="text" disabled="" class="form-control number sisa_kuantitas" value="{{ $barang->kuantitas }}" style="text-align: right;" im-insert="true">
						<div class="input-group-append">
							<span class="input-group-text">MT</span>
						</div>
					</div>

					<div class="ml-2 mr-2 align-self-center">
						<i class="fa fa-arrow-right"></i>
					</div>

					<div class="input-group">
						<input type="text" name="kuantitas[]" autocomplete="off" class="form-control number">
						<div class="input-group-append">
							<span class="input-group-text">MT</span>
						</div>
					</div>
				</div>
			</td>

			@php 
				$total += floatval($barang->nilai);
			@endphp
		</tr>
		@endforeach
	</tbody> 		
</table>
 
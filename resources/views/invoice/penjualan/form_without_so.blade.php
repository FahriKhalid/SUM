<form id="form-without-so"> 
	<div class="card-body" id="layout-parent">  
		<div class="form-row ">
			<div class="form-group col-md-6">
				<div class="form-group"> 
					<label>Customer <span class="text-danger">*</span></label>
					<input class="form-control" disabled value="{{ $info["skpp"]->Customer->perusahaan }}" placeholder="Wajib di isi"> 
				</div> 
			</div> 
			<div class="form-group col-md-6"> 
				<div class="form-group">  
					<label>Alamat <span class="text-danger">*</span></label>
					<input class="form-control" disabled value="{{ $info["skpp"]->Customer->alamat }}" placeholder="Wajib diisi"> 
				</div>
			</div>   
		</div> 
		<div class="table">
			<table class="table table-sm table-bordered" id="table-po">
				<thead>
					<tr>
						<th width="1px">NO</th>
						<th>PRODUK</th>
						<th>SPESIFIKASI</th>
						<th width="300px">KUANTITAS <i class="text-danger">*</i></th> 
						<th width="200x">HARGA JUAL</th>
						<th width="250x">JUMLAH</th>
						<th class="p-1" width="1px">
							<button type="button" class="btn btn-sm btn-success" data-toggle="tooltip" data-placement="top" data-original-title="Reset" onclick="reset_table_po()"><i class="fa fa-sync"></i></button>
						</th> 
					</tr>
				</thead>	
				<tbody id="tbody-po-without-so"> 
					@include('invoice.penjualan.form_po')  
				</tbody>  
			</table>
		</div>
	</div>   
	<div class="card-body border-top d-flex justify-content-between"> 
		<div> 
		</div>
		@if(!PembayaranService::isBayar(Helper::decodex($id)))
		<div>
			@csrf
			<button class="btn btn-primary"><i class="fa fa-save"></i> Simpan</button> 
		</div>
		@endif
	</div>
</form>
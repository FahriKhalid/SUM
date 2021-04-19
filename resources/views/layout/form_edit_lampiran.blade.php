<div class="custom-control custom-checkbox mb-2 mt-4">
    <input type="checkbox" class="custom-control-input" name="is_lampiran" {{ count($info_lampiran) > 0 ? "checked" : "" }} value="1" id="show-form-lampiran">
    <label class="custom-control-label" for="show-form-lampiran">Centang jika ada lampiran</label>
</div>

<div class="collapse {{ count($info_lampiran) > 0 ? "show" : "" }}" id="form-lampiran">
	<table class="table table-sm table-bordered">
		<thead>
			<tr> 
				<th width="300px">File <span class="text-danger">*</span></th> 
				<th>Nama <span class="text-danger">*</span></th>
				<th>Keterangan</th>
				<th width="1px">
					<button type="button" class="btn btn-success btn-sm" onclick="addRowLampiran()" data-toggle="tooltip" data-placement="top" title="Tambah data"><i class="fa fa-plus"></i></button>
				</th> 
			</tr>
		</thead>
		<tbody id="form-parent-lampiran"> 
			@forelse($info_lampiran as $lampiran)
				<tr> 
					<td> 
						<input type="hidden" value="{{ Helper::encodex($lampiran->id_lampiran) }}" name="id_lampiran[]">
						<input type="file" class="lampiran" name="file[]" width="200px">
					</td> 
					<td>
						<input type="text" class="form-control lampiran" value="{{ $lampiran->nama }}" name="nama_file[]">
					</td>
					<td>
						<textarea style="height: 38px" class="form-control lampiran" rows="1" name="keterangan_file[]">{{ $lampiran->keterangan }}</textarea>
					</td> 
					<td><button type="button" url="{{ url('lampiran/destroy/'.Helper::encodex($lampiran->id_lampiran)) }}" class="btn btn-dark btn-sm delete-lampiran" data-toggle="tooltip" data-placement="top" title="Hapus data"><i class="fa fa-trash"></i></button></td>
				</tr> 
				@empty
				<tr> 
					<td>  
						<input type="file" class="lampiran" name="new_file[]">
					</td> 
					<td>
						<input type="text" class="form-control lampiran" name="new_nama_file[]">
					</td>
					<td>
						<textarea style="height: 38px" class="form-control lampiran" rows="1" name="new_keterangan_file[]"></textarea>
					</td> 
					<td><button type="button" class="btn btn-danger btn-sm remove-row-lampiran"><i class="fa fa-minus"></i></button></td>
				</tr> 
			@endforelse
		</tbody>
	</table>
	<small>
		<span class="text-danger font-italic">
			<div>Note : </div>
			<div>- Extensi file lampiran yang diperbolehkan hanya PNG, JPG, JPEG, DOC, DOCX, dan PDF.</div>
			<div>- Maksimal ukuran file 2 Mb.</div> 
		</span>
	</small>
</div>
<div class="custom-control custom-checkbox mt-4">
    <input type="checkbox" class="custom-control-input" name="is_lampiran" value="1" id="show-form-lampiran">
    <label class="custom-control-label" for="show-form-lampiran">Centang jika ada lampiran</label>
</div>
<div class="collapse mt-2" id="form-lampiran">
	<table class="table table-sm table-bordered">
		<thead>
			<tr> 
				<th width="200px">File <i class="text-danger">*</i></th> 
				<th>Nama <i class="text-danger">*</i></th>
				<th>Keterangan</th>
				<th width="1px"></th> 
			</tr>
		</thead>
		<tbody id="form-parent-lampiran">
			<tr> 
				<td> 
					<input type="file" class="lampiran" name="new_file[]" width="200px">
				</td> 
				<td>
					<input type="text" class="form-control lampiran" name="new_nama_file[]">
				</td>
				<td>
					<textarea style="height: 38px" class="form-control lampiran" rows="1" name="new_keterangan_file[]"></textarea>
				</td> 
				<td>
					<button type="button" class="btn btn-success btn-sm" onclick="addRowLampiran()" data-toggle="tooltip" data-placement="top" title="Tambah data"><i class="fa fa-plus"></i></button>
				</td>
			</tr> 
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
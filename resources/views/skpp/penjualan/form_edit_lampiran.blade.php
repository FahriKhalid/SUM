@forelse($info["lampiran"] as $lampiran)
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
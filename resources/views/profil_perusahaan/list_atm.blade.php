@forelse($info["atm"] as $atm) 
<input type="hidden" name="id_atm[]" value="{{ Helper::encodex($atm->id_atm) }}">
<tr>
    <td class="p-2"><input type="text" name="atm[]" class="form-control" value="{{ $atm->nama }}"></td>
    <td class="p-2"><input type="text" name="no_atm[]" class="form-control" value="{{ $atm->nomor }}"></td>
    <td class="p-2">
        <select class="form-control" name="status[]">
            <option value="1" {{ $atm->is_aktif == 1 ? "selected" : "" }}>Aktif</option>
            <option value="0" {{ $atm->is_aktif == 0 ? "selected" : "" }}>Tidak Aktif</option>
        </select>
    </td>
    <td class="p-2">
        <button type="button" url="{{ url('profil_perusahaan/atm/destroy/'.Helper::encodex($atm->id_atm)) }}" class="btn btn-sm btn-dark delete-atm"><i class="fa fa-trash"></i></button>
    </td>
</tr>

@empty
<tr>
    <td class="p-2"><input type="text" name="new_atm[]" class="form-control"></td>
    <td class="p-2"><input type="text" name="new_no_atm[]" class="form-control"></td>
    <td class="p-2">
        <select class="form-control" name="new_status[]">
            <option value="1">Aktif</option>
            <option value="0">Tidak Aktif</option>
        </select>
    </td>
    <td class="p-2">
        <button type="button" disabled class="btn btn-sm btn-danger remove-row"><i class="fa fa-minus"></i></button>
    </td>
</tr>
@endforelse

<div class="modal fade" id="modal-ganti-supir" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="title-modal-form-customer" >Ganti supir</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div> 
            <form id="form-ganti-supir">
                @csrf
                <div class="modal-body"> 
                    <input type="hidden" name="id_so" value="{{ $id }}">
                    <div class="form-group">
                        <label>Supir Lama</label>
                        <div class="form-group"> 
                            <input type="text" class="form-control" disabled value="{{ $info["so"]->SupirAktif[0]->Supir->nama == null ? "-" : $info["so"]->SupirAktif[0]->Supir->nama }}">
                        </div> 
                    </div>  
                    <div class="form-group">
                        <label>Supir baru<span class="text-danger">*</span></label>
                        <div class="form-group"> 
                            <select class="form-control select2" name="supir">
                                @forelse($info["supir"] as $supir)
                                    <option supir="{{ $supir->nama }}" {{ $info["so"]->SupirAktif[0]->id_supir == $supir->id_supir ? "selected" : "" }} value="{{ $supir->id_supir }}">{{ $supir->nama.' - '.$supir->kendaraan .' ('.$supir->plat_nomor.')'}}</option>
                                @empty

                                @endforelse
                            </select> 
                        </div> 
                    </div>  
                    <div class="form-group">
                        <label>Alasan ganti supir <span class="text-danger">*</span></label>
                        <div class="form-group"> 
                            <textarea class="form-control" name="keterangan" rows="3"></textarea>
                        </div> 
                    </div>  
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fa fa-times-circle"></i> Tutup</button>
                    <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Simpan</button>
                </div> 
            </form>
        </div>
    </div>
</div>
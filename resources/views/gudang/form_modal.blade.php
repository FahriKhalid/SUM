<div class="modal fade" id="modal-form-gudang" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="title-modal-form-gudang" ></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="form-gudang">
                @csrf
                <input type="hidden" name="id">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Nama Gudang <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="nama" placeholder="Wajib diisi">
                    </div>   
                    <div class="form-group">
                        <label>Produsen <span class="text-danger">*</span></label>
                        <select class="form-control select2" name="produsen">
                            <option value="">-- Pilih --</option>
                            @foreach($info["produsen"] as $produsen)
                                <option value="{{ $produsen->id_produsen }}">{{ $produsen->perusahaan }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Alamat <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="alamat" placeholder="Wajib diisi">
                    </div>  
                    

                    <div class="form-group"> 
                        <div class="custom-control custom-radio custom-control-inline">
                            <input type="radio" id="aktif" value="1" checked="" required="" name="status" class="custom-control-input">
                            <label class="custom-control-label" for="aktif">Aktif</label>
                        </div>
                        <div class="custom-control custom-radio custom-control-inline">
                            <input type="radio" id="tidak-aktif" value="0" name="status" class="custom-control-input">
                            <label class="custom-control-label" for="tidak-aktif">Tidak aktif</label>
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
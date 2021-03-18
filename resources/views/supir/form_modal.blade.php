<div class="modal fade" id="modal-form-supir" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="title-modal-form-supir" ></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="form-supir">
                @csrf
                <input type="hidden" name="id">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Nama supir <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="nama_supir" maxlength="50" placeholder="Wajib di isi">
                    </div> 
                    <div class="form-group">
                        <label>Nomor telepon </label>
                        <input type="text" class="form-control" name="telepon" maxlength="13" placeholder="Opsional">
                    </div>  
                    <div class="form-group">
                        <label>Plat nomor <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="plat_nomor" maxlength="9" placeholder="Wajib di isi">
                    </div> 
                    <div class="form-group">
                        <label>Kendaraan <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="kendaraan" placeholder="Wajib di isi">
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
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fa fa-times-circle"></i> Close</button>
                    <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
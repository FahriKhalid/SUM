<div class="modal fade" id="modal-form-produk" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="title-modal-form-produk" ></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="form-produk">
                @csrf
                <input type="hidden" name="id">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Nama Produk <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="nama_produk" placeholder="Wajib di isi">
                    </div> 
                    <div class="form-group">
                        <label>Spesifikasi</label>
                        <input type="text" class="form-control" name="spesifikasi" placeholder="Opsional">
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
<div class="modal fade" id="modal-form-produsen" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="title-modal-form-produsen" ></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="form-produsen">
                @csrf
                <input type="hidden" name="id">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Nama Perusahaan <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="nama_perusahaan" placeholder="Wajib di isi">
                    </div> 
                    <div class="form-group">
                        <label>Nama produsen <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="nama_produsen" placeholder="Wajib di isi">
                    </div>
                    <div class="form-group">
                        <label>Email <span class="text-danger">*</span></label>
                        <input type="text" class="form-control email" name="email" placeholder="Wajib di isi">
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
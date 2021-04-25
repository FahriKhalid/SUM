<div class="modal fade" id="modal-form-customer" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="title-modal-form-customer" ></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="form-customer">
                @csrf
                <input type="hidden" name="id">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Nama Perusahaan</label>
                        <input type="text" class="form-control" name="nama_perusahaan" placeholder="Opsional">
                    </div>
                    <div class="form-group">
                        <label>Nomor NPWP</label>
                        <input type="text" class="form-control" name="nomor_npwp" placeholder="Opsional">
                    </div>
                    <div class="form-group">
                        <label>Nama Customer <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="nama_customer" placeholder="Wajib di isi">
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="text" class="form-control email" name="email" placeholder="Opsional">
                    </div>
                    <div class="form-group">
                        <label>Telpon <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="telpon" placeholder="Wajib di isi">
                    </div>
                    <div class="form-group">
                        <label>Alamat <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="alamat" placeholder="Wajib di isi"></textarea>
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
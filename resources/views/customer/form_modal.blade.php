<div class="modal fade" id="modal-form-customer" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="title-modal-form-customer" ></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <nav class="alert-primary">
                <div class="nav nav-tabs nav-justified" id="nav-tab" role="tablist">
                    <a class="nav-item text-dark nav-link active" id="nav-home-tab" data-toggle="tab" href="#nav-1" role="tab" aria-controls="nav-home" aria-selected="true">Perorangan</a>
                    <a class="nav-item text-dark nav-link" id="nav-home-tab" data-toggle="tab" href="#nav-2" role="tab" aria-controls="nav-home" aria-selected="true">Perusahaan</a> 
                </div>
            </nav>
            <form id="form-customer">
                @csrf
                <input type="hidden" name="id">
                <div class="tab-content">
                    <div class="tab-pane fade show active" id="nav-1">
                        <div class="modal-body" id="perorangan">
                            <input type="hidden" name="kategori" value="perorangan">
                            <div class="form-group">
                                <label>Nama Customer <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" required name="nama_customer" placeholder="Wajib di isi">
                            </div> 
                            <div class="form-group">
                                <label>Email</label>
                                <input type="text" class="form-control" name="email" placeholder="Opsional">
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
                    </div> 
                    <div class="tab-pane fade" id="nav-2">
                        <div class="modal-body" id="perusahaan">
                            <div class="form-group">
                            <input type="hidden"  name="kategori" value="perusahaan">
                                <label>Nama Customer <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" required name="nama_customer" placeholder="Wajib di isi">
                            </div>
                            <div class="form-group">
                                <label>Nama Perusahaan <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" required name="nama_perusahaan" placeholder="Wajib di isi">
                            </div>
                            <div class="form-group">
                                <label>Nomor NPWP </label>
                                <input type="text" class="form-control" name="nomor_npwp" placeholder="Opsional">
                            </div>
                            <div class="form-group">
                                <label>Email <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" required name="email" placeholder="Wajib di isi">
                            </div>
                            <div class="form-group">
                                <label>Telpon <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" required name="telpon" placeholder="Wajib di isi">
                            </div>
                            <div class="form-group">
                                <label>Alamat <span class="text-danger">*</span></label>
                                <textarea class="form-control" required name="alamat" placeholder="Wajib di isi"></textarea>
                            </div>
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
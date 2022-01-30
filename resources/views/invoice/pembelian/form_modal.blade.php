<div class="modal fade" id="modal-form-invoice" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="title-modal-form-invoice" ></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="form-invoice" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="id">
                @if($info["skpp"] != null)
                <input type="hidden" name="id_skpp" value="{{ Helper::encodex($info["skpp"]->id_skpp) }}">
                @endif

                <input type="hidden" name="id_pre_order" value="{{ $id }}">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Nomor tagihan <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="nomor_tagihan" placeholder="Wajib diisi">
                    </div> 
                    <div class="form-group">
                        <label>File faktur pajak <span class="text-danger">*</span></label>
                        <input type="file" accept="application/pdf" class="form-control" name="file_faktur_pajak" placeholder="Wajib di isi">
                    </div>
                    <div class="form-group">
                        <label>File invoice <span class="text-danger">*</span></label>
                        <input type="file" accept="application/pdf" class="form-control email" name="file_invoice" placeholder="Wajib di isi">
                    </div> 

                    <div class="mt-3">
                        <small>
                            <span class="text-danger font-italic">
                                <div>Note : </div>
                                <div>- Extensi file lampiran yang diperbolehkan hanya PDF.</div>
                                <div>- Maksimal ukuran file 2 Mb.</div> 
                            </span>
                        </small>
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
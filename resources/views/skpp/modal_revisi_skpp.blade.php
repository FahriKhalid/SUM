<div class="modal fade" id="modal-revisi-skpp" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="title-modal-form-customer" >Form revisi SKPP</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div> 
            <form id="form-revisi-skpp"> 
                <div class="modal-body"> 
                    @if(!PembayaranService::isLunas("penjualan", Helper::decodex($id)))
                        <div class="alert alert-warning">
                            <h4 class="alert-heading"><i class="fa fa-exclamation-circle"></i> Warning</h4>
                            Pembayaran telah lunas. Tidak dapat melakukan revisi SKPP!
                        </div>
                    @endif

                    <div class="form-group">
                        <label>Catatan revisi <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="catatan_revisi" placeholder="wajib di isi" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fa fa-times-circle"></i> Tutup</button>
                    @if(PembayaranService::isLunas("penjualan", Helper::decodex($id)))
                    @csrf
                    <button type="submit" class="btn btn-primary"><i class="fa fa-share"></i> Revisi</button>
                    @endif
                </div> 
            </form>
        </div>
    </div>
</div>
<div class="modal fade" id="modal-cancel-skpp" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="title-modal-form-customer" >Form pembatalan penjualan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div> 
            <form id="form-cancel-skpp"> 
                <div class="modal-body">  
                    <div class="form-group">
                        <label>Catatan pembatalan penjualan <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="catatan_batal_penjualan" placeholder="wajib di isi" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fa fa-times-circle"></i> Tutup</button>
                    @if(PembayaranService::isLunas("penjualan", Helper::decodex($id)))
                    @csrf
                    <button type="submit" class="btn btn-primary"><i class="fa fa-check"></i> Simpan</button>
                    @endif
                </div> 
            </form>
        </div>
    </div>
</div>
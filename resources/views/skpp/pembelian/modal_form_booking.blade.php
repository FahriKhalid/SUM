<div class="modal fade" id="modal-form-booking" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="title-modal-form-booking"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="form-booking" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="id">
                <input type="hidden" name="id_pre_order" value="{{ $id }}">
                <div class="modal-body">
                    {{-- <div class="form-group">
                        <label>Nomor Booking <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="no_booking">
                    </div>   --}}
                    <div class="form-group">
                        <label>Nomor SKPP <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="no_skpp">
                    </div> 
                    <div class="form-group">
                        <label>File SKPP <span class="text-danger">*</span></label>
                        <input type="file" class="form-control" name="file_skpp">
                    </div> 

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Total pembayaran + PPN <span class="text-danger">*</span></label>
                                <input type="text" class="form-control numeric" value="{{ $info["total_pembayaran"] }}" name="total_pembayaran">
                            </div> 
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Terakhir pembayaran <span class="text-danger">*</span></label>
                                <input type="text" autocomplete="off" class="form-control datepicker" name="terakhir_pembayaran">
                            </div>
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
<div class="modal fade" id="modal-form-pembayaran" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="title-modal-form-pembayaran" ></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="form-pembayaran" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="id">
                <div class="modal-body">
                    <div class="form-group">
                        <label>File <span class="text-danger">*</span></label>
                        <input type="file" class="form-control" name="file">
                    </div>  
                    <div class="form-group">
                        <label>Keterangan </label>
                        <textarea class="form-control" name="keterangan" placeholder="Opsional" rows="3"></textarea>
                    </div>   

                    <div class="custom-control custom-checkbox mb-2 mt-4">
                        <input type="checkbox" class="custom-control-input" name="is_parsial" value="1" id="form-is-parsial">
                        <label class="custom-control-label" for="form-is-parsial">Centang jika belum lunas</label>
                    </div>
                    

                    <div class="row d-none" id="form-parsial">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Total yang harus dibayar</label>
                                <div class="input-group"> 
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Rp</span>
                                    </div>
                                    <input type="text" readonly class="form-control align-right" value="{{ Helper::currency($info["piutang"]) }}" name="total_pembayaran">
                                </div> 
                            </div> 
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Jumlah pembayaran <span class="text-danger">*</span></label>
                                <div class="input-group"> 
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Rp</span>
                                    </div>
                                    <input type="text" class="form-control numeric" name="jumlah_pembayaran">
                                </div> 
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
<div class="modal fade" id="modal-konfirmasi-hapus" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content shadow-lg border-0">
            <div class="modal-header">
                <h5 class="modal-title" id="title-modal-form-customer" >Konfirmasi hapus data</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="form-hapus">
                @csrf 
                <div class="modal-body">
                    <p>Apakah anda yakin ingin menghapus data ini? <br> data akan di hapus secara permanen</b></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fa fa-times-circle"></i> Tutup</button>
                    <button type="submit" class="btn btn-danger"><i class="fa fa-trash"></i> Hapus</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modal-konfirmasi-hapus-so_pembelian" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content shadow-lg border-0">
            <div class="modal-header">
                <h5 class="modal-title" id="title-modal-form-customer" >Konfirmasi hapus data</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="form-hapus-so_pembelian">
                @csrf 
                <div class="modal-body">
                    <p>Apakah anda yakin ingin menghapus data sales order penjualan? <br> </b></p>

                    <div class="custom-control custom-checkbox mb-2 mt-2">
                        <input type="checkbox" class="custom-control-input" name="stok_minus" value="1" id="stok-minus">
                        <label class="custom-control-label" for="stok-minus">Centang jika stok ikut berkurang</label>
                    </div>

                    <div id="table-show-produk" class="d-none">
                        
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fa fa-times-circle"></i> Tutup</button>
                    <button type="submit" class="btn btn-danger"><i class="fa fa-trash"></i> Hapus</button>
                </div>
            </form>
        </div>
    </div>
</div>
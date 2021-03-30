<div class="modal fade" id="modal-send-email" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content shadow-lg border-0">
            <div class="modal-header">
                <h5 class="modal-title" id="title-modal-form-customer">Kirim email</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div> 
            <form id="form-send-email">
                @csrf               
                <input type="hidden" name="url">
                <div class="modal-body">
                    <p>Apakah anda yakin ingin mengirim <span id="keterangan-email"></span> ke <b>{{ $info["email"] }}</b></b></p> 
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fa fa-times-circle"></i> Tidak</button>
                    <button type="submit" class="btn btn-primary"><i class="fa fa-check"></i> Iya</button>
                </div> 
            </form>
        </div>
    </div>
</div>
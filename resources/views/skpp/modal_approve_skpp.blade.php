<div class="modal fade" id="modal-approve-skpp" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="title-modal-form-customer" >Approve SKPP</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div> 
            <form id="form-approve-skpp">
                @csrf
                <div class="modal-body">
                    <p>Apakah anda yakin ingin mengapprove SKPP ini?</b></p>

                    <div class="custom-control custom-checkbox mb-2 mt-4">
                        <input type="checkbox" class="custom-control-input" id="send-email" name="is_send_email" value="1">
                        <label class="custom-control-label" for="send-email">Centang jika ingin langsung kirim email</label>
                        <p>{{ $info["skpp"]->Customer->email }}</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fa fa-times-circle"></i> Tidak</button>
                    <button type="submit" class="btn btn-primary"><i class="fa fa-check"></i> Iya</button>
                </div> 
            </form>
        </div>
    </div>
</div>
<div class="modal fade" id="modal-unapprove-skpp" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="title-modal-form-customer" >Unapprove SKPP</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div> 
            <form id="form-unapprove-skpp">
                @csrf
                <div class="modal-body">
                    <p>Apakah anda yakin ingin unapprove SKPP ini?</b></p>
 
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fa fa-times-circle"></i> Tidak</button>
                    <button type="submit" class="btn btn-primary"><i class="fa fa-check"></i> Iya</button>
                </div> 
            </form>
        </div>
    </div>
</div>
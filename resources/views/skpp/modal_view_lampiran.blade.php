<div class="modal fade" id="modal-view-lampiran" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="title-modal-lampiran"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>  
            <div class="modal-body p-0">
                <object data="" type="application/pdf" width="100%" height="800px" id="view-file-lampiran">
                    @include('layout.not_found', ['message' => 'File tidak ditemukan'])
                </object>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fa fa-times-circle"></i> Tutup</button> 
            </div>  
        </div>
    </div>
</div>
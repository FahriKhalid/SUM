<nav class="alert-primary">
    <div class="nav nav-tabs nav-justified" id="nav-tab" role="tablist">
        <a class="nav-item text-dark nav-link active" id="nav-home-tab" data-toggle="tab" href="#nav-file-invoice" role="tab" aria-controls="nav-home" aria-selected="true">Dokumen Invoice</a>
        <a class="nav-item text-dark nav-link" id="nav-agt-keluarga-tab" data-toggle="tab" href="#nav-faktur-pajak" role="tab" aria-controls="nav-agt-keluarga" aria-selected="true">Dokumen Faktur Pajak</a> 
    </div>
</nav>

<div class="tab-content">
    <div class="tab-pane fade show active" id="nav-file-invoice">
        <object data="{{ asset('faktur_pajak/'.$info["invoice"]->file_invoice) }}#view=FitH" type="application/pdf" width="100%" height="800px">
          <div class="text-center">
              <p>File invoice kosong!</p>
          </div>
        </object>
    </div>
    <div class="tab-pane fade" id="nav-faktur-pajak">
        <object data="{{ asset('faktur_pajak/'.$info["invoice"]->file_faktur_pajak) }}#view=FitH" type="application/pdf" width="100%" height="800px">
          <div class="text-center">
              <p>File faktur pajak kosong!</p>
          </div>
        </object>
    </div>
</div>
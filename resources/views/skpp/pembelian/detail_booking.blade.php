<nav class="alert-primary">
    <div class="nav nav-tabs nav-justified bg-white" id="nav-tab" role="tablist">
        <a class="nav-item text-dark nav-link active" id="nav-home-tab" data-toggle="tab" href="#nav-invoice" role="tab" aria-controls="nav-home" aria-selected="true">Booking & pembayaran</a>
        <a class="nav-item text-dark nav-link" id="nav-agt-keluarga-tab" data-toggle="tab" href="#nav-faktur-pajak" role="tab" aria-controls="nav-agt-keluarga" aria-selected="true">SKPP</a> 
    </div>
</nav>

<div class="tab-content">
    <div class="tab-pane fade show active" id="nav-invoice">
        <div class="modal-body"> 
            <table class="table table-borderless mt-2" style="margin-left: -10px"> 
                <tr>
                    <th width="200px">Nomor Kode Booking</th>
                    <th width="1px">:</th>
                    <td>
                        <div>{{ $info["booking"]->no_booking }}</div> 
                    </td>
                </tr> 
                <tr>
                    <th>Nomor SKPP</th>
                    <th>:</th>
                    <td>{{ $info["booking"]->no_skpp }}</td>
                </tr>
                <tr>
                    <th>Total pembayaran</th>
                    <th>:</th>
                    <td>Rp {{ Helper::currency($info["booking"]->total_pembayaran) }}</td>
                </tr>
                <tr>
                    <th>Terakhir pembayaran</th>
                    <th>:</th>
                    <td>{{ Helper::dateIndo($info["booking"]->terakhir_pembayaran) }}</td>
                </tr> 
            </table>  

            <br>
            <button class="btn btn-success" onclick="showFormPembayaran('{{ Helper::encodex($id) }}')">
                <i class="fa fa-plus"></i> Tambah pembayaran
            </button>
            <div id="layout-table-pembayaran">
                @include('booking.table_pembayaran')
            </div>
        </div>
    </div>
    <div class="tab-pane fade" id="nav-faktur-pajak">
        <object data="{{ asset('file_skpp/'.$info["booking"]->file_skpp) }}#view=FitH" type="application/pdf" width="100%" height="800px">
          <div class="text-center">
              <p>File SKPP kosong!</p>
          </div>
        </object>
    </div>
</div>
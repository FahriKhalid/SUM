<nav class="alert-primary">
    <div class="nav nav-tabs nav-justified" id="nav-tab" role="tablist">
        <a class="nav-item text-dark nav-link active" data-toggle="tab" href="#nav-sales-order" role="tab" aria-controls="nav-home" aria-selected="true">Detail</a>
        <a class="nav-item text-dark nav-link" data-toggle="tab" href="#nav-file-sales-order" role="tab" aria-controls="nav-agt-keluarga" aria-selected="true">File</a> 
    </div>
</nav>

<div class="tab-content">
    <div class="tab-pane fade show active" id="nav-sales-order">
        @if($info["sales_order"]->Status->status != "Final")
        <div class="modal-body">
            <a href="{{ url('pembelian/salesorder/edit/'.$id) }}" class="btn btn-primary"><i class="fa fa-edit"></i> Edit</a> 
            <a href="javascript:void(0)" show="{{ url('pembelian/salesorder/show_produk/'.$id) }}" url="{{ url('pembelian/salesorder/destroy/'.$id) }}" class="btn btn-danger hapus"><i class="fa fa-trash"></i> Hapus</a> 
        </div>
        @endif
        <div class="modal-body"> 
            <table class="table table-sm table-borderless"> 
                <tr>
                    <th width="180px">Nomor SKPP</th>
                    <th width="1px">:</th>
                    <td>{{ $info["sales_order"]->SKPP->no_skpp }}</td>
                </tr>
                <tr>
                    <th>Tanggal</th>
                    <th>:</th>
                    <td>{{ $info["sales_order"]->tanggal == null ? '-' : Helper::dateFormat($info["sales_order"]->tanggal, true, 'd/m/Y') }}</td>
                </tr>
                <tr>
                    <th>Nomor sales order</th>
                    <th>:</th>
                    <td>{{ $info["sales_order"]->no_so }}</td>
                </tr>
            </table> 
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th width="1px">NO</th>
                            <th>PRODUK</th>
                            <th>SPESIFIKASI</th>
                            <th>KUANTITAS</th>
                            <th>INCOTERM</th>
                            <th>DOKUMEN</th>
                        </tr>
                    </thead>
                    <tbody>  
                        @php($sub_total = 0)
                        @foreach($info["sales_order"]->SOPO as $sopo)
                        @php($sub_total += (($sopo->Barang->harga_jual / 1.1) * $sopo->kuantitas) )
                        <tr>
                            <td>{{ $loop->iteration }}.</td>
                            <td>{{ $sopo->Barang->Produk->nama }}</td>
                            <td>{{ $sopo->Barang->Produk->spesifikasi }}</td>
                            <td>{{ Helper::comma($sopo->kuantitas) }} MT</td>
                            <td>{{ $sopo->Barang->incoterm }}</td>
                            <td>{{ substr($sopo->SO->SKPP->no_skpp, 0, 4) }}</td> 
                        </tr>
                        @endforeach
                    </tbody>
                </table>

            </div>
        </div>
    </div>
    <div class="tab-pane fade" id="nav-file-sales-order">
        <object data="{{ asset('file_so/'.$info["sales_order"]->file) }}#view=FitH" type="application/pdf" width="100%" height="800px">
          @include('layout.not_found', ['message' => 'File tidak ditemukan'])
        </object>
    </div>
</div>
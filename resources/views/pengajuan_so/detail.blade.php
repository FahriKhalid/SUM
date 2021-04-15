{{-- <nav class="alert-primary">
    <div class="nav nav-tabs nav-justified" id="nav-tab" role="tablist">
        <a class="nav-item text-dark nav-link active" data-toggle="tab" href="#nav-sales-order" role="tab" aria-controls="nav-home" aria-selected="true">Detail</a>
        <a class="nav-item text-dark nav-link" data-toggle="tab" href="#nav-file-sales-order" role="tab" aria-controls="nav-agt-keluarga" aria-selected="true">File</a> 
    </div>
</nav>
 --}}
<div class="tab-content">
    <div class="tab-pane fade show active" id="nav-sales-order">  
        <div class="modal-body">
            <a href="{{ url('pembelian/pengajuan_so/edit/'.$id) }}" class="btn btn-primary"><i class="fa fa-edit"></i> Edit</a>  
            <button type="button" url="{{ url('pembelian/pengajuan_so/destroy/'.$id) }}" class="btn btn-danger delete-pengajuan-so"><i class="fa fa-trash"></i> Hapus</button>
            <button class="btn btn-warning"onclick="show_form_email('dokumen Pengajuan Sales Order', '{{ url('pembelian/pengajuan_so/send_email/'.$id) }}')"><i class="fas fa-paper-plane"></i> Kirim email</button>
        </div>
        <div class="modal-body"> 
            <table class="table table-borderless table-sm"> 
                <tr>
                    <th width="250px">Nomor Pengajuan Sales Order</th>
                    <th width="1px">:</th>
                    <td>{{ $info["pengajuan_so"]->no_pengajuan_so }}</td>
                </tr>
                <tr>
                    <th>Nomor SKPP</th>
                    <th>:</th>
                    <td>{{ $info["pengajuan_so"]->PreOrder->SKPP->no_skpp }}</td>
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
                        @foreach($info["pengajuan_so"]->BarangPengajuanSo as $barang)
                        @php($sub_total += (($barang->harga_jual / 1.1) * $barang->kuantitas) )
                        @if($barang->kuantitas > 0)
                        <tr> 
                            <td>{{ $loop->iteration }}.</td>
                            <td>{{ $barang->Produk->nama }}</td>
                            <td>{{ $barang->Produk->spesifikasi }}</td>
                            <td>{{ $barang->kuantitas }} MT</td>
                            <td>{{ $barang->Barang->incoterm }}</td>
                            <td>{{ substr($info["pengajuan_so"]->PreOrder->SKPP->no_skpp, 0, 4) }}</td> 
                        </tr>
                        @endif
                        @endforeach
                    </tbody>
                </table>

            </div>
        </div>
    </div>
    <div class="tab-pane fade" id="nav-file-sales-order">
        {{-- <object data="{{ asset('file_so/'.$info["pengajuan_so"]->file) }}#view=FitH" type="application/pdf" width="100%" height="800px">
          <div class="text-center">
              <p>File sales order kosong!</p>
          </div>
        </object> --}}
    </div>
</div>
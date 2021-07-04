<nav class="alert-primary">
    <div class="nav nav-tabs nav-justified" id="nav-tab" role="tablist">
        <a class="nav-item text-dark nav-link active" id="nav-home-tab" data-toggle="tab" href="#nav-invoice" role="tab" aria-controls="nav-home" aria-selected="true">Invoice</a>
        <a class="nav-item text-dark nav-link" id="nav-agt-keluarga-tab" data-toggle="tab" href="#nav-faktur-pajak" role="tab" aria-controls="nav-agt-keluarga" aria-selected="true">Faktur Pajak</a> 
    </div>
</nav>

<div class="tab-content">
    <div class="tab-pane fade show active" id="nav-invoice">
        <div class="modal-body">
            <a href="{{ url('penjualan/invoice/edit/'.$id) }}" class="btn btn-primary"><i class="fa fa-edit"></i> Edit</a>
            <a href="{{ url('penjualan/invoice/surat/'.Helper::encodex($info["invoice"]->id_invoice)) }}" target="_blank" class="btn btn-warning"><i class="fa fa-download"></i> Invoice</a>
            <table class="table table-sm table-borderless mt-2"> 
                <tr>
                    <th width="200px">Customer</th>
                    <th width="1px">:</th>
                    <td>
                        <div>{{ $info["invoice"]->SKPP->Customer->kategori == "perusahaan" ? $info["invoice"]->SKPP->Customer->perusahaan ." - ". $info["invoice"]->SKPP->Customer->nama : $info["invoice"]->SKPP->Customer->nama }}</div>
                        <div><i class="fas fa-map-marker-alt"></i> {{ $info["invoice"]->SKPP->Customer->alamat }}</div>
                    </td>
                </tr> 
                <tr>
                    <th>Tanggal</th>
                    <th>:</th>
                    <td>{{ $info["invoice"]->tanggal == null ? '-' : Helper::dateFormat($info["invoice"]->tanggal, true, 'd/m/Y') }}</td>
                </tr>
                <tr>
                    <th>Nomor tagihan</th>
                    <th>:</th>
                    <td>{{ $info["invoice"]->no_tagihan == null ? '-' : $info["invoice"]->no_tagihan }}</td>
                </tr>
                <tr>
                    <th>Nomor resi</th>
                    <th>:</th>
                    <td>{{ $info["invoice"]->no_resi == null ? '-' : $info["invoice"]->no_resi }}</td>
                </tr>
                <tr>
                    <th>Nomor faktur pajak</th>
                    <th>:</th>
                    <td>{{ $info["invoice"]->no_faktur_pajak == null ? '-' : $info["invoice"]->no_faktur_pajak }}</td>
                </tr>
                <tr>
                    <th>Nomor sales order</th>
                    <th>:</th>
                    <td>{{ $info["invoice"]->SO->is_sementara == 1 ? '-' : $info["invoice"]->SO->no_so }}</td>
                </tr>
            </table> 
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th width="1px">NO</th>
                            <th>PRODUK</th>
                            <th>KUANTITAS</th>
                            <th>INCOTERM</th>
                            <th>HARGA JUAL</th>
                            <th>JUMLAH</th>
                        </tr>
                    </thead>
                    <tbody>  
                        @php($sub_total = 0)
                        @foreach($info["invoice"]->SO->SOPO as $sopo)
                        @php($harga = $sopo->Barang->harga_jual / 1.1)
                        @php($sub_total += ($sopo->Barang->harga_jual / 1.1) * (float)$sopo->kuantitas) 
                        <tr>
                            <td>{{ $loop->iteration }}.</td>
                            <td>{{ $sopo->Barang->Produk->nama }}</td>
                            <td>{{ $sopo->kuantitas }} MT</td>
                            <td>{{ $sopo->Barang->incoterm }}</td>
                            <td> 
                                <div class="d-flex justify-content-between">
                                    <div>IDR</div>
                                    <div>{{ Helper::currency(Helper::toFixed(($sopo->Barang->harga_jual / 1.1), 2)) }}</div> 
                                </div>
                            </td>
                            <td width="250px"> 
                                <div class="d-flex justify-content-between">
                                    <div>IDR</div>
                                    <div>{{ Helper::currency(Helper::toFixed((($sopo->Barang->harga_jual / 1.1) * (float)$sopo->kuantitas), 2))  }}</div>
                                </div>
                            </td>
                        </tr>
                        @endforeach  
                        <tr>
                            <td colspan="5" align="right"><b>SUB TOTAL</b></td> 
                            <td>
                                <div class="d-flex justify-content-between">
                                    <div>IDR</div>
                                    <div>{{ Helper::currency(Helper::toFixed($sub_total, 2)) }}</div>
                                </div> 
                            </td>
                        </tr>
                        <tr>
                            <td colspan="5" align="right"><b>PPN</b></td> 
                            <td>
                                <div class="d-flex justify-content-between">
                                    <div>IDR</div> 
                                    <div>{{ Helper::currency(Helper::toFixed($info["invoice"]->ppn, 2)) }}</div>
                                </div>
                            </td>
                        </tr>
                        <tr> 
                            <td colspan="5" align="right"><b>TOTAL</b></td> 
                            <td> 
                                <div class="d-flex justify-content-between">
                                    <div>IDR</div> 
                                    <div>{{ Helper::currency(Helper::toFixed($info["invoice"]->total, 2)) }}</div>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>

            </div>
        </div>
    </div>
    <div class="tab-pane fade" id="nav-faktur-pajak">
        <object data="{{ asset('faktur_pajak/'.$info["invoice"]->file_faktur_pajak) }}#view=FitH" type="application/pdf" width="100%" height="800px">
          <div class="text-center">
              <p>File faktur pajak kosong!</p>
          </div>
        </object>
    </div>
</div>
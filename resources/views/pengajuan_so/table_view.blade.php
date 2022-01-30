<div class="table-responsive">
    <table class="table table-bordered">
        <thead>
            <tr>
                <th width="1px">NO</th>
                <th>PRODUK</th>
                <th>SPESIFIKASI</th>
                <th>KUANTUM</th>
                <th>INCOTERM</th>
                <th>DOKUMEN</th>
            </tr>
        </thead>
        <tbody>  
            @php 
            $total_kuantum = 0;
            @endphp
            @foreach($info["pengajuan_so"]->BarangPengajuanSo as $barang)
            @php  
            $total_kuantum += $barang->kuantitas;
            @endphp

            @if($barang->kuantitas > 0)
            <tr> 
                <td>{{ $loop->iteration }}.</td>
                <td>{{ $barang->Produk->nama }}</td>
                <td>{{ $barang->Produk->spesifikasi }}</td>
                <td>{{ Helper::comma($barang->kuantitas) }} MT</td>
                <td>{{ $barang->Barang->incoterm }}</td>
                <td>{{ substr($info["pengajuan_so"]->PreOrder->SKPP->no_skpp, 0, 4) }}</td> 
            </tr>
            @endif
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td align="right" colspan="3"><b>TOTAL</b></td>
                <td>{{ Helper::comma($total_kuantum) }} MT</td>
                <td></td>
                <td></td>
            </tr>
        </tfoot>
    </table>
</div>
<div class="table-responsive">
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>PRODUK</th>
                <th>SPESIFIKASI</th>
                <th>KUANTITAS</th>
            </tr>
        </thead>
        <tbody>  
            @php($sub_total = 0)
            @foreach($info["sales_order"]->SOPO as $sopo)
            @php($sub_total += (($sopo->Barang->harga_jual / 1.1) * $sopo->kuantitas) )
            <tr>
                <td>{{ $sopo->Barang->Produk->nama }}</td>
                <td>{{ $sopo->Barang->Produk->spesifikasi }}</td>
                <td>{{ $sopo->kuantitas }} MT</td>
            </tr>
            @endforeach
        </tbody>
    </table> 
</div>

<div class="alert alert-warning">
   <i class="fa fa-info-circle"></i> Produk di atas akan dikurangi dengan jumlah stok gudang
</div>
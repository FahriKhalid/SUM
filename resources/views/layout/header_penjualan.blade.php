<div class="height-100 bg-white shadow-sm"> 
   <ul class="nav nav-tabs nav-justified" id="myTab" role="tablist">
        <li class="nav-item">
            <a class="nav-link text-muted {{ Request::segment(2) == 'skpp' ? 'active' : ''}}" href="{{ url('penjualan/skpp/show/'.$id) }}">SKPP</a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-muted {{ Request::segment(2) == 'pembayaran' ? 'active' : ''}}" href="{{ url('penjualan/pembayaran/show/'.$id) }}">Pembayaran</a>
        </li>  
        <li class="nav-item">
            <a class="nav-link text-muted {{ Request::segment(2) == 'salesorder' ? 'active' : ''}}" href="{{ url('penjualan/salesorder/index/'.$id) }}">Sales Order</a>
        </li>  

        <li class="nav-item">
            <a class="nav-link text-muted {{ Request::segment(2) == 'invoice' ? 'active' : ''}}" href="{{ url('penjualan/invoice/index/'.$id) }}">Invoice</a>
        </li>  
    </ul> 
</div>
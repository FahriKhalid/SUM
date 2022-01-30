<div class="height-100 bg-white shadow-sm"> 
   <ul class="nav nav-tabs nav-justified" id="myTab" role="tablist">
        <li class="nav-item">
            <a class="nav-link text-muted {{ Request::segment(2) == 'pre_order' ? 'active' : ''}}" href="{{ url('pembelian/pre_order/show/'.$id) }}">Pre Order</a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-muted {{ Request::segment(2) == 'skpp' ? 'active' : ''}}" href="{{ url('pembelian/skpp/show/'.$id) }}">SKPP & Kode Booking</a>
        </li> 
        <li class="nav-item">
            <a class="nav-link text-muted {{ Request::segment(2) == 'pengajuan_so' ? 'active' : ''}}" href="{{ url('pembelian/pengajuan_so/show/'.$id) }}">Pengajuan SO</a>
        </li> 
        <li class="nav-item">
            <a class="nav-link text-muted {{ Request::segment(2) == 'salesorder' ? 'active' : ''}}" href="{{ url('pembelian/salesorder/show/'.$id) }}">Sales Order</a>
        </li>     
        <li class="nav-item">
            <a class="nav-link text-muted {{ Request::segment(2) == 'invoice' ? 'active' : ''}}" href="{{ url('pembelian/invoice/index/'.$id) }}">Invoice</a>
        </li>       
        <span class="nav-indicator"></span>
    </ul> 
</div>
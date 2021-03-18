<div class="height-100 bg-white shadow-sm"> 
   <ul class="nav nav-tabs nav-justified" id="myTab" role="tablist">
        <li class="nav-item">
            <a class="nav-link text-muted {{ Request::segment(2) == 'pre_order' ? 'active' : ''}}" href="{{ url('pembelian/pre_order/show/'.$id) }}">Pre Order</a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-muted {{ Request::segment(2) == 'skpp' ? 'active' : ''}}" href="{{ url('pembelian/skpp/show/'.$id) }}">SKPP</a>
        </li> 
        <li class="nav-item">
            <a class="nav-link text-muted {{ Request::segment(2) == 'salesorder' ? 'active' : ''}}" href="{{ url('pembelian/salesorder/show/'.$id) }}">Sales Order</a>
        </li>       
    </ul> 
</div>
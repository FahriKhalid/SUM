<div class="height-100 bg-white shadow-sm"> 
   <ul class="nav nav-tabs nav-justified" id="myTab" role="tablist">
        <li class="nav-item">
            <a class="nav-link text-muted {{ Request::segment(2) == 'salesorder' ? 'active' : ''}}" href="{{ url('penjualan/salesorder/show/'.$id) }}">Sales Order</a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-muted {{ Request::segment(1) == 'surat_kuasa' ? 'active' : ''}}" href="{{ url('surat_kuasa/index/'.$id) }}">Surat Kuasa</a>
        </li>   
    </ul> 
</div>
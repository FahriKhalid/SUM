<div class="mb-3">
    <a href="{{ url('surat_kuasa/edit/'.$id) }}" class="btn btn-primary"><i class="fa fa-edit"></i> Edit</a>
    <a href="{{ url('surat_kuasa/surat_kuasa/'.$id) }}" target="_blank" class="btn btn-warning"><i class="fa fa-download"></i> Surat kuasa</a>
    <button class="btn btn-warning"onclick="show_form_email('dokumen Surat Kuasa', '{{ url('surat_kuasa/send_email/'.$id) }}')"><i class="fas fa-paper-plane"></i> Kirim email ({{ isset($info["riwayat_email"]) ? $info["riwayat_email"]->jumlah : '0' }})</button> 
</div>
<table class="table table-sm table-borderless">
    <tr>
        <th width="180px">Nomor surat kuasa</th>
        <th width="1px">:</th>
        <td>{{ $info["sk"]->no_sk }}</td>
    </tr>
    <tr>
        <th>Nama supir</th>
        <th>:</th>
        <td>{{ $info["sk"]->Supir->nama }}</td>
    </tr>
    <tr>
        <th>Plat nomor</th>
        <th>:</th>
        <td>{{ $info["sk"]->Supir->plat_nomor }}</td>
    </tr>
    <tr>
        <th>Nomor telepon</th>
        <th>:</th>
        <td>{{ $info["sk"]->Supir->no_telepon }}</td>
    </tr>
    <tr>
        <th>Gudang</th>
        <th>:</th>
        <td>{{ $info["sk"]->Gudang->nama }}</td>
    </tr>
    <tr>
        <th>Lampiran</th>
        <th>:</th>
        <td>
            @if(count($info["sk"]->Lampiran) > 0) 
                @foreach($info["sk"]->Lampiran as $lampiran)  
                    <span class="badge rounded-pill border custom-pill"> 
                        {{ $lampiran->nama }} . {{ Helper::getExtensionFromString($lampiran->file) }} 
                        @if(Helper::getExtensionFromString($lampiran->file) == "PDF")
                        <a href="javascript:void(0)" onclick="view_lampiran('{{ $lampiran->nama }}','{{ asset('lampiran/'.$lampiran->file) }}')">Lihat dokumen</a>
                        @else
                        <a href="{{ asset('lampiran/'.$lampiran->file) }}" download>Download</a>
                        @endif
                    </span>
                @endforeach 
            @else
            -
            @endif
        </td>
    </tr> 
</table>
<table class="table table-bordered">
    <thead>
        <tr>
            <th>Nama Barang</th>
            <th>Kuantum</th>
            <th>Nomor SO</th>
            <th>Pengambilan</th>
        </tr>
    </thead>
    <tbody>
        @foreach($info["skso"] as $skso)
        <tr>
            <td>{{ $skso->SOPO->Barang->Produk->nama}}</td>
            <td>{{ $skso->kuantitas }} MT</td>
            <td>{{ $skso->SOPO->SO->no_so }}</td>
            <td>{{ $info["sk"]->Gudang->nama }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

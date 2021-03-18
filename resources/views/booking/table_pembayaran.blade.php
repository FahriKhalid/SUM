

<div class="card-body alert-warning text-center mt-3">
    <div class="mt-2">
        <div id="status-piutang">
            @if($info["piutang"] == 0)
                <h3 id="title-sisa-hutang" class="text-dark">Rp {{ Helper::currency($info["booking"]->total_pembayaran) }}</h3>
                <span class="badge-success font-15 badge">LUNAS</span>
            @else
                <h3 id="title-sisa-hutang" class="text-dark">Rp {{ Helper::currency($info["piutang"]) }}</h3>
                <span class="badge-warning font-15 badge">BELUM TERBAYARKAN</span>
            @endif
        </div> 
    </div>
</div>

<table class="table table-sm table-bordered">
    <thead>
        <tr>
            <th>No</th>
            <th width="200px">Bukti pembayaran</th>
            <th>Kode Booking</th>
            <th>Jumlah pembayaran</th>
            <th>Sisa hutang</th>
            <th>Keterangan</th>
            <th>Created by</th>
            <th>Created at</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody> 

        @forelse($info["pembayaran"] as $pembayaran)
        @php($option = $info["last_record"] != $pembayaran->id_pembayaran ? 'disabled' : '')
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>
                <div class="layout-overlay">
                    <img width="100%" src="{{ asset('bukti_pembayaran/'.$pembayaran->file_bukti_pembayaran) }}">
                </div>
            </td>
            <td>{{ $pembayaran->kode_booking}}</td>
            <td>Rp {{ Helper::currency($pembayaran->jumlah_pembayaran) }}</td>
            <td>Rp {{ Helper::currency($pembayaran->sisa_hutang) }}</td>
            <td>{{$pembayaran->keterangan}}</td>
            <td>{{$pembayaran->CreatedBy->nama}}</td>
            <td>{{$pembayaran->created_at}}</td>
            <td> 
                <button {{ $option }} url="{{ url('pembayaran/destroy/'.Helper::encodex($pembayaran->id_pembayaran).'/pembelian/'.Helper::encodex($info["booking"]->id_booking)) }}" jumlah_bayar="{{ $pembayaran->jumlah_pembayaran }}" class="btn btn-sm btn-danger hapus-pembayaran"><i class="fa fa-trash"></i> </button>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="9" class="text-center">
                Belum ada pembayaran
            </td>
        </tr>
        @endforelse
        
    </tbody>
</table>
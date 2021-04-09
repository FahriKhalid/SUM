

<div class="card-body alert-warning text-center mt-3 mb-3">
    <div class="mt-2">
        <div id="status-piutang">
            @if($info["piutang"] == 0)
                <h3 id="title-sisa-hutang" class="text-dark">Rp {{ Helper::currency($info["skpp"]->total_pembayaran) }}</h3>
                <span class="badge-success font-15 badge">LUNAS</span>
            @else
                <h3 id="title-sisa-hutang" class="text-dark">Rp {{ Helper::currency($info["piutang"]) }}</h3>
                <span class="badge-warning font-15 badge">BELUM TERBAYARKAN</span>
            @endif
        </div> 
    </div>
</div>

<table class="table table-sm table-bordered" id="tabel-pembayaran" width="100%">
    <thead>
        <tr>
            <th width="1px">No</th> 
            <th>Kode Booking</th>
            <th>Jumlah pembayaran</th>
            <th>Sisa hutang</th>
            <th>Keterangan</th>
            <th>Created by</th>
            <th>Created at</th>
            <th width="75px">Aksi</th>
        </tr>
    </thead>
</table>
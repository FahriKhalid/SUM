@extends('layout.index')

@section('title', 'SUM - Produk')

@section('css')
	<link rel="stylesheet" type="text/css" href="{{asset('vendor/datatables/dataTables.bootstrap4.min.css')}}"> 
@endsection
 
@section('content')
 
	<div class="container-fluid mt-4"> 
        <div class="row">
            <div class="col-md-12 d-flex justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">PROFIL PERUSAHAAN</h6>   
            </div>  
        </div>
		<div class="card mt-3"> 
			<div class="card-body">
                <a href="{{ url('profil_perusahaan/edit') }}" class="btn btn-warning"><i class="fa fa-edit"></i> Edit</a>
				<div class="table-responsive mt-3">
                    <table class="table table-borderless">
                        <thead>
                            <tr>
                                <th width="200px">Nama perusahaan</th> 
                                <th width="1px">:</th> 
                                <td>{{ $info["data"]->nama }}</td>
                            </tr>
                            <tr>
                                <th>Direktur</th> 
                                <th>:</th> 
                                <td>{{ $info["data"]->direktur }}</td>
                            </tr>
                            <tr>
                                <th>Telepon</th> 
                                <th>:</th> 
                                <td>{{ $info["data"]->telepon }}</td>
                            </tr>
                            <tr>
                                <th>Fax</th> 
                                <th>:</th> 
                                <td>{{ $info["data"]->fax }}</td>
                            </tr>
                            <tr>
                                <th>Alamat</th> 
                                <th>:</th> 
                                <td>{{ $info["data"]->alamat }}</td>
                            </tr>
                            <tr>
                                <th>ATM</th> 
                                <th>:</th> 
                                <td>
                                	@if(count($info["atm"]) > 0)
                                		<ul style="margin-left: -25px">
                                			@foreach($info["atm"] as $atm)
 												<li>{{ $atm->nama }} - {{ $atm->nomor }}</li>
	                                		@endforeach
                                		</ul>
                                	@else

                                	@endif
                                	
                                </td>
                            </tr>
                        </thead> 
                    </table>
                </div>
			</div>
		</div>
	</div>

@endsection
 
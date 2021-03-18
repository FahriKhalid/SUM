@extends('layout.index')

@section('title', 'SUM - Detail SKPP')

@section('css')
	<link rel="stylesheet" type="text/css" href="{{asset('vendor/datatables/dataTables.bootstrap4.min.css')}}"> 
@endsection
 
@section('content')
 
@include('layout.header_pembelian') 

<div class="container-fluid mb-4 mt-4">

    <div class="row">
        <div class="col-md-12 d-flex justify-content-between">
        	<h6 class="m-0 font-weight-bold text-primary"><i class="fa fa-truck-moving"></i> Sales Order</h6>
            <a href="{{ url("pembelian/pre_order") }}" class="text-muted"><i class="fa fa-arrow-left"></i> Kembali</a>   
        </div>  
    </div>    

    <div class="text-center">
		<div>
			<img src="{{ asset('img/add_bg.png') }}" class="mt-4 mb-4" width="200px">

			<p>SKPP masih kosong <br> Buat SKPP terlebih dahulu</p> 
		</div> 
	</div>
</div>

@endsection
 
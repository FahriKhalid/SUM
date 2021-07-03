<!DOCTYPE html>
<html>
<head>
	<title></title>
</head>

<style type="text/css">
	body {
	    /*font-family: Verdana, sans-serif;*/
   		font-size: 12px !important;
	}
	.container {
		padding-left: 20px;
		padding-right: 20px;
	}

	.container-fluid {
		padding: 30px; 
	}

	.text-center {
		text-align:center!important
	}

	.table {
		width: 100%;
		border-collapse: collapse;
	}

	.table-borderless tr, .table-borderless td{
		border: none !important; 
		padding: 0px !important;
	}
 

	.table th, .table td{
		border: 1px solid black; 
	}	

	.table td {
		padding: 5px;
	}

	.mt-25{
		margin-top: 25px;
	}

	.mt-20{
		margin-top: 20px;
	}

	.mt-15{
		margin-top: 15px;
	}

	.mt-10{
		margin-top: 10px;
	}

	.ml-3 {
		margin-left: 3px;
	}

	.border-none {
		border: none !important;
	}

	.text-green-moss {
		color: #103900 !important;
	}

	.verdana {
		font-family: Verdana, sans-serif;
	}

	.text-grey {
		color: #a7a7a7;
	}

	footer {
        position: fixed; 
        bottom: 20px; 
        left: 0px; 
        right: 20px;
        height: 50px; 
        text-align: right;
        color: #a7a7a7;
    }

    .page-break {
	    page-break-after: always;
	} 

	body { 
		background-image: url("img/background_document.png"); 
	  	height: 100%; 
	  	background-position: center;
	  	background-repeat: no-repeat;
	  	background-size: cover;
	}

	@page {
        margin: 0px 0px 0px 0px !important;
        padding: 0px 0px 0px 0px !important;
    }
 	   
	.inline-block {
	  display: inline-block;
	}

	.float-right {
		float: right;
		width: 50%;
	}

	.float-left {
		float: left;
		width: 50%;
	}

	.w-100 {
		width: 100%;
	}

	.table-borderless tr .table-borderless td {
		border: none !important;
	}
</style>

<body>
	<div class="container-fluid">
		<div class="text-dark" style="display: flex; align-items: center;">
			<img src="{{ public_path('img/logo_perusahaan_1.png')}}" width="40px">
			<div style="margin-left: 50px">
				PT SETIAGUNG USAHA MANDIRI
			</div>
		</div>
		
		<div class="text-center" style="margin-top: -20px">
			@yield('title') 
			<div>
				@if(isset($tanggal))
					{{ Helper::dateIndo($tanggal) }}
				@else
					{{ Helper::dateIndo(date('Y-m-d')) }}
				@endif
			</div>
		</div>
		
		@yield('content')
		 
	</div> 

	<div class="container-fluid" style="position: absolute; bottom: 100px">
		<div class="container ttd ml-3"> 
			<div class="text-green-moss"><b>{{ $info["profil_perusahaan"]->nama }}</b> </div> 
			
			<img src="{{ public_path('img/ttd.png') }}" width="110px" style="margin-top: 10px">
			<div><b><u>{{ $info["profil_perusahaan"]->direktur }}</u></b></div>
			<div><b>Direktur</b></div>
		</div>
	</div>


	<footer>
		<div>{{ $info["profil_perusahaan"]->alamat }}</div>
		<div>{{ $info["profil_perusahaan"]->telepon }}</div>
		<div>{{ $info["profil_perusahaan"]->email }}</div>
	</footer>
</body>

</html>
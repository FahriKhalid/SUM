@extends('layout.index')

@section('title', 'SUM - Produk')

@section('css')
	<link rel="stylesheet" type="text/css" href="{{asset('vendor/datatables/dataTables.bootstrap4.min.css')}}"> 
@endsection
 
@section('content')
    
	<div class="container-fluid mt-4 mb-4"> 
        <div class="row">
            <div class="col-md-12 d-flex justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary"><i class="fa fa-edit"></i> Form edit profil perusahaan</h6>
                <a href="{{ url('profil_perusahaan') }}" class="text-muted"><i class="fa fa-arrow-left"></i> Kembali</a>   
            </div>  
        </div>
		<div class="card mt-3"> 
			<form id="form-profil-perusahaan" method="post">
                @csrf
                <div class="card-body">
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>Nama perusahaan  <i class="text-danger">*</i></label>
                            <input type="text" class="form-control" placeholder="wajib diisi" name="nama" value="{{ $info["data"]->nama }}">
                        </div>
                        <div class="form-group col-md-6">
                            <label>Nama direktur  <i class="text-danger">*</i></label>
                            <input type="text" class="form-control" placeholder="wajib diisi" name="direktur" value="{{ $info["data"]->direktur }}">
                        </div>
                        <div class="form-group col-md-6">
                            <label>Telepon  <i class="text-danger">*</i></label>
                            <input type="text" class="form-control" placeholder="wajib diisi" name="telepon" value="{{ $info["data"]->telepon }}">
                        </div>
                        <div class="form-group col-md-6">
                            <label>Fax </label>
                            <input type="text" class="form-control" placeholder="opsional" name="fax" value="{{ $info["data"]->fax }}">
                        </div>
                        <div class="form-group col-md-6">
                            <label>Alamat  <i class="text-danger">*</i></label>
                            <textarea class="form-control" placeholder="wajib diisi" name="alamat">{{ $info["data"]->alamat }}</textarea>
                        </div> 

                        <div class="form-group col-md-6">
                            <label>ATM  <i class="text-danger">*</i></label>
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Nama ATM</th>
                                        <th>No ATM</th>
                                        <th>Status</th>
                                        <th width="1px" class="p-2">
                                            <button type="button" class="btn btn-sm btn-success" onclick="addRow()"><i class="fa fa-plus"></i></button>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody id="form-atm"> 
                                    @include('profil_perusahaan.list_atm')
                                </tbody>
                            </table>     
                        </div>
                    </div>    
                </div>
                <div class="card-footer bg-white">
                    <button class="btn btn-primary"><i class="fa fa-save"></i> Simpan</button>
                </div>         
            </form>
		</div>
	</div>

@endsection

@section('footer')
<script type="text/javascript"> 

    /*
    |--------------------------------------------------------------------------
    | update profil perusahaan
    |--------------------------------------------------------------------------
    */

    $(document).on("submit", "#form-profil-perusahaan", function(e){
        e.preventDefault();

        $.ajax({
            url : '{{ url('profil_perusahaan/update ')}}',
            type : 'POST',
            data : new FormData(this),
            contentType : false,
            processData : false,
            dataType : "json",
            beforeSend : function(){
                loader(".card", true);
            },
            success : function(resp){
                if (resp.status == "error"){
                    for (var i = 0; i < resp.message.length; i++) {
                        toastr.error(resp.message[i],{ "closeButton": true });
                    } 
                } else {
                    toastr.success(resp.message, { "closeButton": true });  
                    $("#form-atm").html(resp.list_atm);

                    location.href = '{{ url('profil_perusahaan') }}';
                }
                
                loader(".card", false);
            },
            error : function(jqXHR, exception){
                errorHandling(jqXHR.status, exception);
                loader(".card", false);
            }
        })
    });


    /*
    |--------------------------------------------------------------------------
    | tambah baris ATM
    |--------------------------------------------------------------------------
    */

    function addRow(){
        var html = $("#form-atm").find("tr:last");

        var clone = html.clone();

        clone.find('button:last')
                .addClass("remove-row")
                .removeClass("btn-dark")
                .removeClass("delete-atm")
                .addClass("btn-danger")
                .attr("onclick", "")
                .find('i').removeClass("fa-trash").addClass("fa-minus");

        clone.find("input").val("");
        clone.find("input[name='atm[]']").attr("name", "new_atm[]");
        clone.find("input[name='no_atm[]']").attr("name", "new_no_atm[]");
        clone.find("select[name='status[]']").attr("name", "new_status[]");
        clone.find("button").prop("disabled", false);

        var append = $("#form-atm").append(clone);
    }

    $("body").delegate(".remove-row", "click", function(e){
        $(this).closest("tr").remove();
    });


    /*
    |--------------------------------------------------------------------------
    | hapus data ATM
    |--------------------------------------------------------------------------
    */  
    $("body").delegate(".delete-atm", "click", function(){  
        $("#form-hapus").attr("action", $(this).attr("url")); 
        $("#modal-konfirmasi-hapus").modal("show");
    });


    $(document).on("submit", "#form-hapus", function(e){
        e.preventDefault();

        $.ajax({
            url : $(this).attr("action"),
            type : 'DELETE',
            data : { "_token" : $('meta[name="csrf-token"]').attr('content') },  
            dataType : "json", 
            beforeSend: function(resp){
                loader(".modal-content", true);
            },
            success : function(resp)
            { 
                if (resp.status == 'success') {
                    toastr.success(resp.message, { "closeButton": true });     
                    $("#modal-konfirmasi-hapus").modal("hide");
                    $("#form-atm").html(resp.list_atm);
                } else {
                    toastr.error(resp.message, { "closeButton": true });
                } 
                loader(".modal-content", false);
            },
            error : function (jqXHR, exception) {
                errorHandling(jqXHR.status, exception); 
                loader(".modal-content", false);
            }
        })
    })

</script>
@endsection
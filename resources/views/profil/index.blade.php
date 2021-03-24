@extends('layout.index')


@section('title', 'SUM | Profil')


@section('content')

<div class="container-fluid mt-4">

    <div class="row mb-3">
        <div class="col-md-12 d-flex justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-user"></i> PROFIL</h6>
            <a href="{{url('dashboard')}}" class="text-muted"><i class="fa fa-arrow-left"></i> Kembali</a>   
        </div>
    </div>

    <div class="card">
        <form id="form-profil">
            @csrf
            <div class="card-body"> 
                <div class="form-group">
                    <label>Nama <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="nama" value="{{ Auth::user()->nama }}" maxlength="50" placeholder="Wajib diisi">
                </div> 
                <div class="form-group">
                    <label>Email <span class="text-danger">*</span></label>
                    <input type="email" class="form-control" name="email" value="{{ Auth::user()->email }}" maxlength="50" placeholder="Wajib diisi">
                </div>   

                <div class="form-group">
                    <label>Username <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="username" value="{{ Auth::user()->username }}" maxlength="50" placeholder="Wajib diisi">
                </div> 

                <div class="custom-control custom-checkbox mb-2 mt-4" id="checkbox-password">
                    <input type="checkbox" class="custom-control-input" value="1" name="is_change_password" id="change-password">
                    <label class="custom-control-label" for="change-password">Centang jika ingin merubah password</label>
                </div>

                <div id="form-password" class="d-none">
                    <div class="form-group">
                        <label>Password <span class="text-danger">*</span></label>
                        <input type="Password" class="form-control" name="password" maxlength="50" placeholder="Wajib diisi">
                    </div> 
                    <div class="form-group">
                        <label>Konfirmasi password <span class="text-danger">*</span></label>
                        <input type="Password" class="form-control" name="password_confirmation" maxlength="50" placeholder="Isian harus sama dengan password">
                    </div> 
                </div> 
            </div>
            <div class="card-footer bg-white">
                <div class="d-flex justify-content-between">
                    <div></div>
                    <div>
                        <button class="btn btn-primary" type="submit" ><i class="fa fa-save"></i> Simpan</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

@endsection

@section('footer')
<script type="text/javascript">
    /*
    |--------------------------------------------------------------------------
    | show hode form password
    |--------------------------------------------------------------------------
    */

    $("body").delegate("#change-password", "click", function(){
        if($(this).is(":checked")){
            $("#form-password").removeClass("d-none"); 
        }else{
            $("#form-password").addClass("d-none");
        }
    });


    /*
    |--------------------------------------------------------------------------
    | form aksi tambha dan edit
    |--------------------------------------------------------------------------
    */ 

    $(document).on("submit", "#form-profil", function(e){
        e.preventDefault();
 
        $.ajax({
            url : '{{ url('profil/update') }}/'+'{{ Helper::encodex(Auth::user()->id_user) }}',
            method : "POST",
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
                }
                
                loader(".card", false);
            },
            error : function(jqXHR, exception){
                errorHandling(jqXHR.status, exception);
                loader(".card", false);
            }
        });
    });
</script>
@endsection

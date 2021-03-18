<div class="modal fade" id="modal-form-user" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="title-modal-form-user" ></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="form-user">
                @csrf
                <input type="hidden" name="id">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Nama <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="nama" maxlength="50" placeholder="Wajib di isi">
                    </div> 
                    <div class="form-group">
                        <label>Email <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" name="email" maxlength="50" placeholder="Wajib di isi">
                    </div>  

                    <div class="form-group">
                        <label>Role <span class="text-danger">*</span></label>
                        <select class="form-control" name="role">
                            @foreach($info["role"] as $role)
                                <option value="{{ $role->id_role }}">{{ $role->role}}</option>
                            @endforeach
                        </select>
                    </div> 

                    <div class="form-group">
                        <label>Username <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="username" maxlength="50" placeholder="Wajib di isi">
                    </div> 

                    <div class="custom-control custom-checkbox mb-2 mt-4" id="checkbox-password">
                        <input type="checkbox" class="custom-control-input" value="1" name="is_change_password" id="change-password">
                        <label class="custom-control-label" for="change-password">Centang jika ingin merubah password</label>
                    </div>

                    <div id="form-password">
                        <div class="form-group">
                            <label>Password <span class="text-danger">*</span></label>
                            <input type="Password" class="form-control" name="password" maxlength="50" placeholder="Wajib di isi">
                        </div> 
                        <div class="form-group">
                            <label>Konfirmasi password <span class="text-danger">*</span></label>
                            <input type="Password" class="form-control" name="password_confirmation" maxlength="50" placeholder="Isian harus sama dengan password">
                        </div> 
                    </div>

                    
                    <div class="form-group"> 
                        <div class="custom-control custom-radio custom-control-inline">
                            <input type="radio" id="aktif" value="1" checked="" required="" name="status" class="custom-control-input">
                            <label class="custom-control-label" for="aktif">Aktif</label>
                        </div>
                        <div class="custom-control custom-radio custom-control-inline">
                            <input type="radio" id="tidak-aktif" value="0" name="status" class="custom-control-input">
                            <label class="custom-control-label" for="tidak-aktif">Tidak aktif</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fa fa-times-circle"></i> Close</button>
                    <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>


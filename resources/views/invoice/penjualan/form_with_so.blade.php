<form id="form-so" enctype="multipart/form-data"> 
	<div class="card-body" id="layout-parent">   
		<div class="form-row "> 
            <div class="form-group col-md-6">
                <div class="form-group"> 
                    <label>Tanggal <span class="text-danger">*</span></label>
                    <input type="text" class="form-control datepicker" value="{{ date('d/m/Y') }}" name="tanggal" placeholder="Wajib di isi"> 
                </div>
            </div> 
            <div class="form-group col-md-6">
                <div class="form-group"> 
                	<label>Nomor Tagihan <span class="text-danger">*</span></label>
                    <input class="form-control" name="nomor_tagihan" value="{{ $info["no_tagihan"]  }}" placeholder="Wajib diisi"> 
                </div>
            </div> 
            <div class="form-group col-md-6">
                <div class="form-group"> 
                	<label>Nomor Resi </label>
                    <input class="form-control" autocomplete="off" name="nomor_resi" value="" placeholder="Opsional"> 
                </div>
            </div> 
            <div class="form-group col-md-6"> 
                <div class="form-group"> 
                	<label>Nomor Faktur pajak <span class="text-danger">*</span></label>
                    <input class="form-control" autocomplete="off" name="nomor_faktur_pajak" value="" placeholder="Wajib diisi"> 
                </div> 
            </div> 
            <div class="form-group col-md-6"> 
                <div class="form-group"> 
                	<label>File Faktur pajak <span class="text-danger">*</span></label>
                    <input type="file" class="form-control" name="file_faktur_pajak"> 
                </div> 
            </div>  
            <div class="form-group col-md-6"> 
                <div class="form-group"> 
                	<label>Nomor Sales Order <span class="text-danger">*</span></label> 
                  <select name="so" class="form-control select2">
                      <option value="">-- Pilih --</option> 
                      @forelse($info["so"] as $so)
                      @if($so->Invoice == null)
                      <option value="{{ Helper::encodex($so->id_so) }}">{{ $so->no_so }}</option>
                      @endif
                      @empty
                      @endforelse 
                  </select>   
              </div>
          </div> 
      </div>
      <div id="table-sopo" class="collapse"></div>
  </div>   
  <div class="card-body border-top d-flex justify-content-between"> 
      <div>
         <small>
            <span class="text-danger font-italic">
               <div>Note : </div>
               <div>- Extensi file yang diperbolehkan hanya JPG, JPEG, PNG dan PDF.</div>
               <div>- Maksimal ukuran file 2 Mb.</div>
               <div>- Pastikan sales order yang dipilih sudah delivered</div>
           </span>
       </small>
   </div>
   @if(!PembayaranService::isBayar(Helper::decodex($id)))
   <div>
     @csrf
     <input type="hidden" name="id_skpp" value="{{ $id }}">
     <button class="btn btn-primary"><i class="fa fa-save"></i> Simpan</button> 
 </div>
 @endif
</div>
</form>
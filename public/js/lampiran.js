/*
|--------------------------------------------------------------------------
| Shoh hide form lampiran
|--------------------------------------------------------------------------
*/

$("body").delegate("#show-form-lampiran", "click", function(){
	if($(this).is(":checked")){
        $("#form-lampiran").collapse("show"); 
    }else{
        $("#form-lampiran").collapse("hide");
    }
});


/*
|--------------------------------------------------------------------------
| add rows lampiran
|--------------------------------------------------------------------------
*/

function addRowLampiran(){
	var clone = $("#form-parent-lampiran").find("tr:last").clone();	

	clone.find('button:last').addClass("remove-lampiran")
			.removeClass("delete-lampiran")
			.removeClass("btn-success")
			.removeClass("btn-dark")
			.addClass("btn-danger")
			.attr("onclick", "")
			.find('i').removeClass("fa-plus").removeClass("fa-trash").addClass("fa-minus");

	clone.find("input[name='file[]']").attr("name", "new_file[]")
		 .parent().find(".input-group-append").addClass("d-none");
	clone.find("input[name='nama_file[]']").attr("name", "new_nama_file[]");
	clone.find("textarea[name='keterangan_file[]']").attr("name", "new_keterangan_file[]");

	clone.find("input").val("");
	clone.find("textarea").val("");

	$("#form-parent-lampiran").append(clone);
}




/*
|--------------------------------------------------------------------------
| remove row lampiran
|--------------------------------------------------------------------------
*/

$("body").delegate(".remove-lampiran", "click", function(){
	$(this).closest("tr").remove();
});
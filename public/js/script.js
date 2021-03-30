/*
|--------------------------------------------------------------------------
| Function Error handling ajax
|--------------------------------------------------------------------------
*/   

function errorHandling(jqXHR, exception) 
{
    let error = "";

    if (jqXHR===0) {
        error = ' Koneksi terputus ';
    }else if(jqXHR===404){

        error = ' request not found ';
    }else if(jqXHR===500){

        error = ' internal server Error ';
    }else if(exception==='parseerror'){

        error = 'Request Json Parse failed';
    }else if(exception==='timeout'){

        error = 'Timeout Error';
    }else if(exception==='abort'){

        error = 'Ajax Request Aborted';
    }else{

        error = 'error '+jqXHR.responseText;
    } 

    Swal.fire('Error', error,'error');
}


/*
|--------------------------------------------------------------------------
| Function Loader
|--------------------------------------------------------------------------
*/ 

function loader(selector, boolean){
    if(boolean === true){
        $(selector).waitMe({
            effect: 'ios',
            text : 'Loading ...',
            bg: 'rgba(255,255,255,0.7)',
            color: '#03a9f4',
            textPos: 'horizontal',
            maxSize:30,
            fontSize :'18px'
        });  
    }else{
        $(selector).waitMe("hide");
    }
} 


/*
|--------------------------------------------------------------------------
| Function input numeric
|--------------------------------------------------------------------------
*/ 

if($("input.numeric:text").length > 0)
{ 
    input_numeric();

    function input_numeric(){
        $("input.numeric:text").inputmask('numeric', {
            groupSeparator: '.',
            radixPoint : ',',
            autoGroup: true,
            digits: 2,
            digitsOptional: false,
            placeholder: '0,00',
        });
    }
}

if($("input.number:text").length > 0)
{ 
    input_number();
    
    function input_number(){
        $("input.number:text").inputmask('numeric', {min: 0}); 
    }
}

if($("input.email").length > 0)
{  
    $("input.email").inputmask({
        mask: "*{1,20}[.*{1,20}][.*{1,20}][.*{1,20}]@*{1,20}[.*{2,6}][.*{1,2}]",
        greedy: false,
        onBeforePaste: function (pastedValue, opts) {
          pastedValue = pastedValue.toLowerCase();
          return pastedValue.replace("mailto:", "");
        },
        definitions: {
          '*': {
            validator: "[0-9A-Za-z!#$%&'*+/=?^_`{|}~\-]",
            casing: "lower"
          }
        }
    });
}

/*
|--------------------------------------------------------------------------
| Function Datatable
|--------------------------------------------------------------------------
*/ 
 
function table(id, url, columns)
{
    if(id.length > 0)
    {
        var table =  $(id).DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url : url
            },
            columns: columns,
            responsive: true,
            colReorder: true, 
        });
        
        // table.on('m-datatableâ€“on-init', function () {
        //     $('[data-toggle="tooltip"]').tooltip();
        // });
    }
}


function refresh_table(selector){
    $(selector).DataTable().draw(true);
}


/*
|--------------------------------------------------------------------------
| Function select2
|--------------------------------------------------------------------------
*/ 

$(".select2").select2({
    theme : 'bootstrap4',
    width : '100%'
});


/*
|--------------------------------------------------------------------------
| Function numeric
|--------------------------------------------------------------------------
*/ 

function convertNumeric(number){
    var string = number.split('.').join("");
    return parseFloat(string.split(',').join("."));
}

function formatNumber(n, p, ts, dp) {
    var t = [];
    // Get arguments, set defaults
    if (typeof p  == 'undefined') p  = 2;
    if (typeof ts == 'undefined') ts = '.';
    if (typeof dp == 'undefined') dp = ',';

    // Get number and decimal part of n
    n = Number(n).toFixed(p).split('.');

    // Add thousands separator and decimal point (if requied):
    for (var iLen = n[0].length, i = iLen? iLen % 3 || 3 : 0, j = 0; i <= iLen; i+=3) {
        t.push(n[0].substring(j, i));
        j = i;
    }
    // Insert separators and return result
    return t.join(ts) + (n[1]? dp + n[1] : '');
}

/*
|--------------------------------------------------------------------------
| Function datpicker
|--------------------------------------------------------------------------
*/

$('.datepicker').datepicker({
    showOtherMonths: true,
    uiLibrary: 'bootstrap4',
    format: 'dd/mm/yyyy'
});


/*
|--------------------------------------------------------------------------
| Function check value duplicate in array
|--------------------------------------------------------------------------
*/

function checkIfDuplicateExists(w){
    return new Set(w).size !== w.length 
}
 

/*
|--------------------------------------------------------------------------
| Function cookies
|--------------------------------------------------------------------------
*/

function setCookie(name,value,days) {
    var expires = "";
    if (days) {
        var date = new Date();
        date.setTime(date.getTime() + (days*24*60*60*1000));
        expires = "; expires=" + date.toUTCString();
    }
    document.cookie = name + "=" + (value || "")  + expires + "; path=/";
}

function getCookie(name) {
    var nameEQ = name + "=";
    var ca = document.cookie.split(';');
    for(var i=0;i < ca.length;i++) {
        var c = ca[i];
        while (c.charAt(0)==' ') c = c.substring(1,c.length);
        if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
    }
    return null;
}

function eraseCookie(name) {   
    document.cookie = name +'=; Path=/; Expires=Thu, 01 Jan 1970 00:00:01 GMT;';
}
 

$("#sidebarToggle").on("click", function(){
    if(menuExpand == null || menuExpand == ""){
        setCookie("menuExpand", "yes", 30); 
        menuExpand = "yes";
        cookie('/cookie/set');
    } else {
        eraseCookie("menuExpand"); 
        menuExpand = null;
        cookie('/cookie/unset');
    } 

    
});
 
function cookie(url){
    $.ajax({
        url : url,
        type : 'GET',
        dataType : 'json',
        success : function(resp){
            console.log(resp);
        }, 
        error : function(){

        }
    })
}


/*
|--------------------------------------------------------------------------
| Function tooltip
|--------------------------------------------------------------------------
*/

$('[data-toggle="tooltip"]').tooltip()


/*
|--------------------------------------------------------------------------
| Function send email
|--------------------------------------------------------------------------
*/

function show_form_email(keterangan, url)
{
    $("#modal-send-email").modal("show"); 
    let parent = $("#form-send-email")
    parent.find("input[name=url]").val(url);
    parent.find("#keterangan-email").html(keterangan);
}

$(document).on("submit", "#form-send-email", function(e){
    e.preventDefault();
    let url = $(this).find("input[name=url]").val();
    $.ajax({
        url : url,
        type : "POST",
        data : new FormData(this),
        contentType : false,
        processData : false,
        dataType : "json",
        beforeSend : function(){
            loader(".modal-content", true);
        },
        success : function(resp){
            if (resp.status == "error"){
                toastr.error(resp.message,{ "closeButton": true });
            } else {
                toastr.success(resp.message, { "closeButton": true });  
                $("#modal-send-email").modal("hide"); 
            } 

            loader(".modal-content", false);
        },
        error : function(jqXHR, exception){
            errorHandling(jqXHR.status, exception);
            loader(".modal-content", false);
        }
    })
});
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" id="csrf-token" content="{{ csrf_token() }}"> 
    <meta name="author" content="Pahri Khalid">
    <meta name="email" content="fahri.halid@gmail.com">
    <link rel="icon" href="/img/favicon.ico" type="image/x-icon"/>

    <title>
        @yield('title')
    </title>

    <!-- Custom fonts for this template-->
    <link href="{{asset('vendor/fontawesome-free/css/all.min.css')}}" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="{{ asset('css/sb-admin-2.min.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/datatables/responsive.dataTables.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('vendor/toastr/toastr.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('vendor/waitme/waitme.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('vendor/select2/select2.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('vendor/select2/select2-bootstrap4.css') }}" rel="stylesheet" type="text/css" >
    <link href="https://unpkg.com/gijgo@1.9.13/css/gijgo.min.css" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" type="text/css" href="{{ asset('css/style.css')}}">

    @yield('css')

</head>

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

        <!-- Sidebar -->
        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion {{ Helper::menu_expand() != null ? 'toggled' : '' }}" id="accordionSidebar">

            <!-- Sidebar - Brand -->
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.html">
                <img src="{{ asset('img/logo_perusahaan.png') }}" width="50px">
                <div class="sidebar-brand-text mx-3">SETIAGUNG <div style="font-size: 10px;">USER MANAGEMENT</div></div>
            </a>

            @include('layout.menu')

            <!-- Sidebar Toggler (Sidebar) -->
            <div class="text-center d-none d-md-inline">
                <button class="rounded-circle border-0" id="sidebarToggle"></button>
            </div>

        </ul>
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- Topbar -->
                <nav class="navbar navbar-expand navbar-light bg-white topbar static-top">

                    <!-- Sidebar Toggle (Topbar) -->
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="fa fa-bars"></i>
                    </button>

                    <!-- Topbar Search -->
                    <form
                        class="d-none d-sm-inline-block form-inline mr-auto ml-md-3 my-2 my-md-0 mw-100 navbar-search">
                        <div class="input-group">
                            <input type="text" class="form-control bg-light border-0 small" placeholder="Search for..."
                                aria-label="Search" aria-describedby="basic-addon2">
                            <div class="input-group-append">
                                <button class="btn btn-primary" type="button">
                                    <i class="fas fa-search fa-sm"></i>
                                </button>
                            </div>
                        </div>
                    </form>

                    <!-- Topbar Navbar -->
                    <ul class="navbar-nav ml-auto">

                        <!-- Nav Item - Search Dropdown (Visible Only XS) -->
                        <li class="nav-item dropdown no-arrow d-sm-none">
                            <a class="nav-link dropdown-toggle" href="#" id="searchDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-search fa-fw"></i>
                            </a>
                            <!-- Dropdown - Messages -->
                            {{-- <div class="dropdown-menu dropdown-menu-right p-3 shadow animated--grow-in"
                                aria-labelledby="searchDropdown">
                                <form class="form-inline mr-auto w-100 navbar-search">
                                    <div class="input-group">
                                        <input type="text" class="form-control bg-light border-0 small"
                                            placeholder="Search for..." aria-label="Search"
                                            aria-describedby="basic-addon2">
                                        <div class="input-group-append">
                                            <button class="btn btn-primary" type="button">
                                                <i class="fas fa-search fa-sm"></i>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div> --}}
                        </li>

                        <!-- Nav Item - Alerts -->
                        {{-- <li class="nav-item dropdown no-arrow mx-1">
                            <a class="nav-link dropdown-toggle" href="#" id="alertsDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-bell fa-fw"></i>
                                <!-- Counter - Alerts -->
                                <span class="badge badge-danger badge-counter">3+</span>
                            </a>
                            <!-- Dropdown - Alerts -->
                            <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                aria-labelledby="alertsDropdown">
                                <h6 class="dropdown-header">
                                    Alerts Center
                                </h6>
                                <a class="dropdown-item d-flex align-items-center" href="#">
                                    <div class="mr-3">
                                        <div class="icon-circle bg-primary">
                                            <i class="fas fa-file-alt text-white"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="small text-gray-500">December 12, 2019</div>
                                        <span class="font-weight-bold">A new monthly report is ready to download!</span>
                                    </div>
                                </a>
                                <a class="dropdown-item d-flex align-items-center" href="#">
                                    <div class="mr-3">
                                        <div class="icon-circle bg-success">
                                            <i class="fas fa-donate text-white"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="small text-gray-500">December 7, 2019</div>
                                        $290.29 has been deposited into your account!
                                    </div>
                                </a>
                                <a class="dropdown-item d-flex align-items-center" href="#">
                                    <div class="mr-3">
                                        <div class="icon-circle bg-warning">
                                            <i class="fas fa-exclamation-triangle text-white"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="small text-gray-500">December 2, 2019</div>
                                        Spending Alert: We've noticed unusually high spending for your account.
                                    </div>
                                </a>
                                <a class="dropdown-item text-center small text-gray-500" href="#">Show All Alerts</a>
                            </div>
                        </li> --}}
 

                        <div class="topbar-divider d-none d-sm-block"></div>

                        <!-- Nav Item - User Information -->
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small">{{ Auth::user()->nama }}</span>
                                <img class="img-profile rounded-circle"
                                    src="https://startbootstrap.github.io/startbootstrap-sb-admin-2/img/undraw_profile.svg">
                            </a>
                            <!-- Dropdown - User Information -->
                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                aria-labelledby="userDropdown">
                                <a class="dropdown-item" href="{{ url('profil') }}">
                                    <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Profile
                                </a> 
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="javascript::void(0);" data-toggle="modal" data-target="#logoutModal">
                                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Logout
                                </a>
                            </div>
                        </li>

                    </ul>

                </nav>
                <!-- End of Topbar -->

                <!-- Begin Page Content -->
                {{-- <div class="container-fluid">

                    
                </div> --}}
                <!-- /.container-fluid -->

                @yield('content')

                @include('layout.modal_konfirmasi_hapus')

                @include('layout.modal_view_dokumen')

            </div>
            <!-- End of Main Content -->

            <!-- Footer -->
            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>Copyright &copy; SUM 2020</span>
                    </div>
                </div>
            </footer>
            <!-- End of Footer -->

        </div>
        <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper --> 
    
    <!-- Logout Modal-->
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Ready to Leave?</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    <a class="btn btn-primary" href="{{ url('logout') }}">Logout</a>
                </div>
            </div>
        </div>
    </div>

    <script type="text/javascript">
        var menuExpand = '{{ Helper::menu_expand() }}'; 
    </script>
    <!-- Bootstrap core JavaScript-->
    <script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <!-- Core plugin JavaScript-->
    <script src="{{ asset('vendor/toastr/toastr.min.js') }}"></script>
    <script src="{{ asset('vendor/waitme/waitme.min.js') }}"></script>
    <script src="{{ asset('vendor/mask/jquery.mask.min.js') }}"></script>
    <script src="{{ asset('vendor/mask/jquery.inputmask.bundle.js') }}"></script> 
    <script src="{{ asset('vendor/select2/select2-bootstrap4.min.js') }}"></script> 
    <script src="{{ asset('vendor/gijgo/gijgo.min.js') }}" type="text/javascript"></script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@10"></script>

    <!-- Custom scripts for all pages-->
    <script src="{{ asset('js/sb-admin-2.min.js') }}"></script>
    {{-- <script src="{{ asset('js/script.js') }}"></script> --}}
    <script type="text/javascript">
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

        if($(".select2").length > 0){
            $(".select2").select2({
                theme : 'bootstrap4',
                width : '100%'
            });
        }

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
        

        /*
        |--------------------------------------------------------------------------
        | Function filter Datatable
        |--------------------------------------------------------------------------
        */   

        function filterDatatable(selector)
        {
            $("body").delegate(selector+" select", "change", function(){
                $(selector).DataTable().draw(true);
            });

            $("body").delegate(selector+" input:not(.datepicker-table)", "keyup", function(){
                $(selector).DataTable().draw(true);
            });

            $('.datepicker-table').each(function() {
                $(this).datepicker({
                    showOtherMonths: true,
                    uiLibrary: 'bootstrap4',
                    format: 'dd/mm/yyyy',
                    change: function (e) {
                        $(selector).DataTable().draw(true);
                    }
                });
            }); 
        }
    </script>
    @yield('footer')

</body>

</html>
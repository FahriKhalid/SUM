<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no"> 
    <meta name="author" content="Pahri Khalid">
    <meta name="email" content="fahri.halid@gmail.com">
    <link rel="icon" href="{{ asset('img/logo_perusahaan.ico') }}" type="image/x-icon" />

    <title>SUM - Login</title>

    <!-- Custom fonts for this template-->
    <link href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="{{ asset('css/sb-admin-2.min.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/waitme/waitme.min.css') }}" rel="stylesheet">
    <style type="text/css">
        .bg-image {
            background: white;
            background-position-x: 0%;
            background-position-y: 0%;
            background-image: none;
            background-size: auto;
            background-image: url('/img/background.png');
            background-size: cover;
            background-position: center center;
        }
    </style>
</head>

<body class="bg-gradient-primary">

    <div class="container">

        <!-- Outer Row -->
        <div class="row justify-content-center">

            <div class="col-xl-10 col-lg-12 col-md-9"> 
                <div class="card o-hidden border-0 shadow-lg my-5">
                    <div class="card-body p-0">
                        <!-- Nested Row within Card Body -->
                        <div class="row">
                            <div class="col-lg-6 d-none d-lg-block bg-login-image"></div>
                            <div class="col-lg-6" id="loader">
                                <div class="p-5">
                                    <div class="text-center">
                                        <img src="{{asset('img/logo_perusahaan.png')}}">
                                        <h1 class="h4 text-gray-900 mb-4">Welcome Back!</h1>
                                    </div>
                                    <form class="user" id="form-login">
                                        @csrf
                                        <div class="form-group">
                                            <input type="text" name="username" class="form-control form-control-user"
                                                id="exampleInputEmail" aria-describedby="emailHelp"
                                                placeholder="Enter username">
                                        </div>
                                        <div class="form-group">
                                            <input type="password" name="password" class="form-control form-control-user"
                                                id="exampleInputPassword" placeholder="Enter password">
                                        </div>
                                        <div class="form-group">
                                            <div class="custom-control custom-checkbox small">
                                                <input type="checkbox" class="custom-control-input" name="remember" id="customCheck">
                                                <label class="custom-control-label" for="customCheck">Remember
                                                    Me</label>
                                            </div>
                                        </div>
                                        <button class="btn btn-primary btn-user btn-block">
                                            Login
                                        </button> 
                                    </form>
                                    <hr>
                                    <div class="text-center">
                                        <a class="small" href="forgot-password.html">Forgot Password?</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>

    </div>

    <!-- Bootstrap core JavaScript-->
    <script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('vendor/waitme/waitme.min.js') }}"></script>

    <!-- Core plugin JavaScript-->
    <script src="{{ asset('vendor/jquery-easing/jquery.easing.min.js') }}"></script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@10"></script>

    <!-- Custom scripts for all pages-->
    <script src="{{ asset('js/sb-admin-2.min.js') }}"></script>
    <script src="{{ asset('js/script.js') }}"></script>

    <script type="text/javascript">
        $(document).on("submit", "#form-login", function(e){
            e.preventDefault(); 
            
            $.ajax({
                url : '{{ url('/') }}',
                type : 'POST',
                data : new FormData(this),
                processData : false,
                contentType : false,
                dataType : 'json',
                beforeSend : function(resp){
                    loader("#loader", true);
                },
                success : function(resp){
                    if (resp.status == "error") {
                        alert(resp.message);
                        loader("#loader", false);
                    } else {
                        location.href = "{{ url('dashboard') }}";
                    }
                },
                error : function(jqXHR, exception){
                    errorHandling(jqXHR, exception);
                    loader("#loader", false);
                }
            });
        }); 
    </script>

</body>

</html>
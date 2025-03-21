<!doctype html>
<html lang="en">


<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/x-icon" href="{{asset('admin/img/logo/logo.png')}}">
    <!-- Title -->
    <title>{{config('app.name')}} | Login</title>
    <!-- Plugins File -->
    <link rel="stylesheet" href="{{asset('admin/css/bootstrap.min.css')}}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="{{asset('admin/css/animate.css')}}">
    <link rel="stylesheet" href="{{asset('admin/css/style.css')}}">
    <style>
        .error {
            color: red;
        }

        .rc_logo img {
            max-width: 100px;
            display: block;
            margin-inline: auto;
            margin-bottom: 15px;
        }
    </style>
</head>

<body class="login-area">

    <!-- Preloader -->
    <div id="preloader">
        <div class="loader">
            <svg width="240" height="240" viewBox="0 0 240 240">
                <circle class="loader-ring loader-ring-a" cx="120" cy="120" r="105" fill="none" stroke="#000" stroke-width="20" stroke-dasharray="0 660" stroke-dashoffset="-330" stroke-linecap="round"></circle>
                <circle class="loader-ring loader-ring-b" cx="120" cy="120" r="35" fill="none" stroke="#000" stroke-width="20" stroke-dasharray="0 220" stroke-dashoffset="-110" stroke-linecap="round"></circle>
                <circle class="loader-ring loader-ring-c" cx="85" cy="120" r="70" fill="none" stroke="#000" stroke-width="20" stroke-dasharray="0 440" stroke-linecap="round"></circle>
                <circle class="loader-ring loader-ring-d" cx="155" cy="120" r="70" fill="none" stroke="#000" stroke-width="20" stroke-dasharray="0 440" stroke-linecap="round"></circle>
            </svg>
        </div>

    </div>
    <!-- /Preloader -->

    <!-- ======================================
    ******* Page Wrapper Area Start **********
    ======================================= -->
    <div class="main-content- h-100vh">
        <div class="container h-100">
            <div class="row h-100 align-items-center justify-content-center">
                <div class="col-sm-10 col-md-7 col-lg-5">
                    <!-- Middle Box -->
                    <div class="middle-box">
                        <div class="card-body">
                            @if(Session::has('error_msg'))
                            <div class="alert alert-danger"> {{ Session::get('error_msg') }} </div>
                            @endif

                            @if (Session::has('success_msg'))
                            <div class="alert alert-success"> {{ Session::get('success_msg') }} </div>
                            @endif

                            <div class="log-header-area card p-4 mb-4 text-center">
                                <div class="rc_logo">
                                    <img src="{{asset('admin/img/logo/logo.png')}}" alt="">

                                </div>
                                <h5 class="text-white">Welcome Back !</h5>
                                <p class="mb-0 text-white">Log in to continue.</p>
                            </div>

                            <div class="card ct_login_body">
                                <div class="card-body p-4">
                                    <form method="POST" action="{{ route('admin-login') }}" id="loginForm">
                                        @csrf
                                        <div class="form-group mb-3">
                                            <label class="text-muted" for="emailaddress">Email address</label>
                                            <input class="form-control ct_input @error('email') is-invalid @enderror" type="email" name="email" id="emailaddress"
                                                placeholder="Enter your email">
                                            @error('email')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                            @enderror
                                        </div>

                                        <div class="form-group mb-3">
                                            <label class="text-muted" for="password">Password</label>
                                            <div class="position-relative">
                                                <input class="form-control ct_input @error('password') is-invalid @enderror" type="password" name="password" id="password"
                                                    placeholder="Enter your password">
                                                <i class="fa fa-eye-slash ct_show_eye" aria-hidden="true"></i>
                                                @error('password')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                                @enderror
                                            </div>
                                        </div>


                                        <div class="form-group mb-3 mt-4">
                                            <button class="ct_custom_btn1 w-100" type="submit">Log In</button>
                                        </div>

                                        @if (Route::has('password.request'))
                                        <a class="btn btn-link" href="{{ route('password.request') }}">
                                            {{ __('Forgot Your Password?') }}
                                        </a>
                                        @endif
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ======================================
    ********* Page Wrapper Area End ***********
    ======================================= -->

    <!-- Must needed plugins to the run this Template -->
    <script src="{{asset('admin/js/jquery.min.js')}}"></script>
    <script src="{{asset('admin/js/bootstrap.bundle.min.js')}}"></script>
    <script src="{{asset('admin/js/default-assets/setting.js')}}"></script>
    <script src="{{asset('admin/js/default-assets/scrool-bar.js')}}"></script>
    <script src="{{asset('admin/js/todo-list.js')}}"></script>

    <!-- Active JS -->
    <script src="{{asset('admin/js/default-assets/active.js')}}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.1/jquery.validate.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#loginForm').validate({
                rules: {
                    email: {
                        required: true,
                        email: true,
                        maxlength: 255
                    },
                    password: {
                        required: true,
                        minlength: 8
                    }
                },
                messages: {
                    email: {
                        required: "Please enter an email address",
                        email: "Please enter a valid email address",
                        maxlength: "Email must not exceed 255 characters"
                    },
                    password: {
                        required: "Please enter a password",
                        minlength: "Password must be at least 8 characters long"
                    }
                },
                submitHandler: function(form) {
                    form.submit();
                }
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            $('.ct_show_eye').click(function() {
                var passwordField = $(this).siblings('input');
                if (passwordField.attr('type') === 'password') {
                    passwordField.attr('type', 'text');
                    $(this).removeClass('fa-eye-slash').addClass('fa-eye');
                } else {
                    passwordField.attr('type', 'password');
                    $(this).removeClass('fa-eye').addClass('fa-eye-slash');
                }
            });
        });
    </script>

</body>


<!-- Mirrored from demo.riktheme.com/fojota/side-menu/login.html by HTTrack Website Copier/3.x [XR&CO'2014], Wed, 06 Mar 2024 10:57:19 GMT -->

</html>
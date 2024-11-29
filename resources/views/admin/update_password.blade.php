@extends('admin.layouts.app')

@section('content')

<!-- Main Content Area -->
<div class="main-content- h-100vh">
    <div class="container h-100">
        <div class="row h-100 align-items-center justify-content-center">
            <div class="col-sm-10 col-md-7 col-lg-5">
                <!-- Middle Box -->
                <div class="middle-box">
                    <div class="card-body">
                        <div class="log-header-area card p-4 mb-4 text-center">
                            <h5 class="mb-0 text-white">Change Password</h5>

                        </div>

                        <div class="card ct_login_body">
                            <div class="card-body p-4">
                                <form action="{{url('update-password')}}" method="post" id="passwordUpdate">
                                    @csrf
                                    <div class="form-group mb-3">
                                        <label class="text-muted" for="password">Current Password</label>
                                        <div class="position-relative">
                                            <input class="form-control ct_input" type="password" id="current_password" name="current_password"
                                                placeholder="Enter your current password">
                                            <i class="fa fa-eye-slash ct_show_eye" aria-hidden="true"></i>
                                            @error('current_password')
                                            <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="form-group mb-3">
                                        <label class="text-muted" for="password">New Password</label>
                                        <div class="position-relative">
                                            <input class="form-control ct_input" type="password" id="password" name="password"
                                                placeholder="Enter your new password">
                                            <i class="fa fa-eye-slash ct_show_eye" aria-hidden="true"></i>
                                            @error('password')
                                            <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="form-group mb-3">
                                        <label class="text-muted" for="password">Confirm Password</label>
                                        <div class="position-relative">
                                            <input class="form-control ct_input" type="password" id="confirm_password" name="confirm_password"
                                                placeholder="Re-Enter your new password">
                                            <i class="fa fa-eye-slash ct_show_eye" aria-hidden="true"></i>
                                            @error('confirm_password')
                                            <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="form-group mb-3 mt-5">
                                        <button class="ct_custom_btn1 w-100" type="submit">Update Password</button>
                                    </div>


                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('script')
<script>
    $(document).ready(function() {
        $('#passwordUpdate').validate({
            rules: {
                current_password: {
                    required: true
                },
                password: {
                    required: true
                },
                confirm_password: {
                    required: true,
                    equalTo: "#password"
                }
            },
            messages: {
                current_password: {
                    required: 'Please enter current password.',
                },
                password: {
                    required: 'Please enter new password.',
                },
                confirm_password: {
                    required: 'Please re-enter new password.',
                    equalTo: 'Password should be same as new password'
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

@endsection
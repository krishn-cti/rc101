@extends('admin.layouts.app')

@section('content')

<!-- Main Content Area -->
<div class="main-content introduction-farm">
    <div class="content-wraper-area">
        <!-- Start Content-->
        <div class="container-fluid">
            <div class="row g-4">


                <div class="col-lg-8 mx-auto mt-5">
                    <div class="card main-profile-cover">

                        <div class="card-body bg-white">
                            <form action="{{url('update-profile')}}" method="post" id="profileForm" enctype="multipart/form-data">
                                @csrf
                                <div class="d-flex align-items-center justify-content-center">
                                    <h3 class="mb-4 ct_fs_22 text-center ">Edit Profile</h3>

                                </div>
                                <div class="d-sm-flex align-items-center mt-4">
                                    <div class="single-profile-image ct_profile_pic mx-auto">
                                        <label for="upload_img1" class="position-relative">
                                            <img id="admin_profile" src="{{ Auth::user()->profile_image }}" alt="Profile Image" class="ct_img_w_100">
                                            <div class="ct_edit_img12">
                                                <i class="fa fa-camera " aria-hidden="true"></i>
                                            </div>
                                            <input type="file" class="d-none ct_input" id="upload_img1" name="profile_image" accept="image/*" onchange="loadFile(event)">
                                        </label>

                                    </div>
                                </div>
                                <div class="mt-4">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <div class="form-group">
                                                <label for="" class="mb-2">Name</label>
                                                <input type="text" class="form-control ct_input" name="name" value="{{auth()->user()->name}}">
                                                @error('name')
                                                <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <div class="form-group">
                                                <label for="" class="mb-2">Email</label>
                                                <input type="text" class="form-control ct_input" name="email" value="{{auth()->user()->email}}" readonly>
                                                @error('email')
                                                <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-12 mb-3">
                                            <div class="form-group">
                                                <label for="" class="mb-2">Number</label>
                                                <input type="number" class="form-control ct_input" name="number" value="{{auth()->user()->number}}">
                                                @error('number')
                                                <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-center mt-4">
                                        <button type="submit" class="ct_custom_btn1 mx-auto">Save Changes</button>
                                    </div>

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
        $.validator.addMethod("noWhitespace", function(value, element) {
            return $.trim(value) != "";
        }, "Whitespace is not allowed.");
        $('#profileForm1').validate({
            rules: {
                name: {
                    required: true,
                    minlength: 3,
                    maxlength: 100,
                    noWhitespace: true
                },
                email: {
                    required: true,
                    email: true
                },
                number: {
                    required: true,
                    rangelength: [10, 12],
                    number: true
                }
            },
            messages: {
                name: {
                    required: "This field is required.",
                    minlength: "Please enter at least 3 characters.",
                    maxlength: "Please enter no more than 100 characters.",
                    noWhitespace: "Whitespace is not allowed."
                },
                email: {
                    required: 'Please enter Email Address.',
                    email: 'Please enter a valid Email Address.',
                },
                number: {
                    required: 'Please enter Contact.',
                    rangelength: 'Contact should be 10 digit number.'
                }
            },
            submitHandler: function(form) {
                form.submit();
            }
        });
    });
</script>
<script>
    var loadFile = function(event) {
        var image = document.getElementById('admin_profile');
        image.src = URL.createObjectURL(event.target.files[0]);
    };
</script>

@endsection
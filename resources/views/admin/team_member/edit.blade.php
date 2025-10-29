@extends('admin.layouts.app')
@section('content')

<!-- Main Content Area -->
<div class="main-content introduction-farm">
    <div class="content-wraper-area">
        <div class="dashboard-area">
            <div class="container-fluid">
                <div class="row g-4">

                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                @if(Session::has('error_msg'))
                                <div class="alert alert-danger"> {{ Session::get('error_msg') }} </div>
                                @endif

                                @if (Session::has('success_msg'))
                                <div class="alert alert-success"> {{ Session::get('success_msg') }} </div>
                                @endif
                                <div
                                    class="card-title border-bootom-none mb-30 d-flex align-items-center justify-content-between">
                                    <h3 class="mb-0 ct_fs_22">Edit Robot Combat Team</h3>
                                    <a href="{{url('users/list-member')}}"> <button class="ct_custom_btn1 mx-auto"> Back to List </button> </a>
                                </div>
                                <form action="{{url('users/update-member')}}" method="POST" id="addUser" enctype="multipart/form-data">
                                    @csrf
                                    <input type="hidden" name="id" value="{{$member->id}}">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group mb-3">
                                                <label for="" class="mb-2">Team Name</label>
                                                <input type="text" class="form-control ct_input" name="name" placeholder="Team Name" value="{{$member->name}}">
                                                @error('name')
                                                <div class="text text-danger mt-2">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group mb-3">
                                                <label for="" class="mb-2">Email</label>
                                                <input type="email" class="form-control ct_input" name="email" placeholder="Email" value="{{ $member->email}}">
                                                @error('email')
                                                <div class="text text-danger mt-2">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group mb-3">
                                                <label for="" class="mb-2">Number</label>
                                                <input type="number" class="form-control ct_input" name="number" placeholder="Number" value="{{$member->number}}">
                                                @error('number')
                                                <div class="text text-danger mt-2">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group mb-3">
                                                <label for="profile_image" class="mb-2"><strong>Profile Image</strong></label>
                                                <input name="profile_image" id="profile_image" type="file" class="form-control ct_input" onchange="loadProfileImage(event)" accept="image/*">

                                                <!-- Display Current or Default Profile Image -->
                                                <div id="imagePreviewWrapper" class="mt-2" style="display: block;">
                                                    <img id="imagePreview" src="{{ old('profile_image', $member->profile_image) }}" alt="Current Image" style="width: 100px; height: 100px; border-radius: 8px;">
                                                </div>

                                                @error('profile_image')
                                                <div class="text text-danger mt-2">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-center mt-4">
                                        <button type="submit" class="ct_custom_btn1 mx-auto">Save</button>
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
        $('#addUser').validate({
            rules: {
                name: {
                    required: true,
                    maxlength: 100,
                },
                email: {
                    required: true,
                    email: true,
                },
                number: {
                    required: true,
                },
            },
            messages: {
                name: {
                    required: "Please enter team name.",
                    maxlength: "The team name must not exceed 100 characters.",
                },
                email: {
                    required: "Please enter your email address.",
                    email: "Please enter a valid email address.",
                },
                number: 'Please enter member mobile number.',
            },

            submitHandler: function(form) {
                form.submit();
            }
        });
    });
</script>
<script>
    var loadProfileImage = function(event) {
        var image = document.getElementById('imagePreview'); // The image preview element
        var wrapper = document.getElementById('imagePreviewWrapper'); // The wrapper for the image preview

        if (event.target.files && event.target.files[0]) {
            image.src = URL.createObjectURL(event.target.files[0]); // Set the new image source
            wrapper.style.display = 'block'; // Ensure the wrapper is visible
        }
    };

    // On page load, handle visibility of the current image
    document.addEventListener('DOMContentLoaded', function() {
        var wrapper = document.getElementById('imagePreviewWrapper');
        var image = document.getElementById('imagePreview');
        wrapper.style.display = image.src ? 'block' : 'none';
    });
</script>
@endsection
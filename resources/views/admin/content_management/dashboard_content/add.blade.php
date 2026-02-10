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
                                    <h3 class="mb-0 ct_fs_22">Add Dashboard Content</h3>
                                    <a href="{{url('cms/dashboard-content-list')}}"> <button class="ct_custom_btn1 mx-auto"> Back to List </button> </a>
                                </div>
                                <form action="{{url('cms/dashboard-content-save')}}" method="POST" id="addDashboardContent" enctype="multipart/form-data">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group mb-3">
                                                <label for="title" class="mb-2">Title</label>
                                                <input type="text" class="form-control ct_input" name="title" placeholder="Title" value="{{ old('title')}}">
                                                @error('title')
                                                <div class="text text-danger mt-2">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group mb-3">
                                                <label for="link" class="mb-2">Link/URL</label>
                                                <input type="text" class="form-control ct_input" name="link" placeholder="Link/URL" value="{{ old('link')}}">
                                                @error('link')
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
        $('#addDashboardContent').validate({
            ignore: [],
            rules: {
                title: {
                    required: true,
                    maxlength: 255, // Ensure the length is within 255 characters
                },
                link: {
                    required: true,
                    maxlength: 255, // Ensure the length is within 255 characters
                },
            },
            messages: {
                title: {
                    required: "The title is required.",
                    maxlength: "The title must not exceed 255 characters.",
                },
                link: {
                    required: "The link is required.",
                    maxlength: "The linked name must not exceed 255 characters.",
                },
            },
            submitHandler: function(form) {
                form.submit();
            }
        });
    });
</script>
@endsection
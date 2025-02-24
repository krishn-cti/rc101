@extends('admin.layouts.app')
@section('content')

<!-- Main Content Area -->
<div class="main-content introduction-farm">
    <div class="content-wraper-area">
        <div class="dashboard-area">
            <div class="container-fluid">
                <div class="row g-4">

                    @if(Session::has('error_msg'))
                    <div class="alert alert-danger"> {{ Session::get('error_msg') }} </div>
                    @endif

                    @if (Session::has('success_msg'))
                    <div class="alert alert-success"> {{ Session::get('success_msg') }} </div>
                    @endif
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <div
                                    class="card-title border-bootom-none mb-30 d-flex align-items-center justify-content-between">
                                    <h3 class="mb-0 ct_fs_22">Edit RCL</h3>
                                    <a href="{{url('cms/knowledgebases/rcl-list')}}"> <button class="ct_custom_btn1 mx-auto"> Back to List </button> </a>
                                </div>
                                <form action="{{url('cms/knowledgebases/rcl-update')}}" method="POST" id="editRcl" enctype="multipart/form-data">
                                    <input type="hidden" name="id" value="{{$rclData->id}}">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group mb-3">
                                                <label for="title" class="mb-2">Title</label>
                                                <input type="text" class="form-control ct_input" name="title" placeholder="Title" value="{{ old('title', $rclData->title) }}">
                                                @error('title')
                                                <div class="text text-danger mt-2">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group mb-3">
                                                <label for="description" class="mb-2">Description</label>
                                                <textarea rows="4" class="form-control" name="description" id="description" placeholder="Description">{{ old('description', $rclData->description) }}</textarea>
                                                @error('description')
                                                <div class="text text-danger mt-2">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group mb-3">
                                                <label for="image" class="mb-2">Image</label>
                                                <input name="image" type="file" class="form-control ct_input" onchange="loadRclImage(event)" accept="image/*">

                                                @if(!empty($rclData->image))

                                                <div id="imagePreviewWrapper" class="mt-2" style="display: 'block';">
                                                    <img id="imagePreview" src="{{ $rclData->image }}" alt="Current Image" style="width: 100px; height: 100px; border-radius: 8px;">
                                                </div>
                                                @else
                                                <div id="imagePreviewWrapper" class="mt-2" style="display: 'none';">
                                                    <img id="imagePreview" src="{{ asset('admin/img/shop-img/no_image.png') }}" alt="Current Image" style="width: 100px; height: 100px; border-radius: 8px;">
                                                </div>
                                                @endif

                                                @error('image')
                                                <div class="text text-danger mt-2">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-center mt-4">
                                        <button type="submit" class="ct_custom_btn1 mx-auto">Update</button>
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
    ClassicEditor
        .create(document.querySelector('#description'), {
            toolbar: [
                'heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote', 'insertTable', '|', 'undo', 'redo'
                // Add other items as needed, but exclude 'imageUpload' and 'mediaEmbed'
            ],
        })
        .catch(error => {
            console.error(error);
        });
</script>
<script>
    $(document).ready(function() {
        $('#editRcl').validate({
            ignore: [],
            rules: {
                title: {
                    required: true,
                    maxlength: 150, // Ensure the length is within 150 characters
                },
                description: {
                    required: true,
                },
            },
            messages: {
                title: {
                    required: "The rcl title is required.",
                    maxlength: "The rcl title must not exceed 150 characters.",
                },
                description: {
                    required: "The rcl description is required.",
                },
            },
            errorPlacement: function(error, element) {
                if (element.attr("name") == "description") {
                    error.appendTo(element.next());
                } else {
                    error.insertAfter(element);
                }
            },
            submitHandler: function(form) {
                form.submit();
            }
        });
    });
</script>
<script>
    var loadRclImage = function(event) {
        var image = document.getElementById('imagePreview');
        var wrapper = document.getElementById('imagePreviewWrapper');

        if (event.target.files && event.target.files[0]) {
            image.src = URL.createObjectURL(event.target.files[0]);
            wrapper.style.display = 'block';
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
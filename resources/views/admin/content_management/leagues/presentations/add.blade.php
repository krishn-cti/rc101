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
                                    <h3 class="mb-0 ct_fs_22">Add Presentations</h3>
                                    <a href="{{url('cms/presentation-list')}}"> <button class="ct_custom_btn1 mx-auto"> Back to List </button> </a>
                                </div>
                                <form action="{{url('cms/presentation-save')}}" method="POST" id="addPresentation" enctype="multipart/form-data">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group mb-3">
                                                <label for="presentation_title" class="mb-2">Presentation Title</label>
                                                <input type="text" class="form-control ct_input" name="presentation_title" placeholder="Presentation Title" value="{{ old('presentation_title')}}">
                                                @error('presentation_title')
                                                <div class="text text-danger mt-2">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group mb-3">
                                                <label for="presentation_youtube_link" class="mb-2">YouTube Link</label>
                                                <input type="text" class="form-control ct_input" name="presentation_youtube_link" placeholder="YouTube Link" value="{{ old('presentation_youtube_link')}}">
                                                @error('presentation_youtube_link')
                                                <div class="text text-danger mt-2">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group mb-3">
                                                <label for="presentation_cover_image" class="mb-2"><strong>Presentation Image</strong></label>
                                                <input name="presentation_cover_image" type="file" class="form-control ct_input" onchange="loadPresentationCoverImage(event)" accept="image/*">

                                                <!-- Display the selected image preview here -->
                                                <div id="imagePreview" class="mt-2" style="display: none;">
                                                    <img id="presentation_cover_image" src="" alt="Image Preview" style="width: 100px; height: 100px; border-radius: 8px;">
                                                </div>
                                                @error('presentation_cover_image')
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
        // Add custom validation method for YouTube links
        $.validator.addMethod("validYouTubeLink", function(value, element) {
            var pattern = /^(?:https?:\/\/)?(?:www\.)?(?:youtube\.com\/(?:shorts\/|watch\?v=)|youtu\.be\/)[\w\-]+(?:&[\w=]*)?$/;
            return pattern.test(value);
        }, "Please enter a valid YouTube link.");

        $('#addPresentation').validate({
            rules: {
                presentation_title: {
                    required: true,
                },
                presentation_youtube_link: {
                    required: true,
                    validYouTubeLink: true // Use custom validation method
                },
            },
            messages: {
                presentation_title: 'Please enter presentation title.',
                presentation_youtube_link: {
                    required: 'Please enter YouTube link.',
                    validYouTubeLink: 'Please enter a valid YouTube link.',
                }
            },
            submitHandler: function(form) {
                form.submit();
            }
        });
    });
</script>
<script>
    var loadPresentationCoverImage = function(event) {
        var image = document.getElementById('presentation_cover_image');
        var imagePreview = document.getElementById('imagePreview');

        if (event.target.files && event.target.files[0]) {
            image.src = URL.createObjectURL(event.target.files[0]);
            image.style.display = 'block';
            imagePreview.style.display = 'block';
        }
    };
</script>
@endsection
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
                                    <h3 class="mb-0 ct_fs_22">Add Event Coverage/Results</h3>
                                    <a href="{{url('cms/event-coverage-list')}}"> <button class="ct_custom_btn1 mx-auto"> Back to List </button> </a>
                                </div>
                                <form action="{{url('cms/event-coverage-save')}}" method="POST" id="addEventCoverage" enctype="multipart/form-data">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group mb-3">
                                                <label for="" class="mb-2">Event Coverage Title</label>
                                                <input type="text" class="form-control ct_input" name="event_coverage_title" placeholder="Event Coverage Title" value="{{ old('event_coverage_title')}}">
                                                @error('event_coverage_title')
                                                <div class="text text-danger mt-2">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group mb-3">
                                                <label for="event_coverage_description" class="mb-2">Event Coverage Description</label>
                                                <textarea rows="4" class="form-control ct_input" name="event_coverage_description" id="event_coverage_description" placeholder="Event Coverage Description">{{ old('event_coverage_description')}}</textarea>
                                                @error('event_coverage_description')
                                                <div class="text text-danger mt-2">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group mb-3">
                                                <label for="event_coverage_image" class="mb-2"><strong>Event Coverage Image</strong></label>
                                                <input name="event_coverage_image" type="file" class="form-control ct_input" onchange="loadEventCoverageImage(event)" accept="image/*">

                                                <!-- Display the selected image preview here -->
                                                <div id="imagePreview" class="mt-2" style="display: none;">
                                                    <img id="event_coverage_image" src="" alt="Image Preview" style="width: 100px; height: 100px; border-radius: 8px;">
                                                </div>
                                                @error('event_coverage_image')
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
    ClassicEditor
        .create(document.querySelector('#event_coverage_description'), {
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
        $('#addEventCoverage').validate({
            ignore: [],
            rules: {
                event_coverage_title: {
                    required: true,
                    maxlength: 100,
                },
                event_coverage_description: {
                    required: true,
                },
            },
            messages: {
                event_coverage_title: {
                    required: "Please enter event coverage title.",
                    maxlength: "The event coverage title must not exceed 100 characters.",
                },
                event_coverage_description: 'Please enter event coverage description.',
            },
            errorPlacement: function(error, element) {
                if (element.attr("name") == "event_coverage_description") {
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
    var loadEventCoverageImage = function(event) {
        var image = document.getElementById('event_coverage_image');
        var imagePreview = document.getElementById('imagePreview');

        if (event.target.files && event.target.files[0]) {
            image.src = URL.createObjectURL(event.target.files[0]);
            image.style.display = 'block';
            imagePreview.style.display = 'block';
        }
    };
</script>
@endsection
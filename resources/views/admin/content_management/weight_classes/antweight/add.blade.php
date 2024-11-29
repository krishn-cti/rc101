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
                                    <h3 class="mb-0 ct_fs_22">Add Antweight</h3>
                                    <a href="{{url('cms/weight-classes/antweight-list')}}"> <button class="ct_custom_btn1 mx-auto"> Back to List </button> </a>
                                </div>
                                <form action="{{url('cms/weight-classes/antweight-save')}}" method="POST" id="addAntweight" enctype="multipart/form-data">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group mb-3">
                                                <label for="weight_class_title" class="mb-2">Weight Class Title</label>
                                                <input type="text" class="form-control ct_input" name="weight_class_title" placeholder="Weight Class Title" value="{{ old('weight_class_title')}}">
                                                @error('weight_class_title')
                                                <div class="text text-danger mt-2">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group mb-3">
                                                <label for="weight_class_description" class="mb-2">Weight Class Description</label>
                                                <textarea rows="4" class="form-control" name="weight_class_description" id="weight_class_description" placeholder="Weight Class Description">{{ old('weight_class_description')}}</textarea>
                                                @error('weight_class_description')
                                                <div class="text text-danger mt-2">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group mb-3">
                                                <label for="weight_class_image" class="mb-2">Weight Class Image</label>
                                                <input name="weight_class_image" type="file" class="form-control ct_input" onchange="loadWeightClassImage(event)" accept="image/*">

                                                <!-- Display the selected image preview here -->
                                                <div id="imagePreview" class="mt-2" style="display: none;">
                                                    <img id="weight_class_image" src="" alt="Image Preview" style="width: 100px; height: 100px; border-radius: 8px;">
                                                </div>

                                                @error('weight_class_image')
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
        .create(document.querySelector('#weight_class_description'), {
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
        $('#addAntweight').validate({
            ignore: [],
            rules: {
                weight_class_title: {
                    required: true,
                    maxlength: 150, // Ensure the length is within 150 characters
                },
                weight_class_description: {
                    required: true,
                },
            },
            messages: {
                weight_class_title: {
                    required: "The weight class title is required.",
                    maxlength: "The weight class title must not exceed 150 characters.",
                },
                weight_class_description: {
                    required: "The weight class description is required.",
                },
            },
            errorPlacement: function(error, element) {
                if (element.attr("name") == "weight_class_description") {
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

    var loadWeightClassImage = function(event) {
        var image = document.getElementById('weight_class_image');
        var imagePreview = document.getElementById('imagePreview');

        if (event.target.files && event.target.files[0]) {
            image.src = URL.createObjectURL(event.target.files[0]);
            image.style.display = 'block';
            imagePreview.style.display = 'block';
        }
    };
</script>
@endsection
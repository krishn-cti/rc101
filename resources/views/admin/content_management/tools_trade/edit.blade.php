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
                                    <h3 class="mb-0 ct_fs_22">Edit Tools of the Trade</h3>
                                    <a href="{{url('cms/tools-trade-list')}}"> <button class="ct_custom_btn1 mx-auto"> Back to List </button> </a>
                                </div>
                                <form action="{{url('cms/tools-trade-update')}}" method="POST" id="editToolsTrade" enctype="multipart/form-data">
                                    <input type="hidden" name="id" value="{{$toolsTradeData->id}}">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group mb-3">
                                                <label for="" class="mb-2">Tools Trade Title</label>
                                                <input type="text" class="form-control ct_input" name="tools_trade_title" placeholder="Tools Trade Title" value="{{ old('tools_trade_title', $toolsTradeData->tools_trade_title) }}">
                                                @error('tools_trade_title')
                                                <div class="text text-danger mt-2">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group mb-3">
                                                <label for="tools_trade_description" class="mb-2">Tools Trade Description</label>
                                                <textarea rows="4" class="form-control" name="tools_trade_description" id="tools_trade_description" placeholder="Tools Trade Description">{{ old('tools_trade_description', $toolsTradeData->tools_trade_description) }}</textarea>
                                                @error('tools_trade_description')
                                                <div class="text text-danger mt-2">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group mb-3">
                                                <label for="" class="mb-2"><strong>Tools Trade Image</strong></label>
                                                <input name="tools_trade_image" type="file" class="form-control ct_input" onchange="loadToolsTradeImage(event)" accept="image/*">

                                                @if(!empty($toolsTradeData->tools_trade_image) && file_exists(public_path('cms_images/' . $toolsTradeData->tools_trade_image)))

                                                <div id="imagePreviewWrapper" class="mt-2" style="display: 'block';">
                                                    <img id="imagePreview" src="{{ asset('cms_images/'.$toolsTradeData->tools_trade_image) }}" alt="Current Image" style="width: 100px; height: 100px; border-radius: 8px;">
                                                </div>
                                                @else
                                                <div id="imagePreviewWrapper" class="mt-2" style="display: 'none';">
                                                    <img id="imagePreview" src="{{ asset('admin/img/shop-img/no_image.png') }}" alt="Current Image" style="width: 100px; height: 100px; border-radius: 8px;">
                                                </div>
                                                @endif

                                                @error('tools_trade_image')
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
        .create(document.querySelector('#tools_trade_description'), {
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
        $('#editToolsTrade').validate({
            rules: {
                tools_trade_title: {
                    required: true,
                    maxlength: 100,
                },
                tools_trade_description: {
                    required: true,
                },
            },
            messages: {
                tools_trade_title: {
                    required: "Please enter tools trade title.",
                    maxlength: "The tools trade title must not exceed 100 characters.",
                },
                tools_trade_description: 'Please enter tools trade description.',
            },
            errorPlacement: function(error, element) {
                if (element.attr("name") == "tools_trade_description") {
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
    var loadToolsTradeImage = function(event) {
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
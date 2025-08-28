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
                                    <h3 class="mb-0 ct_fs_22">Edit Media Contents</h3>
                                    <a href="{{url('cms/media-content-list')}}"> <button class="ct_custom_btn1 mx-auto"> Back to List </button> </a>
                                </div>
                                <form action="{{url('cms/media-content-update')}}" method="POST" id="editMediaContent" enctype="multipart/form-data">
                                    <input type="hidden" name="id" value="{{$mediaContentData->id}}">
                                    @csrf
                                    <div class="row">
                                           <div class="col-md-12">
                                            <div class="form-group mb-3">
                                                <label for="title" class="mb-2">Title</label>
                                                <input type="text" class="form-control ct_input" name="title" placeholder="Title" value="{{ old('title', $mediaContentData->title) }}">
                                                @error('title')
                                                <div class="text text-danger mt-2">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-12">
                                            <div class="form-group mb-3">
                                                <label for="type" class="mb-2">Type</label>
                                                <select class="form-control ct_input" name="type">
                                                    <option value="">Select Video URL Type</option>
                                                    <option value="youtube" {{ old('type', $mediaContentData->type) == 'youtube' ? 'selected' : '' }}>YouTube</option>
                                                    <option value="twitch" {{ old('type', $mediaContentData->type) == 'twitch' ? 'selected' : '' }}>Twitch</option>
                                                    <option value="vimeo" {{ old('type', $mediaContentData->type) == 'vimeo' ? 'selected' : '' }}>Vimeo</option>
                                                    <option value="drive" {{ old('type', $mediaContentData->type) == 'drive' ? 'selected' : '' }}>Drive</option>
                                                    <option value="local" {{ old('type', $mediaContentData->type) == 'local' ? 'selected' : '' }}>Local</option>
                                                </select>
                                                @error('type')
                                                <div class="text text-danger mt-2">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-12">
                                            <div class="form-group mb-3">
                                                <label for="link" class="mb-2">Link</label>
                                                <input type="text" class="form-control ct_input" name="link" placeholder="Video URL Link" value="{{ old('link', $mediaContentData->link) }}">
                                                @error('link')
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
    $(document).ready(function() {
        $('#editMediaContent').validate({
            ignore: [],
            rules: {
                title: {
                    required: true,
                    maxlength: 150,
                },
                type: {
                    required: true,
                },
                link: {
                    required: true,
                    url: true
                }
            },
            messages: {
                title: {
                    required: "Please enter title.",
                    maxlength: "The title must not exceed 150 characters.",
                },
                type: 'Please select video URL type.',
                link: {
                    required: "The link field is required.",
                    url: "Please enter a valid video URL"
                }
            },
            submitHandler: function(form) {
                form.submit();
            }
        });
    });
</script>
@endsection
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
                                    <h3 class="mb-0 ct_fs_22">Add Bots</h3>
                                    <a href="{{url('cms/bot-list')}}"> <button class="ct_custom_btn1 mx-auto"> Back to List </button> </a>
                                </div>
                                <form action="{{url('cms/bot-save')}}" method="POST" id="addBot" enctype="multipart/form-data">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label for="" class="mb-2">Name</label>
                                                <input type="text" class="form-control ct_input" name="name" placeholder="Name" value="{{ old('name')}}">
                                                @error('name')
                                                <div class="text text-danger mt-2">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label for="start_date" class="mb-2">Start Date</label>
                                                <input type="date" class="form-control ct_input" name="start_date" id="start_date" placeholder="Start Date"
                                                    value="{{ old('start_date') }}" min="{{ date('Y-m-d') }}">
                                                @error('start_date')
                                                <div class="text text-danger mt-2">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label for="bot_type_id" class="mb-2">Bot Type</label>
                                                <select class="form-control ct_input" name="bot_type_id" id="bot_type_id">
                                                    <option value="" selected disabled>Select Bot Type</option>
                                                    @if(!empty($botTypes))
                                                    @foreach($botTypes as $botType)
                                                    <option value="{{ $botType->id }}" {{ old('bot_type_id') == $botType->id ? 'selected' : '' }}>
                                                        {{ $botType->name }}
                                                    </option>
                                                    @endforeach
                                                    @endif
                                                </select>
                                                @error('bot_type_id')
                                                <div class="text text-danger mt-2">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label for="weight_class_id" class="mb-2">Weight Class</label>
                                                <select class="form-control ct_input" name="weight_class_id" id="weight_class_id">
                                                    <option value="" selected disabled>Select Weight Class</option>
                                                    @if(!empty($weightClassCategories))
                                                    @foreach($weightClassCategories as $weightClassCategory)
                                                    <option value="{{ $weightClassCategory->id }}" {{ old('weight_class_id') == $weightClassCategory->id ? 'selected' : '' }}>
                                                        {{ $weightClassCategory->name }}
                                                    </option>
                                                    @endforeach
                                                    @endif
                                                </select>
                                                @error('weight_class_id')
                                                <div class="text text-danger mt-2">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label for="design_type" class="mb-2">Design Type</label>
                                                <select class="form-control ct_input" name="design_type" id="design_type">
                                                    <option value="" disabled selected>Select Design Type</option>
                                                    <option value="Custom" {{ old('design_type') == 'Custom' ? 'selected' : '' }}>Custom</option>
                                                    <option value="Kit" {{ old('design_type') == 'Kit' ? 'selected' : '' }}>Kit</option>
                                                </select>
                                                @error('design_type')
                                                <div class="text text-danger mt-2">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label for="created_by" class="mb-2">Created By</label>
                                                <select class="form-control ct_input" name="created_by" id="created_by">
                                                    <option value="1">Select Created By</option>
                                                    @if(!empty($teamMembers))
                                                    @foreach($teamMembers as $teamMember)
                                                    <option value="{{ $teamMember->id }}" {{ old('created_by') == $teamMember->id ? 'selected' : '' }}>
                                                        {{ $teamMember->name }}
                                                    </option>
                                                    @endforeach
                                                    @endif
                                                </select>
                                                @error('created_by')
                                                <div class="text text-danger mt-2">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group mb-3">
                                                <label for="description" class="mb-2">Description</label>
                                                <textarea rows="4" class="form-control ct_input" name="description" id="description" placeholder="Description">{{ old('description')}}</textarea>
                                                @error('description')
                                                <div class="text text-danger mt-2">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group mb-3">
                                                <label for="image" class="mb-2"><strong>Image</strong></label>
                                                <input name="image" type="file" class="form-control ct_input" onchange="loadImage(event)" accept="image/*">

                                                <!-- Display the selected image preview here -->
                                                <div id="imagePreview" class="mt-2" style="display: none;">
                                                    <img id="image" src="" alt="Image Preview" style="width: 100px; height: 100px; border-radius: 8px;">
                                                </div>
                                                @error('image')
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
        $('#addBot').validate({
            rules: {
                name: {
                    required: true,
                    maxlength: 100,
                },
                description : {
                    maxlength : 255,
                }
            },
            messages: {
                name: {
                    required: "Please enter bot name.",
                    maxlength: "The name must not exceed 100 characters.",
                },
                description: {
                    maxlength: "The description must not exceed 255 characters.",
                }
            },

            submitHandler: function(form) {
                form.submit();
            }
        });
    });
</script>
<script>
    var loadImage = function(event) {
        var image = document.getElementById('image');
        var imagePreview = document.getElementById('imagePreview');

        if (event.target.files && event.target.files[0]) {
            image.src = URL.createObjectURL(event.target.files[0]);
            image.style.display = 'block';
            imagePreview.style.display = 'block';
        }
    };
</script>
@endsection
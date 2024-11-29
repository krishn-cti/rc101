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
                                <div class="card-title border-bootom-none mb-30 d-flex align-items-center justify-content-between">
                                    <h3 class="mb-0 ct_fs_22">Add Tournaments</h3>
                                    <a href="{{url('cms/tournament-list')}}"> <button class="ct_custom_btn1 mx-auto"> Back to List </button> </a>
                                </div>
                                <form action="{{url('cms/tournament-save')}}" method="POST" id="addTournament" enctype="multipart/form-data">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group mb-3">
                                                <label for="" class="mb-2">Tournament Title</label>
                                                <input type="text" class="form-control ct_input" name="tournament_title" placeholder="Tournament Title" value="{{ old('tournament_title')}}">
                                                @error('tournament_title')
                                                <div class="text text-danger mt-2">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group mb-3">
                                                <label for="tournament_desc" class="mb-2">Tournament Description</label>
                                                <textarea rows="4" class="form-control ct_input" name="tournament_desc" placeholder="Tournament Description">{{ old('tournament_desc')}}</textarea>
                                                @error('tournament_desc')
                                                <div class="text text-danger mt-2">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label for="" class="mb-2">Start Date</label>
                                                <input type="date" class="form-control ct_input" name="start_date" placeholder="Start Date" value="{{ old('start_date')}}" min="{{date('Y-m-d')}}">
                                                @error('start_date')
                                                <div class="text text-danger mt-2">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label for="" class="mb-2">End Date</label>
                                                <input type="date" class="form-control ct_input" name="end_date" placeholder="End Date" value="{{ old('end_date')}}" min="{{date('Y-m-d')}}">
                                                @error('end_date')
                                                <div class="text text-danger mt-2">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group mb-3">
                                                <label for="" class="mb-2"><strong>Tournament Image</strong></label>
                                                <input name="banner_image" type="file" class="form-control ct_input" onchange="loadBannerImage(event)" accept="image/*">

                                                <!-- Display the selected image preview here -->
                                                <div id="imagePreview" class="mt-2" style="display: none;">
                                                    <img id="banner_image" src="" alt="Image Preview" style="width: 100px; height: 100px; border-radius: 8px;">
                                                </div>

                                                @error('banner_image')
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
    // Custom validation rule to check if the end date is greater than start date
    $.validator.addMethod("greaterThan", function(value, element, param) {
        var startDate = $(param).val();

        if (!startDate || !value) {
            return true;
        }

        var start = new Date(startDate);
        var end = new Date(value);

        return end > start;
    }, "End date must be greater than the start date.");

    $(document).ready(function() {
        // Initialize validation
        $('#addTournament').validate({
            rules: {
                tournament_title: {
                    required: true,
                },
                start_date: {
                    required: true,
                    date: true,
                },
                end_date: {
                    required: true,
                    date: true,
                    greaterThan: "#start_date" // Custom rule for end date validation
                },
            },
            messages: {
                tournament_title: "Please enter tournament title.",
                start_date: "Please select a valid start date.",
                end_date: {
                    required: "Please select a valid end date.",
                    greaterThan: "End date must be greater than the start date."
                },
            },
            submitHandler: function(form) {
                form.submit(); // Submit the form if validation passes
            }
        });

        // Display the image preview on page load if image is already selected
        if (document.getElementById('banner_image').src) {
            document.getElementById('imagePreview').style.display = 'none';
        }
    });

    // Function to load and display the image preview when a file is selected
    var loadBannerImage = function(event) {
        var image = document.getElementById('banner_image');
        var imagePreview = document.getElementById('imagePreview');

        if (event.target.files && event.target.files[0]) {
            image.src = URL.createObjectURL(event.target.files[0]);
            image.style.display = 'block';
            imagePreview.style.display = 'block';
        }
    };
</script>

@endsection
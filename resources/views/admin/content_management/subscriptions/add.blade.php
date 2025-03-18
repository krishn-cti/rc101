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
                                    <h3 class="mb-0 ct_fs_22">Add Plan</h3>
                                    <a href="{{url('subscription-list')}}"> <button class="ct_custom_btn1 mx-auto"> Back to List </button> </a>
                                </div>
                                <form action="{{url('subscription-save')}}" method="POST" id="addSubscription" enctype="multipart/form-data">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group mb-3">
                                                <label for="name" class="mb-2">Name</label>
                                                <input type="text" class="form-control ct_input" name="name" placeholder="Name" value="{{ old('name')}}">
                                                @error('name')
                                                <div class="text text-danger mt-2">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group mb-3">
                                                <label for="monthly_price" class="mb-2">Monthly Price (in dollar)</label>
                                                <input type="text" class="form-control ct_input" name="monthly_price" placeholder="Monthly Price" value="{{ old('monthly_price')}}">
                                                @error('monthly_price')
                                                <div class="text text-danger mt-2">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group mb-3">
                                                <label for="yearly_price" class="mb-2">Yearly Price (in dollar)</label>
                                                <input type="text" class="form-control ct_input" name="yearly_price" placeholder="Yearly Price" value="{{ old('yearly_price')}}">
                                                @error('yearly_price')
                                                <div class="text text-danger mt-2">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group mb-3">
                                                <label for="user_access_count" class="mb-2">User Access Count</label>
                                                <input type="text" class="form-control ct_input" name="user_access_count" placeholder="User Access Count" value="{{ old('user_access_count')}}">
                                                @error('user_access_count')
                                                <div class="text text-danger mt-2">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group mb-3">
                                                <label for="description" class="mb-2">Description</label>
                                                <textarea rows="4" class="form-control" name="description" id="description" placeholder="Description">{{ old('description')}}</textarea>
                                                @error('description')
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
        $('#addSubscription').validate({
            ignore: [],
            rules: {
                name: {
                    required: true,
                    maxlength: 150, // Ensure the length is within 150 characters
                },
                monthly_price: {
                    required: true,
                    number: true,
                    min: 0
                },
                yearly_price: {
                    required: true,
                    number: true,
                    min: 0
                },
                user_access_count: {
                    required: true,
                    number: true,
                    min: 0
                },
                description: {
                    required: true,
                }
            },
            messages: {
                name: {
                    required: "The plan name is required.",
                    maxlength: "The plan name must not exceed 150 characters.",
                },
                monthly_price: {
                    required: "Monthly price is required.",
                    number: "Please enter a valid number.",
                    min: "Price cannot be negative."
                },
                yearly_price: {
                    required: "Yearly price is required.",
                    number: "Please enter a valid number.",
                    min: "Price cannot be negative."
                },
                user_access_count: {
                    required: "User access count is required.",
                    number: "Please enter a valid number.",
                    min: "Count cannot be negative."
                },
                description: {
                    required: "The plan description is required.",
                }
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
@endsection
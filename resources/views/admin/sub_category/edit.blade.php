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
                                    <h3 class="mb-0 ct_fs_22">Edit Sub Categories</h3>
                                    <a href="{{url('list-category')}}"> <button class="ct_custom_btn1 mx-auto"> Back to List </button> </a>
                                </div>
                                <form action="{{url('update-sub-category')}}" method="POST" id="editCategory" enctype="multipart/form-data">
                                    <input type="hidden" name="id" value="{{$sub_category->id}}">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group mb-3">
                                                <label for="" class="mb-2">Sub Category Name</label>
                                                <input type="text" class="form-control ct_input" name="sub_category_name" placeholder="Sub Category Name" value="{{ old('sub_category_name', $sub_category->sub_category_name) }}">
                                                @error('sub_category_name')
                                                <div class="text text-danger mt-2">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group mb-3">
                                                <label for="" class="mb-2">Category Name</label>
                                                <select class="form-control ct_input" name="category_id">
                                                    <option value="">Select Category</option>
                                                    @foreach($categoryData as $key=>$val)
                                                    <option value="{{$val->id}}" {{$val->id==$sub_category->category_id?'selected':''}}>{{$val->category_name}}</option>
                                                    @endforeach
                                                </select>
                                                @error('category_name')
                                                <div class="text text-danger mt-2">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group mb-3">
                                                <label for="" class="mb-2">Description</label>
                                                <textarea rows="4" class="form-control ct_input" name="description" placeholder="Description">{{ old('description', $sub_category->description) }}</textarea>
                                                @error('description')
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
        $('#editCategory').validate({
            rules: {
                sub_category_name: {
                    required: true,
                    maxlength: 100,
                },
                category_id: {
                    required: true,
                },
                description: {
                    required: true,
                    maxlength: 255,
                },
            },
            messages: {
                sub_category_name: {
                    required: 'Please enter sub category name.',
                    maxlength: 'Sub category name cannot exceed 100 characters.',
                },
                category_id: 'Please select category name.',
                description: {
                    required: 'Please enter short description.',
                    maxlength: 'Description cannot exceed 255 characters.',
                },
            },
            submitHandler: function(form) {
                form.submit();
            }
        });
    });
</script>
@endsection
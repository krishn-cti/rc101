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
                                <div class="card-title border-bootom-none mb-30 d-flex align-items-center justify-content-between">
                                    <h3 class="mb-0 ct_fs_22">Edit Product</h3>
                                    <a href="{{url('list-product')}}"> <button class="ct_custom_btn1 mx-auto"> Back to List </button> </a>
                                </div>
                                <div>
                                    <form action="{{url('update-product')}}" method="POST" id="editProduct" enctype="multipart/form-data">
                                        @csrf
                                        <input type="hidden" value="{{$product->id}}" name="product_id">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group mb-3">
                                                    <label for="thumbnail" class="mb-2">Thumbnail</label>
                                                    <input type="file" class="form-control ct_input" name="thumbnail" id="thumbnail" accept="image/*" onchange="previewThumbnail(event)">
                                                    <div id="thumbnailPreviewWrapper" class="mt-2">
                                                        <img id="thumbnailPreview"
                                                            src="{{ $product->productImages->thumbnail }}"
                                                            alt="Thumbnail Preview"
                                                            style="width: 100px; height: 100px; border-radius: 8px;">
                                                    </div>
                                                    @error('thumbnail')
                                                    <div class="text text-danger mt-2">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group mb-3">
                                                    <label for="images" class="mb-2">Other Images</label>
                                                    <input type="file" class="form-control ct_input" multiple name="images[]" id="images" accept="image/*" onchange="previewImages(event)">
                                                    <div id="imagesPreviewWrapper" class="mt-2 d-flex gap-2" style="flex-wrap: wrap;">
                                                        <!-- Display existing images -->
                                                        @if(!empty($other_images))
                                                        @foreach($other_images as $key=>$images)
                                                        <img src="{{$images}}" style="width:100px;height:100px; border-radius:8px;">
                                                        @endforeach
                                                        @endif
                                                    </div>
                                                    @error('images')
                                                    <div class="text text-danger mt-2">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group mb-3">
                                                    <label for="" class="mb-2">Product Name</label>
                                                    <input type="text" class="form-control ct_input" name="product_name" value="{{ old('product_name', $product->product_name ?? '') }}" placeholder="Product Name">
                                                    @error('product_name')
                                                    <div class="text text-danger mt-2">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group mb-3">
                                                    <label for="" class="mb-2">Related Name</label>
                                                    <input type="text" class="form-control ct_input" name="related_name" value="{{ old('related_name', $product->related_name ?? '') }}" placeholder="Related Name">
                                                    @error('related_name')
                                                    <div class="text text-danger mt-2">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group mb-3">
                                                    <label for="" class="mb-2">Category</label>
                                                    <select id="category" name="category_id" class="form-control ct_input">
                                                        <option value="">Select Product Category</option>
                                                        @foreach($category as $key=>$cat)
                                                        <option value="{{$cat->id}}" {{$cat->id==$product->category_id?'selected':''}}>{{$cat->category_name}}</option>
                                                        @endforeach
                                                    </select>
                                                    @error('category_id')
                                                    <div class="text text-danger mt-2">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group mb-3">
                                                    <label for="" class="mb-2">Sub Category</label>
                                                    <select id="sub_category" name="sub_category_id" class="form-control ct_input">
                                                        <option value="">Select Sub Category</option>
                                                        @foreach($sub_category as $subCategory)
                                                        <option value="{{ $subCategory->id }}" {{ $subCategory->id == $product->sub_category_id ? 'selected' : '' }}>
                                                            {{ $subCategory->sub_category_name}}
                                                        </option>
                                                        @endforeach
                                                    </select>
                                                    @error('sub_category_id')
                                                    <div class="text text-danger mt-2">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group mb-3">
                                                    <label for="" class="mb-2">Price</label>
                                                    <input type="number" id="price" class="form-control ct_input" name="price" placeholder="Price" value="{{ old('price', $product->price ?? '') }}">
                                                    @error('price')
                                                    <div class="text text-danger mt-2">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group mb-3">
                                                    <label for="" class="mb-2">Discount</label>
                                                    <input type="number" class="form-control ct_input" name="discount" placeholder="Discount" value="{{ old('discount', $product->discount ?? '') }}">
                                                    @error('discount')
                                                    <div class="text text-danger mt-2">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <!-- <div class="col-md-6">
                                                <div class="form-group mb-3">
                                                    <label for="" class="mb-2">Quantity</label>
                                                    <input type="number" class="form-control ct_input" name="quantity" placeholder="Quantity" value="{{$product->quantity}}">
                                                    @error('quantity')
                                                    <div class="text text-danger mt-2">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div> -->
                                            <div class="col-md-12">
                                                <div class="form-group mb-3">
                                                    <label for="" class="mb-2">Short Description</label>
                                                    <textarea rows="4"
                                                        class="form-control ct_input"
                                                        name="description"
                                                        placeholder="Short Description">{{ old('description', $product->description ?? '') }}</textarea>
                                                    @error('description')
                                                    <div class="text text-danger mt-2">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="col-md-12">
                                                <div class="form-group mb-3">
                                                    <label for="long_description" class="mb-2">Long Description</label>
                                                    <textarea id="long_description" rows="4" class="form-control" name="long_description">{{ old('long_description', $product->long_description ?? '') }}</textarea>
                                                    @error('long_description')
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
</div>
@endsection
@section('script')
<script>
    ClassicEditor
        .create(document.querySelector('#long_description'), {
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
        $.validator.addMethod("lessThanPrice", function(value, element, params) {
            if (!value) return true;
            var price = parseFloat($(params).val());
            var discount = parseFloat(value);
            return discount < price;
        }, "Discount must be less than the price.");

        $('#editProduct').validate({
            rules: {
                product_name: {
                    required: true,
                    maxlength: 100,
                },
                related_name: {
                    required: true,
                    maxlength: 100,
                },
                category_id: {
                    required: true,
                },
                sub_category_id: {
                    required: true,
                },
                price: {
                    required: true,
                    number: true,
                },
                discount: {
                    number: true,
                    lessThanPrice: "#price"
                },
                // quantity: {
                //     required: true,
                //     number: true,
                // },
                description: {
                    required: true,
                    maxlength: 255,
                },
            },
            messages: {
                product_name: {
                    required: 'Please enter product name.',
                    maxlength: 'Product name cannot exceed 100 characters.',
                },
                related_name: {
                    required: 'Please enter related name.',
                    maxlength: 'Related name cannot exceed 100 characters.',
                },
                category_id: 'Please select category.',
                sub_category_id: 'Please select sub category.',
                price: {
                    required: 'Please enter price.',
                    number: 'Please enter a valid number for price.',
                },
                discount: {
                    number: 'Please enter a valid number for discount.',
                    lessThanPrice: 'Discount must be less than the price.',
                },
                // quantity: {
                //     required: 'Please enter quantity.',
                //     number: 'Please enter a valid number for quantity.',
                // },
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
<script>
    // Function to preview new thumbnail
    function previewThumbnail(event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('thumbnailPreview').src = e.target.result;
            };
            reader.readAsDataURL(file);
        }
    }

    // Function to preview new multiple images
    function previewImages(event) {
        const files = event.target.files;
        const previewWrapper = document.getElementById('imagesPreviewWrapper');
        previewWrapper.innerHTML = ''; // Clear existing previews

        Array.from(files).forEach((file) => {
            const reader = new FileReader();
            reader.onload = function(e) {
                const img = document.createElement('img');
                img.src = e.target.result;
                img.style.width = '100px';
                img.style.height = '100px';
                img.style.borderRadius = '8px';
                img.style.marginRight = '10px';
                previewWrapper.appendChild(img);
            };
            reader.readAsDataURL(file);
        });
    }
</script>
<script>
    $(document).ready(function() {
        $('#category').on('change', function() {
            var categoryId = $(this).val();
            if (categoryId) {
                $.ajax({
                    url: '{{ url("subcategories") }}',
                    type: 'GET',
                    data: {
                        category_id: categoryId
                    },
                    dataType: 'json',
                    success: function(data) {
                        $('#sub_category').empty();
                        $('#sub_category').append('<option value="">Select Sub Category</option>');
                        $.each(data, function(id, name) {
                            $('#sub_category').append('<option value="' + id + '">' + name + '</option>');
                        });
                    },
                    error: function(xhr, status, error) {
                        console.error(xhr.responseText);
                    }
                });
            } else {
                $('#sub_category').empty();
                $('#sub_category').append('<option value="">Select Sub Category</option>');
            }
        });
    });
</script>
@endsection
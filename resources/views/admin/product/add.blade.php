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
                                    <h3 class="mb-0 ct_fs_22">Add Products</h3>
                                    <a href="{{url('list-product')}}"> <button class="ct_custom_btn1 mx-auto"> Back to List </button> </a>
                                </div>
                                <div>
                                    <form action="{{url('save-product')}}" method="POST" id="addProduct" enctype="multipart/form-data">
                                        @csrf
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group mb-3">
                                                    <label for="" class="mb-2">Thumbnail</label>
                                                    <input type="file" class="form-control ct_input" name="thumbnail" accept="image/*" onchange="previewThumbnail(event)">
                                                    <div id="thumbnailPreviewWrapper" class="mt-2" style="display: none;">
                                                        <img id="thumbnailPreview" src="" alt="Thumbnail Preview" style="width: 100px; height: 100px; border-radius: 8px;">
                                                    </div>
                                                    @error('thumbnail')
                                                    <div class="text text-danger mt-2">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group mb-3">
                                                    <label for="" class="mb-2">Other Images</label>
                                                    <input type="file" class="form-control ct_input" multiple name="images[]" accept="image/*" onchange="previewImages(event)">
                                                    <div id="imagesPreviewWrapper" class="mt-2 d-flex gap-2" style="flex-wrap: wrap;">
                                                        <!-- Previews will appear here -->
                                                    </div>
                                                    @error('images')
                                                    <div class="text text-danger mt-2">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group mb-3">
                                                    <label for="" class="mb-2">Product Name</label>
                                                    <input type="text" class="form-control ct_input" name="product_name" placeholder="Product Name"  value="{{ old('product_name') }}">
                                                    @error('product_name')
                                                    <div class="text text-danger mt-2">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group mb-3">
                                                    <label for="" class="mb-2">Related Name</label>
                                                    <input type="text" class="form-control ct_input" name="related_name" placeholder="Related Name"  value="{{ old('related_name') }}">
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
                                                        <option value="{{$cat->id}}">{{$cat->category_name}}</option>
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
                                                        <!-- this data getting from on change of category -->
                                                    </select>
                                                    @error('sub_category_id')
                                                    <div class="text text-danger mt-2">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group mb-3">
                                                    <label for="" class="mb-2">Price</label>
                                                    <input type="number" class="form-control ct_input" name="price" id="price" placeholder="Price" value="{{ old('price') }}">
                                                    @error('price')
                                                    <div class="text text-danger mt-2">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group mb-3">
                                                    <label for="" class="mb-2">Discount</label>
                                                    <input type="number" class="form-control ct_input" name="discount" placeholder="Discount" value="{{ old('discount') }}">
                                                    @error('discount')
                                                    <div class="text text-danger mt-2">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <!-- <div class="col-md-6">
                                                <div class="form-group mb-3">
                                                    <label for="" class="mb-2">Quantity</label>
                                                    <input type="number" class="form-control ct_input" name="quantity" placeholder="Quantity">
                                                    @error('quantity')
                                                    <div class="text text-danger mt-2">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div> -->
                                            <div class="col-md-12">
                                                <div class="form-group mb-3">
                                                    <label for="" class="mb-2">Short Description</label>
                                                    <textarea rows="4" class="form-control ct_input" name="description" placeholder="Short Description">{{ old('description') }}</textarea>
                                                    @error('description')
                                                    <div class="text text-danger mt-2">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="form-group mb-3">
                                                    <label for="long_description" class="mb-2">Long Description</label>
                                                    <textarea id="long_description" rows="4" class="form-control" name="long_description">{{ old('long_description') }}</textarea>
                                                    @error('long_description')
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
</div>
@endsection
@section('script')
<!-- <script>
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
</script> -->

<script>
    const {
        ClassicEditor,
        Essentials,
        Bold,
        Italic,
        Underline,
        Font,
        Paragraph,
        List,
        Link,
        Table,
        TableToolbar,
        Heading
    } = CKEDITOR;

    ClassicEditor.create(document.querySelector('#long_description'), {
        plugins: [
            Essentials,
            Paragraph,
            Heading,
            Bold,
            Italic,
            Underline,
            Font,
            List,
            Link,

            // Table
            Table,
            TableToolbar
        ],

        toolbar: [
            'heading',
            '|',
            'undo', 'redo',
            '|',
            'bold', 'italic', 'underline',
            '|',
            'fontSize', 'fontFamily', 'fontColor', 'fontBackgroundColor',
            '|',
            'link',
            'insertTable',
            '|',
            'bulletedList', 'numberedList'
        ],

        heading: {
            options: [
                { model: 'paragraph', title: 'Paragraph', class: 'ck-heading_paragraph' },
                { model: 'heading1', view: 'h1', title: 'Heading 1' },
                { model: 'heading2', view: 'h2', title: 'Heading 2' },
                { model: 'heading3', view: 'h3', title: 'Heading 3' },
                { model: 'heading4', view: 'h4', title: 'Heading 4' },
                { model: 'heading5', view: 'h5', title: 'Heading 5' },
                { model: 'heading6', view: 'h6', title: 'Heading 6' }
            ]
        },

        fontSize: {
            options: [10, 12, 14, 'default', 18, 20, 24, 28]
        },

        table: {
            contentToolbar: [
                'tableColumn',
                'tableRow',
                'mergeTableCells'
            ]
        }
    }).catch(console.error);
</script>
<script>
    // Function to preview thumbnail image with size validation
    function previewThumbnail(event) {
        const file = event.target.files[0];
        if (file) {
            if (file.size > 2 * 1024 * 1024) { // 2 MB in bytes
                alert('Thumbnail size should not exceed 2 MB.');
                event.target.value = ''; // Clear the input
                document.getElementById('thumbnailPreviewWrapper').style.display = 'none';
                return;
            }

            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('thumbnailPreview').src = e.target.result;
                document.getElementById('thumbnailPreviewWrapper').style.display = 'block';
            };
            reader.readAsDataURL(file);
        }
    }

    // Function to preview multiple images with validation
    function previewImages(event) {
        const files = event.target.files;
        const previewWrapper = document.getElementById('imagesPreviewWrapper');
        previewWrapper.innerHTML = ''; // Clear existing previews

        if (files.length > 8) {
            alert('You can only upload a maximum of 8 images.');
            event.target.value = ''; // Clear the input
            return;
        }

        Array.from(files).forEach((file) => {
            if (file.size > 2 * 1024 * 1024) { // 2 MB in bytes
                alert('Each image size should not exceed 2 MB.');
                event.target.value = ''; // Clear the input
                previewWrapper.innerHTML = ''; // Clear previews
                return false; // Exit the loop
            }

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
<script>
    $(document).ready(function() {
        $.validator.addMethod("lessThanPrice", function(value, element, params) {
            if (!value) return true;
            var price = parseFloat($(params).val());
            var discount = parseFloat(value);
            return discount < price;
        }, "Discount must be less than the price.");

        $('#addProduct').validate({
            ignore: [],
            rules: {
                thumbnail: {
                    required: true
                },
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
                thumbnail: 'Please upload thumbnail image.',
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
@endsection
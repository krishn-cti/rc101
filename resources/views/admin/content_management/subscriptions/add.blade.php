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
                                                <label for="is_paid" class="mb-2">Type</label>
                                                <select class="form-control ct_input" name="is_paid" id="is_paid" required>
                                                    <option value="">Select Type</option>
                                                    <option value="Yes" {{ old('is_paid') == 'Yes' ? 'selected' : '' }}>Paid</option>
                                                    <option value="No" {{ old('is_paid') == 'No' ? 'selected' : '' }}>Free</option>
                                                </select>
                                                @error('is_paid')
                                                <div class="text text-danger mt-2">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group mb-3">
                                                <label for="monthly_price" class="mb-2">Monthly Price (in dollar)</label>
                                                <input type="text" class="form-control ct_input" name="monthly_price" id="monthly_price" placeholder="Monthly Price" value="{{ old('monthly_price')}}">
                                                @error('monthly_price')
                                                <div class="text text-danger mt-2">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group mb-3">
                                                <label for="yearly_price" class="mb-2">Yearly Price (in dollar)</label>
                                                <input type="text" class="form-control ct_input" name="yearly_price" id="yearly_price" placeholder="Yearly Price" value="{{ old('yearly_price')}}">
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
<!-- <script>
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
        Heading,
        Alignment
    } = CKEDITOR;

    ClassicEditor.create(document.querySelector('#description'), {
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
            Alignment,

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
            'alignment:left',
            'alignment:center',
            'alignment:right',
            'alignment:justify',
            '|',
            'link',
            'insertTable',
            '|',
            'bulletedList', 'numberedList'
        ],

        heading: {
            options: [{
                    model: 'paragraph',
                    title: 'Normal text',
                    class: 'ck-heading_paragraph'
                },
                {
                    model: 'heading1',
                    view: 'h1',
                    title: 'Title',
                    class: 'ck-heading_heading1'
                },
                {
                    model: 'heading2',
                    view: 'h2',
                    title: 'Subtitle',
                    class: 'ck-heading_heading2'
                },
                {
                    model: 'heading3',
                    view: 'h3',
                    title: 'Heading 1',
                    class: 'ck-heading_heading3'
                },
                {
                    model: 'heading4',
                    view: 'h4',
                    title: 'Heading 2',
                    class: 'ck-heading_heading4'
                },
                {
                    model: 'heading5',
                    view: 'h5',
                    title: 'Heading 3',
                    class: 'ck-heading_heading5'
                },
                {
                    model: 'heading6',
                    view: 'h6',
                    title: 'Heading 4',
                    class: 'ck-heading_heading6'
                }
            ]
        },

        fontSize: {
            options: [10, 12, 14, 'default', 18, 20, 24, 28, 32, 36]
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
    $(document).ready(function() {

        // Initialize validation
        $('#addSubscription').validate({
            ignore: [],
            rules: {
                name: {
                    required: true,
                    maxlength: 150
                },
                monthly_price: {
                    required: function() {
                        return $('#is_paid').val() === 'Yes';
                    },
                    number: true,
                    min: 1 // must be > 0 for paid plans
                },
                yearly_price: {
                    required: function() {
                        return $('#is_paid').val() === 'Yes';
                    },
                    number: true,
                    min: 1
                },
                user_access_count: {
                    required: true,
                    number: true,
                    min: 0
                },
                description: {
                    required: true
                },
                is_paid: {
                    required: true
                }
            },
            messages: {
                name: {
                    required: "The plan name is required.",
                    maxlength: "The plan name must not exceed 150 characters."
                },
                monthly_price: {
                    required: "Monthly price is required for paid plans.",
                    number: "Please enter a valid number.",
                    min: "Monthly price must be greater than 0 for paid plans."
                },
                yearly_price: {
                    required: "Yearly price is required for paid plans.",
                    number: "Please enter a valid number.",
                    min: "Yearly price must be greater than 0 for paid plans."
                },
                user_access_count: {
                    required: "User access count is required.",
                    number: "Please enter a valid number.",
                    min: "Count cannot be negative."
                },
                description: {
                    required: "The plan description is required."
                },
                is_paid: {
                    required: "Please select whether the plan is paid or free."
                }
            },
            errorPlacement: function(error, element) {
                if (element.attr("name") === "description") {
                    error.appendTo(element.next());
                } else {
                    error.insertAfter(element);
                }
            },
            submitHandler: function(form) {
                form.submit();
            }
        });

        // Toggle fields based on plan type
        $('#is_paid').on('change', function() {
            const isPaid = $(this).val();

            if (isPaid === 'No') {
                // FREE PLAN → disable and set price = 0
                $('#monthly_price, #yearly_price').each(function() {
                    $(this)
                        .val('0')
                        .prop('disabled', true)
                        .rules('remove', 'required number min');
                });
            } else {
                // PAID PLAN → enable fields and apply validation
                $('#monthly_price, #yearly_price').each(function() {
                    $(this)
                        .prop('disabled', false)
                        .rules('add', {
                            required: true,
                            number: true,
                            min: 1,
                            messages: {
                                required: "This field is required for paid plans.",
                                number: "Please enter a valid number.",
                                min: "Price must be greater than 0 for paid plans."
                            }
                        });
                });
            }
        }).trigger('change'); // Trigger once on page load
    });
</script>
@endsection
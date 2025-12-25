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
                                    <h3 class="mb-0 ct_fs_22">Edit League Rule</h3>
                                    <a href="{{url('cms/league-rule-list')}}"> <button class="ct_custom_btn1 mx-auto"> Back to List </button> </a>
                                </div>
                                <form action="{{url('cms/league-rule-update')}}" method="POST" id="editSparcRule" enctype="multipart/form-data">
                                    <input type="hidden" name="id" value="{{$sparcRuleData->id}}">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label for="sparc_rule_title" class="mb-2">Title</label>
                                                <input type="text" class="form-control ct_input" name="sparc_rule_title" placeholder="League Rule Title" value="{{ old('sparc_rule_title', $sparcRuleData->sparc_rule_title) }}">
                                                @error('sparc_rule_title')
                                                <div class="text text-danger mt-2">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label for="color_code" class="mb-2">Color (for container)</label>
                                                <div class="d-flex align-items-center">
                                                    <!-- Color Code Display -->
                                                    <input
                                                        type="text"
                                                        class="form-control ct_input"
                                                        id="color_code"
                                                        name="color_code"
                                                        placeholder="Color Code"
                                                        value="{{ old('container_color', $sparcRuleData->container_color ?? '#000000') }}">
                                                    &nbsp;OR&nbsp;
                                                    <!-- Color Picker Input -->
                                                    <input
                                                        type="color"
                                                        class="form-control ct_input"
                                                        id="container_color"
                                                        name="container_color"
                                                        value="{{ old('container_color', $sparcRuleData->container_color ?? '#000000') }}"
                                                        style="height: 40px; cursor: pointer;"
                                                        onchange="updateColorCode(this.value)">
                                                </div>
                                                <div id="color_code_error" class="text-danger mt-1" style="font-size: 0.9rem;"></div>
                                                @error('container_color')
                                                <div class="text text-danger mt-2">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group mb-3">
                                                <label for="sparc_rule_description" class="mb-2">Description</label>
                                                <textarea rows="4" class="form-control" name="sparc_rule_description" id="sparc_rule_description" placeholder="League Rule Description">{{ old('sparc_rule_description', $sparcRuleData->sparc_rule_description) }}</textarea>
                                                @error('sparc_rule_description')
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
<!-- <script>
    ClassicEditor
        .create(document.querySelector('#sparc_rule_description'), {
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

    ClassicEditor.create(document.querySelector('#sparc_rule_description'), {
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
    $(document).ready(function() {
        $('#editSparcRule').validate({
            ignore: [],
            rules: {
                sparc_rule_title: {
                    required: true,
                },
                sparc_rule_description: {
                    required: true,
                },
            },
            messages: {
                sparc_rule_title: 'Please enter league rule title.',
                sparc_rule_description: 'Please enter league rule description.',
            },
            errorPlacement: function(error, element) {
                if (element.attr("name") == "sparc_rule_description") {
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
    $(document).ready(function() {
        const colorCodeInput = $('#color_code');
        const colorPicker = $('#container_color');

        // Set initial value of the color picker from the color code input
        if (/^#[0-9A-Fa-f]{6}$/.test(colorCodeInput.val())) {
            colorPicker.val(colorCodeInput.val());
        }

        // Sync color picker changes with color code input
        colorPicker.on('input', function() {
            colorCodeInput.val($(this).val());
            $('#color_code_error').text(''); // Clear error if valid
        });

        // Validate and update color picker when color code input changes
        colorCodeInput.on('keyup', function() {
            const colorCode = $(this).val();
            if (/^#[0-9A-Fa-f]{6}$/.test(colorCode)) {
                colorPicker.val(colorCode);
                $('#color_code_error').text(''); // Clear error on valid input
            } else {
                $('#color_code_error').text('Invalid color code. Use format #RRGGBB.');
            }
        });
    });
</script>
@endsection
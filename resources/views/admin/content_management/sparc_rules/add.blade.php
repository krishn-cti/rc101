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
                                    <h3 class="mb-0 ct_fs_22">Add League Rule</h3>
                                    <a href="{{url('cms/league-rule-list')}}"> <button class="ct_custom_btn1 mx-auto"> Back to List </button> </a>
                                </div>
                                <form action="{{url('cms/league-rule-save')}}" method="POST" id="addSparcRule" enctype="multipart/form-data">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label for="sparc_rule_title" class="mb-2">Title</label>
                                                <input type="text" class="form-control ct_input" name="sparc_rule_title" placeholder="League Rule Title" value="{{ old('sparc_rule_title')}}">
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
                                                        value="{{ old('container_color', '#000000') }}">
                                                        <!-- Color Picker Input -->
                                                        &nbsp;OR&nbsp;
                                                    <input
                                                        type="color"
                                                        class="form-control ct_input"
                                                        id="container_color"
                                                        name="container_color"
                                                        value="{{ old('container_color', '#000000') }}"
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
                                                <textarea rows="4" class="form-control ct_input" name="sparc_rule_description" id="sparc_rule_description" placeholder="League Rule Description">{{ old('sparc_rule_description')}}</textarea>
                                                @error('sparc_rule_description')
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
        .create(document.querySelector('#sparc_rule_description'), {
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
        $('#addSparcRule').validate({
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
    function updateColorCode(color) {
        $('#color_code').val(color);
        $('#color_code_error').text(''); // Clear any error on color picker change
    }

    $(document).ready(function() {
        const colorCodeInput = $('#color_code');
        const colorPicker = $('#container_color');

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
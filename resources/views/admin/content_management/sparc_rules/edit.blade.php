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
                                    <h3 class="mb-0 ct_fs_22">Edit SPARC Rule</h3>
                                    <a href="{{url('cms/sparc-rule-list')}}"> <button class="ct_custom_btn1 mx-auto"> Back to List </button> </a>
                                </div>
                                <form action="{{url('cms/sparc-rule-update')}}" method="POST" id="editSparcRule" enctype="multipart/form-data">
                                    <input type="hidden" name="id" value="{{$sparcRuleData->id}}">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group mb-3">
                                                <label for="sparc_rule_title" class="mb-2">Sparc Rule Title</label>
                                                <input type="text" class="form-control ct_input" name="sparc_rule_title" placeholder="Sparc Rule Title" value="{{ old('sparc_rule_title', $sparcRuleData->sparc_rule_title) }}">
                                                @error('sparc_rule_title')
                                                <div class="text text-danger mt-2">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label for="container_color" class="mb-2">Choose Container Color</label>
                                                <div class="d-flex align-items-center">
                                                    <!-- Color Picker Input -->
                                                    <input
                                                        type="color"
                                                        class="form-control p-1"
                                                        id="container_color"
                                                        name="container_color"
                                                        value="{{ old('container_color', $sparcRuleData->container_color ?? '#000000') }}"
                                                        style="width: 200px; height: 40px; border: none; cursor: pointer;"
                                                        onchange="updateColorCode(this.value)">
                                                    <!-- Color Code Display -->
                                                    <input
                                                        type="text"
                                                        class="form-control ms-2"
                                                        id="color_code"
                                                        placeholder="Color Code"
                                                        value="{{ old('container_color', $sparcRuleData->container_color ?? '#000000') }}"
                                                        readonly
                                                        style="max-width: 100px; text-align: center;">
                                                </div>
                                                @error('container_color')
                                                <div class="text text-danger mt-2">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group mb-3">
                                                <label for="sparc_rule_description" class="mb-2">Sparc Rule Description</label>
                                                <textarea rows="4" class="form-control" name="sparc_rule_description" id="sparc_rule_description" placeholder="Sparc Rule Description">{{ old('sparc_rule_description', $sparcRuleData->sparc_rule_description) }}</textarea>
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
                sparc_rule_title: 'Please enter sparc rule title.',
                sparc_rule_description: 'Please enter sparc rule description.',
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
    // Function to update the color code input field dynamically
    function updateColorCode(color) {
        document.getElementById('color_code').value = color;
    }
</script>
@endsection
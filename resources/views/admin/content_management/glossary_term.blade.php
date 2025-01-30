@extends('admin.layouts.app')
@section('content')

<!-- Main Content Area -->
<div class="main-content introduction-farm">
    <div class="content-wraper-area">
        <div class="dashboard-area">
            <div class="container-fluid">
                <div class="row g-4">

                    <div class="col-12">
                        @if(Session::has('error_msg'))
                        <div class="alert alert-danger"> {{ Session::get('error_msg') }} </div>
                        @endif

                        @if (Session::has('success_msg'))
                        <div class="alert alert-success"> {{ Session::get('success_msg') }} </div>
                        @endif
                        <div class="card ">
                            <div class="card-body card-breadcrumb">
                                <div class="page-title-box mb-4">
                                    <h3 class="mb-0 ct_fs_22">Terms and Conditions</h3>
                                </div>
                                <form action="{{url('cms/update-terms-and-conditions')}}" method="POST" id="glossaryTermForm" enctype="multipart/form-data">
                                    @csrf
                                    <input type="hidden" name="id" value="{{$glossaryTerm->id ?? null}}">
                                    <div class="mb-3">
                                        <label for=""><strong>Title</strong></label>
                                        <input name="glossary_term_title" type="text" class="form-control ct_input" placeholder="Title" value="{{ old('glossary_term_title', $glossaryTerm->glossary_term_title ?? '') }}">
                                        @error('glossary_term_title')
                                        <div class="text text-danger mt-2">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="mb-3">
                                        <label for="glossary_term_description"><strong>Description</strong></label>
                                        <textarea name="glossary_term_description" id="glossary_term_description" class="form-control" cols="30" rows="5" placeholder="Description">{{ old('glossary_term_description', $glossaryTerm->glossary_term_description ?? '') }}</textarea>
                                        @error('glossary_term_description')
                                        <div class="text text-danger mt-2">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="text-center mt-5">
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
        .create(document.querySelector('#glossary_term_description'), {
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
        $('#glossaryTermForm').validate({
            ignore: [],
            rules: {
                glossary_term_title: {
                    required: true,
                    maxlength: 100,
                },
                glossary_term_description: {
                    required: true,
                },
            },
            messages: {
                glossary_term_title: {
                    required: "Please enter glossary terms title.",
                    maxlength: "The glossary terms title must not exceed 100 characters.",
                },
                glossary_term_description: "Please enter glossary terms content.",
            },
            errorPlacement: function(error, element) {
                if (element.attr("name") == "glossary_term_description") {
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
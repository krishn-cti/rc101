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
                                    <h3 class="mb-0 ct_fs_22">Privacy Policy</h3>
                                </div>
                                <form action="{{url('cms/update-privacy-policy')}}" method="POST" id="privacyPolicyForm" enctype="multipart/form-data">
                                    @csrf
                                    <input type="hidden" name="id" value="{{$privacyPolicy->id ?? null}}">
                                    <div class="mb-3">
                                        <label for=""><strong>Title</strong></label>
                                        <input name="privacy_policy_title" type="text" class="form-control ct_input" placeholder="Title" value="{{ old('privacy_policy_title', $privacyPolicy->privacy_policy_title ?? '') }}">
                                        @error('privacy_policy_title')
                                        <div class="text text-danger mt-2">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="mb-3">
                                        <label for="privacy_policy_description"><strong>Description</strong></label>
                                        <textarea name="privacy_policy_description" id="privacy_policy_description" class="form-control" cols="30" rows="5" placeholder="Description">{{ old('privacy_policy_description', $privacyPolicy->privacy_policy_description ?? '') }}</textarea>
                                        @error('privacy_policy_description')
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
        .create(document.querySelector('#privacy_policy_description'), {
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
        $('#privacyPolicyForm').validate({
            ignore: [],
            rules: {
                privacy_policy_title: {
                    required: true,
                    maxlength: 100,
                },
                privacy_policy_description: {
                    required: true,
                },
            },
            messages: {
                privacy_policy_title: {
                    required: "Please enter privacy policy title.",
                    maxlength: "The privacy policy title must not exceed 100 characters.",
                },
                privacy_policy_description: "Please enter privacy policy content.",
            },
            errorPlacement: function(error, element) {
                if (element.attr("name") == "privacy_policy_description") {
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
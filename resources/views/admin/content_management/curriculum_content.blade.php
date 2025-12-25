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
                                    <h3 class="mb-0 ct_fs_22">Curriculum Overview</h3>
                                </div>
                                <form action="{{url('curriculums/update-overview')}}" method="POST" id="currriculumContentForm" enctype="multipart/form-data">
                                    @csrf
                                    <input type="hidden" name="id" value="{{$currriculumContent->id ?? null}}">
                                    <div class="mb-3">
                                        <label for=""><strong>Title</strong></label>
                                        <input name="title" type="text" class="form-control ct_input" placeholder="Title" value="{{ old('title', $currriculumContent->title ?? '') }}">
                                        @error('title')
                                        <div class="text text-danger mt-2">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="mb-3">
                                        <label for="description"><strong>Description</strong></label>
                                        <textarea name="description" id="description" class="form-control" cols="30" rows="5" placeholder="Description">{{ old('description', $currriculumContent->description ?? '') }}</textarea>
                                        @error('description')
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
        Heading
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

<!-- validation for curriculum content form -->
<script>
    $(document).ready(function() {
        $('#currriculumContentForm').validate({
            ignore: [],
            rules: {
                title: {
                    required: true,
                    maxlength: 150,
                },
                description: {
                    required: true,
                }
            },
            messages: {
                title: {
                    required: "Please enter title.",
                    maxlength: "The title must not exceed 150 characters.",
                },
                description: "Please enter descriprion.",
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
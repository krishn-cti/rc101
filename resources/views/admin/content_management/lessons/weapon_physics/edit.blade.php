@extends('admin.layouts.app')
@section('content')

<!-- Main Content Area -->
<div class="main-content introduction-farm">
    <div class="content-wraper-area">
        <div class="dashboard-area">
            <div class="container-fluid">
                <div class="row g-4">

                    @if(Session::has('error_msg'))
                    <div class="alert alert-danger"> {{ Session::get('error_msg') }} </div>
                    @endif

                    @if (Session::has('success_msg'))
                    <div class="alert alert-success"> {{ Session::get('success_msg') }} </div>
                    @endif
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <div
                                    class="card-title border-bootom-none mb-30 d-flex align-items-center justify-content-between">
                                    <h3 class="mb-0 ct_fs_22">Edit Weapon Physics</h3>
                                    <a href="{{url('cms/lessons/weapon-physics-list')}}"> <button class="ct_custom_btn1 mx-auto"> Back to List </button> </a>
                                </div>
                                <form action="{{url('cms/lessons/weapon-physics-update')}}" method="POST" id="editWeaponPhysics" enctype="multipart/form-data">
                                    <input type="hidden" name="id" value="{{$weaponPhysicsData->id}}">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group mb-3">
                                                <label for="lesson_title" class="mb-2">Lesson Title</label>
                                                <input type="text" class="form-control ct_input" name="lesson_title" placeholder="Lesson Title" value="{{ old('lesson_title', $weaponPhysicsData->lesson_title) }}">
                                                @error('lesson_title')
                                                <div class="text text-danger mt-2">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group mb-3">
                                                <label for="lesson_description" class="mb-2">Lesson Description</label>
                                                <textarea rows="4" class="form-control" name="lesson_description" id="lesson_description" placeholder="Lesson Description">{{ old('lesson_description', $weaponPhysicsData->lesson_description) }}</textarea>
                                                @error('lesson_description')
                                                <div class="text text-danger mt-2">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group mb-3">
                                                <label for="lesson_cover_image" class="mb-2">Lesson Cover Image</label>
                                                <input name="lesson_cover_image" type="file" class="form-control ct_input" onchange="loadLessonCoverImage(event)" accept="image/*">

                                                @if(!empty($weaponPhysicsData->lesson_cover_image) && file_exists(public_path('cms_images/lesson/weapon_physics/' . $weaponPhysicsData->lesson_cover_image)))

                                                <div id="imagePreviewWrapper" class="mt-2" style="display: 'block';">
                                                    <img id="imagePreview" src="{{ asset('cms_images/lesson/weapon_physics/'.$weaponPhysicsData->lesson_cover_image) }}" alt="Current Image" style="width: 100px; height: 100px; border-radius: 8px;">
                                                </div>
                                                @else
                                                <div id="imagePreviewWrapper" class="mt-2" style="display: 'none';">
                                                    <img id="imagePreview" src="{{ asset('admin/img/shop-img/no_image.png') }}" alt="Current Image" style="width: 100px; height: 100px; border-radius: 8px;">
                                                </div>
                                                @endif

                                                @error('lesson_cover_image')
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
        .create(document.querySelector('#lesson_description'), {
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

    ClassicEditor.create(document.querySelector('#lesson_description'), {
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
        $('#editWeaponPhysics').validate({
            ignore: [],
            rules: {
                lesson_title: {
                    required: true,
                    maxlength: 150, // Ensure the length is within 150 characters
                },
                lesson_description: {
                    required: true,
                },
            },
            messages: {
                lesson_title: {
                    required: "The lesson title is required.",
                    maxlength: "The lesson title must not exceed 150 characters.",
                },
                lesson_description: {
                    required: "The lesson description is required.",
                },
            },
            errorPlacement: function(error, element) {
                if (element.attr("name") == "lesson_description") {
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
    var loadLessonCoverImage = function(event) {
        var image = document.getElementById('imagePreview');
        var wrapper = document.getElementById('imagePreviewWrapper');

        if (event.target.files && event.target.files[0]) {
            image.src = URL.createObjectURL(event.target.files[0]);
            wrapper.style.display = 'block';
        }
    };

    // On page load, handle visibility of the current image
    document.addEventListener('DOMContentLoaded', function() {
        var wrapper = document.getElementById('imagePreviewWrapper');
        var image = document.getElementById('imagePreview');
        wrapper.style.display = image.src ? 'block' : 'none';
    });
</script>
@endsection
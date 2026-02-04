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
                                    <h3 class="mb-0 ct_fs_22">Home Section</h3>
                                </div>
                                <form action="{{url('cms/update-home')}}" method="POST" id="homeBannerForm" enctype="multipart/form-data">
                                    @csrf
                                    <input type="hidden" name="id" value="{{$homeBanner->id ?? null}}">
                                    <div class="mb-3">
                                        <label for=""><strong>Banner Title</strong></label>
                                        <input name="banner_title" type="text" class="form-control ct_input" placeholder="Banner Title" value="{{ old('banner_title', $homeBanner->banner_title ?? '') }}">
                                        @error('banner_title')
                                        <div class="text text-danger mt-2">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="mb-3">
                                        <label for="banner_sub_title"><strong>Banner Sub Title</strong></label>
                                        <textarea name="banner_sub_title" id="banner_sub_title" class="form-control ct_input" rows="4" placeholder="Banner Sub Title">{{ old('banner_sub_title', $homeBanner->banner_sub_title ?? '') }}</textarea>
                                        @error('banner_sub_title')
                                        <div class="text text-danger mt-2">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="mb-3">
                                        <label for="banner_text"><strong>Home Page Content</strong></label>
                                        <textarea name="banner_text" id="banner_text" class="form-control" cols="30" rows="5" placeholder="Banner Description" required>{{ old('banner_text', $homeBanner->banner_text ?? '') }}</textarea>
                                        @error('banner_text')
                                        <div class="text text-danger mt-2">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="mb-3">
                                        <label for=""><strong>YouTube Link</strong></label>
                                        <input name="link" type="text" class="form-control ct_input" value="{{ old('link', $homeBanner->link ?? '') }}" placeholder="Banner Link">
                                        @error('link')
                                        <div class="text text-danger mt-2">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="mb-3">
                                        <label for="media_content"><strong>Media Content</strong></label>
                                        <textarea name="media_content" id="media_content" class="form-control" cols="30" rows="5" placeholder="Media Content">{{ old('media_content', $homeBanner->media_content ?? '') }}</textarea>
                                        @error('media_content')
                                        <div class="text text-danger mt-2">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label for="" class="mb-2"><strong>YouTube Cover Image</strong></label>
                                            <input name="youtube_image" type="file" class="form-control ct_input" onchange="loadYoutubeImage(event)" accept="image/*">
                                            @if (!empty($homeBanner->youtube_image) && file_exists(public_path('cms_images/' . $homeBanner->youtube_image)))
                                            <img id="youtube_image" src="{{ asset('cms_images/' . $homeBanner->youtube_image) }}" style="width: 100px; height: 100px; border-radius: 8px;" class="mt-2">
                                            @else
                                            <span>No image available</span>
                                            @endif

                                            @error('banner_image')
                                            <div class="text text-danger mt-2">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-6">
                                            <label for="" class="mb-2"><strong>Home Banner Image</strong></label>
                                            <input name="banner_image" type="file" class="form-control ct_input" onchange="loadBannerImage(event)" accept="image/*">
                                            @if (!empty($homeBanner->banner_image) && file_exists(public_path('cms_images/' . $homeBanner->banner_image)))
                                            <img id="banner_image" src="{{ asset('cms_images/' . $homeBanner->banner_image) }}" style="width: 100px; height: 100px; border-radius: 8px;" class="mt-2">
                                            @else
                                            <span>No image available</span>
                                            @endif

                                            @error('banner_image')
                                            <div class="text text-danger mt-2">{{ $message }}</div>
                                            @enderror
                                        </div>
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
        Image,
        ImageUpload,
        ImageStyle,
        ImageResize,
        ImageToolbar,
        Heading,
        Alignment,
        CKFinderUploadAdapter
    } = CKEDITOR;

    const editorConfig = {
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

            Table,
            TableToolbar,

            Image,
            ImageUpload,
            ImageStyle,
            ImageResize,
            ImageToolbar,

            CKFinderUploadAdapter
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
            'imageUpload',
            'imageResize',
            '|',
            'bulletedList', 'numberedList'
        ],

        image: {
            styles: [
                'alignLeft',
                'alignCenter',
                'alignRight'
            ],
            resizeOptions: [{
                    name: 'resizeImage:original',
                    label: 'Original',
                    value: null
                },
                {
                    name: 'resizeImage:25',
                    label: '25%',
                    value: '25'
                },
                {
                    name: 'resizeImage:50',
                    label: '50%',
                    value: '50'
                },
                {
                    name: 'resizeImage:75',
                    label: '75%',
                    value: '75'
                },
                {
                    name: 'resizeImage:100',
                    label: '100%',
                    value: '100'
                }
            ],
            toolbar: [
                'imageStyle:alignLeft',
                'imageStyle:alignCenter',
                'imageStyle:alignRight',
                '|',
                'resizeImage'
            ]
        },

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
        },

        ckfinder: {
            uploadUrl: "{{ route('home.content.image') }}?_token={{ csrf_token() }}"
        },
    };

    // Initialize multiple editors safely
    ['#banner_text', '#media_content'].forEach(selector => {
        const el = document.querySelector(selector);
        if (el) {
            ClassicEditor.create(el, editorConfig).catch(console.error);
        }
    });
</script>

<script>
    $(document).ready(function() {
        // Add custom validation method for YouTube links
        $.validator.addMethod("validYouTubeLink", function(value, element) {
            var pattern = /^(?:https?:\/\/)?(?:www\.)?(?:youtube\.com\/(?:shorts\/|watch\?v=)|youtu\.be\/)[\w\-]+(?:&[\w=]*)?$/;
            return pattern.test(value);
        }, "Please enter a valid YouTube link.");

        $.validator.addMethod("filesize", function(value, element, param) {
            // Validate only if a file is selected
            if (element.files.length > 0) {
                var fileSizeInKB = element.files[0].size / 1024; // Size in KB
                return fileSizeInKB <= param;
            }
            return true;
        }, 'File size must not exceed 2MB.');

        $('#homeBannerForm').validate({
            ignore: [],
            debug: false,
            rules: {
                banner_title: {
                    required: true,
                    maxlength: 100,
                },
                banner_sub_title: {
                    maxlength: 255,
                },
                link: {
                    required: true,
                    validYouTubeLink: true, // Use custom validation method
                },
                youtube_image: {
                    extension: "jpg|jpeg|png|gif", // Allowed file extensions
                    filesize: 2048 // File size in KB
                },
                banner_image: {
                    extension: "jpg|jpeg|png|gif", // Allowed file extensions
                    filesize: 2048 // File size in KB
                },
            },
            messages: {
                banner_title: {
                    required: "Please enter banner title.",
                    maxlength: "The banner title must not exceed 100 characters.",
                },
                banner_sub_title: {
                    maxlength: 'The banner sub title must not exceed 255 characters.',
                },
                link: {
                    required: 'Please enter YouTube link.',
                    validYouTubeLink: 'Please enter a valid YouTube link.',
                },
                youtube_image: {
                    extension: 'Allowed file types are JPG, JPEG, PNG, GIF.',
                    filesize: 'File size must not exceed 2MB.'
                },
                banner_image: {
                    extension: 'Allowed file types are JPG, JPEG, PNG, GIF.',
                    filesize: 'File size must not exceed 2MB.'
                }
            },
            submitHandler: function(form) {
                form.submit();
            }
        });

    });
</script>
<script>
    var loadBannerImage = function(event) {
        var image = document.getElementById('banner_image');
        image.src = URL.createObjectURL(event.target.files[0]);
    };
    var loadYoutubeImage = function(event) {
        var image = document.getElementById('youtube_image');
        image.src = URL.createObjectURL(event.target.files[0]);
    };
</script>
@endsection
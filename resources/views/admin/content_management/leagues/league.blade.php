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
                                    <h3 class="mb-0 ct_fs_22">League</h3>
                                </div>
                                <form action="{{url('cms/update-league')}}" method="POST" id="leaguePageForm" enctype="multipart/form-data">
                                    @csrf
                                    <input type="hidden" name="id" value="{{$leaguePage->id ?? null}}">
                                    <div class="mb-3">
                                        <label for="banner_title"><strong>Banner Title</strong></label>
                                        <input name="banner_title" type="text" class="form-control ct_input" placeholder="Banner Title" value="{{ old('banner_title', $leaguePage->banner_title ?? '') }}">
                                    </div>
                                    <div class="mb-3">
                                        <label for="banner_text"><strong>Banner Text</strong></label>
                                        <textarea name="banner_text" id="banner_text" class="form-control" cols="30" rows="4" placeholder="Banner Text">{{ old('banner_text', $leaguePage->banner_text ?? '') }}</textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label for="about_league_title"><strong>About League Title</strong></label>
                                        <input name="about_league_title" type="text" class="form-control ct_input" placeholder="About League Title" value="{{ old('about_league_title', $leaguePage->about_league_title ?? '') }}">
                                    </div>
                                    <div class="mb-3">
                                        <label for="about_league_desc"><strong>About League Description</strong></label>
                                        <textarea name="about_league_desc" id="about_league_desc" class="form-control" cols="30" rows="4" placeholder="About League Description">{{ old('about_league_desc', $leaguePage->about_league_desc ?? '') }}</textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label for="league_page_title"><strong>League Page Title</strong></label>
                                        <input name="league_page_title" type="text" class="form-control ct_input" placeholder="League Page Title" value="{{ old('league_page_title', $leaguePage->league_page_title ?? '') }}">
                                    </div>
                                    <div class="mb-3">
                                        <label for="league_page_desc"><strong>League Page Description</strong></label>
                                        <textarea name="league_page_desc" id="league_page_desc" class="form-control" cols="30" rows="4" placeholder="League Page Description">{{ old('league_page_desc', $leaguePage->league_page_desc ?? '') }}</textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label for="league_youtube_link"><strong>League YouTube Link</strong></label>
                                        <input name="league_youtube_link" type="text" class="form-control ct_input" placeholder="League YouTube Link" value="{{ old('league_youtube_link', $leaguePage->league_youtube_link ?? '') }}">
                                    </div>
                                    <!-- <div class="mb-3">
                                        <label for="league_media_content"><strong>League Media Content</strong></label>
                                        <textarea name="league_media_content" id="league_media_content" class="form-control" cols="30" rows="4" placeholder="League Media Content">{{ old('league_media_content', $leaguePage->league_media_content ?? '') }}</textarea>
                                    </div> -->
                                    <div class="row">
                                        <div class="mb-3 col-md-6">
                                            <label for="banner_image" class="mb-2"><strong>Banner Image</strong></label>
                                            <input name="banner_image" type="file" class="form-control ct_input" onchange="loadBannerImage(event)" accept="image/*">

                                            <img
                                                id="banner_image"
                                                src="{{ $leaguePage && $leaguePage->banner_image ? asset('cms_images/leagues/' . $leaguePage->banner_image) : '' }}"
                                                style="width: 100px; height: 100px; border-radius: 8px; display: <?= $leaguePage && $leaguePage->banner_image ? 'block' : 'none' ?>"
                                                class="mt-2">

                                            @error('banner_image')
                                            <div class="text-danger mt-2">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="mb-3 col-md-6">
                                            <label for="league_cover_image" class="mb-2"><strong>League Cover Image</strong></label>
                                            <input name="league_cover_image" type="file" class="form-control ct_input" onchange="loadLeagueCoverImage(event)" accept="image/*">

                                            <img
                                                id="league_cover_image"
                                                src="{{ $leaguePage && $leaguePage->league_cover_image ? asset('cms_images/leagues/' . $leaguePage->league_cover_image) : '' }}"
                                                style="width: 100px; height: 100px; border-radius: 8px; display: <?= $leaguePage && $leaguePage->league_cover_image ? 'block' : 'none' ?>"
                                                class="mt-2">

                                            @error('league_cover_image')
                                            <div class="text-danger mt-2">{{ $message }}</div>
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
    ClassicEditor
        .create(document.querySelector('#banner_text'), {
            toolbar: [
                'heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote', 'insertTable', '|', 'undo', 'redo'
                // Add other items as needed, but exclude 'imageUpload' and 'mediaEmbed'
            ],
        })
        .catch(error => {
            console.error(error);
        });
    ClassicEditor
        .create(document.querySelector('#about_league_desc'), {
            toolbar: [
                'heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote', 'insertTable', '|', 'undo', 'redo'
                // Add other items as needed, but exclude 'imageUpload' and 'mediaEmbed'
            ],
        })
        .catch(error => {
            console.error(error);
        });
    ClassicEditor
        .create(document.querySelector('#league_page_desc'), {
            toolbar: [
                'heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote', 'insertTable', '|', 'undo', 'redo'
                // Add other items as needed, but exclude 'imageUpload' and 'mediaEmbed'
            ],
        })
        .catch(error => {
            console.error(error);
        });
    // ClassicEditor
    //     .create(document.querySelector('#league_media_content'), {
    //         toolbar: [
    //             'heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote', 'insertTable', '|', 'undo', 'redo'
    //             // Add other items as needed, but exclude 'imageUpload' and 'mediaEmbed'
    //         ],
    //     })
    //     .catch(error => {
    //         console.error(error);
    //     });
</script>
<script>
    $(document).ready(function() {
        // Add custom validation method for YouTube links
        $.validator.addMethod("validYouTubeLink", function(value, element) {
            // Regular expression pattern to match YouTube video URLs (including standard and shorts)
            var pattern = /^(?:https?:\/\/)?(?:www\.)?(?:youtube\.com\/(?:shorts\/|watch\?v=)|youtu\.be\/)[\w\-]+(?:&[\w=]*)?$/;
            return pattern.test(value);
        }, "Please enter a valid YouTube link.");

        $('#leaguePageForm').validate({
            ignore: [],
            debug: false,
            rules: {
                banner_title: {
                    required: true,
                },
                league_youtube_link: {
                    validYouTubeLink: true // Use custom validation method
                },
            },
            messages: {
                banner_title: 'Please enter league banner title.',
                league_youtube_link: {
                    validYouTubeLink: 'Please enter a valid YouTube link.',
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
        if (event.target.files && event.target.files[0]) {
            image.src = URL.createObjectURL(event.target.files[0]);
            image.style.display = 'block';
        }
    };

    var loadLeagueCoverImage = function(event) {
        var image = document.getElementById('league_cover_image');
        if (event.target.files && event.target.files[0]) {
            image.src = URL.createObjectURL(event.target.files[0]);
            image.style.display = 'block';
        }
    };
</script>
@endsection
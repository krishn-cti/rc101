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
                                    <h3 class="mb-0 ct_fs_22">Edit Curriculum</h3>
                                    <a href="{{url('curriculums/unit-list')}}"> <button class="ct_custom_btn1 mx-auto"> Back to List </button> </a>
                                </div>
                                <form action="{{url('curriculums/unit-update')}}" method="POST" id="editCurriculums" enctype="multipart/form-data">
                                    <input type="hidden" name="id" value="{{$curriculumsData->id}}">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label for="title" class="mb-2">Title</label>
                                                <input type="text" class="form-control ct_input" name="title" placeholder="Title" value="{{ old('title', $curriculumsData->title) }}">
                                                @error('title')
                                                <div class="text text-danger mt-2">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <!-- <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label for="type" class="mb-2">Type</label>
                                                <select name="type" class="form-control ct_input">
                                                    <option value="" disabled>Select Doc/Slide</option>
                                                    <option value="doc" {{ (old('type', $curriculumsData->type ?? '') == 'doc') ? 'selected' : '' }}>Doc</option>
                                                    <option value="slide" {{ (old('type', $curriculumsData->type ?? '') == 'slide') ? 'selected' : '' }}>Slide</option>
                                                </select>
                                                @error('type')
                                                <div class="text text-danger mt-2">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div> -->
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label for="type" class="mb-2">Type</label>
                                                <select name="type" class="form-control ct_input" id="doc_type">
                                                    <option value="" disabled>Select Doc/Slide/PDF</option>
                                                    <option value="doc" {{ old('type', $curriculumsData->type) == 'doc' ? 'selected' : '' }}>Doc</option>
                                                    <option value="slide" {{ old('type', $curriculumsData->type) == 'slide' ? 'selected' : '' }}>Slide</option>
                                                    <option value="pdf" {{ old('type', $curriculumsData->type) == 'pdf' ? 'selected' : '' }}>PDF</option>
                                                </select>
                                                @error('type')
                                                <div class="text text-danger mt-2">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label for="file_type" class="mb-2">File Type</label>
                                                <select name="file_type" class="form-control ct_input">
                                                    <option value="" disabled>Select File Type</option>
                                                    <option value="slide_decks" {{ (old('file_type', $curriculumsData->file_type ?? '') == 'slide_decks') ? 'selected' : '' }}>Slide Decks</option>
                                                    <option value="student_pages" {{ (old('file_type', $curriculumsData->file_type ?? '') == 'student_pages') ? 'selected' : '' }}>Student Pages</option>
                                                    <option value="instructions" {{ (old('file_type', $curriculumsData->file_type ?? '') == 'instructions') ? 'selected' : '' }}>Instructions</option>
                                                    <option value="samples" {{ (old('file_type', $curriculumsData->file_type ?? '') == 'samples') ? 'selected' : '' }}>Samples</option>
                                                </select>
                                                @error('file_type')
                                                <div class="text text-danger mt-2">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label for="category_id" class="mb-2">Unit Category</label>
                                                <select name="category_id" class="form-control ct_input">
                                                    <option value="" disabled>Select Unit Category</option>
                                                    @foreach($categoryData as $category)
                                                    <option value="{{ $category->id }}"
                                                        {{ (old('category_id', $curriculumsData->category_id) == $category->id) ? 'selected' : '' }}>
                                                        {{ $category->category_name }}
                                                    </option>
                                                    @endforeach
                                                </select>
                                                @error('category_id')
                                                <div class="text text-danger mt-2">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <!-- <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label for="embed_link" class="mb-2">Embed Link</label>
                                                <input type="text" class="form-control ct_input" name="embed_link" placeholder="Embed Link" value="{{ old('title', $curriculumsData->embed_link) }}">
                                                @error('embed_link')
                                                <div class="text text-danger mt-2">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div> -->

                                        <div class="form-group col-md-6 mb-3">
                                            <label for="embed_link" class="mb-2" id="embedLabel">Embed Link</label>

                                            <!-- Text input -->
                                            <input type="text"
                                                class="form-control ct_input"
                                                name="embed_link"
                                                id="embedText"
                                                placeholder="Embed Link"
                                                value="{{ old('embed_link', $curriculumsData->type != 'pdf' ? $curriculumsData->embed_link : '') }}">

                                            <!-- File input -->
                                            <input type="file"
                                                class="form-control ct_input d-none"
                                                name="embed_link"
                                                id="embedFile"
                                                accept="application/pdf">

                                            {{-- Show existing PDF --}}
                                            @if($curriculumsData->type === 'pdf' && $curriculumsData->embed_link)
                                            <small class="d-block mt-2">
                                                Current PDF:
                                                <a href="{{ url('uploads/curriculum_pdfs/'.$curriculumsData->embed_link) }}" target="_blank">
                                                    {{ $curriculumsData->embed_link }}
                                                </a>
                                            </small>
                                            @endif

                                            @error('embed_link')
                                            <div class="text text-danger mt-2">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label for="number_of_days" class="mb-2">Number of Days</label>
                                                <input type="number" class="form-control ct_input" name="number_of_days" placeholder="Number of Days" min="1" value="{{ old('number_of_days', $curriculumsData->number_of_days) }}">
                                                @error('number_of_days')
                                                <div class="text text-danger mt-2">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group mb-3">
                                                <label for="description" class="mb-2">Description</label>
                                                <textarea class="form-control ct_input" name="description" placeholder="Description">{{ old('description', $curriculumsData->description) }}</textarea>
                                                @error('description')
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
    $(document).ready(function () {

        $('#editCurriculums').validate({
            ignore: [],
            rules: {
                title: {
                    required: true,
                    maxlength: 150,
                },
                description: {
                    required: true,
                    maxlength: 255,
                },
            },
            messages: {
                title: {
                    required: "The title is required.",
                    maxlength: "The title must not exceed 150 characters.",
                },
                description: {
                    required: "The description is required.",
                    maxlength: "The description must not exceed 255 characters.",
                },
            },
            submitHandler: function(form) {
                form.submit();
            }
        });

        function toggleEmbedField() {
            let type = $('#doc_type').val();

            if (type === 'pdf') {
                $('#embedText').addClass('d-none');
                $('#embedFile').removeClass('d-none');
                $('#embedLabel').text('Upload PDF');
            } else {
                $('#embedFile').addClass('d-none');
                $('#embedText').removeClass('d-none');
                $('#embedLabel').text('Embed Link');
            }
        }

        $('#doc_type').on('change', toggleEmbedField);

        // on load
        toggleEmbedField();
    });
</script>
@endsection
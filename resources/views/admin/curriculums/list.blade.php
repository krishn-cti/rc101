@extends('admin.layouts.app')
@section('content')
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
                                <div class="card-title border-bootom-none mb-30 d-flex align-items-center justify-content-between">
                                    <h6 class="mb-0">All Curriculums</h6>
                                    <a href="{{ url('curriculums/unit-add') }}">
                                        <button class="ct_custom_btn1 mx-auto">Add New Curriculum</button>
                                    </a>
                                </div>

                                <div class="">
                                    <table class="table curriculum-data-table table-responsive table-bordered table-hover mb-0" id="curriculumTable">
                                        <thead>
                                            <tr>
                                                <th width="40">Order</th>
                                                <th>No</th>
                                                <th>Title</th>
                                                <th>Category</th>
                                                <th>Type (Doc/Slide)</th>
                                                <th>File Type</th>
                                                <th># of Days</th>
                                                <th>Embed Link</th>
                                                <th width="100px">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- geting this result from dataTable -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(function() {

        var table = $('#curriculumTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ url('curriculums/unit-list') }}",
            rowId: 'DT_RowId',
            columns: [{
                    data: 'drag',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'serial_number',
                    name: 'serial_number'
                },
                {
                    data: 'title',
                    name: 'title'
                },
                {
                    data: 'category',
                    name: 'category'
                },
                {
                    data: 'type',
                    name: 'type'
                },
                {
                    data: 'file_type',
                    name: 'file_type'
                },
                {
                    data: 'number_of_days',
                    name: 'number_of_days'
                },
                {
                    data: 'embed_link',
                    name: 'embed_link'
                },
                {
                    data: 'action',
                    orderable: false,
                    searchable: false
                }
            ],
            drawCallback: function() {
                enableDragDrop();
            }
        });

        function enableDragDrop() {
            $("#curriculumTable tbody").sortable({
                handle: '.drag-handle',
                axis: 'y',
                update: function() {
                    updateSequence();
                }
            }).disableSelection();
        }

        function updateSequence() {
            let items = [];

            $('#curriculumTable tbody tr').each(function(index) {
                items.push({
                    id: this.id.replace('row_', ''),
                    sequence: index + 1
                });
            });

            $.ajax({
                url: "{{ url('curriculums/update-sequence') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    items: items
                },
                success: function(res) {
                    $('#curriculumTable').DataTable().ajax.reload(null, false);
                    toastr.success(res.message);
                },
                error: function() {
                    toastr.error('Failed to update order');
                }
            });
        }

    });
</script>
<script>
    function deleteConfirm(id) {
        bootbox.confirm({
            closeButton: false,
            message: '<p class="text-center mb-0" style="font-size: 20px;">Are you sure you want to delete?</p>',
            buttons: {
                'cancel': {
                    label: 'No',
                    className: 'btn-danger'
                },
                'confirm': {
                    label: 'Yes',
                    className: 'btn-success'
                }
            },
            callback: function(result) {
                if (result) {
                    $.ajax({
                        url: "{{ url('curriculums/unit-delete') }}",
                        type: "POST",
                        cache: false,
                        data: {
                            _token: '{{ csrf_token() }}',
                            id: id
                        },
                        success: function(response) {
                            if (response.error) {
                                toastr.error(response.error);
                            } else {
                                toastr.success(response.message);
                                $('#curriculumTable').DataTable().ajax.reload(null, false);
                            }
                        },
                        error: function(xhr, textStatus, errorThrown) {
                            if (xhr.status === 422) {
                                toastr.error(xhr.responseJSON.error);
                            } else if (xhr.status === 404) {
                                toastr.error(xhr.responseJSON.error);
                            } else {
                                toastr.error('An error occurred while processing your request.');
                            }
                        }

                    });
                }
            }
        });
    }
</script>
@endsection
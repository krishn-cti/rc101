@extends('admin.layouts.app')
@section('content')
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
                                <div class="card-title border-bootom-none mb-30 d-flex align-items-center justify-content-between">
                                    <h6 class="mb-0">All Users</h6>
                                    <a href="{{ url('add-user') }}">
                                        <button class="ct_custom_btn1 mx-auto">Add New User</button>
                                    </a>
                                </div>
                                <table class="table user-data-table table-responsive table-bordered table-hover mb-0" id="userTable">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Profile</th>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Number</th>
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
     
<script type="text/javascript">
    $(function () {  
        var table = $('.user-data-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ url('list-user') }}",
            columns: [
                {data: 'serial_number', name: 'serial_number'}, // Change 'id' to 'serial_number'
                {data: 'profile_image', name: 'profile_image'},
                {data: 'name', name: 'name'},
                {data: 'email', name: 'email'},
                {data: 'number', name: 'number'},
                {data: 'action', name: 'action', orderable: false, searchable: false},
            ]
        });
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
                        url: "{{ url('delete-user') }}",
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
                                $('#userTable').DataTable().ajax.reload(null, false);
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
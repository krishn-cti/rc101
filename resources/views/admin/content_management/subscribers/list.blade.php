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
                                    <h6 class="mb-0">All Subscribers</h6>
                                    <!-- <a href="{{ url('subscription-add') }}">
                                        <button class="ct_custom_btn1 mx-auto">Add New Plan</button>
                                    </a> -->
                                </div>

                                <table class="table subscriber-data-table table-responsive table-bordered table-hover mb-0" id="nhrlTable">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Subscriber</th>
                                            <th>Plan</th>
                                            <th>Type</th>
                                            <th>Amount</th>
                                            <th>Start Date</th>
                                            <th>End Date</th>
                                            <th>Status</th>
                                            <!-- <th>Action</th> -->
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
    $(function() {
        var table = $('.subscriber-data-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ url('list-subscriber') }}",
            columns: [{
                    data: 'serial_number',
                    name: 'serial_number'
                }, // Change 'id' to 'serial_number'
                {
                    data: 'user_name',
                    name: 'user_name'
                },
                {
                    data: 'subscription_name',
                    name: 'subscription_name'
                },
                {
                    data: 'type',
                    name: 'type'
                },
                {
                    data: 'amount',
                    name: 'amount',
                },
                {
                    data: 'start_date',
                    name: 'start_date'
                },
                {
                    data: 'end_date',
                    name: 'end_date'
                },
                {
                    data: 'status',
                    name: 'status'
                },
                // {
                //     data: 'actions',
                //     name: 'actions',
                //     orderable: false,
                //     searchable: false
                // },
            ]
        });
        // // Handle Edit button click
        // $(document).on('click', '.edit-end-date', function() {
        //     $('#subscription_id').val($(this).data('id'));
        //     $('#end_date').val($(this).data('end-date'));
        //     console.log("jhfhfhfhf")
        //     $('#editEndDateModal').modal('show');
        // });

        // // Handle form submit
        // $('#editEndDateForm').on('submit', function(e) {
        //     e.preventDefault();
        //     var id = $('#subscription_id').val();
        //     var endDate = $('#end_date').val();

        //     $.ajax({
        //         url: "{{ url('update-end-date') }}",
        //         method: 'POST',
        //         data: {
        //             _token: '{{ csrf_token() }}',
        //             id: id,
        //             end_date: endDate
        //         },
        //         success: function(res) {
        //             if (res.success) {
        //                 toastr.success(res.message);
        //                 $('#editEndDateModal').modal('hide');
        //                 $('#nhrlTable').DataTable().ajax.reload(null, false);
        //             } else {
        //                 toastr.error(res.message);
        //             }
        //         },
        //         error: function() {
        //             toastr.error('Something went wrong.');
        //         }
        //     });
        // });
    });
</script>
<script>
    $(document).on('click', '.edit-end-date', function() {
        const parent = $(this).closest('.editable-end-date');
        const dateText = parent.find('.date-text');
        const currentDateTime = dateText.text().trim();
        const id = parent.data('id');

        // Convert to datetime-local format (YYYY-MM-DDTHH:MM)
        let formattedValue = "";
        if (currentDateTime && currentDateTime !== "-") {
            formattedValue = currentDateTime.replace(' ', 'T').slice(0, 19);
        } else {
            const now = new Date();
            formattedValue = now.toISOString().slice(0, 19);
        }

        const now = new Date();
        const formattedNow = now.toISOString().slice(0, 19);

        const inputHtml = `
            <input type="datetime-local"
                class="form-control form-control-sm new-end-date"
                value="${formattedValue}"
                min="${formattedNow}"
                step="1"
                style="width:220px; display:inline-block;">
            <a href="javascript:void(0)" 
            class="save-end-date" 
            data-id="${id}" 
            data-old-date="${currentDateTime}" 
            style="margin-left:6px; font-size:18px; color:#d78d2e;">
                <i class="fas fa-check"></i>
            </a>
        `;
        parent.html(inputHtml);

        // Automatically focus the input for quick editing
        parent.find('input').focus();
    });

    $(document).on('keypress', '.new-end-date', function(e) {
        if (e.which === 13) { // Enter key
            $(this).siblings('.save-end-date').trigger('click');
        }
    });

    $(document).on('click', '.save-end-date', function() {
        const $this = $(this);
        const parent = $this.closest('.editable-end-date');
        const id = $this.data('id');
        const oldDate = $this.data('old-date')?.trim();
        const newDateTime = parent.find('.new-end-date').val();

        // Format to MySQL datetime (YYYY-MM-DD HH:MM:SS)
        let formattedNewDate = '';
        if (newDateTime) {
            const date = new Date(newDateTime);
            const pad = (n) => n.toString().padStart(2, '0');
            formattedNewDate = `${date.getFullYear()}-${pad(date.getMonth() + 1)}-${pad(date.getDate())} ${pad(date.getHours())}:${pad(date.getMinutes())}:${pad(date.getSeconds())}`;
        }

        // If nothing changed, just revert
        if (formattedNewDate === '' || formattedNewDate === oldDate) {
            revertToOldValue(parent, oldDate);
            return;
        }

        bootbox.confirm({
            closeButton: false,
            message: '<p class="text-center mb-0" style="font-size: 18px;">Are you sure you want to update the end date?</p>',
            buttons: {
                cancel: {
                    label: 'No',
                    className: 'btn-danger'
                },
                confirm: {
                    label: 'Yes',
                    className: 'btn-success'
                }
            },
            callback: function(result) {
                if (result) {
                    $this.find('i')
                        .removeClass('fa-check')
                        .addClass('fa-spinner fa-spin')
                        .css('color', '#d78d2e');
                    $this.css('pointer-events', 'none');

                    updateEndDate(id, formattedNewDate, parent, $this);
                }
            }
        });
    });

    function updateEndDate(id, newDate, parent, buttonEl) {
        $.ajax({
            url: "{{ url('update-end-date') }}",
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                id: id,
                end_date: newDate
            },
            success: function(response) {
                if (response.success) {
                    // Replace cell content with updated date
                    parent.html(`
                    <span class="date-text">${newDate}</span>
                        <a href="javascript:void(0)" class="edit-end-date">
                            <lord-icon
                                src="https://cdn.lordicon.com/wuvorxbv.json"
                                trigger="hover"
                                colors="primary:#333333,secondary:#333333"
                                style="width:20px;height:20px">
                            </lord-icon>
                        </a>
                    `);

                    // Optional subtle highlight animation
                    parent.css('background', '#d4edda');
                    setTimeout(() => parent.css('background', ''), 1200);
                } else {
                    // Show server validation message in Bootbox
                    let message = response.message || 'Something went wrong.';
                    if (response.errors && response.errors.end_date) {
                        message = response.errors.end_date[0];
                    }
                    toastr.error(message);
                    resetSaveIcon(buttonEl);
                }
            },
            error: function(xhr) {
                let message = 'Failed to update date.';
                if (xhr.responseJSON) {
                    if (xhr.responseJSON.message) message = xhr.responseJSON.message;
                    else if (xhr.responseJSON.errors && xhr.responseJSON.errors.end_date) {
                        message = xhr.responseJSON.errors.end_date[0];
                    }
                }
                toastr.error(message);
                resetSaveIcon(buttonEl);
            }
        });
    }

    function revertToOldValue(parent, oldDate) {
        parent.html(`
        <span class="date-text">${oldDate || '-'}</span>
            <a href="javascript:void(0)" class="edit-end-date">
                <lord-icon
                    src="https://cdn.lordicon.com/wuvorxbv.json"
                    trigger="hover"
                    colors="primary:#333333,secondary:#333333"
                    style="width:20px;height:20px">
                </lord-icon>
            </a>
        `);
    }

    function resetSaveIcon(buttonEl) {
        buttonEl.find('i')
            .removeClass('fa-spinner fa-spin')
            .addClass('fa-check')
            .css('color', '#d78d2e');
        buttonEl.css('pointer-events', 'auto');
    }
</script>
@endsection
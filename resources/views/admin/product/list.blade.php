@extends('admin.layouts.app')
@section('content')

<!-- Main Content Area -->
<div class="main-content introduction-farm">
    <div class="content-wraper-area">
        <div class="dashboard-area">
            <div class="container-fluid">
                <div class="row g-4">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="card-title border-bootom-none mb-30 d-flex align-items-center justify-content-between">
                                    <h6 class="mb-0">All Products</h6>
                                    <a href="{{ url('add-product') }}">
                                        <button class="ct_custom_btn1 mx-auto">Add New Product</button>
                                    </a>
                                </div>
                                <div class="table-responsive">
                                    <table class="table product-data-table table-bordered table-hover mb-0" id="productTable">
                                        <!-- <table class="table table-centered table-nowrap table-hover mb-0"> -->
                                        <thead>
                                            <tr>
                                                <th>Sr No</th>
                                                <th>Image</th>
                                                <th>Product Name</th>
                                                <th>Price</th>
                                                <th>Discount</th>
                                                <th>Quantity</th>
                                                <th>Category</th>
                                                <th>Sub Category</th>
                                                <!-- <th>Sku</th> -->
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
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
        var table = $('.product-data-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ url('list-product') }}",
            columns: [{
                    data: 'serial_number',
                    name: 'serial_number'
                }, // Change 'id' to 'serial_number'
                {
                    data: 'thumbnail',
                    name: 'thumbnail'
                },
                {
                    data: 'product_name',
                    name: 'product_name'
                },
                {
                    data: 'price',
                    name: 'price'
                },
                {
                    data: 'discount',
                    name: 'discount'
                },
                {
                    data: 'quantity',
                    name: 'quantity'
                },
                {
                    data: 'category',
                    name: 'category'
                },
                {
                    data: 'sub_category',
                    name: 'sub_category'
                },
                // {data: 'sku', name: 'sku'},
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                },
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
                        url: "{{url('delete-product')}}",
                        type: "POST",
                        cache: false,
                        data: {
                            _token: '{{ csrf_token() }}',
                            id: id
                        },
                        success: function(data, textStatus, xhr) {
                            if (data == true && textStatus == 'success' && xhr.status == '200') {
                                toastr.warning('Product Deleted !!');
                                $('#productTable').DataTable().ajax.reload(null, false);
                            } else {
                                toastr.error(data);
                            }
                        }
                    });
                }
            }
        });
    }
</script>
@endsection
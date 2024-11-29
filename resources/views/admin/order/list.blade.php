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
                                <div class="card-title border-bootom-none mb-30 d-flex align-items-center justify-content-between">
                                    <h6 class="mb-0">All Orders</h6>
                                </div>
                                <table class="table order-data-table table-responsive table-bordered table-hover mb-0">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Product</th>
                                            <th>Category</th>
                                            <th>Sub Category</th>
                                            <th>Total Price</th>
                                            <th>Status</th>
                                            <th>Payment Method</th>
                                            <!-- <th width="100px">Action</th> -->
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
        var table = $('.order-data-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ url('list-order') }}",
            columns: [
                {data: 'serial_number', name: 'serial_number'}, // Change 'id' to 'serial_number'
                {data: 'thumbnail', name: 'thumbnail'},
                {data: 'category', name: 'category'},
                {data: 'sub_category', name: 'sub_category'},
                {data: 'total_price', name: 'total_price'},
                {data: 'status', name: 'status'},
                {data: 'payment_method', name: 'payment_method'},
                //{data: 'action', name: 'action', orderable: false, searchable: false},
            ]
        });
    });
</script>
@endsection
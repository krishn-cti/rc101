<!-- Must needed plugins to the run this Template -->
<script src="{{asset('admin/js/jquery.min.js')}}"></script>

<!-- jQuery UI (sortable depends on this) -->
<link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>

<script src="{{asset('admin/js/bootstrap.bundle.min.js')}}"></script>
<script src="{{asset('admin/js/default-assets/scrool-bar.js')}}"></script>
<script src="{{asset('admin/js/default-assets/active.js')}}"></script>

<script src="{{asset('admin/js/apexcharts.min.js')}}"></script>
<script src="{{asset('admin/js/dashboard-custom.js')}}"></script>

<script src="https://cdn.lordicon.com/lordicon.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.1/jquery.validate.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>

<meta name="csrf-token" content="{{ csrf_token() }}">

<link href="https://cdn.datatables.net/1.11.4/css/dataTables.bootstrap5.min.css" rel="stylesheet">
<script src="https://cdn.datatables.net/1.11.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.4/js/dataTables.bootstrap5.min.js"></script>

<script src="https://cdn.ckeditor.com/ckeditor5/43.0.0/ckeditor5.umd.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootbox.js/5.5.2/bootbox.min.js"></script>

<!-- Script to display Toastr messages -->
@if(Session::has('message'))
<script>
    $(function() {
        toastr['{{ Session::get("alert-class") }}']('{{ Session::get("message") }}');
    });
</script>
@endif
@if(Session::has('error_msg') || Session::has('success_msg'))
<script>
    $(".alert").delay(2500).slideUp(500, function() {
        $(this).alert('close');
    });
</script>
@endif
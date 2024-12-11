<!DOCTYPE html>

<html class="loading @auth {{auth()->user()->theam_mode}} @endauth" lang="en" data-textdirection="ltr">

<head>
    <meta charset="utf-8">
    <link rel="icon" type="image/x-icon" href="{{asset('admin/img/logo/logo.png')}}">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Title -->
    <title>{{config('app.name')}} | Admin</title>
    @include('admin.layouts.css')
    @yield('css')
</head>

<body>
    <!-- Preloader -->
    <div id="preloader">
        <div class="loader">
            <svg width="240" height="240" viewBox="0 0 240 240">
                <circle class="loader-ring loader-ring-a" cx="120" cy="120" r="105" fill="none" stroke="#000" stroke-width="20" stroke-dasharray="0 660" stroke-dashoffset="-330" stroke-linecap="round"></circle>
                <circle class="loader-ring loader-ring-b" cx="120" cy="120" r="35" fill="none" stroke="#000" stroke-width="20" stroke-dasharray="0 220" stroke-dashoffset="-110" stroke-linecap="round"></circle>
                <circle class="loader-ring loader-ring-c" cx="85" cy="120" r="70" fill="none" stroke="#000" stroke-width="20" stroke-dasharray="0 440" stroke-linecap="round"></circle>
                <circle class="loader-ring loader-ring-d" cx="155" cy="120" r="70" fill="none" stroke="#000" stroke-width="20" stroke-dasharray="0 440" stroke-linecap="round"></circle>
            </svg>
        </div>

    </div>
    <!-- /Preloader -->
    <div class="flapt-page-wrapper">
        <!-- BEGIN: Main Menu-->
        @include('admin.layouts.sidebar')
        <!-- END: Main Menu-->
        <div class="flapt-page-content">
            <!-- BEGIN: Header-->
            @include('admin.layouts.header')
            <!-- END: Header-->
            <!-- BEGIN: Content-->
            @yield('content')

            <!-- END: Content-->
        </div>
    </div>

    @include('admin.layouts.footer')

    @include('admin.layouts.js')
    @yield('script')
</body>

</html>
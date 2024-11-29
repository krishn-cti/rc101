@extends('admin.layouts.app')

@section('content')

<!-- Main Content Area -->
<div class="main-content introduction-farm">
    <div class="content-wraper-area">
        <!-- Start Content-->
        <div class="container-fluid">
            <div class="row g-4">


                <div class="col-lg-8 mx-auto mt-5">
                    <div class="card main-profile-cover">

                        <div class="card-body bg-white">
                            <div class="d-flex align-items-center mb-4 justify-content-between">
                                <h3 class="mb-0 ct_fs_22">Profile</h3>
                                <div>
                                    <a class="ct_custom_btn1" href="{{url('edit-profile')}}">Edit Profile</a>
                                </div>
                            </div>
                            <div class="d-sm-flex align-items-center">
                                <div class="single-profile-image">
                                    <img src="{{auth()->user()->profile_image}}" alt="" class="ct_img_w_50">
                                </div>
                                <div class="flex-fill main-profile-meta">
                                    <h4 class="ct_fs_22 mb-0">{{auth()->user()->name}}</h4>

                                </div>
                            </div>
                            <div class="mt-4">

                                <ul class="ps-0">
                                    <li class="list-group-item ps-0">
                                        <div class="d-flex flex-wrap align-items-center">
                                            <div class="me-2"> Name : </div> <span
                                                class="text-muted">{{auth()->user()->name}}</span>
                                        </div>
                                    </li>
                                    <li class="list-group-item  ps-0">
                                        <div class="d-flex flex-wrap align-items-center">
                                            <div class="me-2"> Email : </div> <span
                                                class="text-muted">{{auth()->user()->email}}</span>
                                        </div>
                                    </li>
                                    <li class="list-group-item  ps-0">
                                        <div class="d-flex flex-wrap align-items-center">
                                            <div class="me-2"> Phone : </div> <span
                                                class="text-muted">{{auth()->user()->number}}</span>
                                        </div>
                                    </li>

                                </ul>
                            </div>

                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
    @endsection
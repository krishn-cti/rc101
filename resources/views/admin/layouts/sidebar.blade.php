<!-- Sidemenu Area -->
<div class="flapt-sidemenu-wrapper">
    <!-- Desktop Logo -->
    <div class="flapt-logo">
        <!-- <a href="index.html"><img class="desktop-logo" src="img/core-img/logo.png" alt="Desktop Logo"> <img
                class="small-logo" src="img/core-img/small-logo.png" alt="Mobile Logo"></a> -->
        <a href="{{url('dashboard')}}">
            <img class="desktop-logo" src="{{ asset('/admin/img/logo/logo.png') }}" alt="Desktop Logo">
            <img class="small-logo" src="{{ asset('/admin/img/logo/logo.png') }}" alt="Mobile Logo">
        </a>
        <h4>{{config('app.name')}}</h4>
    </div>
    <hr class="mb-0 mt-4">
    <!-- Side Nav -->
    <div class="flapt-sidenav" id="flaptSideNav">
        <!-- Side Menu Area -->
        <div class="side-menu-area">
            <!-- Sidebar Menu -->
            <nav>
                <ul id="ct_sidebar" class="sidebar-menu" data-widget="tree">
                    <li class="menu-header-title {{ request()->is('dashboard') ? 'active' : '' }} ps-0">
                        <a href="{{ url('dashboard') }}"><i class='bx bxs-dashboard'></i> Dashboard</a>
                    </li>

                    <li class="menu-header-title {{ request()->is('list-product', 'add-product', 'edit-product/*') ? 'active' : '' }} ps-0">
                        <a href="{{ url('list-product') }}"><i class='bx bxl-product-hunt'></i> Products</a>
                    </li>

                    <li class="menu-header-title {{ request()->is('list-category', 'add-category', 'edit-category/*') ? 'active' : '' }} ps-0">
                        <a href="{{ url('list-category') }}"><i class='bx bx-notepad'></i> Categories</a>
                    </li>

                    <li class="menu-header-title {{ request()->is('list-sub-category', 'add-sub-category', 'edit-sub-category/*') ? 'active' : '' }} ps-0">
                        <a href="{{ url('list-sub-category') }}"><i class='bx bx-food-menu'></i> Sub Categories</a>
                    </li>

                    <li class="menu-header-title {{ request()->is('list-user', 'add-user', 'edit-user/*') ? 'active' : '' }} ps-0">
                        <a href="{{ url('list-user') }}"><i class='bx bx-group'></i>All Users</a>
                    </li>

                    <li class="menu-header-title {{ request()->is('partner-list', 'partner-add', 'partner-edit/*') ? 'active' : '' }} ps-0">
                        <a href="{{ url('partner-list') }}"><i class='bx bx-buildings'></i> Partners</a>
                    </li>

                    <li class="menu-header-title {{ request()->is('list-order') ? 'active' : '' }} ps-0">
                        <a href="{{ url('list-order') }}"><i class='bx bx-receipt'></i> Orders</a>
                    </li>

                    <li class="menu-header-title treeview ps-0 {{ request()->is('cms/*') ? 'menu-open active' : '' }}">
                        <a href="javascript:void(0)">
                            <i class='bx bx-book-content'></i> <span>CMS</span>
                            <i class="fa fa-angle-right"></i>
                        </a>

                        @if(request()->is('cms/*'))
                        <ul class="treeview-menu" style="display: block;">
                            @else
                            <ul class="treeview-menu" style="display: none;">
                                @endif
                                <li class="menu-header-title {{ request()->is('cms/home') ? 'active' : '' }} ps-0">
                                    <a href="{{ url('cms/home') }}"><i class='bx bx-home'></i> Home</a>
                                </li>

                                <li class="menu-header-title {{ request()->is('cms/about') ? 'active' : '' }} ps-0">
                                    <a href="{{ url('cms/about') }}"><i class='bx bx-info-circle'></i> About</a>
                                </li>

                                <li class="menu-header-title treeview ps-0 {{ request()->is('cms/league', 'cms/tournament-list', 'cms/tournament-add', 'cms/tournament-edit/*', 'cms/presentation-list', 'cms/presentation-add', 'cms/presentation-edit/*') ? 'menu-open active' : '' }}">
                                    <a href="javascript:void(0)">
                                        <i class='bx bx-trophy'></i> <span>League/Tournament</span>
                                        <i class="fa fa-angle-right"></i>
                                    </a>

                                    @if( request()->is('cms/league', 'cms/tournament-list', 'cms/tournament-add', 'cms/tournament-edit/*', 'cms/presentation-list', 'cms/presentation-add', 'cms/presentation-edit/*'))
                                    <ul class="treeview-menu" style="display: block;">
                                        @else
                                        <ul class="treeview-menu" style="display: none;">
                                            @endif
                                            <li class="menu-header-title {{ request()->is('cms/league') ? 'active' : '' }} ps-0">
                                                <a href="{{ url('cms/league') }}"><i class='bx bxs-component'></i> League</a>
                                            </li>
                                            <li class="menu-header-title {{ request()->is('cms/tournament-list', 'cms/tournament-add', 'cms/tournament-edit/*') ? 'active' : '' }} ps-0">
                                                <a href="{{ url('cms/tournament-list') }}"><i class='bx bx-calendar-event'></i> Tournaments</a>
                                            </li>
                                            <li class="menu-header-title {{ request()->is('cms/presentation-list', 'cms/presentation-add', 'cms/presentation-edit/*') ? 'active' : '' }} ps-0">
                                                <a href="{{ url('cms/presentation-list') }}"><i class='bx bx-slideshow'></i> Presentations</a>
                                            </li>
                                        </ul>
                                </li>

                                <li class="menu-header-title treeview ps-0 {{ request()->is('cms/lessons/*') ? 'menu-open active' : '' }}">
                                    <a href="javascript:void(0)">
                                        <i class='bx bxs-file-plus'></i> <span>Lessons</span>
                                        <i class="fa fa-angle-right"></i>
                                    </a>

                                    @if( request()->is('cms/lessons/*'))
                                    <ul class="treeview-menu" style="display: block;">
                                        @else
                                        <ul class="treeview-menu menu-list" style="display: none;">
                                            @endif
                                            <li class="menu-header-title {{ request()->is('cms/lessons/3d-modeling-list', 'cms/lessons/3d-modeling-add', 'cms/lessons/3d-modeling-edit/*') ? 'active' : '' }} ps-0">
                                                <a href="{{ url('cms/lessons/3d-modeling-list') }}"><i class='bx bxs-file'></i> 3D Modeling</a>
                                            </li>
                                            <li class="menu-header-title {{ request()->is('cms/lessons/3d-printing-list', 'cms/lessons/3d-printing-add', 'cms/lessons/3d-printing-edit/*') ? 'active' : '' }} ps-0">
                                                <a href="{{ url('cms/lessons/3d-printing-list') }}"><i class='bx bxs-printer'></i> 3D Printing</a>
                                            </li>
                                            <li class="menu-header-title {{ request()->is('cms/lessons/batteries-list', 'cms/lessons/batteries-add', 'cms/lessons/batteries-edit/*') ? 'active' : '' }} ps-0">
                                                <a href="{{ url('cms/lessons/batteries-list') }}"><i class='bx bx-battery'></i> Batteries</a>
                                            </li>
                                            <li class="menu-header-title {{ request()->is('cms/lessons/brushed-brushless-list', 'cms/lessons/brushed-brushless-add', 'cms/lessons/brushed-brushless-edit/*') ? 'active' : '' }} ps-0">
                                                <a href="{{ url('cms/lessons/brushed-brushless-list') }}"><i class='bx bx-rotate-left'></i> Brushed vs. brushless motors</a>
                                            </li>
                                            <li class="menu-header-title {{ request()->is('cms/lessons/bescs-list', 'cms/lessons/bescs-add', 'cms/lessons/bescs-edit/*') ? 'active' : '' }} ps-0">
                                                <a href="{{ url('cms/lessons/bescs-list') }}"><i class='bx bx-cog'></i> ESCs/BESCs</a>
                                            </li>
                                            <li class="menu-header-title {{ request()->is('cms/lessons/electrical-engineering-list', 'cms/lessons/electrical-engineering-add', 'cms/lessons/electrical-engineering-edit/*') ? 'active' : '' }} ps-0">
                                                <a href="{{ url('cms/lessons/electrical-engineering-list') }}"><i class='bx bx-chip'></i> Electrical engineering and robot combat</a>
                                            </li>
                                            <li class="menu-header-title {{ request()->is('cms/lessons/fusion-list', 'cms/lessons/fusion-add', 'cms/lessons/fusion-edit/*') ? 'active' : '' }} ps-0">
                                                <a href="{{ url('cms/lessons/fusion-list') }}"><i class='bx bx-layer'></i> Fusion</a>
                                            </li>
                                            <li class="menu-header-title {{ request()->is('cms/lessons/gear-ratios-list', 'cms/lessons/gear-ratios-add', 'cms/lessons/gear-ratios-edit/*') ? 'active' : '' }} ps-0">
                                                <a href="{{ url('cms/lessons/gear-ratios-list') }}"><i class='bx bx-cog'></i> Gear ratios and mechanisms</a>
                                            </li>
                                            <li class="menu-header-title {{ request()->is('cms/lessons/material-science-list', 'cms/lessons/material-science-add', 'cms/lessons/material-science-edit/*') ? 'active' : '' }} ps-0">
                                                <a href="{{ url('cms/lessons/material-science-list') }}"><i class='bx bxs-flask'></i> Materials Science and Robot Combat</a>
                                            </li>
                                            <li class="menu-header-title {{ request()->is('cms/lessons/pcbs-list', 'cms/lessons/pcbs-add', 'cms/lessons/pcbs-edit/*') ? 'active' : '' }} ps-0">
                                                <a href="{{ url('cms/lessons/pcbs-list') }}"><i class='bx bx-dna'></i> PCBs</a>
                                            </li>
                                            <li class="menu-header-title {{ request()->is('cms/lessons/physics-geometry-list', 'cms/lessons/physics-geometry-add', 'cms/lessons/physics-geometry-edit/*') ? 'active' : '' }} ps-0">
                                                <a href="{{ url('cms/lessons/physics-geometry-list') }}"><i class='bx bx-shape-triangle'></i> Physics, Geometry, and Robot Combat</a>
                                            </li>
                                            <li class="menu-header-title {{ request()->is('cms/lessons/receivers-list', 'cms/lessons/receivers-add', 'cms/lessons/receivers-edit/*') ? 'active' : '' }} ps-0">
                                                <a href="{{ url('cms/lessons/receivers-list') }}"><i class='bx bx-bar-chart-alt-2'></i> Receivers</a>
                                            </li>
                                            <li class="menu-header-title {{ request()->is('cms/lessons/slicing-list', 'cms/lessons/slicing-add', 'cms/lessons/slicing-edit/*') ? 'active' : '' }} ps-0">
                                                <a href="{{ url('cms/lessons/slicing-list') }}"><i class='bx bx-cut'></i> Slicing</a>
                                            </li>
                                            <li class="menu-header-title {{ request()->is('cms/lessons/soldering-list', 'cms/lessons/soldering-add', 'cms/lessons/soldering-edit/*') ? 'active' : '' }} ps-0">
                                                <a href="{{ url('cms/lessons/soldering-list') }}"><i class='bx bx-wrench'></i> Soldering</a>
                                            </li>
                                            <li class="menu-header-title {{ request()->is('cms/lessons/thinkercad-list', 'cms/lessons/thinkercad-add', 'cms/lessons/thinkercad-edit/*') ? 'active' : '' }} ps-0">
                                                <a href="{{ url('cms/lessons/thinkercad-list') }}"><i class='bx bx-cube'></i> ThinkerCAD</a>
                                            </li>
                                            <li class="menu-header-title {{ request()->is('cms/lessons/weapon-physics-list', 'cms/lessons/weapon-physics-add', 'cms/lessons/weapon-physics-edit/*') ? 'active' : '' }} ps-0">
                                                <a href="{{ url('cms/lessons/weapon-physics-list') }}"><i class='bx bx-target-lock'></i> Weapon Physics</a>
                                            </li>
                                        </ul>
                                </li>

                                <li class="menu-header-title treeview ps-0 {{ request()->is('cms/weight-classes/*') ? 'menu-open active' : '' }}">
                                    <a href="javascript:void(0)">
                                        <i class='bx bx-slider'></i> <span>Weight Classes</span>
                                        <i class="fa fa-angle-right"></i>
                                    </a>

                                    @if( request()->is('cms/weight-classes/*'))
                                    <ul class="treeview-menu" style="display: block;">
                                        @else
                                        <ul class="treeview-menu menu-list" style="display: none;">
                                            @endif
                                            <li class="menu-header-title {{ request()->is('cms/weight-classes/antweight-list', 'cms/weight-classes/antweight-add', 'cms/weight-classes/antweight-edit/*') ? 'active' : '' }} ps-0">
                                                <a href="{{ url('cms/weight-classes/antweight-list') }}"><i class='bx bxs-bug'></i> Antweights</a>
                                            </li>
                                            <li class="menu-header-title {{ request()->is('cms/weight-classes/beetleweight-list', 'cms/weight-classes/beetleweight-add', 'cms/weight-classes/beetleweight-edit/*') ? 'active' : '' }} ps-0">
                                                <a href="{{ url('cms/weight-classes/beetleweight-list') }}"><i class='bx bxs-bug-alt'></i> Beetleweights</a>
                                            </li>
                                            <li class="menu-header-title {{ request()->is('cms/weight-classes/fairyweight-list', 'cms/weight-classes/fairyweight-add', 'cms/weight-classes/fairyweight-edit/*') ? 'active' : '' }} ps-0">
                                                <a href="{{ url('cms/weight-classes/fairyweight-list') }}"><i class="bx bxs-magic-wand"></i> Fairyweights</a>
                                            </li>
                                            <li class="menu-header-title {{ request()->is('cms/weight-classes/featherweight-list', 'cms/weight-classes/featherweight-add', 'cms/weight-classes/featherweight-edit/*') ? 'active' : '' }} ps-0">
                                                <a href="{{ url('cms/weight-classes/featherweight-list') }}"><i class='bx bxs-plane'></i> Featherweights</a>
                                            </li>
                                            <li class="menu-header-title {{ request()->is('cms/weight-classes/hobbyweight-list', 'cms/weight-classes/hobbyweight-add', 'cms/weight-classes/hobbyweight-edit/*') ? 'active' : '' }} ps-0">
                                                <a href="{{ url('cms/weight-classes/hobbyweight-list') }}"><i class='bx bx-joystick'></i> Hobbyweights</a>
                                            </li>
                                            <li class="menu-header-title {{ request()->is('cms/weight-classes/plastic-antweight-list', 'cms/weight-classes/plastic-antweight-add', 'cms/weight-classes/plastic-antweight-edit/*') ? 'active' : '' }} ps-0">
                                                <a href="{{ url('cms/weight-classes/plastic-antweight-list') }}"><i class='bx bx-bug-alt'></i> Plastic Antweights (30lb.)</a>
                                            </li>
                                            <li class="menu-header-title {{ request()->is('cms/weight-classes/sportsman-list', 'cms/weight-classes/sportsman-add', 'cms/weight-classes/sportsman-edit/*') ? 'active' : '' }} ps-0">
                                                <a href="{{ url('cms/weight-classes/sportsman-list') }}"><i class='bx bx-run'></i> Sportsmans (30lb.)</a>
                                            </li>
                                        </ul>
                                </li>

                                <li class="menu-header-title {{ request()->is('cms/service-list', 'cms/service-add', 'cms/service-edit/*') ? 'active' : '' }} ps-0">
                                    <a href="{{ url('cms/service-list') }}"><i class='bx bxs-offer'></i> Services</a>
                                </li>
                                <!-- <li class="menu-header-title {{ request()->is('cms/leader-list', 'cms/leader-add', 'cms/leader-edit/*') ? 'active' : '' }} ps-0">
                                    <a href="{{ url('cms/leader-list') }}"><i class='bx bx-user-pin'></i> Our Leaders</a>
                                </li> -->
                                <li class="menu-header-title {{ request()->is('cms/weight-class-list', 'cms/weight-class-add', 'cms/weight-class-edit/*') ? 'active' : '' }} ps-0">
                                    <a href="{{ url('cms/weight-class-list') }}"><i class='bx bx-slider-alt'></i> Weight Class/Restrictions</a>
                                </li>
                                <li class="menu-header-title {{ request()->is('cms/league-rule-list', 'cms/league-rule-add', 'cms/league-rule-edit/*') ? 'active' : '' }} ps-0">
                                    <a href="{{ url('cms/league-rule-list') }}"><i class="bx bx-task"></i> League Rules</a>
                                </li>
                                <li class="menu-header-title {{ request()->is('cms/event-coverage-list', 'cms/event-coverage-add', 'cms/event-coverage-edit/*') ? 'active' : '' }} ps-0">
                                    <a href="{{ url('cms/event-coverage-list') }}"><i class="bx bx-calendar-event"></i> Event Coverage/Results</a>
                                </li>
                                <li class="menu-header-title {{ request()->is('cms/tools-trade-list', 'cms/tools-trade-add', 'cms/tools-trade-edit/*') ? 'active' : '' }} ps-0">
                                    <a href="{{ url('cms/tools-trade-list') }}"><i class="bx bx-wrench"></i> Tools of the Trade</a>
                                </li>
                                <li class="menu-header-title {{ request()->is('cms/terms-and-conditions') ? 'active' : '' }} ps-0">
                                    <a href="{{ url('cms/terms-and-conditions') }}"><i class="bx bx-book"></i> Terms and Conditions</a>
                                </li>
                                <li class="menu-header-title {{ request()->is('cms/privacy-policy') ? 'active' : '' }} ps-0">
                                    <a href="{{ url('cms/privacy-policy') }}"><i class="bx bx-lock"></i> Privacy Policy</a>
                                </li>
                            </ul>
                    </li>
                </ul>
            </nav>

        </div>
    </div>
</div>
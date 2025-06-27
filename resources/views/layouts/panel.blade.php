<!DOCTYPE html>
<html lang="en">

<meta http-equiv="content-type" content="text/html;charset=UTF-8" />

<head>

    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1, shrink-to-fit=no" name="viewport">
    <meta name="description" content="Admin Panel laravel Dashboard">
    <meta name="author" content="">
    <meta name="keywords" content="">

    <!-- Favicon -->
    <link rel="icon" href="{{ url('panel/assets/img/brand/logo-responsive.png') }}" type="image/x-icon" />

    <!-- Title -->
    <title>{{ env('APP_NAME') }} @if (trim($__env->yieldContent('title')))
        | @yield('title')
        @endif
    </title>

    <!-- Bootstrap css-->
    <link href="{{ url('panel/assets/plugins/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet" />

    <!-- Icons css-->
    <link href="{{ url('panel/assets/plugins/web-fonts/icons.css') }}" rel="stylesheet" />
    <link href="{{ url('panel/assets/plugins/web-fonts/font-awesome/font-awesome.min.css') }}" rel="stylesheet">
    <link href="{{ url('panel/assets/plugins/web-fonts/plugin.css') }}" rel="stylesheet" />



    <!--- Style css -->
    @if (App::getLocale() == 'en')
    <link href="{{ url('panel/assets/css/style/style.css') }}" rel="stylesheet">
    <link href="{{ url('panel/assets/css/skins.css') }}" rel="stylesheet">
    <link href="{{ url('panel/assets/css/dark-style.css') }}" rel="stylesheet">
    <link href="{{ url('panel/assets/css/colors/default.css') }}" rel="stylesheet">
    <link id="theme" rel="stylesheet" type="text/css" media="all" href="{{ url('panel/assets/css/colors/color.css') }}">
    <link href="{{ url('panel/assets/css/sidemenu/sidemenu.css') }}" rel="stylesheet">
    <link href="{{ url('panel/assets/switcher/css/switcher.css') }}" rel="stylesheet">
    @else
    <link href="{{ asset('panel/assets/css-rtl/style/style.css') }}" rel="stylesheet">
    <link href="{{ asset('panel/assets/css-rtl/skins.css') }}" rel="stylesheet">
    <link href="{{ asset('panel/assets/css-rtl/dark-style.css') }}" rel="stylesheet">
    <link id="theme" rel="stylesheet" type="text/css" media="all" href="{{ asset('panel/assets/css-rtl/colors/color.css') }}">
    <link href="{{ asset('panel/assets/css-rtl/sidemenu/sidemenu.css') }}" rel="stylesheet">
    <link href="{{ asset('panel/assets/switcher/css/switcher-rtl.css') }}" rel="stylesheet">
    @endif
    <!--- Style css -->

    <!-- Style css-->


    <!-- Color css-->


    <!-- Select2 css-->
    <link href="{{ url('panel/assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet">

    <!-- Mutipleselect css-->
    <link rel="stylesheet" href="{{ url('panel/assets/plugins/multipleselect/multiple-select.css') }}">

    <!-- Sidemenu css-->


    <!-- Switcher css-->

    <link href="{{ url('panel/assets/switcher/demo.css') }}" rel="stylesheet">
    <!-- CkEditor -->
    <script src="https://cdn.ckeditor.com/4.15.0/full/ckeditor.js"></script>
    <!-- Include this in your blade layout -->
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
</head>
<style>
    .language-switcher {
        display: flex;
        gap: 10px;
    }

    .language-switcher a {
        text-decoration: none;
        color: #000;
        /* لون النص الذي ترغب في استخدامه */
        font-weight: bold;
        /* إضافة أي خصائص أخرى للتصميم حسب الحاجة */
    }

</style>
<body class="main-body leftmenu">

    <!-- Loader -->
    <div id="global-loader">
        <img src="{{ url('panel/assets/img/loader.svg') }}" class="loader-img" alt="Loader">
    </div>
    <!-- End Loader -->

    <!-- Page -->
    <div class="page">

        <!-- Sidemenu -->
        <div class="main-sidebar main-sidebar-sticky side-menu">
            <div class="sidemenu-logo">
                <a class="main-logo" href="{{ url('/') }}">
                    <img src="{{ url('panel/assets/img/brand/logo.png') }}" class="header-brand-img desktop-logo" alt="logo" style="max-height: 52px">
                    <img src="{{ url('panel/assets/img/brand/logo-responsive.png') }}" class="header-brand-img icon-logo" alt="logo" style="max-height: 52px">
                    <img src="{{ url('panel/assets/img/brand/logo-responsive.png') }}" class="header-brand-img desktop-logo theme-logo" alt="logo" style="max-height: 52px">
                    <img src="{{ url('panel/assets/img/brand/logo.png') }}" class="header-brand-img icon-logo theme-logo" alt="logo" style="max-height: 52px">
                </a>
            </div>


            <div class="main-sidebar-body">
                <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">
                    <li class="nav-header"><span class="nav-label">ECOC Tank Rentals</span></li>
                    <li class="nav-item">
                        <a class="nav-link @if(Route::currentRouteName() == 'dashboard') active @endif" href="{{ route('dashboard') }}">
                            <span class="shape1"></span><span class="shape2"></span>
                            <i class="ti-home sidemenu-icon"></i>
                            <span class="sidemenu-label" style="font-size: 0.9rem;">Dashboard</span>
                        </a>
                    </li>
                    @if (auth()->user() && auth()->user()->isSuperAdmin())
                    <li class="nav-item">
                        <a class="nav-link @if(Route::currentRouteName() == 'tanks.settings') active @endif" href="{{ route('tanks.settings') }}">
                            <span class="shape1"></span><span class="shape2"></span>
                            <i class="ti-settings sidemenu-icon"></i>
                            <span class="sidemenu-label" style="font-size: 0.9rem;">Tank Settings</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link @if(Route::currentRouteName() == 'products.index') active @endif" href="{{ route('products.index') }}">
                            <span class="shape1"></span><span class="shape2"></span>
                            <i class="ti-package sidemenu-icon"></i>
                            <span class="sidemenu-label" style="font-size: 0.9rem;">Products</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link @if(Route::currentRouteName() == 'vessels.index') active @endif" href="{{ route('vessels.index') }}">
                            <span class="shape1"></span><span class="shape2"></span>
                            <i class="ti-anchor sidemenu-icon"></i>
                            <span class="sidemenu-label" style="font-size: 0.9rem;">Vessels</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link @if(Route::currentRouteName() == 'users.index') active @endif" href="{{ route('users.index') }}">
                            <span class="shape1"></span><span class="shape2"></span>
                            <i class="ti-user sidemenu-icon"></i>
                            <span class="sidemenu-label" style="font-size: 0.9rem;">Admins</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link @if(Route::currentRouteName() == 'clients.index') active @endif" href="{{ route('clients.index') }}">
                            <span class="shape1"></span><span class="shape2"></span>
                            <i class="ti-id-badge sidemenu-icon"></i>
                            <span class="sidemenu-label" style="font-size: 0.9rem;">Clients</span>
                        </a>
                    </li>
                    @endif
                    <li class="nav-item">
                        <a class="nav-link @if(Route::currentRouteName() == 'transactions.create') active @endif" href="{{ route('transactions.create') }}">
                            <span class="shape1"></span><span class="shape2"></span>
                            <i class="ti-exchange-vertical sidemenu-icon"></i>
                            <span class="sidemenu-label" style="font-size: 0.9rem;">Transactions</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link @if(Route::currentRouteName() == 'profile') active @endif" href="{{ route('profile') }}">
                            <span class="shape1"></span><span class="shape2"></span>
                            <i class="ti-user sidemenu-icon"></i>
                            <span class="sidemenu-label" style="font-size: 0.9rem;">Profile</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="nav-link btn btn-link p-0 w-100 text-left @if(Route::currentRouteName() == 'logout') active @endif">
                                <span class="shape1"></span><span class="shape2"></span>
                                <i class="ti-power-off sidemenu-icon"></i>
                                <span class="sidemenu-label" style="font-size: 0.9rem;">Logout</span>
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
        <!-- End Sidemenu -->
        <!-- Main Header-->
        <div class="main-header side-header sticky">
            <div class="container-fluid">
                <div class="main-header-left">
                    <a class="main-header-menu-icon" href="#" id="mainSidebarToggle"><span></span></a>
                </div>
                <div class="main-header-center">
                    <div class="responsive-logo">
                        <a href="index.html"><img src="{{ url('panel/assets/img/brand/logo.png') }}" class="mobile-logo d-lg-none header-brand-img text-left float-left" alt="ECOC Logo" style="background-color: #000b43; border-radius: 5px; max-height: 52px"></a>
                        <a href="index.html"><img src="{{ url('panel/assets/img/brand/logo.png') }}" class="mobile-logo-dark" alt="logo"></a>
                    </div>
                    <div class="input-group">
                        <div class="input-group-btn search-panel">
                            <select class="form-control select2-no-search">
                                <option label="All categories">
                                </option>
                                <option value="IT Projects">
                                    Tanks
                                </option>
                                <option value="Business Case">
                                    Transactions
                                </option>
                                <option value="Microsoft Project">
                                    Shipments
                                </option>
                                <option value="Risk Management">
                                    Clients
                                </option>
                                <option value="Team Building">
                                    Admins
                                </option>
                            </select>
                        </div>
                        <input type="search" class="form-control" placeholder="Search for anything...">
                        <button class="btn search-btn"><i class="fe fe-search"></i></button>
                    </div>
                </div>
                <div class="main-header-right">
                    <div class="dropdown header-search">
                        <a class="nav-link icon header-search">
                            <i class="fe fe-search header-icons"></i>
                        </a>
                        <div class="dropdown-menu">
                            <div class="main-form-search p-2">
                                <div class="input-group">
                                    <div class="input-group-btn search-panel">
                                        <select class="form-control select2-no-search">
                                            <option label="All categories">
                                            </option>
                                            <option value="IT Projects">
                                                IT Projects
                                            </option>
                                            <option value="Business Case">
                                                Business Case
                                            </option>
                                            <option value="Microsoft Project">
                                                Microsoft Project
                                            </option>
                                            <option value="Risk Management">
                                                Risk Management
                                            </option>
                                            <option value="Team Building">
                                                Team Building
                                            </option>
                                        </select>
                                    </div>
                                    <input type="search" class="form-control" placeholder="Search for anything...">
                                    <button class="btn search-btn"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-search">
                                            <circle cx="11" cy="11" r="8"></circle>
                                            <line x1="21" y1="21" x2="16.65" y2="16.65">
                                            </line>
                                        </svg></button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="dropdown d-md-flex">
                        <a class="nav-link icon full-screen-link" href="#">
                            <i class="fe fe-maximize fullscreen-button fullscreen header-icons"></i>
                            <i class="fe fe-minimize fullscreen-button exit-fullscreen header-icons"></i>
                        </a>
                    </div>

                    <div class="language-switcher">
                        @foreach(LaravelLocalization::getSupportedLocales() as $localeCode => $properties)
                        @if($localeCode !== App::getLocale())
                        <a href="{{ LaravelLocalization::getLocalizedURL($localeCode, null, [], true) }}">
                            {{ $properties['native'] }}
                        </a>
                        @endif
                        @endforeach
                    </div>

                    <div class="dropdown main-profile-menu">
                        <a class="d-flex" href="#">
                            <span class="main-img-user"><img alt="avatar" src="{{ url('panel/assets/img/users/1.jpg') }}"></span>
                        </a>
                        <div class="dropdown-menu">
                            <div class="header-navheading">
                                <h6 class="main-notification-title">{{ auth()->user()->full_name }}</h6>
                                <p class="main-notification-text">{{ auth()->user()->role->display_name }}</p>
                            </div>
                            <a class="dropdown-item" href="{{ route('profile') }}">
                                <i class="fe fe-edit"></i> Edit Profile
                            </a>
                            <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault();
                           document.getElementById('logout-form').submit();">
                                <i class="fe fe-power"></i> Sign Out
                            </a>

                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                @csrf
                            </form>
                        </div>
                    </div>
                    <button class="navbar-toggler navresponsive-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent-4" aria-controls="navbarSupportedContent-4" aria-expanded="false" aria-label="Toggle navigation">
                        <i class="fe fe-more-vertical header-icons navbar-toggler-icon"></i>
                    </button><!-- Navresponsive closed -->
                </div>
            </div>
        </div>
        <!-- End Main Header-->
        <!-- Mobile-header -->
        <div class="mobile-main-header">
            <div class="mb-1 navbar navbar-expand-lg  nav nav-item  navbar-nav-right responsive-navbar navbar-dark  ">
                <div class="collapse navbar-collapse" id="navbarSupportedContent-4">
                    <div class="d-flex order-lg-2 ml-auto">
                        <div class="dropdown header-search">
                            <a class="nav-link icon header-search">
                                <i class="fe fe-search header-icons"></i>
                            </a>
                            <div class="dropdown-menu">
                                <div class="main-form-search p-2">
                                    <div class="input-group">
                                        <div class="input-group-btn search-panel">
                                            <select class="form-control select2-no-search">
                                                <option label="All categories">
                                                </option>
                                                <option value="IT Projects">
                                                    Tanks
                                                </option>
                                                <option value="Business Case">
                                                    Transactions
                                                </option>
                                                <option value="Microsoft Project">
                                                    Shipments
                                                </option>
                                                <option value="Risk Management">
                                                    Clients
                                                </option>
                                                <option value="Team Building">
                                                    Admins
                                                </option>
                                            </select>
                                        </div>
                                        <input type="search" class="form-control" placeholder="Search for anything...">
                                        <button class="btn search-btn"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-search">
                                                <circle cx="11" cy="11" r="8"></circle>
                                                <line x1="21" y1="21" x2="16.65" y2="16.65">
                                                </line>
                                            </svg></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="dropdown ">
                            <a class="nav-link icon full-screen-link">
                                <i class="fe fe-maximize fullscreen-button fullscreen header-icons"></i>
                                <i class="fe fe-minimize fullscreen-button exit-fullscreen header-icons"></i>
                            </a>
                        </div>
                        <div class="dropdown main-profile-menu">
                            <a class="d-flex" href="#">
                                <span class="main-img-user"><img alt="avatar" src="{{ url('panel/assets/img/users/1.jpg') }}"></span>
                            </a>
                            <div class="dropdown-menu">
                                <div class="header-navheading">
                                    <h6 class="main-notification-title">{{ auth()->user()->full_name }}</h6>
                                    <p class="main-notification-text">{{ auth()->user()->role->display_name }}</p>
                                </div>
                                <a class="dropdown-item" href="{{ route('profile') }}">
                                    <i class="fe fe-edit"></i> Edit Profile
                                </a>
                                <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault();
                           document.getElementById('logout-form').submit();">
                                    <i class="fe fe-power"></i> Sign Out
                                </a>

                                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                    @csrf
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Mobile-header closed -->
        <!-- Main Content-->
        @yield('content')
        <!-- End Main Content-->

        <!-- Main Footer-->
        <div class="main-footer text-center">
            <div class="container">
                <div class="row row-sm">
                    <div class="col-md-12">
                        <span>Copyright © {{ date('Y') }} <a href="#">{{ env('APP_NAME') }}</a>. Designed
                            and developed by <a href="https://www.ecoc.com/">Ecoc Develop</a> All rights
                            reserved.</span>
                    </div>
                </div>
            </div>
        </div>
        <!--End Footer-->
    </div>
    <!-- End Page -->

    <!-- Back-to-top -->
    <a href="#top" id="back-to-top"><i class="fe fe-arrow-up"></i></a>

    <!-- Jquery js-->
    <script src="{{ url('panel/assets/plugins/jquery/jquery.min.js') }}"></script>

    <!-- Bootstrap js-->
    <script src="{{ url('panel/assets/plugins/bootstrap/js/popper.min.js') }}"></script>



    <!--- Style css -->
    @if (App::getLocale() == 'en')
    <script src="{{ url('panel/assets/plugins/bootstrap/js/bootstrap.min.js') }}"></script>
    <script src="{{ url('panel/assets/plugins/perfect-scrollbar/perfect-scrollbar.min.js') }}"></script>
    <script src="{{ url('panel/assets/plugins/sidemenu/sidemenu.js') }}"></script>
    <script src="{{ url('panel/assets/plugins/sidebar/sidebar.js') }}"></script>
    @else
    <script src="{{ asset('panel/assets/plugins/bootstrap/js/bootstrap-rtl.js') }}"></script>
    <script src="{{ asset('panel/assets/plugins/perfect-scrollbar/perfect-scrollbar.min-rtl.js') }}"></script>
    <script src="{{ asset('panel/assets/plugins/sidemenu/sidemenu-rtl.js') }}"></script>
    <script src="{{ asset('panel/assets/plugins/sidebar/sidebar-rtl.js') }}"></script>
    <!-- Switcher js -->
    <script src="{{ asset('panel/assets/switcher/js/switcher-rtl.js') }}"></script>
    @endif

    <!-- Select2 js-->
    <script src="{{ url('panel/assets/plugins/select2/js/select2.min.js') }}"></script>

    <!-- Perfect-scrollbar js -->


    <!-- Sidemenu js -->


    <!-- Sidebar js -->


    <!-- Internal Chart.Bundle js-->
    <script src="{{ url('panel/assets/plugins/chart.js/Chart.bundle.min.js') }}"></script>

    <!-- Peity js-->
    <script src="{{ url('panel/assets/plugins/peity/jquery.peity.min.js') }}"></script>

    <!-- Internal Morris js -->
    <script src="{{ url('panel/assets/plugins/raphael/raphael.min.js') }}"></script>
    <script src="{{ url('panel/assets/plugins/morris.js/morris.min.js') }}"></script>

    <!-- Circle Progress js-->
    <script src="{{ url('panel/assets/js/circle-progress.min.js') }}"></script>
    <script src="{{ url('panel/assets/js/chart-circle.js') }}"></script>

    <!-- Internal Dashboard js-->
    <script src="{{ url('panel/assets/js/index.js') }}"></script>

    <!-- Sticky js -->
    <script src="{{ url('panel/assets/js/sticky.js') }}"></script>

    <!-- Custom js -->
    <script src="{{ url('panel/assets/js/custom.js') }}"></script>

    <!-- Switcher js -->
    <script src="{{ url('panel/assets/switcher/js/switcher.js') }}"></script>
    <!-- REQUIRED SCRIPTS -->

    <!-- DataTables -->
    <script src="{{ URL::to('/') . '/plugins/datatables/jquery.dataTables.min.js' }}"></script>
    <script src="{{ URL::to('/') . '/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js' }}"></script>
    <script src="{{ URL::to('/') . '/plugins/datatables-responsive/js/dataTables.responsive.min.js' }}"></script>
    <script src="{{ URL::to('/') . '/plugins/datatables-responsive/js/responsive.bootstrap4.min.js' }}"></script>

    <script>
        $(function() {
            $("#table").DataTable({
                "responsive": true
                , "autoWidth": false
            , });
        });
        $(function() {
            $('[data-toggle="tooltip"]').tooltip()
        })

        $("textarea").each(function() {
            CKEDITOR.replace(this);
        });

    </script>
    @foreach ($errors->all() as $error)
    <script>
        toastr.error('{{ $error }}')

    </script>
    <script>
        $(document).ready(function() {
            $('.js-example-basic-single').select2();
        });

    </script>
    @endforeach
    @yield('js')
</body>

</html>

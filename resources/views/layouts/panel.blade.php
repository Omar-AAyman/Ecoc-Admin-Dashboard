<!DOCTYPE html>
<html lang="{{ App::getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1, shrink-to-fit=no" name="viewport">
    <meta name="description" content="Admin Panel laravel Dashboard">
    <meta name="author" content="">
    <meta name="keywords" content="">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Favicon -->
    <link rel="icon" href="{{ url('panel/assets/img/brand/logo-responsive.png') }}" type="image/x-icon" />

    <!-- Title -->
    <title>{{ env('APP_NAME') }} @if (trim($__env->yieldContent('title'))) | @yield('title') @endif</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Icons CSS -->
    <link href="{{ url('panel/assets/plugins/web-fonts/icons.css') }}" rel="stylesheet" />
    <link href="{{ url('panel/assets/plugins/web-fonts/font-awesome/font-awesome.min.css') }}" rel="stylesheet">
    <link href="{{ url('panel/assets/plugins/web-fonts/plugin.css') }}" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/chart.js" rel="stylesheet">

    <!-- Style CSS -->
    @if (App::getLocale() == 'en')
        <link href="{{ url('panel/assets/css/style/style.css') }}" rel="stylesheet">
        <link href="{{ url('panel/assets/css/skins.css') }}" rel="stylesheet">
        <link href="{{ url('panel/assets/css/dark-style.css') }}" rel="stylesheet">
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

    <!-- Select2 CSS -->
    <link href="{{ url('panel/assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet">

    <!-- Multiple Select CSS -->
    <link rel="stylesheet" href="{{ url('panel/assets/plugins/multipleselect/multiple-select.css') }}">

    <!-- Switcher CSS -->
    <link href="{{ url('panel/assets/switcher/demo.css') }}" rel="stylesheet">

    <!-- CKEditor -->
    <script src="https://cdn.ckeditor.com/4.15.0/full/ckeditor.js"></script>

    <!-- SweetAlert -->
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

    <style>
        .language-switcher {
            display: flex;
            gap: 10px;
        }
        .language-switcher a {
            text-decoration: none;
            color: #000;
            font-weight: bold;
        }
        .search-results-dropdown {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: #fff;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            max-height: 300px;
            overflow-y: auto;
            z-index: 1000;
            display: none;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
        }
        .search-results-dropdown .list-group-item {
            cursor: pointer;
            padding: 0.75rem 1rem;
            border: none;
            border-bottom: 1px solid #e9ecef;
        }
        .search-results-dropdown .list-group-item:hover {
            background-color: #f8f9fa;
        }
        .search-results-dropdown .list-group-item:last-child {
            border-bottom: none;
        }
    </style>
    @yield('css')
</head>
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
                <a class="main-logo" href="{{ url('https://ecoc-site.com/') }}" target="_blank">
                    <img src="{{ url('panel/assets/img/brand/logo.png') }}" class="header-brand-img desktop-logo" alt="logo" style="max-height: 52px">
                    <img src="{{ url('panel/assets/img/brand/logo-responsive.png') }}" class="header-brand-img icon-logo" alt="logo" style="max-height: 52px">
                    <img src="{{ url('panel/assets/img/brand/logo-responsive.png') }}" class="header-brand-img desktop-logo theme-logo" alt="logo" style="max-height: 52px">
                    <img src="{{ url('panel/assets/img/brand/logo.png') }}" class="header-brand-img icon-logo theme-logo" alt="logo" style="max-height: 52px">
                </a>
            </div>
            <div class="main-sidebar-body">
                <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">
                    <li class="nav-header"><span class="nav-label">ECOC Tank Rentals</span></li>
                    <li class="nav-item @if(Route::currentRouteName() == 'dashboard') active @endif">
                        <a class="nav-link" href="{{ route('dashboard') }}">
                            <span class="shape1"></span><span class="shape2"></span>
                            <i class="ti-home sidemenu-icon"></i>
                            <span class="sidemenu-label" style="font-size: 0.9rem;">Dashboard</span>
                        </a>
                    </li>
                    @if (auth()->user() && auth()->user()->isSuperAdmin())
                    <li class="nav-item @if(Route::currentRouteName() === 'tanks.settings') active @endif">
                        <a class="nav-link" href="{{ route('tanks.settings') }}">
                            <span class="shape1"></span><span class="shape2"></span>
                            <i class="ti-settings sidemenu-icon"></i>
                            <span class="sidemenu-label" style="font-size: 0.9rem;">Tank Settings</span>
                        </a>
                    </li>
                    @endif
                    <li class="nav-item @if(Route::currentRouteName() === 'transactions.index') active @endif">
                        <a class="nav-link" href="{{ route('transactions.index') }}">
                            <span class="shape1"></span><span class="shape2"></span>
                            <i class="ti-exchange-vertical sidemenu-icon"></i>
                            <span class="sidemenu-label" style="font-size: 0.9rem;">Transactions</span>
                        </a>
                    </li>

                    @if (auth()->user() && auth()->user()->isSuperAdmin())
                        <li class="nav-item @if(Route::currentRouteName() === 'users.index') active @endif">
                            <a class="nav-link" href="{{ route('users.index') }}">
                                <span class="shape1"></span><span class="shape2"></span>
                                <i class="ti-user sidemenu-icon"></i>
                                <span class="sidemenu-label" style="font-size: 0.9rem;">Admins</span>
                            </a>
                        </li>
                        <li class="nav-item @if(Route::currentRouteName() === 'clients.index') active @endif">
                            <a class="nav-link" href="{{ route('clients.index') }}">
                                <span class="shape1"></span><span class="shape2"></span>
                                <i class="ti-id-badge sidemenu-icon"></i>
                                <span class="sidemenu-label" style="font-size: 0.9rem;">Clients</span>
                            </a>
                        </li>
                        <li class="nav-item @if(Route::currentRouteName() === 'products.index') active @endif">
                            <a class="nav-link" href="{{ route('products.index') }}">
                                <span class="shape1"></span><span class="shape2"></span>
                                <i class="ti-package sidemenu-icon"></i>
                                <span class="sidemenu-label" style="font-size: 0.9rem;">Products</span>
                            </a>
                        </li>
                        <li class="nav-item @if(Route::currentRouteName() === 'vessels.index') active @endif">
                            <a class="nav-link" href="{{ route('vessels.index') }}">
                                <span class="shape1"></span><span class="shape2"></span>
                                <i class="fas fa-ship sidemenu-icon"></i>
                                <span class="sidemenu-label" style="font-size: 0.9rem;">Vessels</span>
                            </a>
                        </li>
                        <li class="nav-item @if(Route::currentRouteName() === 'trucks.index') active @endif">
                            <a class="nav-link" href="{{ route('trucks.index') }}">
                                <span class="shape1"></span><span class="shape2"></span>
                                <i class="fas fa-truck sidemenu-icon"></i>
                                <span class="sidemenu-label" style="font-size: 0.9rem;">Trucks</span>
                            </a>
                        </li>
                        <li class="nav-item @if(Route::currentRouteName() === 'trailers.index') active @endif">
                            <a class="nav-link" href="{{ route('trailers.index') }}">
                                <span class="shape1"></span><span class="shape2"></span>
                                <i class="fas fa-trailer sidemenu-icon"></i>
                                <span class="sidemenu-label" style="font-size: 0.9rem;">Trailers</span>
                            </a>
                        </li>
                        <li class="nav-item @if(Route::currentRouteName() === 'drivers.index') active @endif">
                            <a class="nav-link" href="{{ route('drivers.index') }}">
                                <span class="shape1"></span><span class="shape2"></span>
                                <i class="fas fa-user-tie sidemenu-icon"></i>
                                <span class="sidemenu-label" style="font-size: 0.9rem;">Drivers</span>
                            </a>
                        </li>
                        <li class="nav-item @if(in_array(Route::currentRouteName(), ['activity-logs.index', 'activity-logs.show'])) active @endif">
                            <a class="nav-link" href="{{ route('activity-logs.index') }}">
                                <i class="ti-receipt sidemenu-icon"></i>
                                <span class="sidemenu-label">Activity Logs</span>
                            </a>
                        </li>
                    @endif

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
                        <a href="{{ route('dashboard') }}"><img src="{{ url('panel/assets/img/brand/logo.png') }}" class="mobile-logo d-lg-none header-brand-img text-left float-left" alt="ECOC Logo" style="background-color: #000b43; border-radius: 5px; max-height: 52px"></a>
                        <a href="{{ route('dashboard') }}"><img src="{{ url('panel/assets/img/brand/logo.png') }}" class="mobile-logo-dark" alt="logo"></a>
                    </div>
                    @if (auth()->user() && auth()->user()->isSuperAdmin() )
                    <div class="input-group position-relative">
                        <div class="input-group-btn search-panel">
                            <select class="form-control select2-no-search" id="search-category">
                                <option value="all" selected>All Categories</option>
                                <option value="tanks">Tanks</option>
                                <option value="transactions">Transactions</option>
                                <option value="clients">Clients</option>
                                <option value="users">Admins</option>
                                <option value="products">Products</option>
                                <option value="vessels">Vessels</option>
                            </select>
                        </div>
                        <input type="search" class="form-control" id="global-search" placeholder="Search for anything..." autocomplete="off">
                        <button class="btn search-btn" id="search-submit"><i class="fe fe-search"></i></button>
                        <div class="search-results-dropdown list-group"></div>
                    </div>
                    @endif
                </div>
                <div class="main-header-right">
                    @if (auth()->user() && auth()->user()->isSuperAdmin() )
                    <div class="dropdown header-search d-lg-none">

                        <a class="nav-link icon header-search" data-bs-toggle="dropdown">
                            <i class="fe fe-search header-icons"></i>
                        </a>
                        <div class="dropdown-menu">
                            <div class="main-form-search p-2">
                                <div class="input-group position-relative">
                                    <div class="input-group-btn search-panel">
                                        <select class="form-control select2-no-search" id="mobile-search-category">
                                            <option value="all" selected>All Categories</option>
                                            <option value="tanks">Tanks</option>
                                            <option value="transactions">Transactions</option>
                                            <option value="clients">Clients</option>
                                            <option value="users">Admins</option>
                                            <option value="products">Products</option>
                                            <option value="vessels">Vessels</option>
                                        </select>
                                    </div>
                                    <input type="search" class="form-control" id="mobile-global-search" placeholder="Search for anything..." autocomplete="off">
                                    <button class="btn search-btn" id="mobile-search-submit">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-search">
                                            <circle cx="11" cy="11" r="8"></circle>
                                            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                                        </svg>
                                    </button>
                                    <div class="search-results-dropdown list-group"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <div class="dropdown d-md-flex">
                        <a class="nav-link icon full-screen-link" href="#">
                            <i class="fe fe-maximize fullscreen-button fullscreen header-icons"></i>
                            <i class="fe fe-minimize fullscreen-button exit-fullscreen header-icons"></i>
                        </a>
                    </div>
                    <div class="dropdown main-profile-menu">
                        <a class="d-flex" href="#">
                            <span class="main-img-user"><img alt="avatar" src="{{ url(auth()->user()->image_url) }}"></span>
                        </a>
                        <div class="dropdown-menu">
                            <div class="header-navheading">
                                <h6 class="main-notification-title">{{ auth()->user()->full_name }}</h6>
                                <p class="main-notification-text">{{ auth()->user()->role->display_name }}</p>
                            </div>
                            <a class="dropdown-item" href="{{ route('profile') }}">
                                <i class="fe fe-edit"></i> Edit Profile
                            </a>
                            <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="fe fe-power"></i> Sign Out
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                @csrf
                            </form>
                        </div>
                    </div>
                    <button class="navbar-toggler navresponsive-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent-4" aria-controls="navbarSupportedContent-4" aria-expanded="false" aria-label="Toggle navigation">
                        <i class="fe fe-more-vertical header-icons navbar-toggler-icon"></i>
                    </button>
                </div>
            </div>
        </div>
        <!-- End Main Header-->

        <!-- Mobile-header -->
        <div class="mobile-main-header">
            <div class="mb-1 navbar navbar-expand-lg nav nav-item navbar-nav-right responsive-navbar navbar-dark">
                <div class="collapse navbar-collapse" id="navbarSupportedContent-4">
                    <div class="d-flex order-lg-2 ml-auto">
                        @if (auth()->user() && auth()->user()->isSuperAdmin() )
                        <div class="dropdown header-search">
                            <a class="nav-link icon header-search" data-bs-toggle="dropdown">
                                <i class="fe fe-search header-icons"></i>
                            </a>
                            <div class="dropdown-menu">
                                <div class="main-form-search p-2">
                                    <div class="input-group position-relative">
                                        <div class="input-group-btn search-panel">
                                            <select class="form-control select2-no-search" id="mobile-search-category">
                                                <option value="all" selected>All Categories</option>
                                                <option value="tanks">Tanks</option>
                                                <option value="transactions">Transactions</option>
                                                <option value="clients">Clients</option>
                                                <option value="users">Admins</option>
                                                <option value="products">Products</option>
                                                <option value="vessels">Vessels</option>
                                            </select>
                                        </div>
                                        <input type="search" class="form-control" id="mobile-global-search" placeholder="Search for anything..." autocomplete="off">
                                        <button class="btn search-btn" id="mobile-search-submit">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-search">
                                                <circle cx="11" cy="11" r="8"></circle>
                                                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                                            </svg>
                                        </button>
                                        <div class="search-results-dropdown list-group"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif

                        {{-- <div class="dropdown">
                            <a class="nav-link icon full-screen-link">
                                <i class="fe fe-maximize fullscreen-button fullscreen header-icons"></i>
                                <i class="fe fe-minimize fullscreen-button exit-fullscreen header-icons"></i>
                            </a>
                        </div> --}}
                        <div class="dropdown main-profile-menu">
                            <a class="d-flex" href="#">
                                <span class="main-img-user"><img alt="avatar" src="{{ url(auth()->user()->image_url) }}"></span>
                            </a>
                            <div class="dropdown-menu">
                                <div class="header-navheading">
                                    <h6 class="main-notification-title">{{ auth()->user()->full_name }}</h6>
                                    <p class="main-notification-text">{{ auth()->user()->role->display_name }}</p>
                                </div>
                                <a class="dropdown-item" href="{{ route('profile') }}">
                                    <i class="fe fe-edit"></i> Edit Profile
                                </a>
                                <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
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
                        <span>Copyright © {{ date('Y') }} <a href="#">{{ env('APP_NAME') }}</a>. All rights reserved. Designed and developed by <a href="https://omar-aayman.github.io/Portfolio/">Omar Ayman</a>.</span>
                    </div>
                </div>
            </div>
        </div>
        <!--End Footer-->
    </div>
    <!-- End Page -->

    <!-- Back-to-top -->
    <a href="#top" id="back-to-top"><i class="fe fe-arrow-up"></i></a>


    <!-- Jquery JS -->
    <script src="{{ url('panel/assets/plugins/jquery/jquery.min.js') }}"></script>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Perfect Scrollbar -->
    <script src="{{ url('panel/assets/plugins/perfect-scrollbar/perfect-scrollbar.min.js') }}"></script>


    <!-- Sidemenu JS -->
    @if (App::getLocale() == 'en')
        <script src="{{ url('panel/assets/plugins/sidemenu/sidemenu.js') }}"></script>
        <script src="{{ url('panel/assets/plugins/sidebar/sidebar.js') }}"></script>
    @else
        <script src="{{ asset('panel/assets/plugins/perfect-scrollbar/perfect-scrollbar.min-rtl.js') }}"></script>
        <script src="{{ asset('panel/assets/plugins/sidemenu/sidemenu-rtl.js') }}"></script>
        <script src="{{ asset('panel/assets/plugins/sidebar/sidebar-rtl.js') }}"></script>
        <script src="{{ asset('panel/assets/switcher/js/switcher-rtl.js') }}" defer></script>
    @endif

    <!-- Select2 JS -->
    <script src="{{ url('panel/assets/plugins/select2/js/select2.min.js') }}"></script>

    <!-- Chart JS -->
    <script src="{{ url('panel/assets/plugins/chart.js/Chart.bundle.min.js') }}"></script>

    <!-- Peity JS -->
    <script src="{{ url('panel/assets/plugins/peity/jquery.peity.min.js') }}"></script>

    <!-- Morris JS -->
    <script src="{{ url('panel/assets/plugins/raphael/raphael.min.js') }}"></script>
    <script src="{{ url('panel/assets/plugins/morris.js/morris.min.js') }}"></script>

    <!-- Circle Progress JS -->
    <script src="{{ url('panel/assets/js/circle-progress.min.js') }}"></script>
    <script src="{{ url('panel/assets/js/chart-circle.js') }}"></script>

    <!-- Internal Dashboard JS -->
    <script src="{{ url('panel/assets/js/index.js') }}"></script>

    <!-- Sticky JS -->
    <script src="{{ url('panel/assets/js/sticky.js') }}"></script>

    <!-- Custom JS -->
    <script src="{{ url('panel/assets/js/custom.js') }}"></script>

    <!-- Switcher JS -->
    <script src="{{ url('panel/assets/switcher/js/switcher.js') }}"></script>

    <!-- DataTables JS -->
    <script src="{{ url('plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ url('plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ url('plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ url('plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize tooltips
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.forEach(function(tooltipTriggerEl) {
                new bootstrap.Tooltip(tooltipTriggerEl);
            });

            // Initialize CKEditor for textareas
            const textareas = document.querySelectorAll('textarea');
            textareas.forEach(function(textarea) {
                CKEDITOR.replace(textarea);
            });

            // Initialize Select2
            const selectElements = document.querySelectorAll('.js-example-basic-single');
            if (typeof $.fn.select2 !== 'undefined') {
                $('.js-example-basic-single').select2();
            }

            // Debounce function to limit API calls
            function debounce(func, wait) {
                let timeout;
                return function executedFunction(...args) {
                    const later = () => {
                        clearTimeout(timeout);
                        func(...args);
                    };
                    clearTimeout(timeout);
                    timeout = setTimeout(later, wait);
                };
            }

            // Global Search Functionality
            function performSearch(input, categorySelect, resultsContainer) {
                const query = input.value.trim();
                const category = categorySelect.value;
                if (query.length < 2) {
                    resultsContainer.innerHTML = '';
                    resultsContainer.style.display = 'none';
                    return;
                }

                const baseUrl = '{{ url("/api/search") }}';
                const url = new URL(baseUrl);
                url.searchParams.append('query', query);
                url.searchParams.append('category', category);
                console.log('Search URL:', url.toString()); // Debug URL

                fetch(url, {
                    method: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(`Network response was not ok: ${response.status}`);
                        }
                        return response.json();
                    })
                    .then(data => {
                        resultsContainer.innerHTML = '';
                        if (!data.results || data.results.length === 0) {
                            resultsContainer.innerHTML = '<div class="list-group-item">No results found</div>';
                            resultsContainer.style.display = 'block';
                            return;
                        }
                        data.results.forEach(result => {
                            const item = document.createElement('div');
                            item.className = 'list-group-item';
                            item.textContent = result.text;
                            item.dataset.url = result.url;
                            item.style.cursor = 'pointer';
                            item.addEventListener('click', () => {
                                window.location.href = result.url;
                            });
                            resultsContainer.appendChild(item);
                        });
                        resultsContainer.style.display = 'block';
                    })
                    .catch(error => {
                        console.error('Error fetching search results:', error);
                        resultsContainer.innerHTML = '<div class="list-group-item text-danger">Error fetching results: ' + error.message + '</div>';
                        resultsContainer.style.display = 'block';
                    });
            }

            // Desktop search
            const globalSearch = document.getElementById('global-search');
            const searchCategory = document.getElementById('search-category');
            const desktopResults = document.querySelector('.main-header-center .search-results-dropdown');

            if (globalSearch && searchCategory && desktopResults) {
                globalSearch.addEventListener('input', debounce(function() {
                    performSearch(globalSearch, searchCategory, desktopResults);
                }, 300));
            }

            // Mobile search
            const mobileGlobalSearch = document.getElementById('mobile-global-search');
            const mobileSearchCategory = document.getElementById('mobile-search-category');
            const mobileResults = document.querySelector('.mobile-main-header .search-results-dropdown');

            if (mobileGlobalSearch && mobileSearchCategory && mobileResults) {
                mobileGlobalSearch.addEventListener('input', debounce(function() {
                    performSearch(mobileGlobalSearch, mobileSearchCategory, mobileResults);
                }, 300));
            }

            // Search button redirect
            const searchButtons = document.querySelectorAll('#search-submit, #mobile-search-submit');
            searchButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    const input = this.parentElement.querySelector('input');
                    const select = this.parentElement.querySelector('select');
                    const query = input.value.trim();
                    const category = select.value;
                    if (query) {
                        window.location.href = '{{ url("/search") }}?query=' + encodeURIComponent(query) + '&category=' + encodeURIComponent(category);
                    }
                });
            });

            // Hide dropdown when clicking outside
            document.addEventListener('click', function(e) {
                if (!e.target.closest('.input-group')) {
                    document.querySelectorAll('.search-results-dropdown').forEach(dropdown => {
                        dropdown.style.display = 'none';
                    });
                }
            });

            // Inject CSRF token meta tag if it doesn't exist
            let csrfTokenMeta = document.querySelector('meta[name="csrf-token"]');
            if (!csrfTokenMeta) {
                csrfTokenMeta = document.createElement('meta');
                csrfTokenMeta.name = 'csrf-token';
                csrfTokenMeta.content = '{{ csrf_token() }}';
                document.head.appendChild(csrfTokenMeta);
                console.log('CSRF token meta tag created');
            }
        });

        // Display error messages
        @foreach ($errors->all() as $error)
            toastr.error('{{ $error }}');
        @endforeach
    </script>

    @yield('js')
</body>
</html>
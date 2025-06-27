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
    <link href="{{url('panel/assets/plugins/bootstrap/css/bootstrap.min.css')}}" rel="stylesheet"/>

    <!-- Icons css-->
    <link href="{{url('panel/assets/plugins/web-fonts/icons.css')}}" rel="stylesheet"/>
    <link href="{{url('panel/assets/plugins/web-fonts/font-awesome/font-awesome.min.css')}}" rel="stylesheet">
    <link href="{{url('panel/assets/plugins/web-fonts/plugin.css')}}" rel="stylesheet"/>

    <!-- Style css-->
    <link href="{{url('panel/assets/css/style/style.css')}}" rel="stylesheet">
    <link href="{{url('panel/assets/css/skins.css')}}" rel="stylesheet">
    <link href="{{url('panel/assets/css/dark-style.css')}}" rel="stylesheet">
    <link href="{{url('panel/assets/css/colors/default.css')}}" rel="stylesheet">


    <!-- Color css-->
    <link id="theme" rel="stylesheet" type="text/css" media="all" href="{{url('panel/assets/css/colors/color.css')}}">

    <!-- Switcher css-->
    <link href="{{url('panel/assets/switcher/css/switcher.css')}}" rel="stylesheet">
    <link href="{{url('panel/assets/switcher/demo.css')}}" rel="stylesheet">



</head>

<body class="main-body leftmenu">


<!-- End Switcher -->

<!-- Loader -->
<div id="global-loader">
    <img src="{{url('panel/assets/img/loader.svg')}}" class="loader-img" alt="Loader">
</div>
<!-- End Loader -->


@yield('content')

<!-- Jquery js-->
<script src="{{url('panel/assets/plugins/jquery/jquery.min.js')}}"></script>

<!-- Bootstrap js-->
<script src="{{url('panel/assets/plugins/bootstrap/js/popper.min.js')}}"></script>
<script src="{{url('panel/assets/plugins/bootstrap/js/bootstrap.min.js')}}"></script>

<!-- Select2 js-->
<script src="{{url('panel/assets/plugins/select2/js/select2.min.js')}}"></script>


<!-- Custom js -->
<script src="{{url('panel/assets/js/custom.js')}}"></script>

<!-- Switcher js -->
<script src="{{url('panel/assets/switcher/js/switcher.js')}}"></script>




</body>

</html>

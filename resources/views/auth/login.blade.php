@extends('layouts.auth')

@section('title', 'Login')

@section('content')
<!-- Page -->
<div class="page main-signin-wrapper">
    <!-- Row -->
    <div class="row signpages text-center">
        <div class="col-md-12">
            <div class="card shadow-sm">
                <div class="row row-sm">
                    <div class="col-lg-6 col-xl-5 d-none d-lg-block text-center bg-primary details">
                        <div class="mt-5 pt-4 p-2 pos-absolute">
                            <img src="{{ url('panel/assets/img/brand/logo.png') }}" class="ht-100 mb-0" alt="ECOC Logo">
                            <h5 class="mt-4 text-white">Login To Your Account</h5>
                            <span class="tx-white-6 tx-13 mb-5 mt-xl-0">Manage tanks, track transactions, and oversee clients with ease in the ECOC Admin Dashboard.</span>
                        </div>
                    </div>
                    <div class="col-lg-6 col-xl-7 col-xs-12 col-sm-12 login_form">
                        <div class="container-fluid">
                            <div class="row row-sm">
                                <div class="card-body my-4">
                                    <img src="{{ url('panel/assets/img/brand/logo.png') }}" class="d-lg-none header-brand-img text-left float-left mb-4" alt="ECOC Logo" style="background-color: #000b43; padding: 10px; border-radius: 5px;">
                                    <div class="clearfix"></div>
                                    <form method="POST" action="{{ route('login') }}">
                                        @csrf
                                        <h5 class="text-left mb-3">Sign in to ECOC Admin Dashboard</h5>
                                        @if ($errors->any())
                                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                            <ul class="mb-0">
                                                @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                        @endif
                                        <p class="mb-4 text-muted tx-13 ml-0 text-left"></p>
                                        <div class="form-group text-left">
                                            <label for="email" class="form-label">Email</label>
                                            <input class="form-control @error('email') is-invalid @enderror" id="email" name="email" placeholder="Enter your email" type="email" value="{{ old('email') }}" required autofocus>
                                            @error('email')
                                            <span class="invalid-feedback" role="alert">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div class="form-group text-left">
                                            <label for="password" class="form-label">Password</label>
                                            <input class="form-control @error('password') is-invalid @enderror" id="password" name="password" placeholder="Enter your password" type="password" required>
                                            @error('password')
                                            <span class="invalid-feedback" role="alert">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div class="form-group form-check text-left">
                                            <input type="checkbox" name="remember" id="remember" class="form-check-input">
                                            <label for="remember" class="form-check-label">Remember Me</label>
                                        </div>
                                        <button class="btn ripple btn-primary btn-block">Sign In</button>
                                    </form>
                                    {{-- <div class="text-left mt-4">
                                        <a href="{{ route('password.request') }}">Forgot password?</a>
                                </div> --}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- End Row -->
</div>
<!-- End Page -->
@endsection

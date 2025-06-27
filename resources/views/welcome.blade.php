<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Rental Dashboard') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <div class="container text-center mt-5">
        <h1>Welcome to {{ config('app.name', 'Ecoc Dashboard') }}</h1>
        @if (auth()->check())
            <p>Hello, {{ auth()->user()->first_name }}! <a href="{{ route('dashboard') }}">Go to Dashboard</a></p>
        @else
            <p><a href="{{ route('login') }}">Login</a> to get started.</p>
        @endif
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
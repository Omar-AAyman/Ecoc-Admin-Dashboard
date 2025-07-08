@extends('layouts.panel')

@section('title', 'Create Admin')

@section('css')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');

    body {
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        background-color: #f1f5f9;
        color: #1f2937;
    }

    .container-fluid {
        max-width: 1440px;
        padding: 0 1.5rem;
    }

    .hero-header {
        padding: 2.5rem 0;
        border-radius: 16px;
        margin-bottom: 2rem;
        box-shadow: 0 6px 20px rgba(0, 11, 67, 0.2);
        background-color: #ffffff;
    }

    .hero-header h2 {
        font-size: 2.25rem;
        font-weight: 700;
        margin-bottom: 0;
    }

    .card {
        border: none;
        border-radius: 16px;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
    }

    .form-control,
    .form-select {
        border-radius: 8px;
        border: 1px solid #d1d5db;
        transition: border-color 0.2s ease, box-shadow 0.2s ease;
        height: 38px;
        font-size: 0.875rem;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: #2563eb;
        box-shadow: 0 0 0 0.2rem rgba(37, 99, 235, 0.2);
        outline: none;
    }

    .form-label {
        font-size: 0.875rem;
        font-weight: 500;
        color: #1f2937;
        margin-bottom: 0.5rem;
    }

    .btn {
        border-radius: 8px;
        font-weight: 500;
        transition: all 0.2s ease;
    }

    .btn-primary {
        background-color: #000b43;
        border-color: #000b43;
        color: #ffffff;
        padding: 0.5rem 1rem;
    }

    .btn-primary:hover {
        background-color: #1d4ed8;
        border-color: #1d4ed8;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
    }

    .text-danger {
        font-size: 0.75rem;
        margin-top: 0.25rem;
    }

    @media (max-width: 768px) {
        .hero-header h2 {
            font-size: 1.75rem;
        }
        .form-control,
        .form-select {
            font-size: 0.75rem;
            height: 34px;
        }
        .form-label {
            font-size: 0.75rem;
        }
        .btn {
            padding: 0.4rem 0.8rem;
            font-size: 0.875rem;
        }
    }
</style>
@endsection

@section('content')
<div class="main-content side-content my-2 pt-0">
    <div class="container-fluid px-4 py-4">
        <div class="inner-body">
            <!-- Page Header -->
            <div class="hero-header">
                <div class="container">
                    <h2 class="mb-0">
                        <i class="fas fa-user-plus me-2"></i>Create Admin
                    </h2>
                </div>
            </div>

            <!-- Alert Messages -->
            @include('components.alerts')

            <!-- Form -->
            <div class="card">
                <div class="card-body p-4">
                    <form action="{{ route('users.store') }}" method="POST">
                        @csrf
                        <div class="row mb-3">
                            <div class="col-12 col-md-6 mb-3 mb-md-0">
                                <label for="first_name" class="form-label">First Name</label>
                                <input type="text" name="first_name" id="first_name" class="form-control" value="{{ old('first_name') }}" required>
                                @error('first_name')
                                <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12 col-md-6">
                                <label for="last_name" class="form-label">Last Name</label>
                                <input type="text" name="last_name" id="last_name" class="form-control" value="{{ old('last_name') }}" required>
                                @error('last_name')
                                <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-12">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" name="email" id="email" class="form-control" value="{{ old('email') }}" required>
                                @error('email')
                                <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-12 col-md-6 mb-3 mb-md-0">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" name="password" id="password" class="form-control" required>
                                @error('password')
                                <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12 col-md-6">
                                <label for="password_confirmation" class="form-label">Confirm Password</label>
                                <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" required>
                                @error('password_confirmation')
                                <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-12 col-md-6 mb-3 mb-md-0">
                                <label for="role_id" class="form-label">Role</label>
                                <select name="role_id" id="role_id" class="form-select" required>
                                    <option value="" disabled {{ old('role_id') ? '' : 'selected' }}>Select Role</option>
                                    @foreach($roles as $role)
                                    <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>{{ $role->display_name }}</option>
                                    @endforeach
                                </select>
                                @error('role_id')
                                <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12 col-md-6">
                                <label for="status" class="form-label">Status</label>
                                <select name="status" id="status" class="form-select" required>
                                    <option value="" disabled {{ old('status') ? '' : 'selected' }}>Select Status</option>
                                    <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                </select>
                                @error('status')
                                <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-12">
                                <label for="position" class="form-label">Position</label>
                                <select name="position" id="position" class="form-select" required>
                                    <option value="None" {{ old('position') == 'None' ? 'selected' : '' }}>None</option>
                                    <option value="Engineer" {{ old('position') == 'Engineer' ? 'selected' : '' }}>Engineer</option>
                                    <option value="Technician" {{ old('position') == 'Technician' ? 'selected' : '' }}>Technician</option>
                                    <option value="CEO" {{ old('position') == 'CEO' ? 'selected' : '' }}>CEO</option>
                                    <option value="Other" {{ old('position') == 'Other' ? 'selected' : '' }}>Other</option>
                                </select>
                                @error('position')
                                <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Create Admin</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
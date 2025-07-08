@extends('layouts.panel')

@section('title', 'Edit Vessel')

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

    .form-control {
        border-radius: 8px;
        border: 1px solid #d1d5db;
        transition: border-color 0.2s ease, box-shadow 0.2s ease;
        height: 38px;
        font-size: 0.875rem;
    }

    .form-control:focus {
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

    .btn-secondary {
        background-color: #6b7280;
        border-color: #6b7280;
        color: #ffffff;
        padding: 0.5rem 1rem;
    }

    .btn-secondary:hover {
        background-color: #4b5563;
        border-color: #4b5563;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(107, 114, 128, 0.3);
    }

    .text-danger {
        font-size: 0.75rem;
        margin-top: 0.25rem;
    }

    @media (max-width: 768px) {
        .hero-header h2 {
            font-size: 1.75rem;
        }
        .form-control {
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
                        <i class="fas fa-ship me-2"></i>Edit Vessel
                    </h2>
                </div>
            </div>

            <!-- Alert Messages -->
            @include('components.alerts')

            <!-- Form -->
            <div class="card">
                <div class="card-body p-4">
                    <form action="{{ route('vessels.update', $vessel->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="row mb-3">
                            <div class="col-12 col-md-6 mb-3 mb-md-0">
                                <label for="name" class="form-label">Name</label>
                                <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $vessel->name) }}" required>
                                @error('name')
                                <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12 col-md-6">
                                <label for="nationality" class="form-label">Nationality</label>
                                <input type="text" name="nationality" id="nationality" class="form-control" value="{{ old('nationality', $vessel->nationality) }}" required>
                                @error('nationality')
                                <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">Update Vessel</button>
                            <a href="{{ route('vessels.index') }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
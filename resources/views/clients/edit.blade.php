@extends('layouts.panel')

@section('title', 'Client Edit')

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

    .card-body {
        padding: 1.5rem;
    }

    .form-control,
    .form-select {
        border-radius: 8px;
        border: 1px solid #d1d5db;
        transition: border-color 0.2s ease, box-shadow 0.2s ease;
        height: 38px;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: #2563eb;
        box-shadow: 0 0 0 0.2rem rgba(37, 99, 235, 0.2);
        outline: none;
    }

    .form-label {
        font-weight: 500;
        color: #1f2937;
    }

    .text-danger {
        font-size: 0.875rem;
        margin-top: 0.25rem;
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

    .btn-danger {
        background-color: #dc2626;
        border-color: #dc2626;
        color: #ffffff;
        padding: 0.5rem 1rem;
    }

    .btn-danger:hover {
        background-color: #b91c1c;
        border-color: #b91c1c;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(220, 38, 38, 0.3);
    }

    .custom-file-upload {
        display: inline-block;
        padding: 0.5rem 1rem;
        background-color: #e5e7eb;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .custom-file-upload:hover {
        background-color: #d1d5db;
    }

    .image-preview {
        max-width: 100px;
        max-height: 100px;
        object-fit: contain;
        margin-top: 0.5rem;
        border-radius: 4px;
    }

    @media (max-width: 768px) {
        .hero-header h2 {
            font-size: 1.75rem;
        }

        .form-control,
        .form-select {
            font-size: 0.875rem;
        }

        .btn {
            padding: 0.4rem 0.8rem;
            font-size: 0.875rem;
        }

        .image-preview {
            max-width: 80px;
            max-height: 80px;
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
                    <div class="d-flex justify-content-between align-items-center">
                        <h2 class="mb-0">
                            <i class="fas fa-user-edit me-2"></i>Edit Client
                        </h2>
                        <div class="d-flex gap-2">
                            <a href="{{ route('clients.index') }}" class="btn btn-secondary d-flex align-items-center">
                                <i class="fas fa-arrow-left me-2"></i>Back to Clients
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Alert Messages -->
            @include('components.alerts')

            <!-- Edit Form -->
            <div class="card">
                <div class="card-body">
                    <form id="update-client-form" action="{{ route('clients.update', $client->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="row mb-3">
                            <div class="col-12 col-md-6 mb-3 mb-md-0">
                                <label for="first_name" class="form-label">First Name</label>
                                <input type="text" name="first_name" id="first_name" class="form-control" value="{{ old('first_name', $client->first_name) }}" required>
                                @error('first_name')
                                <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12 col-md-6">
                                <label for="last_name" class="form-label">Last Name</label>
                                <input type="text" name="last_name" id="last_name" class="form-control" value="{{ old('last_name', $client->last_name) }}" required>
                                @error('last_name')
                                <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-12 col-md-6 mb-3 mb-md-0">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" name="email" id="email" class="form-control" value="{{ old('email', $client->email) }}" required>
                                @error('email')
                                <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12 col-md-6">
                                <label for="phone" class="form-label">Phone Number</label>
                                <input type="text" name="phone" id="phone" class="form-control" value="{{ old('phone', $client->phone) }}">
                                @error('phone')
                                <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-12 col-md-6 mb-3 mb-md-0">
                                <label for="password" class="form-label">Password (leave blank to keep unchanged)</label>
                                <input type="password" name="password" id="password" class="form-control">
                                @error('password')
                                <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12 col-md-6">
                                <label for="password_confirmation" class="form-label">Confirm Password</label>
                                <input type="password" name="password_confirmation" id="password_confirmation" class="form-control">
                                @error('password_confirmation')
                                <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-12 col-md-6 mb-3 mb-md-0">
                                <label for="company_name" class="form-label">Company Name</label>
                                <input type="text" name="company_name" id="company_name" class="form-control" value="{{ old('company_name', $client->company->name) }}" required>
                                @error('company_name')
                                <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12 col-md-6">
                                <label for="status" class="form-label">Status</label>
                                <select name="status" id="status" class="form-select" required>
                                    <option value="active" {{ old('status', $client->status) == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ old('status', $client->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                </select>
                                @error('status')
                                <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-12 col-md-6">
                                <label for="image" class="form-label">Company Logo (optional)</label>
                                <input type="file" name="image" id="image" class="form-control" accept="image/jpeg,image/png,image/jpg">
                                @error('image')
                                <div class="text-danger">{{ $message }}</div>
                                @enderror
                                @if ($client->image)
                                <div class="mt-2">
                                    <img src="{{ $client->image_url }}" alt="{{ $client->company->name ?? 'Logo' }}" class="image-preview">
                                    <div class="form-check mt-2">
                                        <input type="checkbox" name="remove_image" id="remove_image" class="form-check-input" value="1">
                                        <label for="remove_image" class="form-check-label">Remove current logo</label>
                                    </div>
                                </div>
                                @endif
                                <div id="image-preview" class="mt-2"></div>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">Update Client</button>
                            <a href="{{ route('clients.index') }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>

                    @can('delete', $client)
                    <form id="delete-client-form" action="{{ route('clients.destroy', $client->id) }}" method="POST" class="d-inline mt-3">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete client {{ $client->first_name }} {{ $client->last_name }}?')">Delete Client</button>
                    </form>
                    @endcan
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const imageInput = document.getElementById('image');
    const imagePreview = document.getElementById('image-preview');
    const removeImageCheckbox = document.getElementById('remove_image');
    const updateForm = document.getElementById('update-client-form');

    imageInput.addEventListener('change', function(e) {
        imagePreview.innerHTML = '';
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const img = document.createElement('img');
                img.src = e.target.result;
                img.className = 'image-preview';
                imagePreview.appendChild(img);
            };
            reader.readAsDataURL(file);
            if (removeImageCheckbox) {
                removeImageCheckbox.checked = false;
                imageInput.disabled = false;
            }
        }
    });

    if (removeImageCheckbox) {
        removeImageCheckbox.addEventListener('change', function() {
            if (this.checked) {
                imageInput.disabled = true;
                imageInput.value = '';
                imagePreview.innerHTML = '';
            } else {
                imageInput.disabled = false;
            }
        });
    }

    // Prevent accidental delete form submission
    updateForm.addEventListener('submit', function(e) {
        // Ensure the update form submits to the correct route
        if (updateForm.getAttribute('action').includes('destroy')) {
            e.preventDefault();
            alert('Error: Form action is incorrect. Please try again.');
        }
    });
});
</script>
@endsection
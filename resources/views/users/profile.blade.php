@extends('layouts.panel')

@section('title', 'Edit Profile')

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

    .text-danger {
        font-size: 0.75rem;
        margin-top: 0.25rem;
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

    .logo-container {
        margin-bottom: 1.5rem;
        text-align: center;
    }

    .logo-name {
        font-size: 1.25rem;
        font-weight: 600;
        color: #1f2937;
        margin-top: 0.5rem;
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
        .image-preview {
            max-width: 80px;
            max-height: 80px;
        }
        .logo-name {
            font-size: 1rem;
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
                    <h2 class="my-3 my-md-0">
                        <i class="fas fa-user-edit me-2"></i>Edit Profile
                    </h2>
                </div>
            </div>

            <!-- Alert Messages -->
            @include('components.alerts')

            <!-- Form -->
            <div class="card">
                <div class="card-body p-4">
                    <form id="profile-form" action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        @if($user->isClient())
                        <div class="logo-container">
                            <label for="image" class="form-label">Company Logo</label>
                            <input type="file" name="image" id="image" class="form-control" accept="image/jpeg,image/png,image/jpg">
                            @error('image')
                            <div class="text-danger">{{ $message }}</div>
                            @enderror
                            @if($user->image)
                            <div class="mt-2">
                                <img src="{{ $user->image_url }}" alt="{{ $user->company->name ?? 'Company Logo' }}" class="image-preview">
                                <div class="form-check mt-2">
                                    <input type="checkbox" name="remove_image" id="remove_image" class="form-check-input" value="1">
                                    <label for="remove_image" class="form-check-label">Remove current logo</label>
                                </div>
                            </div>
                            @endif
                            <div id="image-preview" class="mt-2"></div>
                            <div class="logo-name">{{ $user->company->name ?? 'No Company' }}</div>
                        </div>
                        @endif
                        <div class="mb-3">
                            <label for="first_name" class="form-label">First Name</label>
                            <input type="text" name="first_name" id="first_name" class="form-control" value="{{ old('first_name', $user->first_name) }}" required>
                            @error('first_name')
                            <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="last_name" class="form-label">Last Name</label>
                            <input type="text" name="last_name" id="last_name" class="form-control" value="{{ old('last_name', $user->last_name) }}" required>
                            @error('last_name')
                            <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" id="email" class="form-control" value="{{ $user->email }}" readonly>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password (leave blank to keep unchanged)</label>
                            <input type="password" name="password" id="password" class="form-control">
                            @error('password')
                            <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">Confirm Password</label>
                            <input type="password" name="password_confirmation" id="password_confirmation" class="form-control">
                            @error('password_confirmation')
                            <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <button type="submit" class="btn btn-primary">Update Profile</button>
                    </form>
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
    const form = document.getElementById('profile-form');

    if (imageInput) {
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
    }

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

    // Debug form submission
    form.addEventListener('submit', function(e) {
        const formData = new FormData(form);
        console.log('Form Data:');
        for (let [key, value] of formData.entries()) {
            console.log(`${key}: ${value instanceof File ? value.name : value}`);
        }
    });
});
</script>
@endsection
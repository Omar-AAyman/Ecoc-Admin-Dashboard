@extends('layouts.panel')

@section('title', 'Edit Tank')

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

    .card-header {
        background-color: #f8f9fa;
        border-bottom: none;
        padding: 1.5rem;
        font-weight: 600;
        font-size: 1.25rem;
    }

    .card-body {
        padding: 1.5rem;
    }

    .form-control, .form-select {
        border-radius: 8px;
        border: 1px solid #d1d5db;
        transition: border-color 0.2s ease, box-shadow 0.2s ease;
        height: 38px;
    }

    .form-control:focus, .form-select:focus {
        border-color: #2563eb;
        box-shadow: 0 0 0 0.2rem rgba(37, 99, 235, 0.2);
        outline: none;
    }

    .form-control:disabled {
        background-color: #e9ecef;
        opacity: 1;
    }

    .btn {
        border-radius: 8px;
        margin-left: 2px;
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
        font-size: 0.875rem;
        margin-top: 0.25rem;
    }

    .modal-content {
        border-radius: 12px;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.2);
    }

    .modal-header {
        background-color: #f8f9fa;
        border-bottom: none;
    }

    .modal-footer {
        border-top: none;
    }

    @media (max-width: 768px) {
        .hero-header h2 {
            font-size: 1.75rem;
        }

        .card-header {
            font-size: 1.1rem;
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
                    <div class="d-flex justify-content-between align-items-center">
                        <h2 class="my-3 my-md-0">
                            <i class="fas fa-cogs me-2"></i>Edit Tank-{{ $tank->id }}
                        </h2>
                        <a href="{{ route('tanks.settings') }}" class="btn btn-secondary d-flex align-items-center">
                            <i class="fas fa-arrow-left me-2"></i>Back to Tanks
                        </a>
                    </div>
                </div>
            </div>

            <!-- Alert Messages -->
            @include('components.alerts')

            <!-- Edit Tank Form -->
            <div class="card">
                <div class="card-header">Tank Details</div>
                <div class="card-body">
                    <form id="edit-tank-form" action="{{ route('tanks.update', $tank->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label for="cubic_meter_capacity" class="form-label">Original Capacity (m³)</label>
                            <input type="number" name="cubic_meter_capacity" id="cubic_meter_capacity" class="form-control" value="{{ old('cubic_meter_capacity', $tank->cubic_meter_capacity) }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="company_id" class="form-label">Company</label>
                            <select name="company_id" id="company_id" class="form-select">
                                <option value="" {{ old('company_id', $tank->company_id) ? '' : 'selected' }}>None</option>
                                @foreach ($companies as $company)
                                <option value="{{ $company->id }}" {{ old('company_id', $tank->company_id) == $company->id ? 'selected' : '' }}>{{ $company->name }}</option>
                                @endforeach
                            </select>
                            @error('company_id')
                            <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="product_id" class="form-label">Product</label>
                            <option value="" {{ old('product_id', $tank->product_id) ? '' : 'selected' }}>None</option>
                            <select name="product_id" id="product_id" class="form-select">
                                @foreach ($products as $product)
                                <option value="{{ $product->id }}" {{ old('product_id', $tank->product_id) == $product->id ? 'selected' : '' }}>{{ $product->name }}</option>
                                @endforeach
                            </select>
                            @error('product_id')
                            <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3" id="max-capacity-group" style="display: {{ $tank->product_id ? 'block' : 'none' }};">
                            <label for="max_capacity" class="form-label">Max Capacity (mt)</label>
                            <input type="number" id="max_capacity" class="form-control" disabled>
                        </div>
                        <div class="mb-3" id="current-level-group" style="display: {{ $tank->product_id ? 'block' : 'none' }};">
                            <label for="current_level" class="form-label">Current Capacity (mt)</label>
                            <input type="number" name="current_level" id="current_level" class="form-control" value="{{ old('current_level', $tank->current_level) }}" step="0.01" min="0">
                            @error('current_level')
                            <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3" id="temperature-group" style="display: {{ $tank->product_id ? 'block' : 'none' }};">
                            <label for="temperature" class="form-label">Temperature (°C)</label>
                            <input type="number" name="temperature" id="temperature" class="form-control" value="{{ old('temperature', $tank->temperature) }}" step="0.01" min="-50" max="100">
                            @error('temperature')
                            <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">Update Tank</button>
                            <a href="{{ route('tanks.settings') }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Confirmation Modal -->
<div class="modal fade" id="confirmLevelModal" tabindex="-1" aria-labelledby="confirmLevelModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmLevelModalLabel">Confirm Tank Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Setting the current capacity or temperature manually is a critical action that will be logged for auditing and may affect tank transactions. Are you sure you want to proceed?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="confirmSubmit">Confirm</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('Edit tank script loaded');
    if (typeof bootstrap === 'undefined') {
        console.error('Bootstrap 5 JavaScript is not loaded');
    } else {
        console.log('Bootstrap 5 JavaScript is loaded');
    }

    const productSelect = document.getElementById('product_id');
    const cubicMeterCapacityInput = document.getElementById('cubic_meter_capacity');
    const maxCapacityInput = document.getElementById('max_capacity');
    const maxCapacityGroup = document.getElementById('max-capacity-group');
    const currentLevelGroup = document.getElementById('current-level-group');
    const temperatureGroup = document.getElementById('temperature-group');
    const currentLevelInput = document.getElementById('current_level');
    const temperatureInput = document.getElementById('temperature');
    const form = document.getElementById('edit-tank-form');
    const confirmModal = new bootstrap.Modal(document.getElementById('confirmLevelModal'));
    const confirmSubmit = document.getElementById('confirmSubmit');

    function updateMaxCapacity() {
        const productId = productSelect.value;
        const cubicMeterCapacity = parseFloat(cubicMeterCapacityInput.value) || 0;
        if (productId) {
            fetch(`/api/products/${productId}`)
                .then(response => response.json())
                .then(data => {
                    const density = parseFloat(data.density) || 1;
                    maxCapacityInput.value = (cubicMeterCapacity * density).toFixed(2);
                    maxCapacityGroup.style.display = 'block';
                    currentLevelGroup.style.display = 'block';
                    temperatureGroup.style.display = 'block';
                })
                .catch(error => {
                    console.error('Error fetching product density:', error);
                    maxCapacityInput.value = '';
                    maxCapacityGroup.style.display = 'none';
                    currentLevelGroup.style.display = 'none';
                    temperatureGroup.style.display = 'none';
                    currentLevelInput.value = 0;
                    temperatureInput.value = '';
                });
        } else {
            maxCapacityInput.value = '';
            maxCapacityGroup.style.display = 'none';
            currentLevelGroup.style.display = 'none';
            temperatureGroup.style.display = 'none';
            currentLevelInput.value = 0;
            temperatureInput.value = '';
        }
    }

    productSelect.addEventListener('change', updateMaxCapacity);
    cubicMeterCapacityInput.addEventListener('input', updateMaxCapacity);

    // Initialize on page load
    updateMaxCapacity();

    form.addEventListener('submit', function(event) {
        const currentLevel = parseFloat(currentLevelInput.value) || 0;
        const temperature = parseFloat(temperatureInput.value);
        if (currentLevel > 0 || temperature) {
            event.preventDefault();
            confirmModal.show();
        }
    });

    confirmSubmit.addEventListener('click', function() {
        confirmModal.hide();
        form.submit();
    });
});
</script>
@endsection
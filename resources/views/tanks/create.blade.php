@extends('layouts.panel')

@section('title', 'Create Tank')

@section('content')
<div class="main-content side-content mt-2 pt-0">
    <div class="container-fluid">
        <div class="inner-body">
            <h2 class="mb-4">Create Tank</h2>
            @include('components.alerts')
            <form action="{{ route('tanks.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="number" class="form-label">Tank Number</label>
                    <input type="text" name="number" id="number" class="form-control" value="{{ old('number') }}" required>
                    @error('number')
                    <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="cubic_meter_capacity" class="form-label">Capacity (m³)</label>
                    <input type="number" name="cubic_meter_capacity" id="cubic_meter_capacity" class="form-control" value="{{ old('cubic_meter_capacity') }}" required>
                    @error('cubic_meter_capacity')
                    <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="status" class="form-label">Status</label>
                    <select name="status" id="status" class="form-select" required>
                        <option value="Available" {{ old('status') == 'Available' ? 'selected' : '' }}>Available</option>
                        <option value="In Use" {{ old('status') == 'In Use' ? 'selected' : '' }}>In Use</option>
                    </select>
                    @error('status')
                    <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="product_id" class="form-label">Product</label>
                    <select name="product_id" id="product_id" class="form-select">
                        <option value="">Select Product</option>
                        @foreach ($products as $product)
                        <option value="{{ $product->id }}" {{ old('product_id') == $product->id ? 'selected' : '' }}>{{ $product->name }}</option>
                        @endforeach
                    </select>
                    @error('product_id')
                    <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="company_id" class="form-label">Company</label>
                    <select name="company_id" id="company_id" class="form-select">
                        <option value="">No Company</option>
                        @foreach ($companies as $company)
                        <option value="{{ $company->id }}" {{ old('company_id') == $company->id ? 'selected' : '' }}>{{ $company->name }}</option>
                        @endforeach
                    </select>
                    @error('company_id')
                    <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>
                <button type="submit" class="btn btn-primary">Create Tank</button>
            </form>
        </div>
    </div>
</div>
@endsection

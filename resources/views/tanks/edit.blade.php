@extends('layouts.panel')

@section('title', 'Edit Tank')

@section('content')
<div class="main-content side-content mt-2 pt-0">
    <div class="container-fluid">
        <div class="inner-body">
            <h2 class="mb-4">Edit Tank</h2>
            @include('components.alerts')
            <form action="{{ route('tanks.updateSettings', $tank->id) }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="name" class="form-label">Name</label>
                    <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $tank->name) }}" required>
                </div>
                <div class="mb-3">
                    <label for="capacity" class="form-label">Capacity (L)</label>
                    <input type="number" name="capacity" id="capacity" class="form-control" value="{{ old('capacity', $tank->capacity) }}" required>
                </div>
                <div class="mb-3">
                    <label for="current_volume" class="form-label">Current Volume (L)</label>
                    <input type="number" name="current_volume" id="current_volume" class="form-control" value="{{ old('current_volume', $tank->current_volume) }}" required>
                </div>
                <div class="mb-3">
                    <label for="status" class="form-label">Status</label>
                    <select name="status" id="status" class="form-select" required>
                        <option value="active" {{ $tank->status === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ $tank->status === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Update Tank</button>
            </form>
        </div>
    </div>
</div>
@endsection

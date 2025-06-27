@extends('layouts.panel')

@section('title', 'Vessels')

@section('content')
<div class="main-content side-content mt-2 pt-0">
    <div class="container-fluid">
        <div class="inner-body">
            <h1>Vessels</h1>
            @include('components.alerts')
            <a href="{{ route('vessels.create') }}" class="btn btn-primary mb-3">Add Vessel</a>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Nationality</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($vessels as $vessel)
                    <tr>
                        <td>{{ $vessel->name }}</td>
                        <td>{{ $vessel->nationality }}</td>
                        <td>
                            <a href="{{ route('vessels.edit', $vessel->id) }}" class="btn btn-sm btn-primary">Edit</a>
                            <form action="{{ route('vessels.destroy', $vessel->id) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

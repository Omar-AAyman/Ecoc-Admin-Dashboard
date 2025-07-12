@foreach ($drivers as $driver)
<tr>
    <td>{{ $driver->name }}</td>
    <td>
        <div class="d-flex gap-1 justify-content-start">
            @can('update', \App\Models\Driver::class)
            <a href="{{ route('drivers.edit', $driver->id) }}" class="btn btn-sm btn-primary" title="Edit">
                <i class="fas fa-edit"></i>
            </a>
            @endcan
            @can('delete', \App\Models\Driver::class)
            <form action="{{ route('drivers.destroy', $driver->id) }}" method="POST" class="d-inline delete-driver-form">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-sm btn-danger delete-btn" data-driver-number="{{ $driver->name }}" title="Delete">
                    <i class="fas fa-trash"></i>
                </button>
            </form>
            @endcan
        </div>
    </td>
</tr>
@endforeach
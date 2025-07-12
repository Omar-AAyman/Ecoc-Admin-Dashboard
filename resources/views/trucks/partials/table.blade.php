@foreach ($trucks as $truck)
<tr>
    <td>{{ $truck->truck_number }}</td>
    <td>
        <div class="d-flex gap-1 justify-content-start">
            @can('update', \App\Models\Truck::class)
            <a href="{{ route('trucks.edit', $truck->id) }}" class="btn btn-sm btn-primary" title="Edit">
                <i class="fas fa-edit"></i>
            </a>
            @endcan
            @can('delete', \App\Models\Truck::class)
            <form action="{{ route('trucks.destroy', $truck->id) }}" method="POST" class="d-inline delete-truck-form">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-sm btn-danger delete-btn" data-truck-number="{{ $truck->truck_number }}" title="Delete">
                    <i class="fas fa-trash"></i>
                </button>
            </form>
            @endcan
        </div>
    </td>
</tr>
@endforeach
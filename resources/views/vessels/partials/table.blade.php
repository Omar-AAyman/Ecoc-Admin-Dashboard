@foreach ($vessels as $vessel)
<tr>
    <td>{{ $vessel->name }}</td>
    <td>{{ $vessel->nationality }}</td>
    <td>
        <div class="d-flex gap-1 justify-content-start">
            @can('update', \App\Models\Vessel::class)
            <a href="{{ route('vessels.edit', $vessel->id) }}" class="btn btn-sm btn-primary" title="Edit">
                <i class="fas fa-edit"></i>
            </a>
            @endcan
            @can('delete', \App\Models\Vessel::class)
            <form action="{{ route('vessels.destroy', $vessel->id) }}" method="POST" class="d-inline delete-vessel-form">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-sm btn-danger delete-btn" data-vessel-name="{{ $vessel->name }}" title="Delete">
                    <i class="fas fa-trash"></i>
                </button>
            </form>
            @endcan
        </div>
    </td>
</tr>
@endforeach

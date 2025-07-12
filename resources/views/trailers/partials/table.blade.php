@foreach ($trailers as $trailer)
<tr>
    <td>{{ $trailer->trailer_number }}</td>
    <td>
        <div class="d-flex gap-1 justify-content-start">
            @can('update', \App\Models\trailer::class)
            <a href="{{ route('trailers.edit', $trailer->id) }}" class="btn btn-sm btn-primary" title="Edit">
                <i class="fas fa-edit"></i>
            </a>
            @endcan
            @can('delete', \App\Models\trailer::class)
            <form action="{{ route('trailers.destroy', $trailer->id) }}" method="POST" class="d-inline delete-trailer-form">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-sm btn-danger delete-btn" data-trailer-number="{{ $trailer->trailer_number }}" title="Delete">
                    <i class="fas fa-trash"></i>
                </button>
            </form>
            @endcan
        </div>
    </td>
</tr>
@endforeach
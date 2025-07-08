@foreach ($users as $user)
<tr>
    <td>{{ $user->first_name }} {{ $user->last_name }}</td>
    <td>{{ $user->email }}</td>
    <td>{{ $user->role->display_name }}</td>
    <td>{{ ucfirst($user->status) }}</td>
    <td>{{ $user->position }}</td>
    <td>
        <div class="d-flex gap-1 justify-content-start">
            @can('update', $user)
            <a href="{{ route('users.edit', $user->id) }}" class="btn btn-sm btn-primary" title="Edit">
                <i class="fas fa-edit"></i>
            </a>
            @endcan
            @can('delete', $user)
            @if(auth()->id() !== $user->id)
            <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="d-inline delete-admin-form">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-sm btn-danger delete-btn" data-admin-name="{{ $user->first_name }} {{ $user->last_name }}" title="Delete">
                    <i class="fas fa-trash"></i>
                </button>
            </form>
            @endif
            @endcan
        </div>
    </td>
</tr>
@endforeach
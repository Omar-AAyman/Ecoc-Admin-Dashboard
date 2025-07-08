@foreach ($clients as $client)
<tr>
    <td>{{ $client->id }}</td>
    <td>
        <img src="{{ $client->image_url }}" alt="{{ $client->company->name ?? 'Logo' }}" class="client-logo">
    </td>
    <td>{{ $client->first_name }} {{ $client->last_name }}</td>
    <td>{{ $client->email }}</td>
    <td>{{ $client->company->name ?? 'N/A' }}</td>
    <td>
        <span class="status-badge status-{{ $client->status }}">{{ ucfirst($client->status) }}</span>
    </td>
    <td>
        <div class="d-flex gap-2 justify-content-start">
            @can('view', $client)
            <a href="{{ route('clients.show', $client->id) }}" class="btn btn-sm btn-secondary " title="View Client">
                <i class="fas fa-eye"></i>
            </a>
            @endcan
            @if (auth()->user()->hasAnyRole(['super_admin', 'ceo']))
            @can('update', $client)
            <a href="{{ route('clients.edit', $client->id) }}" class="btn btn-sm btn-primary" title="Edit Client">
                <i class="fas fa-edit"></i>
            </a>
            @endcan
            @can('delete', $client)
            <button type="button" class="btn btn-sm btn-danger delete-btn" data-client-id="{{ $client->id }}" data-client-name="{{ $client->full_name }}" data-bs-toggle="modal" data-bs-target="#deleteModal" title="Delete Client">
                <i class="fas fa-trash"></i>
            </button>
            @endcan
            @endif
        </div>
    </td>
</tr>
@endforeach

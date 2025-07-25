@foreach ($tanks as $tank)
<tr>
    <td>{{ $tank->number }}</td>
    <td>{{ $tank->cubic_meter_capacity }}</td>
    <td>{{ $tank->maxCapacity() }}</td>
    <td>{{ $tank->current_level }}</td>
    <td>
        @if ($tank->maxCapacity() > 0)
        {{ number_format(($tank->current_level / $tank->maxCapacity()) * 100, 2) }}%
        @else
        0%
        @endif
    </td>
    <td>
        @if (in_array($tank->status, ['in_use', 'available']))
        <span class="status-badge status-{{ $tank->status }}">{{ ucfirst(str_replace('_', ' ', $tank->status)) }}</span>
        @else
        {{ ucfirst(str_replace('_', ' ', $tank->status)) }}
        @endif
    </td>
    <td>
        @if ($tank->product)
        <span class="product-tooltip" data-tooltip="Density: {{ $tank->product->density }} g/cm³">
            {{ $tank->product->name }}
        </span>
        @else
        None
        @endif
    </td>
    <td>
        @if ($tank->company && null !== $tank->company->users->first())
        <a href="{{ route('clients.show', $tank->company->users->first()->id) }}">
            {{ $tank->company->name }}
        </a>
        @else
        None
        @endif
    </td>
    <td>{{ $tank->temperature !== null ? number_format($tank->temperature, 2) . '°C' : 'N/A' }}</td>
    <td>{{ $tank->temperature_fahrenheit !== null ? number_format($tank->temperature_fahrenheit, 2) . '°F' : 'N/A' }}</td>
    <td>
        <div class="d-flex gap-1 justify-content-start">
            <a href="{{ route('tanks.edit', $tank->id) }}" class="btn btn-sm btn-primary" title="Edit">
                <i class="fas fa-edit"></i>
            </a>
            @if (auth()->user()->hasRole('super_admin'))
            <button type="button" class="btn btn-sm btn-secondary reset-btn" data-tank-id="{{ $tank->id }}" data-tank-number="{{ $tank->number }}" data-bs-toggle="modal" data-bs-target="#resetModal" title="Reset">
                <i class="fas fa-undo"></i>
            </button>
            @endif
        </div>
    </td>
</tr>
@endforeach

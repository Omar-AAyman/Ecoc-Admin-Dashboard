@foreach ($products as $product)
<tr>
    <td>{{ $product->name }}</td>
    <td>{{ $product->density }}</td>
    <td>
        <div class="d-flex gap-1 justify-content-start">
            @can('update', \App\Models\Product::class)
            <a href="{{ route('products.edit', $product->id) }}" class="btn btn-sm btn-primary" title="Edit">
                <i class="fas fa-edit"></i>
            </a>
            @endcan
            @can('delete', \App\Models\Product::class)
            <form action="{{ route('products.destroy', $product->id) }}" method="POST" class="d-inline delete-product-form">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-sm btn-danger delete-btn" data-product-name="{{ $product->name }}" title="Delete">
                    <i class="fas fa-trash"></i>
                </button>
            </form>
            @endcan
        </div>
    </td>
</tr>
@endforeach
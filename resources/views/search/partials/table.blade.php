<div class="table-responsive">
<table id="table" class="table table-bordered table-hover">
    <thead>
        <tr>
            <th class="type-column">Type</th>
            <th>Description</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($results as $result)
            <tr>
                <td class="type-column">{{ $result['type'] }}</td>
                <td>{{ $result['text'] }}</td>
                <td><a href="{{ $result['url'] }}" class="btn btn-primary btn-sm">View</a></td>
            </tr>
        @endforeach
    </tbody>
</table>
</div>
{{ $results->appends(['per_page' => $perPage ?? 10, 'query' => $query, 'category' => $category])->links('pagination::bootstrap-5') }}
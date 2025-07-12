@extends('layouts.panel')

@section('title', 'Search Results')

@section('css')
<style>
    .hero-header {
        padding: 2.5rem 0;
        border-radius: 16px;
        margin-bottom: 2rem;
        box-shadow: 0 6px 20px rgba(0, 11, 67, 0.2);
        background-color: #ffffff;
    }
    .hero-header h2 {
        font-size: 2.25rem;
        font-weight: 700;
        margin-bottom: 0;
    }
    .card {
        border: none;
        border-radius: 16px;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
    }
    .table th, .table td {
        vertical-align: middle;
    }
    #table th.type-column, #table td.type-column {
        width: auto !important;
        max-width: 150px !important;
        min-width: 100px !important; /* Ensure minimum width */
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        display: table-cell !important; /* Force table-cell display */
    }
    @media (max-width: 768px) {
        .hero-header h2 {
            font-size: 1.75rem;
        }
    }
</style>
@endsection

@section('content')
<div class="main-content side-content my-2 pt-0">
    <div class="container-fluid px-4 py-4">
        <div class="inner-body">
            <!-- Page Header -->
            <div class="hero-header">
                <div class="container">
                    <div class="d-flex justify-content-between align-items-center">
                        <h2 class="my-3 my-md-0">
                            <i class="fe fe-search me-2"></i>Search Results for "{{ $query }}"
                        </h2>
                        <a href="{{ route('dashboard') }}" class="btn btn-primary d-flex align-items-center">
                            <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
                        </a>
                    </div>
                </div>
            </div>

            <!-- Search Form -->
            <div class="card mb-4">
                <div class="card-body">
                    <form action="{{ url('/search') }}" method="GET" class="input-group">
                        <div class="input-group-btn search-panel">
                            <select name="category" class="form-control select2-no-search">
                                <option value="all" {{ $category == 'all' ? 'selected' : '' }}>All Categories</option>
                                <option value="tanks" {{ $category == 'tanks' ? 'selected' : '' }}>Tanks</option>
                                <option value="transactions" {{ $category == 'transactions' ? 'selected' : '' }}>Transactions</option>
                                <option value="clients" {{ $category == 'clients' ? 'selected' : '' }}>Clients</option>
                                <option value="users" {{ $category == 'users' ? 'selected' : '' }}>Admins</option>
                                <option value="products" {{ $category == 'products' ? 'selected' : '' }}>Products</option>
                                <option value="vessels" {{ $category == 'vessels' ? 'selected' : '' }}>Vessels</option>
                            </select>
                        </div>
                        <input type="search" name="query" class="form-control" placeholder="Search for anything..." value="{{ $query }}">
                        <button class="btn search-btn"><i class="fe fe-search"></i></button>
                    </form>
                </div>
            </div>

            <!-- Results Table -->
            <div class="card">
                <div class="card-body">
                    @if (empty($results))
                        <p>No results found for "{{ $query }}".</p>
                    @else
                        @include('search.partials.table', ['perPage' => $perPage])
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
    $(document).ready(function() {
        const table = $('#table');
        if (table.length && !$.fn.DataTable.isDataTable(table)) {
            table.DataTable({
                responsive: true,
                autoWidth: false,
                pageLength: {{ $perPage ?? 10 }},
                searching: false,
                ordering: false,
                serverSide: true,
                ajax: {
                    url: '{{ url("/search") }}',
                    data: function(d) {
                        d.query = '{{ $query }}';
                        d.category = '{{ $category }}';
                        d.per_page = d.length;
                    }
                },
                columns: [
                    { data: 'type', title: 'Type' },
                    { data: 'text', title: 'Description' },
                    {
                        data: 'url',
                        title: 'Action',
                        render: function(data) {
                            return '<a href="' + data + '" class="btn btn-primary btn-sm">View</a>';
                        }
                    }
                ]
            });
        }
    });
</script>
@endsection
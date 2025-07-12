@extends('layouts.panel')

@section('title', 'Trucks')

@section('css')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');

    body {
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        background-color: #f1f5f9;
        color: #1f2937;
    }

    .container-fluid {
        max-width: 1440px;
        padding: 0 1.5rem;
    }

    .hero-header {
        padding: 2.5rem 0;
        border-radius: 16px;
        margin-bottom: 2rem;
        box-shadow: 0 6px 20px rgba(0, 11, 67, 0.2);
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
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
    }

    .table {
        border-radius: 16px;
        overflow: hidden;
        background-color: #ffffff;
    }

    .table th,
    .table td {
        padding: 1rem;
        vertical-align: middle;
    }

    .table thead {
        background-color: #f8f9fa;
    }

    .table thead th {
        font-weight: 600;
    }

    .form-control {
        border-radius: 8px;
        border: 1px solid #d1d5db;
        transition: border-color 0.2s ease, box-shadow 0.2s ease;
        height: 38px;
    }

    .form-control:focus {
        border-color: #2563eb;
        box-shadow: 0 0 0 0.2rem rgba(37, 99, 235, 0.2);
        outline: none;
    }

    .btn {
        border-radius: 8px;
        font-weight: 500;
        transition: all 0.2s ease;
    }

    .btn-primary {
        background-color: #000b43;
        border-color: #000b43;
        color: #ffffff;
        padding: 0.5rem 1rem;
    }

    .btn-primary:hover {
        background-color: #1d4ed8;
        border-color: #1d4ed8;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
    }

    .btn-danger {
        background-color: #dc2626;
        border-color: #dc2626;
        color: #ffffff;
        padding: 0.5rem 1rem;
    }

    .btn-danger:hover {
        background-color: #b91c1c;
        border-color: #b91c1c;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(220, 38, 38, 0.3);
    }

    .btn-sm {
        padding: 0.375rem 0.75rem;
        font-size: 0.875rem;
    }

    .pagination-container {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .pagination-container select {
        width: auto;
        max-width: 100px;
    }

    .search-container {
        max-width: 300px;
    }

    @media (max-width: 768px) {
        .hero-header h2 {
            font-size: 1.75rem;
        }
        .table th,
        .table td {
            padding: 0.75rem;
            font-size: 0.875rem;
        }
        .btn {
            padding: 0.4rem 0.8rem;
            font-size: 0.875rem;
        }
        .btn-sm {
            padding: 0.3rem 0.6rem;
            font-size: 0.75rem;
        }
        .pagination-container select {
            max-width: 80px;
        }
        .search-container {
            max-width: 200px;
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
                            <i class="fas fa-truck me-2"></i>Trucks
                        </h2>
                        @can('create', \App\Models\Truck::class)
                        <div class="d-flex gap-2">
                            <a href="{{ route('trucks.create') }}" class="btn btn-primary d-flex align-items-center">
                                <i class="fas fa-plus me-2"></i>Add Truck
                            </a>
                        </div>
                        @endcan
                    </div>
                </div>
            </div>

            <!-- Alert Messages -->
            @include('components.alerts')

            @if ($trucks->isEmpty())
            <div class="card">
                <div class="card-body text-center py-4">
                    <div class="text-muted">
                        <i class="fas fa-search fa-2x mb-3"></i>
                        <p class="mb-0">No trucks found</p>
                    </div>
                </div>
            </div>
            @else
            <!-- Filters and Pagination -->
            <div class="card mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="search-container">
                            <input type="text" id="truck-search" class="form-control" placeholder="Search by truck number..." value="{{ request('search') }}">
                        </div>
                        <div class="pagination-container">
                            <label for="per-page" class="small fw-bold me-2">Show</label>
                            <select id="per-page" class="form-control">
                                <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10</option>
                                <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                                <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Trucks Table -->
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" id="trucks-table">
                            <thead>
                                <tr>
                                    <th>Truck Number</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="trucks-table-body">
                                @include('trucks.partials.table')
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-between align-items-center mt-4">
                <div class="text-muted small" id="pagination-info">
                    Showing {{ $trucks->firstItem() ?? 0 }} to {{ $trucks->lastItem() ?? 0 }} of {{ $trucks->total() }} entries
                </div>
                <div id="pagination-links">
                    {{ $trucks->appends(request()->all())->links('pagination::bootstrap-5') }}
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
document.addEventListener('DOMContentLoaded', function() {
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    function fetchTrucks(search = '', perPage = 10, page = 1) {
        $.ajax({
            url: '{{ route("trucks.index") }}',
            type: 'GET',
            data: {
                search: search,
                per_page: perPage,
                page: page,
                ajax: true
            },
            success: function(response) {
                $('#trucks-table-body').html(response.table);
                $('#pagination-links').html(response.pagination);
                $('#pagination-info').text(`Showing ${response.first_item} to ${response.last_item} of ${response.total} entries`);
                attachDeleteHandlers();
            },
            error: function(xhr) {
                console.error('AJAX error:', xhr);
                alert('An error occurred while fetching trucks. Please try again.');
            }
        });
    }

    function attachDeleteHandlers() {
        document.querySelectorAll('.delete-truck-form').forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                const truckNumber = this.querySelector('.delete-btn').dataset.truckNumber;
                if (confirm(`Are you sure you want to delete truck ${truckNumber}?`)) {
                    this.submit();
                }
            });
        });
    }

    attachDeleteHandlers();

    const searchInput = document.getElementById('truck-search');
    const debouncedSearch = debounce(function() {
        const searchTerm = searchInput.value;
        const perPage = document.getElementById('per-page').value;
        fetchTrucks(searchTerm, perPage);
    }, 300);
    searchInput.addEventListener('input', debouncedSearch);

    document.getElementById('per-page').addEventListener('change', function() {
        const perPage = this.value;
        const searchTerm = document.getElementById('truck-search').value;
        fetchTrucks(searchTerm, perPage);
    });

    document.getElementById('pagination-links').addEventListener('click', function(e) {
        e.preventDefault();
        if (e.target.tagName === 'A' && e.target.getAttribute('href')) {
            const url = new URL(e.target.getAttribute('href'));
            const page = url.searchParams.get('page') || 1;
            const searchTerm = document.getElementById('truck-search').value;
            const perPage = document.getElementById('per-page').value;
            fetchTrucks(searchTerm, perPage, page);
        }
    });

    let csrfTokenMeta = document.querySelector('meta[name="csrf-token"]');
    if (!csrfTokenMeta) {
        csrfTokenMeta = document.createElement('meta');
        csrfTokenMeta.name = 'csrf-token';
        csrfTokenMeta.content = '{{ csrf_token() }}';
        document.head.appendChild(csrfTokenMeta);
    }
});
</script>
@endsection
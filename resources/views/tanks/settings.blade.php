@extends('layouts.panel')

@section('title', 'Tank Settings')

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

    .btn-secondary {
        background-color: #6b7280;
        border-color: #6b7280;
        color: #ffffff;
        padding: 0.5rem 1rem;
    }

    .btn-secondary:hover {
        background-color: #4b5563;
        border-color: #4b5563;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(107, 114, 128, 0.3);
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

    .status-flag {
        display: inline-block;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        margin-right: 8px;
    }

    .status-available {
        background-color: #22c55e;
    }

    .status-in_use {
        background-color: #ef4444;
    }

    .product-tooltip {
        position: relative;
        cursor: pointer;
    }

    .product-tooltip:hover::after {
        content: attr(data-tooltip);
        position: absolute;
        top: -2rem;
        left: 50%;
        transform: translateX(-50%);
        background-color: #1f2937;
        color: #ffffff;
        padding: 0.25rem 0.5rem;
        border-radius: 4px;
        font-size: 0.75rem;
        white-space: nowrap;
        z-index: 10;
    }

    .modal-content {
        border-radius: 12px;
        border: none;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.2);
    }

    .modal-header {
        border-bottom: none;
        padding: 1.5rem 1.5rem 0;
    }

    .modal-title {
        font-weight: 600;
        font-size: 1.25rem;
    }

    .modal-body {
        padding: 1rem 1.5rem;
    }

    .modal-footer {
        border-top: none;
        padding: 0 1.5rem 1.5rem;
    }

    .modal-footer .btn {
        padding: 0.5rem 1rem;
        font-weight: 500;
    }

    .warning-text {
        color: #dc2626;
        font-weight: 500;
        margin-top: 0.5rem;
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

        .modal-title {
            font-size: 1.1rem;
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
                        <h2 class="mb-0">
                            <i class="fas fa-cogs me-2"></i>Tank Settings
                        </h2>
                    </div>
                </div>
            </div>

            <!-- Alert Messages -->
            @include('components.alerts')

            @if ($tanks->isEmpty())
            <div class="card">
                <div class="card-body text-center py-4">
                    <div class="text-muted">
                        <i class="fas fa-search fa-2x mb-3"></i>
                        <p class="mb-0">No tanks found</p>
                    </div>
                </div>
            </div>
            @else
            <!-- Filters and Pagination -->
            <div class="card mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="search-container">
                            <input type="text" id="tank-search" class="form-control" placeholder="Search by number, product, or status..." value="{{ request('search') }}">
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

            <!-- Tanks Table -->
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" id="tanks-table">
                            <thead>
                                <tr>
                                    <th>Number</th>
                                    <th>Capacity (m³)</th>
                                    <th>Max Capacity (mt)</th>
                                    <th>Current Level (mt)</th>
                                    <th>Fill %</th>
                                    <th>Status</th>
                                    <th>Product</th>
                                    <th>Company</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="tanks-table-body">
                                @include('tanks.partials.table')
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-between align-items-center mt-4">
                <div class="text-muted small" id="pagination-info">
                    Showing {{ $tanks->firstItem() ?? 0 }} to {{ $tanks->lastItem() ?? 0 }} of {{ $tanks->total() }} entries
                </div>
                <div id="pagination-links">
                    {{ $tanks->appends(request()->all())->links('pagination::bootstrap-5') }}
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Reset Confirmation Modal -->
<div class="modal fade" id="resetModal" tabindex="-1" aria-labelledby="resetModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="resetModalLabel">Confirm Tank Reset</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to reset tank <strong id="resetTankNumber"></strong>? This will clear the company, status, and current level.
                <p class="warning-text">This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="resetTankForm" action="" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-danger">Reset Tank</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Debounce function to limit AJAX calls
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

    // Function to fetch tanks via AJAX
    function fetchTanks(search = '', perPage = 10, page = 1) {
        $.ajax({
            url: '{{ route("tanks.settings") }}',
            type: 'GET',
            data: {
                search: search,
                per_page: perPage,
                page: page,
                ajax: true
            },
            success: function(response) {
                // Update table body
                $('#tanks-table-body').html(response.table);
                // Update pagination links
                $('#pagination-links').html(response.pagination);
                // Update showing info
                $('#pagination-info').text(`Showing ${response.first_item} to ${response.last_item} of ${response.total} entries`);
                // Reattach reset handlers
                attachResetHandlers();
            },
            error: function(xhr) {
                console.error('AJAX error:', xhr);
                alert('An error occurred while fetching tanks. Please try again.');
            }
        });
    }

    // Attach reset confirmation handlers
    function attachResetHandlers() {
        document.querySelectorAll('.reset-btn').forEach(button => {
            button.addEventListener('click', function() {
                const tankId = this.dataset.tankId;
                const tankNumber = this.dataset.tankNumber;
                const form = document.getElementById('resetTankForm');
                const resetUrl = '{{ route("tanks.reset", ":id") }}'.replace(':id', tankId);
                form.action = resetUrl;
                document.getElementById('resetTankNumber').textContent = tankNumber;
                console.log('Reset button clicked for tank ID:', tankId, 'URL:', resetUrl);
            });
        });
    }

    // Initial reset handlers
    attachResetHandlers();

    // Search functionality
    const searchInput = document.getElementById('tank-search');
    const debouncedSearch = debounce(function() {
        const searchTerm = searchInput.value;
        const perPage = document.getElementById('per-page').value;
        fetchTanks(searchTerm, perPage);
    }, 300);
    searchInput.addEventListener('input', debouncedSearch);

    // Per-page change handler
    document.getElementById('per-page').addEventListener('change', function() {
        const perPage = this.value;
        const searchTerm = document.getElementById('tank-search').value;
        fetchTanks(searchTerm, perPage);
    });

    // Pagination link click handler
    document.getElementById('pagination-links').addEventListener('click', function(e) {
        e.preventDefault();
        if (e.target.tagName === 'A' && e.target.getAttribute('href')) {
            const url = new URL(e.target.getAttribute('href'));
            const page = url.searchParams.get('page') || 1;
            const searchTerm = document.getElementById('tank-search').value;
            const perPage = document.getElementById('per-page').value;
            fetchTanks(searchTerm, perPage, page);
        }
    });

    // Inject CSRF token meta tag if it doesn't exist
    let csrfTokenMeta = document.querySelector('meta[name="csrf-token"]');
    if (!csrfTokenMeta) {
        csrfTokenMeta = document.createElement('meta');
        csrfTokenMeta.name = 'csrf-token';
        csrfTokenMeta.content = '{{ csrf_token() }}';
        document.head.appendChild(csrfTokenMeta);
        console.log('CSRF token meta tag created');
    }
});
</script>
@endsection
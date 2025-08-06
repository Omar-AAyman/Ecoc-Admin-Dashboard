@extends('layouts.panel')

@section('title', 'All Clients')

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
        padding: 0.5rem 1rem;
        font-size: 0.875rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
    }

    .btn-primary {
        background-color: #000b43;
        border-color: #000b43;
        color: #ffffff;
    }

    .btn-primary:hover {
        background-color: #1d4ed8;
        border-color: #1d4ed8;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
    }

    .btn-primary:active {
        transform: translateY(0);
        box-shadow: 0 2px 8px rgba(37, 99, 235, 0.2);
    }

    .btn-secondary {
        background-color: #6b7280;
        border-color: #6b7280;
        color: #ffffff;
    }

    .btn-secondary:hover {
        background-color: #4b5563;
        border-color: #4b5563;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(107, 114, 128, 0.3);
    }

    .btn-secondary:active {
        transform: translateY(0);
        box-shadow: 0 2px 8px rgba(107, 114, 128, 0.2);
    }

    .btn-danger {
        background-color: #dc2626;
        border-color: #dc2626;
        color: #ffffff;
    }

    .btn-danger:hover {
        background-color: #b91c1c;
        border-color: #b91c1c;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(220, 38, 38, 0.3);
    }

    .btn-danger:active {
        transform: translateY(0);
        box-shadow: 0 2px 8px rgba(220, 38, 38, 0.2);
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

    .status-badge {
        display: inline-block;
        padding: 0.35em 0.65em;
        font-size: 0.75em;
        font-weight: 700;
        line-height: 1;
        color: #fff;
        text-align: center;
        white-space: nowrap;
        vertical-align: baseline;
        border-radius: 0.25rem;
    }

    .status-active {
        background-color: #22c55e;
    }

    .status-inactive {
        background-color: #ef4444;
    }

    .client-logo {
        width: 40px;
        height: 40px;
        object-fit: contain;
        border-radius: 4px;
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
            width: 100%;
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

        .client-logo {
            width: 30px;
            height: 30px;
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
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <h2 class="my-3 my-md-0">
                            <i class="fas fa-users me-2"></i>All Clients
                        </h2>
                        @can('create', \App\Models\User::class)
                        <div class="d-flex gap-2">
                            <a href="{{ route('clients.create') }}" class="btn btn-primary d-flex align-items-center">
                                <i class="fas fa-plus me-2"></i>Add Client
                            </a>
                        </div>
                        @endcan
                    </div>
                </div>
            </div>

            <!-- Alert Messages -->
            @include('components.alerts')

            @if ($clients->isEmpty())
            <div class="card">
                <div class="card-body text-center py-4">
                    <div class="text-muted">
                        <i class="fas fa-search fa-2x mb-3"></i>
                        <p class="mb-0">No clients found</p>
                    </div>
                </div>
            </div>
            @else
            <!-- Filters and Pagination -->
            <div class="card mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <div class="search-container">
                            <input type="text" id="client-search" class="form-control" placeholder="Search by name, email, or company..." value="{{ request('search') }}">
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

            <!-- Clients Table -->
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" id="clients-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Logo</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Company</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="clients-table-body">
                                @include('clients.partials.table')
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-between align-items-center mt-4">
                <div class="text-muted small" id="pagination-info">
                    Showing {{ $clients->firstItem() ?? 0 }} to {{ $clients->lastItem() ?? 0 }} of {{ $clients->total() }} entries
                </div>
                <div id="pagination-links">
                    {{ $clients->appends(request()->all())->links('pagination::bootstrap-5') }}
                </div>
            </div>

            <!-- Delete Confirmation Modal -->
            <div class="modal fade" id="deleteClientModal" tabindex="-1" aria-labelledby="deleteClientModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="deleteClientModalLabel">Confirm Client Deletion</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            Are you sure you want to delete client <strong id="deleteClientName"></strong>? This action cannot be undone.
                            <p class="warning-text">All associated data will be permanently removed.</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary d-inline w-25" data-bs-dismiss="modal">Cancel</button>
                            <form id="deleteClientForm" action="" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger">Delete Client</button>
                            </form>
                        </div>
                    </div>
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
    // Ensure Bootstrap is loaded
    if (typeof bootstrap === 'undefined') {
        console.error('Bootstrap JavaScript is not loaded. Please check your script inclusions.');
        alert('Error: Bootstrap JavaScript is not loaded. Please contact support.');
        return;
    }

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

    // Function to fetch clients via AJAX
    function fetchClients(search = '', perPage = 10, page = 1) {
        $.ajax({
            url: '{{ route("clients.index") }}',
            type: 'GET',
            data: {
                search: search,
                per_page: perPage,
                page: page,
                ajax: true
            },
            success: function(response) {
                // Update table body
                $('#clients-table-body').html(response.table);
                // Update pagination links
                $('#pagination-links').html(response.pagination);
                // Update showing info
                $('#pagination-info').text(`Showing ${response.first_item} to ${response.last_item} of ${response.total} entries`);
                // Reattach delete confirmation handlers
                attachDeleteHandlers();
            },
            error: function(xhr) {
                console.error('AJAX error:', xhr);
                alert('An error occurred while fetching clients. Please try again.');
            }
        });
    }

    // Attach delete confirmation handlers
    function attachDeleteHandlers() {
        document.querySelectorAll('.delete-btn').forEach(button => {
            button.removeEventListener('click', handleDeleteClick); // Remove previous listeners to prevent duplicates
            button.addEventListener('click', handleDeleteClick);
        });
    }

    function handleDeleteClick() {
        const clientId = this.dataset.clientId;
        const clientName = this.dataset.clientName;
        const form = document.getElementById('deleteClientForm');
        const deleteUrl = '{{ route("clients.destroy", ":id") }}'.replace(':id', clientId);
        form.action = deleteUrl;
        document.getElementById('deleteClientName').textContent = clientName;
        console.log('Delete button clicked for client ID:', clientId, 'URL:', deleteUrl);

        // Initialize modal
        try {
            const modalElement = document.getElementById('deleteClientModal');
            const modal = new bootstrap.Modal(modalElement, {
                backdrop: true,
                keyboard: true
            });
            modal.show();
        } catch (error) {
            console.error('Error initializing modal:', error);
            alert('Error: Unable to open delete confirmation modal. Please try again.');
        }
    }

    // Initial delete handlers
    attachDeleteHandlers();

    // Search functionality
    const searchInput = document.getElementById('client-search');
    const debouncedSearch = debounce(function() {
        const searchTerm = searchInput.value;
        const perPage = document.getElementById('per-page').value;
        fetchClients(searchTerm, perPage);
    }, 300);
    searchInput.addEventListener('input', debouncedSearch);

    // Per-page change handler
    document.getElementById('per-page').addEventListener('change', function() {
        const perPage = this.value;
        const searchTerm = document.getElementById('client-search').value;
        fetchClients(searchTerm, perPage);
    });

    // Pagination link click handler
    document.getElementById('pagination-links').addEventListener('click', function(e) {
        e.preventDefault();
        if (e.target.tagName === 'A' && e.target.getAttribute('href')) {
            const url = new URL(e.target.getAttribute('href'));
            const page = url.searchParams.get('page') || 1;
            const searchTerm = document.getElementById('client-search').value;
            const perPage = document.getElementById('per-page').value;
            fetchClients(searchTerm, perPage, page);
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
@extends('layouts.panel')

@section('title', 'Activity Logs')

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
        border-radius: 10px;
        border: 1px solid #d1d5db;
        transition: border-color 0.2s ease, box-shadow 0.2s ease;
    }

    .form-control:focus {
        border-color: #000b43;
        box-shadow: 0 0 0 0.2rem rgba(0, 11, 67, 0.2);
        outline: none;
    }

    .btn {
        border-radius: 8px;
        padding: 0.5rem 1rem;
        font-weight: 500;
        transition: background-color 0.2s ease, transform 0.2s ease;
    }

    .btn-primary {
        background-color: #000b43;
        border-color: #000b43;
        color: #f8f8f8;
    }

    .btn-primary:hover {
        background-color: #1e3a8a;
        border-color: #1e3a8a;
        transform: translateY(-2px);
    }


    .btn-outline-secondary {
        border-color: #d1d5db;
        color: #1f2937;
    }

    .btn-outline-secondary:hover {
        border-color: #000b43;
        color: #000b43;
    }

    .btn-outline-danger {
        border-color: #d1d5db;
        color: #1f2937;
    }

    .btn-outline-danger:hover {
        border-color: #000b43;
        color: #000b43;
    }

    .modal-content {
        border-radius: 16px;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
    }

    .modal-body pre {
        background: #f8f9fa;
        padding: 15px;
        border-radius: 10px;
        font-size: 0.875rem;
        max-height: 400px;
        overflow-y: auto;
    }

    @media (max-width: 768px) {
        .hero-header h2 {
            font-size: 1.75rem;
        }
        .btn {
            padding: 0.5rem 1rem;
        }
        .table th,
        .table td {
            padding: 0.75rem;
            font-size: 0.875rem;
        }
        .card {
            margin-bottom: 1rem;
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
                            <i class="fas fa-history me-2"></i>Activity Logs
                        </h2>
                        <div class="d-flex gap-2">
                            <button class="btn btn-outline-secondary d-flex align-items-center" type="button" data-bs-toggle="collapse" data-bs-target="#filterSection" aria-expanded="false" aria-controls="filterSection">
                                <i class="fas fa-filter me-2"></i> Filters
                                <i class="fas fa-chevron-down ms-2 small"></i>
                            </button>
                            <a href="{{ route('activity-logs.index') }}" class="btn btn-outline-danger">
                                <i class="fas fa-redo me-2"></i> Reset
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Alert Messages -->
            @include('components.alerts')

            <!-- Filters Section -->
            <div class="collapse {{ request()->hasAny(['action', 'model_type', 'user_id', 'search', 'from', 'to']) ? 'show' : '' }} mb-4" id="filterSection">
                <div class="card">
                    <div class="card-header bg-light py-3">
                        <h5 class="mb-0">Advanced Filters</h5>
                    </div>
                    <div class="card-body">
                        <form id="filter-form" method="GET" action="{{ route('activity-logs.index') }}" class="row g-3">
                            <div class="col-md-3 my-1">
                                <label for="filter_action" class="form-label small fw-bold">Action</label>
                                <select name="action" id="filter_action" class="form-control">
                                    <option value="">All Actions</option>
                                    @foreach($actions as $action)
                                    <option value="{{ $action }}" {{ request('action') == $action ? 'selected' : '' }}>{{ $action }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3 my-1">
                                <label for="filter_model_type" class="form-label small fw-bold">Model Type</label>
                                <select name="model_type" id="filter_model_type" class="form-control">
                                    <option value="">All Models</option>
                                    @foreach($modelTypes as $key => $value)
                                    <option value="{{ $key }}" {{ request('model_type') == $key ? 'selected' : '' }}>{{ $value }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3 my-1">
                                <label for="filter_user_id" class="form-label small fw-bold">User</label>
                                <select name="user_id" id="filter_user_id" class="form-control">
                                    <option value="">All Users</option>
                                    @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>{{ $user->first_name }} {{ $user->last_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3 my-1">
                                <label for="filter_search" class="form-label small fw-bold">Search Description</label>
                                <input type="text" name="search" id="filter_search" class="form-control" value="{{ request('search') }}" placeholder="Search description...">
                            </div>
                            <div class="col-md-3 my-1">
                                <label for="filter_from" class="form-label small fw-bold">From Date</label>
                                <input type="date" name="from" id="filter_from" class="form-control" value="{{ request('from') }}">
                            </div>
                            <div class="col-md-3 my-1">
                                <label for="filter_to" class="form-label small fw-bold">To Date</label>
                                <input type="date" name="to" id="filter_to" class="form-control" value="{{ request('to') }}">
                            </div>
                            <div class="col-md-3 d-flex align-items-end my-1">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-search me-2"></i>Apply Filters
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Activity Logs Table -->
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th class="border-0">ID</th>
                                    <th class="border-0">User</th>
                                    <th class="border-0">Action</th>
                                    <th class="border-0">Description</th>
                                    <th class="border-0">Model Type</th>
                                    <th class="border-0">Model ID</th>
                                    <th class="border-0">Date</th>
                                    <th class="border-0">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($logs as $log)
                                <tr>
                                    <td>{{ $log->id }}</td>
                                    <td>{{ $log->user ? $log->user->first_name . ' ' . $log->user->last_name : 'N/A' }}</td>
                                    <td>{{ $log->action }}</td>
                                    <td>{{ $log->description }}</td>
                                    <td>{{ str_replace('App\\Models\\', '', $log->model_type) }}</td>
                                    <td>{{ $log->model_id }}</td>
                                    <td>{{ $log->created_at->format('d M Y H:i') }}</td>
                                    <td>
                                        <div class="d-flex gap-2 justify-content-center">
                                            <button class="btn btn-sm btn-outline-primary view-details" data-id="{{ $log->id }}" title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="fas fa-search fa-2x mb-3"></i>
                                            <p class="mb-0">No activity logs found</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-between align-items-center mt-4">
                <div class="text-muted small">
                    Showing {{ $logs->firstItem() ?? 0 }} to {{ $logs->lastItem() ?? 0 }} of {{ $logs->total() }} entries
                </div>
                {{ $logs->appends(request()->all())->links('pagination::bootstrap-5') }}
            </div>

            <!-- Modal -->
            <div class="modal fade" id="detailsModal" tabindex="-1" aria-labelledby="detailsModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="detailsModalLabel">Activity Log Details</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <p><strong>ID:</strong> <span id="modal-id"></span></p>
                            <p><strong>User:</strong> <span id="modal-user"></span></p>
                            <p><strong>Action:</strong> <span id="modal-action"></span></p>
                            <p><strong>Description:</strong> <span id="modal-description"></span></p>
                            <p><strong>Model Type:</strong> <span id="modal-model-type"></span></p>
                            <p><strong>Model ID:</strong> <span id="modal-model-id"></span></p>
                            <p><strong>Date:</strong> <span id="modal-created-at"></span></p>
                            <p><strong>Details:</strong></p>
                            <pre id="modal-details"></pre>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const filterToggleBtn = document.querySelector('[data-bs-toggle="collapse"]');

    // Ensure collapse toggle works
    filterToggleBtn.addEventListener('click', function() {
        const isExpanded = this.getAttribute('aria-expanded') === 'true';
        this.querySelector('.fas.fa-chevron-down').classList.toggle('fa-chevron-up', !isExpanded);
        this.querySelector('.fas.fa-chevron-down').classList.toggle('fa-chevron-down', isExpanded);
    });

    // Inject CSRF token meta tag if it doesn't exist
    let csrfTokenMeta = document.querySelector('meta[name="csrf-token"]');
    if (!csrfTokenMeta) {
        csrfTokenMeta = document.createElement('meta');
        csrfTokenMeta.name = 'csrf-token';
        csrfTokenMeta.content = '{{ csrf_token() }}';
        document.head.appendChild(csrfTokenMeta);
    }

    // Handle View Details button click
    $('.view-details').on('click', function() {
        const logId = $(this).data('id');
        $.ajax({
            url: '/activity-logs/' + logId,
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': csrfTokenMeta.content,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function(data) {
                $('#modal-id').text(data.id);
                $('#modal-user').text(data.user);
                $('#modal-action').text(data.action);
                $('#modal-description').text(data.description);
                $('#modal-model-type').text(data.model_type);
                $('#modal-model-id').text(data.model_id);
                $('#modal-created-at').text(data.created_at);
                $('#modal-details').text(JSON.stringify(data.details, null, 2));
                $('#detailsModal').modal('show');
            },
            error: function(xhr) {
                console.error('Error fetching log details:', xhr.responseText);
                alert('Failed to load log details.');
            }
        });
    });
});
</script>
@endsection
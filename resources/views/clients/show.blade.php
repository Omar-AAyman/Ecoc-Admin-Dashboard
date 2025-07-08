@extends('layouts.panel')

@section('title', 'Client Details')

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
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
    }

    .card-header {
        background-color: #f8f9fa;
        border-bottom: none;
        padding: 1.5rem;
        font-weight: 600;
        font-size: 1.25rem;
    }

    .card-body {
        padding: 1.5rem;
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

    .btn {
        border-radius: 8px;
        margin-left: 2px;
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

    .status-rental-active {
        background-color: #22c55e;
    }

    .status-rental-ended {
        background-color: #6b7280;
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

    .client-details-grid {
        display: grid;
        grid-template-columns: 1fr 2fr;
        gap: 1rem;
    }

    .client-details-grid label {
        font-weight: 600;
    }

    .client-details-grid span {
        display: inline-block;
    }

    .client-logo {
        max-width: 150px;
        max-height: 150px;
        object-fit: contain;
        border-radius: 8px;
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

        .modal-title {
            font-size: 1.1rem;
        }

        .client-details-grid {
            grid-template-columns: 1fr;
        }

        .client-logo {
            max-width: 100px;
            max-height: 100px;
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
                            <i class="fas fa-user me-2"></i>Client Details
                        </h2>
                        <div class="d-flex gap-2">
                            @if (auth()->user()->hasAnyRole(['super_admin', 'ceo']))
                            <a href="{{ route('clients.edit', $client->id) }}" class="btn btn-primary d-flex align-items-center" title="Edit Client">
                                <i class="fas fa-edit me-2"></i>Edit Client
                            </a>
                            <button type="button" class="btn btn-danger d-flex align-items-center delete-btn" data-client-id="{{ $client->id }}" data-client-name="{{ $client->full_name }}" data-bs-toggle="modal" data-bs-target="#deleteModal" title="Delete Client">
                                <i class="fas fa-trash me-2"></i>Delete Client
                            </button>
                            @endif
                            <a href="{{ route('clients.index') }}" class="btn btn-secondary d-flex align-items-center">
                                <i class="fas fa-arrow-left me-2"></i>Back to Clients
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Alert Messages -->
            @include('components.alerts')

            <!-- Client Details -->
            <div class="card mb-4">
                <div class="card-header">Client Information</div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-12 col-md-4 mb-4 mb-md-0">
                            <img src="{{ $client->image_url }}" alt="{{ $client->company?->name ?? 'Logo' }}" class="client-logo">
                        </div>
                        <div class="col-12 col-md-8">
                            <div class="client-details-grid">
                                <label>Full Name</label>
                                <span>{{ $client->full_name }}</span>
                                <label>Email</label>
                                <span>{{ $client->email }}</span>
                                @if ($client->phone)
                                <label>Phone</label>
                                <span>{{ $client->phone }}</span>
                                @endif
                                <label>Status</label>
                                <span>
                                    <span class="status-badge status-{{ $client->status }}">{{ ucfirst($client->status) }}</span>
                                </span>
                                <label>Company</label>
                                <span>{{ $client->company?->name ?? 'N/A' }}</span>
                                <label>Role</label>
                                <span>{{ $client->role->display_name ?? 'N/A' }}</span>
                                <label>Created At</label>
                                <span>{{ $client->created_at->format('d/m/Y H:i') }}</span>
                                <label>Updated At</label>
                                <span>{{ $client->updated_at->format('d/m/Y H:i') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tank Rentals -->
            <div class="card">
                <div class="card-header">Associated Tank Rentals</div>
                <div class="card-body">
                    @if ($client->company && $client->company->tankRentals->isNotEmpty())
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>Tank Number</th>
                                        <th>Start Date</th>
                                        <th>End Date</th>
                                        <th>Product</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($client->company->tankRentals as $rental)
                                    <tr>
                                        <td>{{ $rental->tank?->number ?? 'N/A' }}</td>
                                        <td>{{ $rental->start_date?->format('Y-m-d') ?? 'N/A' }}</td>
                                        <td>{{ $rental->end_date?->format('Y-m-d') ?? 'N/A' }}</td>
                                        <td>{{ $rental->product?->name ?? 'None' }}</td>
                                        <td>
                                            <span class="status-badge status-{{ $rental->end_date ? 'rental-ended' : 'rental-active' }}">
                                                {{ $rental->end_date ? 'Rental Ended' : 'Rental Active' }}
                                            </span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-search fa-2x mb-3"></i>
                            <p class="mb-0">No tank rentals found for this client</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Confirm Client Deletion</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete client <strong id="deleteClientName"></strong>?
                <p class="warning-text">This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteClientForm" action="" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete Client</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Client show script loaded');

        // Delete modal handler
        const deleteButtons = document.querySelectorAll('.delete-btn');
        if (deleteButtons.length === 0) {
            console.warn('No delete buttons found on the page');
        }
        deleteButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                const clientId = this.dataset.clientId;
                const clientName = this.dataset.clientName;
                const form = document.getElementById('deleteClientForm');
                const deleteUrl = '{{ route("clients.destroy", ":id") }}'.replace(':id', clientId);
                if (form) {
                    form.action = deleteUrl;
                    document.getElementById('deleteClientName').textContent = clientName;
                    console.log('Delete button clicked for client ID:', clientId, 'URL:', deleteUrl);
                } else {
                    console.error('Delete form not found');
                }
            });
        });

        // Form submission debugging
        const deleteForm = document.getElementById('deleteClientForm');
        if (deleteForm) {
            deleteForm.addEventListener('submit', function(e) {
                console.log('Delete form submitted with action:', this.action);
            });
        } else {
            console.error('Delete form not found');
        }

        // Test Bootstrap modal availability
        if (typeof bootstrap === 'undefined') {
            console.error('Bootstrap JavaScript is not loaded');
        } else {
            console.log('Bootstrap JavaScript is loaded');
        }
    });
</script>
@endsection

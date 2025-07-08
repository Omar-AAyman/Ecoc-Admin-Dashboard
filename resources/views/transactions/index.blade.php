@extends('layouts.panel')

@section('title', 'Transactions')

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
        /* background-color: #ffffff; */
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

    .badge {
        font-weight: 500;
        padding: 0.5rem 1rem;
        border-radius: 12px;
        font-size: 0.875rem;
    }

    .badge-loading {
        background-color: #008001;
        color: #f8f8f8;
    }

    .badge-discharging {
        background-color: #fef200;
        color: #1f2937;
    }

    .badge-transfer {
        background-color: #000b43;
        color: #f8f8f8;
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

    .btn-outline-secondary {
        border-color: #d1d5db;
        color: #1f2937;
    }

    .btn-outline-secondary:hover {
        border-color: #2563eb;
        color: #2563eb;
        transform: translateY(-2px);
    }

    .btn-outline-danger {
        border-color: #d1d5db;
        color: #1f2937;
    }

    .btn-outline-danger:hover {
        border-color: #dc2626;
        color: #dc2626;
        transform: translateY(-2px);
    }

    .btn-light {
        background-color: #f8f9fa;
        border-color: #d1d5db;
        color: #1f2937;
    }

    .btn-light:hover {
        background-color: #e5e7eb;
        border-color: #2563eb;
        color: #2563eb;
        transform: translateY(-2px);
    }

    .filter-toggle .fas.fa-chevron-down {
        transition: transform 0.3s ease;
    }

    .filter-toggle.collapsed .fas.fa-chevron-down {
        transform: rotate(180deg);
    }

    #statisticsSection {
        transition: opacity 0.3s ease, max-height 0.5s ease;
        overflow: hidden;
        max-height: 1000px;
        opacity: 1;
    }

    #statisticsSection.hiding {
        max-height: 0;
        opacity: 0;
        margin-bottom: 0 !important;
    }

    #statsLoader,
    #statsContent {
        transition: opacity 0.4s ease;
    }

    #statsContent.fade-in {
        animation: fadeIn 0.5s ease forwards;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    #statsContent .card {
        opacity: 0;
        transform: translateY(15px);
        border-left: 4px solid;
    }

    #statsContent.animate-cards .card {
        animation: cardAppear 0.5s ease forwards;
    }

    #statsContent.animate-cards .card:nth-child(1) {
        animation-delay: 0.05s;
        border-left-color: #000b43;
    }

    #statsContent.animate-cards .card:nth-child(2) {
        animation-delay: 0.1s;
        border-left-color: #008001;
    }

    #statsContent.animate-cards .card:nth-child(3) {
        animation-delay: 0.15s;
        border-left-color: #fef200;
    }

    #statsContent.animate-cards .card:nth-child(4) {
        animation-delay: 0.2s;
        border-left-color: #1e3a8a;
    }

    @keyframes cardAppear {
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .icon-circle {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        background: #e0e7ff;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: transform 0.2s ease;
    }

    .icon-circle:hover {
        transform: scale(1.1);
    }

    .icon-circle i {
        color: #000b43;
        font-size: 1.5rem;
    }

    .icon-circle.bg-loading i {
        color: #008001;
    }

    .icon-circle.bg-discharging i {
        color: #fef200;
    }

    .icon-circle.bg-transfer i {
        color: #1e3a8a;
    }

    .transaction-detail {
        display: block;
        font-size: 0.75rem;
        color: #6b7280;
        margin-top: 0.25rem;
    }

    @media (max-width: 768px) {
        .hero-header h2 {
            font-size: 1.75rem;
        }
        .btn {
            padding: 0.4rem 0.8rem;
            font-size: 0.875rem;
        }
        .table th,
        .table td {
            padding: 0.75rem;
            font-size: 0.875rem;
        }
        .card {
            margin-bottom: 1rem;
        }
        .transaction-detail {
            font-size: 0.7rem;
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
                            <i class="fas fa-exchange-alt me-2"></i>Transaction Management
                        </h2>
                        <div class="d-flex gap-2">
                            @can('create', \App\Models\Transaction::class)
                            <a href="{{ route('transactions.create') }}" class="btn btn-primary d-flex align-items-center">
                                <i class="fas fa-plus me-2"></i>New Transaction
                            </a>
                            @endcan
                            <button class="btn btn-light d-flex align-items-center" type="button" id="showStatsBtn">
                                <i class="fas fa-chart-bar me-2"></i>Show Statistics
                            </button>
                            <button class="btn btn-outline-secondary d-flex align-items-center filter-toggle" type="button" data-bs-toggle="collapse" data-bs-target="#filterSection" aria-expanded="{{ request()->hasAny(['type', 'tank_id', 'destination_tank_id', 'original_vessel_id', 'company_id', 'product_id', 'engineer_id', 'technician_id', 'transport_type', 'search', 'from', 'to']) ? 'true' : 'false' }}" aria-controls="filterSection">
                                <i class="fas fa-filter me-2"></i>Filters
                                <i class="fas fa-chevron-down ms-2 small"></i>
                            </button>
                            <a href="{{ route('transactions.index') }}" class="btn btn-outline-danger">
                                <i class="fas fa-redo me-2"></i>Reset
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Alert Messages -->
            @include('components.alerts')

            <!-- Statistics Section -->
            <div id="statisticsSection" class="mb-4" style="display: none;">
                <div class="card mb-4">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                        <h5 class="mb-0 text-primary">
                            <i class="fas fa-chart-line me-2"></i>Transaction Statistics
                        </h5>
                        <button type="button" class="btn-close" id="closeStatsBtn" aria-label="Close"></button>
                    </div>
                    <div class="card-body">
                        <div class="text-center py-5" id="statsLoader">
                            <div class="spinner-border text-primary" role="status">
                            </div>
                            <p class="mt-2 text-muted">Loading statistics...</p>
                        </div>
                        <div id="statsContent" style="display: none;">
                            <div class="row g-4">
                                <div class="col-md-3">
                                    <div class="card h-100">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div>
                                                    <h6 class="text-muted mb-2">Total Transactions</h6>
                                                    <h3 class="mb-0" id="totalTransactions">-</h3>
                                                </div>
                                                <div class="icon-circle">
                                                    <i class="fas fa-exchange-alt"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card h-100">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div>
                                                    <h6 class="text-muted mb-2">Loading Transactions</h6>
                                                    <h3 class="mb-0" id="loadingTransactions">-</h3>
                                                </div>
                                                <div class="icon-circle bg-loading">
                                                    <i class="fas fa-arrow-up"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card h-100">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div>
                                                    <h6 class="text-muted mb-2">Discharging Transactions</h6>
                                                    <h3 class="mb-0" id="dischargingTransactions">-</h3>
                                                </div>
                                                <div class="icon-circle bg-discharging">
                                                    <i class="fas fa-arrow-down"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card h-100">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div>
                                                    <h6 class="text-muted mb-2">Transfer Transactions</h6>
                                                    <h3 class="mb-0" id="transferTransactions">-</h3>
                                                </div>
                                                <div class="icon-circle bg-transfer">
                                                    <i class="fas fa-exchange-alt"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters Section -->
            <div class="collapse {{ request()->hasAny(['type', 'tank_id', 'destination_tank_id', 'original_vessel_id', 'company_id', 'product_id', 'engineer_id', 'technician_id', 'transport_type', 'search', 'from', 'to']) ? 'show' : '' }} mb-4" id="filterSection">
                <div class="card">
                    <div class="card-header bg-light py-3">
                        <h5 class="mb-0">Advanced Filters</h5>
                    </div>
                    <div class="card-body">
                        <form id="filter-form" method="GET" action="{{ route('transactions.index') }}" class="row g-3">
                            <div class="col-md-3 my-1">
                                <label for="filter_search" class="form-label small fw-bold">Search</label>
                                <input type="text" name="search" id="filter_search" class="form-control" value="{{ request('search') }}" placeholder="Search by company, product, or tank...">
                            </div>
                            <div class="col-md-3 my-1">
                                <label for="filter_type" class="form-label small fw-bold">Transaction Type</label>
                                <select name="type" id="filter_type" class="form-control">
                                    <option value="">All Types</option>
                                    <option value="loading" {{ request('type') === 'loading' ? 'selected' : '' }}>Loading</option>
                                    <option value="discharging" {{ request('type') === 'discharging' ? 'selected' : '' }}>Discharging</option>
                                    <option value="transfer" {{ request('type') === 'transfer' ? 'selected' : '' }}>Transfer</option>
                                </select>
                            </div>
                            <div class="col-md-3 my-1">
                                <label for="filter_tank_id" class="form-label small fw-bold">Source Tank</label>
                                <select name="tank_id" id="filter_tank_id" class="form-control">
                                    <option value="">All Tanks</option>
                                    @foreach($tanks as $tank)
                                    <option value="{{ $tank->id }}" {{ request('tank_id') == $tank->id ? 'selected' : '' }}>{{ $tank->number }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3 my-1">
                                <label for="filter_destination_tank_id" class="form-label small fw-bold">Destination Tank</label>
                                <select name="destination_tank_id" id="filter_destination_tank_id" class="form-control">
                                    <option value="">All Tanks</option>
                                    @foreach($tanks as $tank)
                                    <option value="{{ $tank->id }}" {{ request('destination_tank_id') == $tank->id ? 'selected' : '' }}>{{ $tank->number }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3 my-1">
                                <label for="filter_original_vessel_id" class="form-label small fw-bold">Original Vessel</label>
                                <select name="original_vessel_id" id="filter_original_vessel_id" class="form-control">
                                    <option value="">All Vessels</option>
                                    @foreach($vessels as $vessel)
                                    <option value="{{ $vessel->id }}" {{ request('original_vessel_id') == $vessel->id ? 'selected' : '' }}>{{ $vessel->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3 my-1">
                                <label for="filter_company_id" class="form-label small fw-bold">Company</label>
                                <select name="company_id" id="filter_company_id" class="form-control">
                                    <option value="">All Companies</option>
                                    @foreach($companies as $company)
                                    <option value="{{ $company->id }}" {{ request('company_id') == $company->id ? 'selected' : '' }}>{{ $company->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3 my-1">
                                <label for="filter_product_id" class="form-label small fw-bold">Product</label>
                                <select name="product_id" id="filter_product_id" class="form-control">
                                    <option value="">All Products</option>
                                    @foreach($products as $product)
                                    <option value="{{ $product->id }}" {{ request('product_id') == $product->id ? 'selected' : '' }}>{{ $product->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3 my-1">
                                <label for="filter_engineer_id" class="form-label small fw-bold">Engineer</label>
                                <select name="engineer_id" id="filter_engineer_id" class="form-control">
                                    <option value="">All Engineers</option>
                                    @foreach($engineers as $engineer)
                                    <option value="{{ $engineer->id }}" {{ request('engineer_id') == $engineer->id ? 'selected' : '' }}>{{ $engineer->first_name }} {{ $engineer->last_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3 my-1">
                                <label for="filter_technician_id" class="form-label small fw-bold">Technician</label>
                                <select name="technician_id" id="filter_technician_id" class="form-control">
                                    <option value="">All Technicians</option>
                                    @foreach($technicians as $technician)
                                    <option value="{{ $technician->id }}" {{ request('technician_id') == $technician->id ? 'selected' : '' }}>{{ $technician->first_name }} {{ $technician->last_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3 my-1">
                                <label for="filter_transport_type" class="form-label small fw-bold">Transport Type</label>
                                <select name="transport_type" id="filter_transport_type" class="form-control">
                                    <option value="">All Transport Types</option>
                                    <option value="vessel" {{ request('transport_type') === 'vessel' ? 'selected' : '' }}>Vessel</option>
                                    <option value="truck" {{ request('transport_type') === 'truck' ? 'selected' : '' }}>Truck</option>
                                </select>
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

            <!-- Transactions Table -->
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th class="border-0">ID</th>
                                    <th class="border-0">Type</th>
                                    <th class="border-0">Source Tank</th>
                                    <th class="border-0">Original Vessel</th>
                                    <th class="border-0">Company</th>
                                    <th class="border-0">Product</th>
                                    <th class="border-0">Quantity (MT)</th>
                                    <th class="border-0">Date</th>
                                    <th class="border-0">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($transactions as $transaction)
                                <tr>
                                    <td>{{ $transaction->id }}</td>
                                    <td>
                                        <span class="badge badge-{{ $transaction->type }}">{{ ucfirst($transaction->type) }}</span>
                                        <span class="transaction-detail">
                                            @if($transaction->type === 'transfer')
                                                {{ $transaction->tank->number ?? 'N/A' }} -> {{ $transaction->destinationTank->number ?? 'N/A' }}
                                            @elseif($transaction->type === 'loading')
                                                {{ $transaction->shipment->transport_type === 'vessel' ? ($transaction->shipment->vessel->name ?? 'Vessel N/A') : 'Truck' }} -> {{ $transaction->tank->number ?? 'N/A' }}
                                            @elseif($transaction->type === 'discharging')
                                                {{ $transaction->tank->number ?? 'N/A' }} -> {{ $transaction->delivery->transport_type === 'vessel' ? ($transaction->delivery->vessel->name ?? 'Vessel N/A') : 'Truck' }}
                                            @endif
                                        </span>
                                    </td>
                                    <td>{{ $transaction->tank->number ?? 'N/A' }}</td>
                                    <td>{{ $transaction->originalVessel->name ?? 'N/A' }}</td>
                                    <td>{{ $transaction->company->name ?? 'N/A' }}</td>
                                    <td>{{ $transaction->product->name ?? 'N/A' }}</td>
                                    <td>{{ number_format($transaction->quantity, 2) }}</td>
                                    <td>{{ $transaction->date->format('d M Y H:i') }}</td>
                                    <td>
                                        <div class="d-flex gap-2 justify-content-center">
                                            <a href="{{ route('transactions.show', $transaction->id) }}" class="btn btn-sm btn-outline-primary" title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="9" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="fas fa-search fa-2x mb-3"></i>
                                            <p class="mb-0">No transactions found</p>
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
                    Showing {{ $transactions->firstItem() ?? 0 }} to {{ $transactions->lastItem() ?? 0 }} of {{ $transactions->total() }} entries
                </div>
                {{ $transactions->appends(request()->all())->links('pagination::bootstrap-5') }}
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
    const showStatsBtn = document.getElementById('showStatsBtn');
    const closeStatsBtn = document.getElementById('closeStatsBtn');
    const statisticsSection = document.getElementById('statisticsSection');
    const statsLoader = document.getElementById('statsLoader');
    const statsContent = document.getElementById('statsContent');
    const filterToggleBtn = document.querySelector('.filter-toggle');
    const filterSection = document.getElementById('filterSection');

    // Inject CSRF token meta tag if it doesn't exist
    let csrfTokenMeta = document.querySelector('meta[name="csrf-token"]');
    if (!csrfTokenMeta) {
        csrfTokenMeta = document.createElement('meta');
        csrfTokenMeta.name = 'csrf-token';
        csrfTokenMeta.content = '{{ csrf_token() }}';
        document.head.appendChild(csrfTokenMeta);
        console.log('CSRF token meta tag created');
    }

    // Ensure single event listener for filter toggle
    filterToggleBtn.addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation(); // Prevent event bubbling
        console.log('Filter toggle clicked, aria-expanded:', this.getAttribute('aria-expanded'));
        console.log('Filter section classList:', filterSection.classList);
        try {
            const bsCollapse = bootstrap.Collapse.getOrCreateInstance(filterSection, {
                toggle: false // Prevent automatic toggle
            });
            console.log('Bootstrap Collapse instance:', bsCollapse);
            // Manually toggle collapse
            if (filterSection.classList.contains('show')) {
                bsCollapse.hide();
                filterToggleBtn.setAttribute('aria-expanded', 'false');
                filterToggleBtn.classList.add('collapsed');
            } else {
                bsCollapse.show();
                filterToggleBtn.setAttribute('aria-expanded', 'true');
                filterToggleBtn.classList.remove('collapsed');
            }
        } catch (error) {
            console.error('Error with Bootstrap Collapse:', error);
        }
    }, { once: false }); // Ensure listener persists

    // Monitor collapse events
    filterSection.addEventListener('show.bs.collapse', function() {
        console.log('Collapse show event triggered');
        filterToggleBtn.setAttribute('aria-expanded', 'true');
        filterToggleBtn.classList.remove('collapsed');
    });

    filterSection.addEventListener('hide.bs.collapse', function() {
        console.log('Collapse hide event triggered');
        filterToggleBtn.setAttribute('aria-expanded', 'false');
        filterToggleBtn.classList.add('collapsed');
    });

    filterSection.addEventListener('shown.bs.collapse', function() {
        console.log('Collapse fully shown');
    });

    filterSection.addEventListener('hidden.bs.collapse', function() {
        console.log('Collapse fully hidden');
    });

    // Handle statistics toggle
    function formatNumber(num) {
        return new Intl.NumberFormat().format(Math.round(num));
    }

    function loadStatistics() {
        statisticsSection.style.display = 'block';
        statisticsSection.classList.remove('hiding');
        statsLoader.style.display = 'block';
        statsLoader.style.opacity = '1';
        statsContent.style.display = 'none';
        statsContent.classList.remove('fade-in', 'animate-cards');

        const urlParams = new URLSearchParams(window.location.search);
        const filterParams = new URLSearchParams();
        for (const [key, value] of urlParams.entries()) {
            filterParams.append(key, value);
        }

        const statsUrl = '{{ route('transactions.statistics') }}?' + filterParams.toString();
        console.log('Fetching statistics from:', statsUrl);

        fetch(statsUrl, {
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': csrfTokenMeta.content,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
            .then(response => {
                console.log('Response status:', response.status, response.statusText);
                if (!response.ok) {
                    throw new Error(`HTTP error! Status: ${response.status} ${response.statusText}`);
                }
                return response.json();
            })
            .then(data => {
                console.log('Received data:', data);
                document.getElementById('totalTransactions').textContent = formatNumber(data.totalTransactions);
                document.getElementById('loadingTransactions').textContent = formatNumber(data.transactionsByType.find(t => t.type === 'loading')?.count || 0);
                document.getElementById('dischargingTransactions').textContent = formatNumber(data.transactionsByType.find(t => t.type === 'discharging')?.count || 0);
                document.getElementById('transferTransactions').textContent = formatNumber(data.transactionsByType.find(t => t.type === 'transfer')?.count || 0);

                statsLoader.style.opacity = '0';
                setTimeout(() => {
                    statsLoader.style.display = 'none';
                    statsContent.style.display = 'block';
                    setTimeout(() => {
                        statsContent.classList.add('fade-in');
                        statsContent.classList.add('animate-cards');
                    }, 50);
                }, 400);
            })
            .catch(error => {
                console.error('Error fetching statistics:', error.message);
                statsLoader.innerHTML = `
                    <div class="text-danger">
                        <i class="fas fa-exclamation-triangle fa-2x mb-3"></i>
                        <p>Failed to load statistics: ${error.message}. Please try again.</p>
                        <button class="btn btn-outline-primary mt-2" onclick="loadStatistics()">
                            <i class="fas fa-redo me-2"></i>Retry
                        </button>
                    </div>
                `;
            });
    }

    function hideStatistics() {
        statisticsSection.classList.add('hiding');
        setTimeout(() => {
            statisticsSection.style.display = 'none';
        }, 500);
        showStatsBtn.innerHTML = '<i class="fas fa-chart-bar me-2"></i>Show Statistics';
    }

    showStatsBtn.addEventListener('click', function() {
        if (statisticsSection.style.display === 'none') {
            loadStatistics();
            showStatsBtn.innerHTML = '<i class="fas fa-chart-bar me-2"></i>Hide Statistics';
        } else {
            hideStatistics();
        }
    });

    closeStatsBtn.addEventListener('click', hideStatistics);
});
</script>
@endsection
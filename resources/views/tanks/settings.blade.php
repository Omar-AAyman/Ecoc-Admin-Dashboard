@extends('layouts.panel')

@section('title', 'Tank Settings')

@section('content')
<div class="main-content side-content mt-2 pt-0">
    <div class="container-fluid">
        <div class="inner-body">
            <h2 class="mb-4">Tank Settings</h2>
            @if ($tanks->isEmpty())
            <div class="alert alert-info">No tanks available.</div>
            @else
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Manage Tanks</h5>
                    @include('components.alerts')
                    <a href="{{ route('tanks.create') }}" class="btn btn-primary mb-3"><i class="ti-plus sidemenu-icon"></i> Add</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive-wrapper">
                        <table class="table table-striped" id="table">
                            <thead>
                                <tr>
                                    <th>Number</th>
                                    <th>Capacity (m³)</th>
                                    <th>Current Level (m³)</th>
                                    <th>Status</th>
                                    <th>Product</th>
                                    <th>Company</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($tanks as $tank)
                                <tr>
                                    <td>{{ $tank->number }}</td>
                                    <td>{{ $tank->cubic_meter_capacity }}</td>
                                    <td>{{ $tank->current_level }}</td>
                                    <td>{{ ucfirst($tank->status) }}</td>
                                    <td>
                                        <form action="{{ route('tanks.updateSettings', $tank->id) }}" method="POST" class="update-tank-form">
                                            @csrf
                                            <select name="product_id" class="select2-product w-100" required>
                                                <option value="">Select Product</option>
                                                @foreach ($products as $product)
                                                <option value="{{ $product->id }}" {{ $tank->product_id == $product->id ? 'selected' : '' }}>{{ $product->name }}</option>
                                                @endforeach
                                            </select>
                                    </td>
                                    <td>
                                        <select name="company_id" class="select2-company w-100">
                                            <option value="">No Company</option>
                                            @foreach ($companies as $company)
                                            <option value="{{ $company->id }}" {{ $tank->company_id == $company->id ? 'selected' : '' }}>{{ $company->name }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <button type="submit" class="btn btn-sm btn-primary">Update</button>
                                        </form>
                                        @if (auth()->user()->hasRole('super_admin'))
                                        <form action="{{ route('tanks.destroy', $tank->id) }}" method="POST" class="d-inline delete-tank-form">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger delete-btn" data-tank-number="{{ $tank->number }}"><i class="fe fe-trash"></i> Delete</button>
                                        </form>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('css')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@1.5.2/dist/select2-bootstrap4.min.css" rel="stylesheet">
<style>
    .select2-container .select2-selection--single {
        height: 31px;
        border: 1px solid #ced4da;
        border-radius: 4px;
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 31px;
        padding-left: 10px;
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 31px;
    }

    .select2-container {
        width: 200px !important;
        display: inline-block;
        vertical-align: middle;
    }

    .select2-container--default .select2-results__option--highlighted[aria-selected] {
        background-color: #007bff !important;
    }

</style>
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    const gallerySection = document.getElementById('gallery-section');
    const selectedContainerSection = document.getElementById('selected-container-section');
    const detailedAnalysisSection = document.getElementById('detailed-analysis-section');
    const gallery = document.getElementById('gallery');
    const backButton = document.getElementById('back-to-gallery-button');
    const moreDetailsButton = document.getElementById('more-details-button');
    const enlargedContainerDisplay = document.getElementById('enlarged-container-display');
    const searchInput = document.getElementById('tank-search');
    const refreshButton = document.getElementById('refresh-tanks');

    // Fetch tank data from the API
    async function fetchTanks() {
        try {
            const response = await fetch('/api/tanks', {
                headers: {
                    'Accept': 'application/json'
                    , 'Authorization': 'Bearer ' + localStorage.getItem('token') // Add if your API requires authentication
                }
            });
            if (!response.ok) {
                throw new Error('Failed to fetch tanks');
            }
            const tanks = await response.json();
            const mappedTanks = tanks.map(tank => ({
                id: tank.number
                , content: tank.product ? .name || 'N/A'
                , status: tank.status
                , location: tank.location || 'N/A'
                , updated: tank.updated_at || 'N/A'
                , client: tank.company ? .name || 'N/A'
                , notes: tank.notes || 'N/A'
                , usageTrend: tank.usage_trend || 'N/A'
                , maintenanceHistory: tank.maintenance_history || 'N/A'
                , capacityUtilization: tank.current_level && tank.cubic_meter_capacity ?
                    `${(tank.current_level / tank.cubic_meter_capacity * 100).toFixed(1)}%` : '0%'
                , nextMaintenance: tank.next_maintenance || 'N/A'
                , environmentalReadings: tank.environmental_readings || 'N/A'
                , liquidColor: getLiquidColor(tank.product ? .name)
            }));
            return mappedTanks;
        } catch (error) {
            console.error('Error fetching tanks:', error);
            return [];
        }
    }

    // Assign liquid colors based on product name
    function getLiquidColor(productName) {
        const colorMap = {
            'Crude Oil': ['#ef4444', '#b91c1c']
            , 'Chemical X': ['#4ade80', '#16a34a']
            , 'Lubricant': ['#60a5fa', '#2563eb']
            , 'Solvent': ['#fb923c', '#ea580c']
            , 'Diesel': ['#c084fc', '#9333ea']
            , 'Kerosene': ['#22d3ee', '#0891b2']
            , 'Acid': ['#f472b6', '#e82688']
            , 'Base Oil': ['#fcd34d', '#fbbf24']
            , 'Coolant': ['#94a3b8', '#64748b']
            , 'Petroleum': ['#d9f99d', '#a3e635']
        };
        return colorMap[productName] || ['#94a3b8', '#64748b'];
    }

    // Render the gallery with fetched tanks
    async function renderGallery() {
        const tanks = await fetchTanks();
        gallery.innerHTML = '';
        tanks.forEach(container => {
            const card = document.createElement('div');
            card.className = 'col-12 col-md-6 col-xl-2 d-flex justify-content-center';
            let liquidHeight = container.capacityUtilization !== '0%' ? container.capacityUtilization : '0%';
            let capacityText = container.capacityUtilization || 'N/A';
            card.innerHTML = `
                <div class="container-card" data-id="${container.id}">
                    <div class="barrel">
                        <div class="barrel-top"></div>
                        <div class="barrel-rings ring-top"></div>
                        <div class="barrel-rings ring-bottom"></div>
                        <div class="liquid-container" style="--liquid-color-light: ${container.liquidColor[0]}; --liquid-color-dark: ${container.liquidColor[1]}; height: ${liquidHeight};">
                            <div class="liquid-wave"></div>
                            <div class="liquid-wave"></div>
                            <div class="liquid-wave"></div>
                        </div>
                        <p class="capacity-label">${capacityText}</p>
                        <div class="container-label">
                            <p>${container.id}</p>
                        </div>
                        <div class="status-badge" style="background-color: ${getStatusColor(container.status)}">${container.status}</div>
                        <div class="container-info">
                            <p>${container.content}</p>
                            <p>${container.status}</p>
                        </div>
                    </div>
                </div>
            `;
            card.querySelector('.container-card').addEventListener('click', () => {
                showContainerBasicDetails(container);
            });
            gallery.appendChild(card);
        });
        // Reset gallery state
        gallery.classList.remove('small');
        selectedContainerSection.classList.remove('visible');
        selectedContainerSection.style.display = 'none';
        detailedAnalysisSection.classList.remove('visible');
        detailedAnalysisSection.style.display = 'none';
    }

    // Define status colors
    function getStatusColor(status) {
        switch (status) {
            case 'In Use':
                return '#10B981';
            case 'Empty':
                return '#6B7280';
            case 'Maintenance':
                return '#EF4444';
            case 'In Transit':
                return '#3B82F6';
            default:
                return '#9CA3AF';
        }
    }

    // Existing detail display functions (unchanged except for minor adjustments)
    function showContainerBasicDetails(container) {
        selectedContainerSection.style.display = 'block';
        selectedContainerSection.classList.add('visible');
        setTimeout(() => {
            gallery.classList.add('small');
            const galleryCards = gallery.querySelectorAll('.container-card');
            galleryCards.forEach(card => {
                if (card.dataset.id === container.id) {
                    card.classList.remove('small');
                    card.classList.add('selected');
                } else {
                    card.classList.add('small');
                    card.classList.remove('selected');
                }
            });

            let liquidHeight = container.capacityUtilization !== '0%' ? container.capacityUtilization : '0%';
            const enlargedLiquidContainer = enlargedContainerDisplay.querySelector('.liquid-container');
            enlargedLiquidContainer.style.setProperty('--liquid-color-light', container.liquidColor[0]);
            enlargedLiquidContainer.style.setProperty('--liquid-color-dark', container.liquidColor[1]);
            enlargedLiquidContainer.style.height = liquidHeight;
            document.getElementById('selected-capacity-label').textContent = container.capacityUtilization || 'N/A';

            document.getElementById('selected-container-id').textContent = container.id;
            document.getElementById('selected-container-content').textContent = container.content;
            document.getElementById('selected-container-status').textContent = container.status;
            document.getElementById('detail-header-id').textContent = container.id;
            document.getElementById('detail-id').textContent = container.id;
            document.getElementById('detail-content').textContent = container.content;
            document.getElementById('detail-status').textContent = container.status;
            document.getElementById('detail-location').textContent = container.location;
            document.getElementById('detail-updated').textContent = container.updated;
            document.getElementById('detail-client').textContent = container.client;
            document.getElementById('detail-notes').textContent = container.notes;

            populateDetailedAnalysis(container);
            enlargedContainerDisplay.classList.add('active');
            detailedAnalysisSection.classList.remove('visible');
            detailedAnalysisSection.style.display = 'none';
            moreDetailsButton.style.display = 'inline-block';
            moreDetailsButton.style.backgroundColor = container.liquidColor[0];
            moreDetailsButton.classList.remove('bg-primary', 'text-white');
        }, 50);
    }

    function showFullAnalysis() {
        detailedAnalysisSection.style.display = 'block';
        detailedAnalysisSection.classList.add('visible');
        moreDetailsButton.style.display = 'none';
    }

    function populateDetailedAnalysis(container) {
        document.getElementById('analysis-container-id').textContent = container.id;
        document.getElementById('analysis-usage-trend').textContent = container.usageTrend || 'N/A';
        document.getElementById('analysis-maintenance-history').textContent = container.maintenanceHistory || 'N/A';
        document.getElementById('analysis-capacity-utilization').textContent = container.capacityUtilization || 'N/A';
        document.getElementById('analysis-next-maintenance').textContent = container.nextMaintenance || 'N/A';
        document.getElementById('analysis-environmental-readings').textContent = container.environmentalReadings || 'N/A';
    }

    backButton.addEventListener('click', () => {
        selectedContainerSection.classList.remove('visible');
        detailedAnalysisSection.classList.remove('visible');
        enlargedContainerDisplay.classList.remove('active');
        gallery.classList.remove('small');
        const galleryCards = gallery.querySelectorAll('.container-card');
        galleryCards.forEach(card => {
            card.classList.remove('small', 'selected');
        });
        setTimeout(() => {
            selectedContainerSection.style.display = 'none';
            detailedAnalysisSection.style.display = 'none';
        }, 500);
        moreDetailsButton.style.backgroundColor = '';
        moreDetailsButton.classList.add('bg-primary', 'text-white');
    });

    moreDetailsButton.addEventListener('click', showFullAnalysis);

    // Search functionality
    searchInput.addEventListener('input', () => {
        const searchTerm = searchInput.value.toLowerCase();
        const tankCards = gallery.querySelectorAll('.container-card');
        tankCards.forEach(card => {
            const tankId = card.dataset.id.toLowerCase();
            const content = card.querySelector('.container-info p:first-child').textContent.toLowerCase();
            const status = card.querySelector('.container-info p:last-child').textContent.toLowerCase();
            if (tankId.includes(searchTerm) || content.includes(searchTerm) || status.includes(searchTerm)) {
                card.parentElement.style.display = '';
            } else {
                card.parentElement.style.display = 'none';
            }
        });
    });

    // Refresh button
    refreshButton.addEventListener('click', renderGallery);

    // Initial render
    document.addEventListener('DOMContentLoaded', renderGallery);

</script>
@endsection

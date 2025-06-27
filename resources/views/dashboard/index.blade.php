@extends('layouts.panel')

@section('title', 'Tanks Dashboard')

@push('styles')
<style>
    /* Tank Visualization Styles */
    .container-card {
        width: 200px;
        height: 360px;
        position: relative;
        perspective: 1000px;
        cursor: pointer;
        transition: width 0.5s ease-in-out, height 0.5s ease-in-out, box-shadow 0.3s ease;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
        border-radius: 8px;
        margin: auto;
    }

    .container-card.small {
        width: 120px;
        height: 200px;
    }

    .container-card.selected {
        width: 180px;
        height: 320px;
        box-shadow: 0 0 20px 5px rgba(255, 255, 255, 0.9);
        position: relative;
    }

    .container-card:hover {
        transform: scale(1.05);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.25);
    }

    .barrel {
        width: 100%;
        height: 100%;
        position: relative;
        transform-style: preserve-3d;
        /* transform: rotateY(-10deg); */
        background: linear-gradient(to right, #4b5563, #6b7280, #4b5563);
        border-radius: 20px 20px 10px 10px;
        box-shadow:
            0 0 0 2px rgba(0,0,0,0.1),
            inset 0 0 10px rgba(255, 255, 255, 0.1),
            inset 0 0 20px rgba(0, 0, 0, 0.3),
            5px 5px 15px rgba(0, 0, 0, 0.4);
        overflow: hidden;
    }

    .barrel-top {
        position: absolute;
        top: -10px;
        width: 100%;
        height: 20px;
        background: radial-gradient(circle at center, #525252 0%, #374151 100%);
        border-radius: 20px 20px 0 0;
        transform: translateZ(10px);
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.3), inset 0 0 5px rgba(255,255,255,0.2);
    }

    .barrel-rings {
        position: absolute;
        width: 100%;
        height: 10px;
        background: linear-gradient(to right, #374151, #7f868d, #374151);
        transform: translateZ(5px);
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.4), inset 0 0 2px rgba(255,255,255,0.3);
    }

    .ring-top { top: 20px; }
    .ring-bottom { bottom: 20px; }

    .liquid-container {
        position: absolute;
        bottom: 0;
        width: 100%;
        overflow: hidden;
        transform: translateZ(2px);
        border-radius: 0 0 10px 10px;
    }

    .liquid-wave {
        position: absolute;
        bottom: 0;
        width: 200%;
        height: 100%;
        background: linear-gradient(to top, var(--liquid-color-dark), var(--liquid-color-light));
        background-size: 200% 200%;
        animation: waveFlow 6s ease-in-out infinite;
        filter: blur(1px);
        border-radius: 40% 60% 0 0 / 100% 100% 0 0;
        transform-origin: center bottom;
    }

    .liquid-wave:nth-child(2) {
        opacity: 0.7;
        animation-delay: -3s;
        transform: translateX(-15%) translateY(-2px) rotateZ(0.5deg);
    }

    .liquid-wave:nth-child(3) {
        opacity: 0.5;
        animation-delay: -1.5s;
        transform: translateX(-10%) translateY(-1px) rotateZ(-0.5deg);
    }

    @keyframes waveFlow {
        0% { transform: translateX(0%) translateY(0px) rotateZ(0deg); background-position: 0% 50%; }
        15% { transform: translateX(-10%) translateY(-3px) rotateZ(0.5deg); background-position: 25% 50%; }
        30% { transform: translateX(-20%) translateY(0px) rotateZ(-0.5deg); background-position: 50% 50%; }
        45% { transform: translateX(-30%) translateY(-4px) rotateZ(1deg); background-position: 75% 50%; }
        60% { transform: translateX(-20%) translateY(-1px) rotateZ(-0.8deg); background-position: 100% 50%; }
        75% { transform: translateX(-10%) translateY(-3px) rotateZ(0.5deg); background-position: 75% 50%; }
        100% { transform: translateX(0%) translateY(0px) rotateZ(0deg); background-position: 0% 50%; }
    }

    .container-label {
        position: absolute;
        top: 30px;
        width: 100%;
        text-align: center;
        color: white;
        font-weight: bold;
        font-size: 1.2rem;
        text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.8);
        transform: translateZ(5px);
        pointer-events: none;
    }

    .container-card.small .container-label {
        font-size: 0.9rem;
    }

    .container-card.selected .container-label {
        font-size: 1.1rem;
    }

    .container-info {
        position: absolute;
        bottom: 10px;
        width: 100%;
        text-align: center;
        color: white;
        font-size: 0.9rem;
        text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.8);
        transform: translateZ(5px);
        pointer-events: none;
    }

    .container-card.small .container-info {
        font-size: 0.7rem;
    }

    .container-card.selected .container-info {
        font-size: 0.8rem;
    }

    .capacity-label {
        position: absolute;
        top: 40%;
        width: 100%;
        text-align: center;
        color: white;
        font-weight: bold;
        font-size: 1rem;
        text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.8);
        z-index: 15;
        transform: translateZ(15px);
        pointer-events: none;
    }

    .container-card.small .capacity-label {
        font-size: 0.7rem;
        top: 40%;
    }

    .container-card.selected .capacity-label {
        font-size: 0.9rem;
        top: 40%;
    }

    .enlarged-container-detail .capacity-label {
        font-size: 1rem;
        top: 40%;
    }

    .enlarged-container-detail {
        width: 200px;
        height: 360px;
        opacity: 0;
        visibility: hidden;
        transition: opacity 0.5s ease-in-out, visibility 0.5s ease-in-out;
    }

    .enlarged-container-detail.active {
        opacity: 1;
        visibility: visible;
    }

    #selected-container-section {
        display: none;
        opacity: 0;
        visibility: hidden;
        transition: opacity 0.5s ease-in-out, visibility 0.5s ease-in-out;
        margin-bottom: 1.5rem;
        position: relative;
    }

    #selected-container-section.visible {
        opacity: 1;
        visibility: visible;
        display: block;
    }

    #detailed-analysis-section {
        display: none;
        opacity: 0;
        visibility: hidden;
        transition: opacity 0.5s ease-in-out, visibility 0.5s ease-in-out;
        background-color: #e9ecef;
        padding: 1.5rem;
        border-radius: 0.5rem;
        margin-top: 1.5rem;
    }

    #detailed-analysis-section.visible {
        opacity: 1;
        visibility: visible;
        display: block;
    }

    .action-button {
        padding: 0.75rem 1.5rem;
        font-weight: 600;
        cursor: pointer;
        transition: background-color 0.3s ease, opacity 0.2s ease;
        border: none;
    }

    .action-button:hover {
        opacity: 0.9;
    }

    .x-button {
        background-color: #dc3545;
        color: white;
        font-size: 1.5rem;
        line-height: 1;
        width: 35px;
        height: 35px;
        border-radius: 50%;
        display: flex;
        justify-content: center;
        align-items: center;
        cursor: pointer;
        transition: background-color 0.3s ease, transform 0.2s ease;
        border: none;
        position: absolute;
        top: 1rem;
        right: 1rem;
        z-index: 20;
    }

    .x-button:hover {
        background-color: #c82333;
        transform: scale(1.1);
    }

    #gallery-section {
        transition: transform 0.5s ease-in-out;
        padding-top: 1.5rem;
        position: relative;
        z-index: 1;
    }

    #gallery {
        padding-top: 1rem;
    }

    #gallery.small .container-card {
        width: 120px;
        height: 200px;
    }

    .status-badge {
        position: absolute;
        top: 10px;
        right: 10px;
        padding: 0.25rem 0.5rem;
        border-radius: 0.25rem;
        font-size: 0.75rem;
        color: white;
        z-index: 10;
    }

    /* Search Bar Styling */
    #tank-search {
        max-width: 300px;
        margin-bottom: 1rem;
    }

    /* RTL adjustments */
    [dir="rtl"] .container-label,
    [dir="rtl"] .container-info,
    [dir="rtl"] .capacity-label {
        direction: rtl;
    }

    [dir="rtl"] .x-button {
        right: auto;
        left: 1rem;
    }

    /* Responsive adjustments */
    @media (max-width: 576px) {
        .container-card {
            width: 150px;
            height: 270px;
        }
        .container-card.small {
            width: 100px;
            height: 180px;
        }
        .container-card.selected {
            width: 140px;
            height: 280px;
        }
        .container-label {
            font-size: 1rem;
        }
        .container-info, .capacity-label {
            font-size: 0.8rem;
        }
        .enlarged-container-detail {
            width: 150px;
            height: 270px;
        }
        #tank-search {
            max-width: 100%;
        }
    }
</style>


@section('content')
<div class="main-content side-content mt-2 pt-0">
    <div class="container-fluid">
        <div class="inner-body">
            <h2 class="mb-4">Tanks Dashboard</h2>
            @include('components.alerts')
            @if ($tanks->isEmpty())
                <div class="alert alert-info">No tanks available.</div>
            @else
                <div id="selected-container-section" class="card shadow-sm mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="card-title mb-0">Tank Details: <span id="detail-header-id"></span></h5>
                        <button id="back-to-gallery-button" class="x-button">×</button>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12 col-md-4 d-flex justify-content-center">
                                <div id="enlarged-container-display" class="container-card enlarged-container-detail">
                                    <div class="barrel">
                                        <div class="barrel-top"></div>
                                        <div class="barrel-rings ring-top"></div>
                                        <div class="barrel-rings ring-bottom"></div>
                                        <div class="liquid-container">
                                            <div class="liquid-wave"></div>
                                            <div class="liquid-wave"></div>
                                            <div class="liquid-wave"></div>
                                        </div>
                                        <p class="capacity-label" id="selected-capacity-label"></p>
                                        <div class="container-label">
                                            <p id="selected-container-id"></p>
                                        </div>
                                        <div class="container-info">
                                            <p id="selected-container-content"></p>
                                            <p id="selected-container-status"></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-md-8">
                                <p><strong>ID:</strong> <span id="detail-id"></span></p>
                                <p><strong>Content:</strong> <span id="detail-content"></span></p>
                                <p><strong>Status:</strong> <span id="detail-status"></span></p>
                                <p><strong>Capacity:</strong> <span id="detail-capacity"></span></p>
                                @if (auth()->user()->hasAnyRole(['super_admin', 'ceo']))
                                    <p><strong>Company:</strong> <span id="detail-company"></span></p>
                                @endif
                                <div class="mt-3">
                                    @if (auth()->user()->hasAnyRole(['super_admin', 'ceo']))
                                        <a href="#" class="btn btn-primary action-button me-2" id="edit-settings-button">Edit Settings</a>
                                    @endif
                                    <button id="more-details-button" class="btn btn-primary action-button">See More Analysis</button>
                                </div>
                            </div>
                        </div>
                        <div id="detailed-analysis-section" class="mt-4">
                            <h5 class="fw-semibold mb-3">Detailed Analysis for <span id="analysis-container-id"></span></h5>
                            <p><strong>Usage Trend:</strong> <span id="analysis-usage-trend">N/A</span></p>
                            <p><strong>Maintenance History:</strong> <span id="analysis-maintenance-history">N/A</span></p>
                            <p><strong>Capacity Utilization:</strong> <span id="analysis-capacity-utilization"></span></p>
                            <p><strong>Next Scheduled Maintenance:</strong> <span id="analysis-next-maintenance">N/A</span></p>
                            <p><strong>Environmental Readings:</strong> <span id="analysis-environmental-readings">N/A</span></p>
                        </div>
                    </div>
                </div>
                <div id="gallery-section">
                    <h5 class="fw-semibold mb-3">Tanks Overview</h5>
                    <input type="text" id="tank-search" class="form-control" placeholder="Search by tank ID, content, or status">
                    <div id="gallery" class="row"></div>
                </div>

            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const containers = [
            @foreach ($tanks->sortBy('number') as $tank)
            {
                id: "{{ $tank->number }}",
                dbId: "{{ $tank->id }}", // Numeric ID for route
                content: "{{ $tank->product ? $tank->product->name : 'N/A' }}",
                status: "{{ ucfirst($tank->status) }}",
                capacity: "{{ $tank->cubic_meter_capacity }} m³",
                @if (auth()->user()->hasAnyRole(['super_admin', 'ceo']))
                company: "{{ $tank->company ? $tank->company->name : 'N/A' }}",
                @endif
                capacityUtilization: "{{ $tank->cubic_meter_capacity > 0 ? number_format(($tank->current_level / $tank->cubic_meter_capacity) * 100, 0) : 0 }}%",
                liquidColor: [
                    @switch($tank->product_id % 10)
                        @case(1) ["#ef4444", "#b91c1c"], @break
                        @case(2) ["#4ade80", "#16a34a"], @break
                        @case(3) ["#60a5fa", "#2563eb"], @break
                        @case(4) ["#fb923c", "#ea580c"], @break
                        @case(5) ["#c084fc", "#9333ea"], @break
                        @case(6) ["#22d3ee", "#0891b2"], @break
                        @case(7) ["#f472b6", "#e82688"], @break
                        @case(8) ["#fcd34d", "#fbbf24"], @break
                        @case(9) ["#94a3b8", "#64748b"], @break
                        @default ["#d9f99d", "#a3e635"]
                    @endswitch
                ],
                location: "N/A",
                updated: "{{ $tank->updated_at ? $tank->updated_at->format('Y-m-d') : 'N/A' }}",
                notes: "N/A",
                usageTrend: "N/A",
                maintenanceHistory: "N/A",
                nextMaintenance: "N/A",
                environmentalReadings: "N/A"
            },
            @endforeach
        ];

        const gallerySection = document.getElementById('gallery-section');
        const selectedContainerSection = document.getElementById('selected-container-section');
        const detailedAnalysisSection = document.getElementById('detailed-analysis-section');
        const gallery = document.getElementById('gallery');
        const backButton = document.getElementById('back-to-gallery-button');
        const moreDetailsButton = document.getElementById('more-details-button');
        const enlargedContainerDisplay = document.getElementById('enlarged-container-display');
        const searchInput = document.getElementById('tank-search');
        const editSettingsButton = document.getElementById('edit-settings-button');

        function getStatusColor(status) {
            switch (status.toLowerCase()) {
                case 'in use': return '#10B981';
                case 'empty': return '#6B7280';
                case 'maintenance': return '#EF4444';
                case 'in transit': return '#3B82F6';
                default: return '#9CA3AF';
            }
        }

        function renderGallery(tanks = containers) {
            gallery.innerHTML = '';
            tanks.forEach(container => {
                const card = document.createElement('div');
                card.className = 'col-12 col-sm-6 col-md-4 col-lg-3 col-xl-2 d-flex justify-content-center';
                const liquidHeight = container.capacityUtilization || '0%';
                const capacityText = container.capacityUtilization || 'N/A';
                const tooltipContent = `
                    ID: ${container.id} <br>
                    Content: ${container.content} <br>
                    Status: ${container.status} <br>
                    Capacity: ${container.capacity} <br>
                    Utilization: ${capacityText}
                    ${container.company ? `<br>Company: ${container.company}` : ''}
                `;
                card.innerHTML = `
                    <div class="container-card" data-id="${container.id}" data-bs-toggle="tooltip" data-bs-html="true" title="${tooltipContent}">
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
                            <div class="container-info">
                                <p>${container.content}</p>
                                <p>${container.status}</p>
                            </div>
                            <div class="status-badge" style="background-color: ${getStatusColor(container.status)}">${container.status}</div>
                        </div>
                    </div>
                `;
                card.querySelector('.container-card').addEventListener('click', () => {
                    showContainerBasicDetails(container);
                });
                gallery.appendChild(card);
            });

            // Initialize Bootstrap tooltips
            document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(element => {
                new bootstrap.Tooltip(element, {
                    placement: 'top',
                    container: 'body'
                });
            });

            gallery.classList.remove('small');
            gallerySection.style.transform = 'translateY(0)';
            selectedContainerSection.classList.remove('visible');
            selectedContainerSection.style.display = 'none';
            detailedAnalysisSection.classList.remove('visible');
            detailedAnalysisSection.style.display = 'none';
            moreDetailsButton.style.backgroundColor = '';
            moreDetailsButton.classList.add('btn-primary', 'text-white');
        }

        function showContainerBasicDetails(container) {
            selectedContainerSection.style.display = 'block';
            selectedContainerSection.classList.add('visible');

            setTimeout(() => {
                const selectedContainerHeight = selectedContainerSection.offsetHeight;
                const sectionMarginBottom = parseFloat(getComputedStyle(selectedContainerSection).marginBottom);
                const offsetForGallery = selectedContainerHeight + sectionMarginBottom + 10;

                gallerySection.style.transform = `translateY(${offsetForGallery}px)`;
                gallerySection.style.transition = 'transform 0.5s ease-in-out';
                gallery.classList.add('small');
                gallery.querySelectorAll('.container-card').forEach(card => {
                    if (card.dataset.id === container.id) {
                        card.classList.remove('small');
                        card.classList.add('selected');
                    } else {
                        card.classList.add('small');
                        card.classList.remove('selected');
                    }
                });

                const liquidHeight = container.capacityUtilization || '0%';
                const capacityText = container.capacityUtilization || 'N/A';
                const liquidContainer = enlargedContainerDisplay.querySelector('.liquid-container');
                liquidContainer.style.setProperty('--liquid-color-light', container.liquidColor[0]);
                liquidContainer.style.setProperty('--liquid-color-dark', container.liquidColor[1]);
                liquidContainer.style.height = liquidHeight;
                document.getElementById('selected-capacity-label').textContent = capacityText;

                document.getElementById('selected-container-id').textContent = container.id;
                document.getElementById('selected-container-content').textContent = container.content;
                document.getElementById('selected-container-status').textContent = container.status;
                document.getElementById('detail-header-id').textContent = container.id;
                document.getElementById('detail-id').textContent = container.id;
                document.getElementById('detail-content').textContent = container.content;
                document.getElementById('detail-status').textContent = container.status;
                document.getElementById('detail-capacity').textContent = container.capacity;
                @if (auth()->user()->hasAnyRole(['super_admin', 'ceo']))
                    document.getElementById('detail-company').textContent = container.company;
                @endif

                // Update Edit Settings link with the correct tank ID
                if (editSettingsButton) {
                    editSettingsButton.href = '/en/tanks/' + encodeURIComponent(container.dbId) + '/edit';
                }

                populateDetailedAnalysis(container);

                enlargedContainerDisplay.classList.remove('active');
                setTimeout(() => {
                    enlargedContainerDisplay.classList.add('active');
                }, 0);

                detailedAnalysisSection.classList.remove('visible');
                detailedAnalysisSection.style.display = 'none';
                moreDetailsButton.style.display = 'inline-block';
                moreDetailsButton.style.backgroundColor = container.liquidColor[0];
                moreDetailsButton.classList.remove('btn-primary', 'text-white');

                // Hide tooltips
                document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(tooltip => {
                    bootstrap.Tooltip.getInstance(tooltip)?.hide();
                });
            }, 50);
        }

        function showFullAnalysis() {
            detailedAnalysisSection.style.display = 'block';
            detailedAnalysisSection.classList.add('visible');
            moreDetailsButton.style.display = 'none';
        }

        function populateDetailedAnalysis(container) {
            document.getElementById('analysis-container-id').textContent = container.id;
            document.getElementById('analysis-usage-trend').textContent = container.usageTrend;
            document.getElementById('analysis-maintenance-history').textContent = container.maintenanceHistory;
            document.getElementById('analysis-capacity-utilization').textContent = container.capacityUtilization;
            document.getElementById('analysis-next-maintenance').textContent = container.nextMaintenance;
            document.getElementById('analysis-environmental-readings').textContent = container.environmentalReadings;
        }

        backButton.addEventListener('click', () => {
            selectedContainerSection.classList.remove('visible');
            detailedAnalysisSection.classList.remove('visible');
            enlargedContainerDisplay.classList.remove('active');
            gallerySection.style.transform = 'translateY(0)';
            gallerySection.style.transition = 'transform 0.5s ease-in-out';
            gallery.classList.remove('small');
            gallery.querySelectorAll('.container-card').forEach(card => {
                card.classList.remove('small', 'selected');
                card.style.opacity = '';
                card.style.transitionDelay = '';
            });

            setTimeout(() => {
                selectedContainerSection.style.display = 'none';
                detailedAnalysisSection.style.display = 'none';
            }, 500);

            moreDetailsButton.style.backgroundColor = '';
            moreDetailsButton.classList.add('btn-primary', 'text-white');
        });

        moreDetailsButton.addEventListener('click', showFullAnalysis);

        // Search functionality
        searchInput.addEventListener('input', () => {
            const searchTerm = searchInput.value.toLowerCase();
            const filteredTanks = containers.filter(container =>
                container.id.toLowerCase().includes(searchTerm) ||
                container.content.toLowerCase().includes(searchTerm) ||
                container.status.toLowerCase().includes(searchTerm)
            );
            renderGallery(filteredTanks);
        });

        // Initial render
        if (containers.length > 0) {
            renderGallery();
        }
    });
</script>
@endsection

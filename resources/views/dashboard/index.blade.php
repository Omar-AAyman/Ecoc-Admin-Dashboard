@extends('layouts.panel')

@section('title', 'Tanks Dashboard')

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

    .action-button {
        padding: 0.5rem 1rem;
        font-weight: 500;
    }

    .x-button {
        background-color: #dc2626;
        border-color: #dc2626;
        color: #ffffff;
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
        background-color: #b91c1c;
        transform: scale(1.1);
        box-shadow: 0 4px 12px rgba(220, 38, 38, 0.3);
    }

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
        margin: 1rem auto;
        will-change: transform, box-shadow;
    }

    .container-card.small {
        width: 120px;
        height: 200px;
    }

    .container-card.selected {
        width: 180px;
        height: 320px;
        box-shadow: 0 0 20px 5px rgba(255, 255, 255, 0.9);
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
        min-height: 10px; /* Increased for visibility */
        overflow: visible;
        transform: translateZ(2px);
        border-radius: 0 0 10px 10px;
        transition: height 0.5s ease-in-out;
        z-index: 2;
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
        will-change: transform, background-position;
        z-index: 3;
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
        line-height: 1.2;
        text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.8);
        transform: translateZ(5px);
        pointer-events: none;
        z-index: 5;
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
        line-height: 1.3;
        text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.8);
        transform: translateZ(5px);
        pointer-events: none;
        z-index: 5;
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
        line-height: 1.2;
        text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.8);
        transform: translateZ(15px);
        pointer-events: none;
        z-index: 15;
    }

    .container-card.small .capacity-label {
        font-size: 0.7rem;
    }

    .container-card.selected .capacity-label {
        font-size: 0.9rem;
    }

    .enlarged-container-detail {
        width: 180px;
        height: 320px;
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
    }

    #selected-container-section.visible {
        opacity: 1;
        visibility: visible;
        display: block;
    }

    .card-header {
        background: #000b43;
        color: #ffffff;
        border-radius: 16px 16px 0 0;
    }

    #detailed-analysis-section {
        display: none;
        opacity: 0;
        visibility: hidden;
        transition: opacity 0.5s ease-in-out, visibility 0.5s ease-in-out;
        background-color: #ffffff;
        padding: 1.5rem;
        border-radius: 0.5rem;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
    }

    #detailed-analysis-section.visible {
        opacity: 1;
        visibility: visible;
        display: block;
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

    #tank-search {
        max-width: 300px;
        margin-bottom: 1rem;
    }

    [dir="rtl"] .container-label,
    [dir="rtl"] .container-info,
    [dir="rtl"] .capacity-label {
        direction: rtl;
    }

    [dir="rtl"] .x-button {
        right: auto;
        left: 1rem;
    }

    @media (max-width: 768px) {
        .hero-header h2 {
            font-size: 1.75rem;
        }

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
            width: 140px;
            height: 280px;
        }

        #tank-search {
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
                        <h2 class="mb-0">
                            <i class="fas fa-tint me-2"></i>Tanks Dashboard
                        </h2>
                    </div>
                </div>
            </div>

            <!-- Alert Messages -->
            @include('components.alerts')

            <div id="selected-container-section" class="card shadow-sm mb-4">
                <div class="card-header text-white">
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
                                    <div class="status-badge" id="selected-status-badge"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-8">
                            <p><strong>ID:</strong> <span id="detail-id"></span></p>
                            <p><strong>Content:</strong> <span id="detail-content"></span></p>
                            <p><strong>Status:</strong> <span id="detail-status"></span></p>
                            <p><strong>Max Capacity:</strong> <span id="detail-max-capacity"></span></p>
                            <p><strong>Current Level:</strong> <span id="detail-current-level"></span></p>
                            <p><strong>Capacity Utilization:</strong> <span id="detail-capacity-utilization"></span></p>
                            @if (auth()->user()->hasAnyRole(['super_admin', 'ceo']))
                                <p><strong>Company:</strong> <span id="detail-company"></span></p>
                            @endif
                            <div class="mt-3 d-flex gap-2">
                                @if (auth()->user()->hasAnyRole(['super_admin', 'ceo']))
                                    <a href="#" class="btn btn-primary action-button" id="edit-settings-button">Edit Settings</a>
                                @endif
                                <button id="more-details-button" class="btn btn-primary action-button">See More Analysis</button>
                            </div>
                        </div>
                    </div>
                    <div id="detailed-analysis-section" class="mt-4">
                        <h5 class="fw-semibold mb-3">Detailed Analysis for <span id="analysis-container-id"></span></h5>
                        <p><strong>Max Capacity:</strong> <span id="analysis-max-capacity"></span></p>
                        <p><strong>Current Level:</strong> <span id="analysis-current-level"></span></p>
                        <p><strong>Capacity Utilization:</strong> <span id="analysis-capacity-utilization"></span></p>
                        <p><strong>Rental History:</strong></p>
                        <ul id="analysis-rental-history"></ul>
                        <p><strong>Transactions:</strong></p>
                        <ul id="analysis-transactions"></ul>
                    </div>
                </div>
            </div>
            <div id="gallery-section">
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="search-container">
                                <input type="text" id="tank-search" class="form-control" placeholder="Search by tank ID, content, or status">
                            </div>
                        </div>
                    </div>
                </div>
                <div id="gallery" class="row"></div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const containers = [
        @foreach (app(\App\Services\TankService::class)->getTanks(auth()->user()) as $tank)
        {
            id: "{{ $tank->number }}",
            dbId: "{{ $tank->id }}",
            content: "{{ $tank->product ? $tank->product->name : 'N/A' }}",
            status: "{{ ucfirst($tank->status) }}",
            cubicMeterCapacity: "{{ $tank->cubic_meter_capacity }}",
            currentLevel: "{{ $tank->current_level ?? 0 }}",
            maxCapacity: "{{ $tank->product && $tank->product->density ? number_format($tank->cubic_meter_capacity * $tank->product->density, 2) : $tank->cubic_meter_capacity }}",
            @if (auth()->user()->hasAnyRole(['super_admin', 'ceo']))
            company: "{{ $tank->company ? $tank->company->name : 'N/A' }}",
            @endif
            capacityUtilization: "{{ $tank->cubic_meter_capacity > 0 && $tank->product && $tank->product->density ? number_format(($tank->current_level / ($tank->cubic_meter_capacity * $tank->product->density)) * 100, 0) : ($tank->cubic_meter_capacity > 0 ? number_format(($tank->current_level / $tank->cubic_meter_capacity) * 100, 0) : 0) }}%",
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
            case 'in use': return '#ef4444';
            case 'empty': return '#6b7280';
            case 'maintenance': return '#dc2626';
            case 'in transit': return '#2563eb';
            case 'available': return '#22c55e';
            default: return '#9ca3af';
        }
    }

    function renderGallery(tanks = containers) {
        console.log('Rendering gallery with tanks:', tanks); // Debugging
        gallery.innerHTML = '';
        tanks.forEach(container => {
            const liquidHeight = container.currentLevel && container.maxCapacity && container.maxCapacity > 0 ?
                `${Math.min((parseFloat(container.currentLevel) / parseFloat(container.maxCapacity)) * 100, 100)}%` : '0%';
            console.log(`Tank ${container.id}: currentLevel=${container.currentLevel}, maxCapacity=${container.maxCapacity}, liquidHeight=${liquidHeight}`); // Debugging
            const capacityText = container.currentLevel && container.maxCapacity ?
                `${parseFloat(container.currentLevel).toFixed(1)} mt / ${parseFloat(container.maxCapacity).toFixed(1)} mt (${container.capacityUtilization})` : 'N/A';
            const tooltipContent = `
                ID: ${container.id}<br>
                Content: ${container.content}<br>
                Status: ${container.status}<br>
                Max Capacity: ${container.maxCapacity} mt<br>
                Current Level: ${container.currentLevel} mt<br>
                Utilization: ${container.capacityUtilization}
                ${container.company ? `<br>Company: ${container.company}` : ''}
            `;
            const card = document.createElement('div');
            card.className = 'col-12 col-sm-6 col-md-4 col-lg-3 col-xl-2 d-flex justify-content-center';
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

        // Dispose existing tooltips to prevent leaks
        document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(element => {
            const tooltip = bootstrap.Tooltip.getInstance(element);
            if (tooltip) tooltip.dispose();
        });

        // Initialize new tooltips
        document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(element => {
            new bootstrap.Tooltip(element, {
                placement: 'top',
                container: 'body',
                html: true
            });
        });

        gallery.classList.remove('small');
        selectedContainerSection.classList.remove('visible');
        selectedContainerSection.style.display = 'none';
        detailedAnalysisSection.classList.remove('visible');
        detailedAnalysisSection.style.display = 'none';
        moreDetailsButton.style.backgroundColor = '';
        moreDetailsButton.classList.add('btn-primary', 'text-white');
    }

    function showContainerBasicDetails(container) {
        console.log('Showing details for container:', container); // Debugging
        selectedContainerSection.style.display = 'block';
        setTimeout(() => {
            selectedContainerSection.classList.add('visible');

            const selectedContainerHeight = selectedContainerSection.offsetHeight;
            const sectionMarginBottom = parseFloat(getComputedStyle(selectedContainerSection).marginBottom);
            const offsetForGallery = selectedContainerHeight + sectionMarginBottom + 10;

            gallerySection.style.transition = 'transform 0.5s ease-in-out';
            gallery.classList.add('small');
            gallery.querySelectorAll('.container-card').forEach(card => {
                card.classList.toggle('small', card.dataset.id !== container.id);
                card.classList.toggle('selected', card.dataset.id === container.id);
            });

            const liquidHeight = container.currentLevel && container.maxCapacity && container.maxCapacity > 0 ?
                `${Math.min((parseFloat(container.currentLevel) / parseFloat(container.maxCapacity)) * 100, 100)}%` : '0%';
            console.log(`Enlarged tank ${container.id}: liquidHeight=${liquidHeight}`); // Debugging
            const capacityText = container.currentLevel && container.maxCapacity ?
                `${parseFloat(container.currentLevel).toFixed(1)} mt / ${parseFloat(container.maxCapacity).toFixed(1)} mt (${container.capacityUtilization})` : 'N/A';
            const liquidContainer = enlargedContainerDisplay.querySelector('.liquid-container');
            liquidContainer.style.setProperty('--liquid-color-light', container.liquidColor[0]);
            liquidContainer.style.setProperty('--liquid-color-dark', container.liquidColor[1]);
            liquidContainer.style.height = liquidHeight;
            document.getElementById('selected-capacity-label').textContent = capacityText;
            document.getElementById('selected-status-badge').textContent = container.status;
            document.getElementById('selected-status-badge').style.backgroundColor = getStatusColor(container.status);

            document.getElementById('selected-container-id').textContent = container.id;
            document.getElementById('selected-container-content').textContent = container.content;
            document.getElementById('selected-container-status').textContent = container.status;
            document.getElementById('detail-header-id').textContent = container.id;
            document.getElementById('detail-id').textContent = container.id;
            document.getElementById('detail-content').textContent = container.content;
            document.getElementById('detail-status').textContent = container.status;
            document.getElementById('detail-max-capacity').textContent = `${parseFloat(container.maxCapacity).toFixed(1)} mt`;
            document.getElementById('detail-current-level').textContent = `${parseFloat(container.currentLevel).toFixed(1)} mt`;
            document.getElementById('detail-capacity-utilization').textContent = container.capacityUtilization;
            @if (auth()->user()->hasAnyRole(['super_admin', 'ceo']))
                document.getElementById('detail-company').textContent = container.company || 'N/A';
            @endif

            if (editSettingsButton) {
                editSettingsButton.href = `/en/tanks/${encodeURIComponent(container.dbId)}/edit`;
            }

            // Fetch rental history and transactions via AJAX
            fetchTankDetails(container.dbId);

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

    function fetchTankDetails(tankId) {
        console.log('Fetching details for tank ID:', tankId); // Debugging
        $.ajax({
            url: `/api/tanks/${tankId}/details`,
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            success: function(data) {
                console.log('Tank details fetched:', data); // Debugging
                populateDetailedAnalysis(data);
            },
            error: function(xhr) {
                console.error('Error fetching tank details:', xhr.responseText);
                populateDetailedAnalysis({
                    id: document.getElementById('detail-id').textContent,
                    maxCapacity: document.getElementById('detail-max-capacity').textContent,
                    currentLevel: document.getElementById('detail-current-level').textContent,
                    capacityUtilization: document.getElementById('detail-capacity-utilization').textContent,
                    rentals: [],
                    transactions: []
                });
            }
        });
    }

    function populateDetailedAnalysis(data) {
        document.getElementById('analysis-container-id').textContent = data.id;
        document.getElementById('analysis-max-capacity').textContent = data.maxCapacity;
        document.getElementById('analysis-current-level').textContent = data.currentLevel;
        document.getElementById('analysis-capacity-utilization').textContent = data.capacityUtilization;

        const rentalList = document.getElementById('analysis-rental-history');
        rentalList.innerHTML = '';
        if (data.rentals && data.rentals.length > 0) {
            data.rentals.forEach(rental => {
                const li = document.createElement('li');
                li.textContent = `From ${rental.start_date} to ${rental.end_date || 'Present'} - Company: ${rental.company_name || 'N/A'}, Product: ${rental.product_name || 'N/A'}`;
                rentalList.appendChild(li);
            });
        } else {
            const li = document.createElement('li');
            li.textContent = 'No rental history available.';
            rentalList.appendChild(li);
        }

        const transactionList = document.getElementById('analysis-transactions');
        transactionList.innerHTML = '';
        if (data.transactions && data.transactions.length > 0) {
            data.transactions.forEach(transaction => {
                const li = document.createElement('li');
                li.textContent = `ID: ${transaction.id}, Quantity: ${transaction.quantity} mt, Date: ${transaction.created_at}, Type: ${transaction.type || 'N/A'}`;
                transactionList.appendChild(li);
            });
        } else {
            const li = document.createElement('li');
            li.textContent = 'No transactions available.';
            transactionList.appendChild(li);
        }
    }

    function showFullAnalysis() {
        console.log('Showing full analysis'); // Debugging
        detailedAnalysisSection.style.display = 'block';
        setTimeout(() => {
            detailedAnalysisSection.classList.add('visible');
        }, 0);
        moreDetailsButton.style.display = 'none';
    }

    moreDetailsButton.addEventListener('click', showFullAnalysis);

    backButton.addEventListener('click', () => {
        console.log('Back to gallery clicked'); // Debugging
        selectedContainerSection.classList.remove('visible');
        detailedAnalysisSection.classList.remove('visible');
        enlargedContainerDisplay.classList.remove('active');
        gallerySection.style.transition = 'transform 0.5s ease-in-out';
        gallery.classList.remove('small');
        gallery.querySelectorAll('.container-card').forEach(card => {
            card.classList.remove('small', 'selected');
        });

        setTimeout(() => {
            selectedContainerSection.style.display = 'none';
            detailedAnalysisSection.style.display = 'none';
        }, 500);

        moreDetailsButton.style.backgroundColor = '';
        moreDetailsButton.classList.add('btn-primary', 'text-white');
        renderGallery();
    });

    // Debounced search
    let searchTimeout;
    searchInput.addEventListener('input', () => {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            const searchTerm = searchInput.value.toLowerCase();
            console.log('Search term:', searchTerm); // Debugging
            const filteredTanks = containers.filter(container =>
                container.id.toLowerCase().includes(searchTerm) ||
                container.content.toLowerCase().includes(searchTerm) ||
                container.status.toLowerCase().includes(searchTerm)
            );
            renderGallery(filteredTanks);
        }, 300);
    });

    // Initial render
    renderGallery();
});
</script>
@endsection
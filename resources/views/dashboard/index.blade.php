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
        padding: 1.5rem 0;
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
        padding: 0.5rem 1rem;
        font-size: 0.875rem;
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
        min-height: 10px;
        overflow: hidden;
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

    .chart-container {
        position: relative;
        height: 300px;
        width: 100%;
        margin-bottom: 2rem;
    }

    .details-table {
        font-size: 0.85rem;
        margin-top: 1rem;
    }

    .details-table th, .details-table td {
        padding: 0.25rem;
        vertical-align: middle;
    }

    @media (max-width: 992px) {
        .hero-header h2 {
            font-size: 2rem;
        }
        .header-actions {
            justify-content: center;
            margin-top: 1rem;
        }
        .btn {
            padding: 0.4rem 0.8rem;
            font-size: 0.85rem;
        }
    }

    @media (max-width: 768px) {
        .hero-header {
            padding: 1rem 0;
        }
        .hero-header h2 {
            font-size: 1.75rem;
            text-align: center;
        }
        .header-actions {
            flex-direction: column;
            align-items: stretch;
            gap: 0.5rem;
        }
        .btn {
            width: 100%;
            text-align: center;
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
        .chart-container {
            height: 200px;
        }
        .details-table {
            font-size: 0.75rem;
        }
    }

    @media (max-width: 576px) {
        .hero-header h2 {
            font-size: 1.5rem;
        }
        .btn {
            font-size: 0.8rem;
            padding: 0.35rem 0.7rem;
        }
        .container-card {
            width: 120px;
            height: 220px;
        }
        .container-card.small {
            width: 90px;
            height: 160px;
        }
        .container-card.selected {
            width: 110px;
            height: 200px;
        }
        .container-label {
            font-size: 0.9rem;
        }
        .container-info, .capacity-label {
            font-size: 0.7rem;
        }
        .enlarged-container-detail {
            width: 110px;
            height: 200px;
        }
    }
</style>
@endsection

@section('content')
<div class="main-content side-content my-2 pt-0">
    <div class="container-fluid px-4 py-4">
        <div class="inner-body">
            <!-- Welcome Section -->
            <div class="hero-header">
                <div class="container">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center">
                        <h2 class="my-3 my-md-0">
                            <i class="fas fa-tint me-2"></i>Welcome back, {{ auth()->user()->first_name }}!
                        </h2>
                    </div>
                    <p class="text-muted">Total Tanks: {{ $totalTanks }}, Average Capacity Utilization: {{ $avgCapacityUtilization }}%</p>
                </div>
            </div>

            <!-- Charts Section -->
            <div class="row">
                <div class="col-12 col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Rental Overview</h5>
                        </div>
                        <div class="card-body">
                            <div class="chart-container">
                                <canvas id="rentalChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Utilization Trends</h5>
                        </div>
                        <div class="card-body">
                            <div class="chart-container">
                                <canvas id="utilizationChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Analysis Cards Section -->
            <div class="row mt-4">
                <div class="col-12 col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Average Capacity Utilization</h5>
                        </div>
                        <div class="card-body text-center">
                            <h3>{{ $avgCapacityUtilization }}%</h3>
                            <p class="text-muted">Average across all tanks</p>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Overall Analysis</h5>
                        </div>
                        <div class="card-body text-center">
                            <p>Active Rentals: {{ $activeRentals }}</p>
                            <p>Completed Rentals: {{ $completedRentals }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Process Metrics</h5>
                        </div>
                        <div class="card-body text-center">
                            <p>Discharge: {{ $totalDischarge }} mt</p>
                            <p>Load: {{ $totalLoad }} mt</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Selected Container Section -->
            <div id="selected-container-section" class="card shadow-sm my-4">
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
                            <p><strong>Current capacity:</strong> <span id="detail-current-level"></span></p>
                            <p><strong>Capacity Utilization:</strong> <span id="detail-capacity-utilization"></span></p>
                            <p><strong>Company:</strong> <span id="detail-company"></span></p>
                            <div class="mt-3 d-flex gap-2">
                                @if (auth()->user() && auth()->user()->isSuperAdmin())
                                    <a id="edit-settings-button" class="btn btn-primary action-button">Edit Settings</a>
                                @endif
                                <button id="more-details-button" class="btn btn-primary action-button">See More Analysis</button>
                            </div>
                        </div>
                    </div>
                    <div id="detailed-analysis-section" class="mt-4">
                        <h5 class="fw-semibold mb-3">Detailed Analysis for <span id="analysis-container-id"></span></h5>
                        <div class="details-table">
                            <h6>Rental History</h6>
                            <div class="table-responsive">
                                <table class="table table-striped table-sm text-center">
                                    <thead>
                                        <tr>
                                            <th>Company</th>
                                            <th>Product</th>
                                            <th>Start Date</th>
                                            <th>End Date</th>
                                        </tr>
                                    </thead>
                                    <tbody id="rental-history-table"></tbody>
                                </table>
                            </div>
                            <div class="table-responsive">
                                <h6 class="mt-3">Transactions</h6>
                                <table class="table table-striped table-sm text-center">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Work Order</th>
                                            <th>Bill Of Lading</th>
                                            <th>Charge Permit</th>
                                            <th>Discharge Permit</th>
                                            <th>Type</th>
                                            <th>Quantity</th>
                                            <th>Date</th>
                                            <th>Company</th>
                                            <th>Product</th>
                                        </tr>
                                    </thead>
                                    <tbody id="transactions-table"></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Gallery Section -->
            <div id="gallery-section" class="mt-4">
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
<script>
document.addEventListener('DOMContentLoaded', function () {
    const tanks = @json($tanks) || [];
    const performanceTrends = @json($performanceTrends) || { utilization: [], rentals: [], labels: [] };

    const gallerySection = document.getElementById('gallery-section');
    const selectedContainerSection = document.getElementById('selected-container-section');
    const detailedAnalysisSection = document.getElementById('detailed-analysis-section');
    const gallery = document.getElementById('gallery');
    const backButton = document.getElementById('back-to-gallery-button');
    const moreDetailsButton = document.getElementById('more-details-button');
    const enlargedContainerDisplay = document.getElementById('enlarged-container-display');
    const searchInput = document.getElementById('tank-search');
    const editSettingsButton = document.getElementById('edit-settings-button');
    const rentalHistoryTable = document.getElementById('rental-history-table');
    const transactionsTable = document.getElementById('transactions-table');

    // Initial render of gallery
    renderGallery(tanks);

    // Rental Chart
    const rentalChart = new Chart(document.getElementById('rentalChart').getContext('2d'), {
        type: 'bar',
        data: {
            labels: ['Active Rentals', 'Completed Rentals'],
            datasets: [{
                label: 'Number of Rentals',
                data: [{{ $activeRentals }}, {{ $completedRentals }}],
                backgroundColor: ['#4ade80', '#ef4444'],
                borderColor: ['#16a34a', '#b91c1c'],
                borderWidth: 1
            }]
        },
        options: {
            scales: { y: { beginAtZero: true } }
        }
    });

    // Utilization Trends Chart
    const utilizationChart = new Chart(document.getElementById('utilizationChart').getContext('2d'), {
        type: 'line',
        data: {
            labels: performanceTrends.labels,
            datasets: [{
                label: 'Capacity Utilization (%)',
                data: performanceTrends.utilization,
                fill: false,
                borderColor: '#2563eb',
                tension: 0.1
            }, {
                label: 'Rental Count',
                data: performanceTrends.rentals,
                fill: false,
                borderColor: '#ef4444',
                tension: 0.1
            }]
        },
        options: {
            scales: { y: { beginAtZero: true } }
        }
    });

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

    function renderGallery(tanks = []) {
        console.log('Rendering gallery with tanks:', tanks);
        if (!tanks || tanks.length === 0) {
            gallery.innerHTML = '<p class="text-center">No tanks available.</p>';
            return;
        }
        gallery.innerHTML = '';
        tanks.forEach(container => {
            const maxCapacity = parseFloat(container.maxCapacity.replace(',', ''));
            const currentLevel = parseFloat(container.currentLevel);
            const liquidHeight = maxCapacity > 0 ? Math.min((currentLevel / maxCapacity) * 100, 100) + '%' : '0%';
            console.log(`Tank ${container.id}: currentLevel=${currentLevel}, maxCapacity=${maxCapacity}, liquidHeight=${liquidHeight}`);
            const capacityText = maxCapacity > 0 ? `${currentLevel.toFixed(1)} mt / ${maxCapacity.toFixed(1)} mt (${container.capacityUtilization})` : 'N/A';

            const card = document.createElement('div');
            card.className = 'col-12 col-sm-6 col-md-4 col-lg-3 col-xl-2 d-flex justify-content-center';
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

        gallery.classList.remove('small');
        selectedContainerSection.classList.remove('visible');
        selectedContainerSection.style.display = 'none';
        detailedAnalysisSection.classList.remove('visible');
        detailedAnalysisSection.style.display = 'none';
        moreDetailsButton.style.backgroundColor = '';
        moreDetailsButton.classList.add('btn-primary', 'text-white');
    }

    function showContainerBasicDetails(container) {
        console.log('Showing details for container:', container);
        selectedContainerSection.style.display = 'block';
        setTimeout(() => {
            selectedContainerSection.classList.add('visible');

            gallerySection.style.transition = 'transform 0.5s ease-in-out';
            gallery.classList.add('small');
            gallery.querySelectorAll('.container-card').forEach(card => {
                card.classList.toggle('small', card.dataset.id !== container.id);
                card.classList.toggle('selected', card.dataset.id === container.id);
            });

            const maxCapacity = parseFloat(container.maxCapacity.replace(',', ''));
            const currentLevel = parseFloat(container.currentLevel);
            const liquidHeight = maxCapacity > 0 ? Math.min((currentLevel / maxCapacity) * 100, 100) + '%' : '0%';
            console.log(`Enlarged tank ${container.id}: liquidHeight=${liquidHeight}`);
            const capacityText = maxCapacity > 0 ? `${currentLevel.toFixed(1)} mt / ${maxCapacity.toFixed(1)} mt (${container.capacityUtilization})` : 'N/A';
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
            document.getElementById('detail-max-capacity').textContent = `${maxCapacity.toFixed(1)} mt`;
            document.getElementById('detail-current-level').textContent = `${currentLevel.toFixed(1)} mt`;
            document.getElementById('detail-capacity-utilization').textContent = container.capacityUtilization;
            document.getElementById('detail-company').textContent = container.company || 'N/A';

            if (editSettingsButton) {
                editSettingsButton.href = `/tanks/${encodeURIComponent(container.dbId)}/edit`;
            }

            enlargedContainerDisplay.classList.remove('active');
            setTimeout(() => {
                enlargedContainerDisplay.classList.add('active');
            }, 0);

            detailedAnalysisSection.classList.remove('visible');
            detailedAnalysisSection.style.display = 'none';
            moreDetailsButton.style.display = 'inline-block';
            moreDetailsButton.style.backgroundColor = container.liquidColor[0];
            moreDetailsButton.classList.remove('btn-primary', 'text-white');
        }, 50);
    }

    function populateDetailedAnalysis(container) {
        document.getElementById('analysis-container-id').textContent = container.id;

        // Populate rental history table
        rentalHistoryTable.innerHTML = '';
        if (container.rentalHistory && container.rentalHistory.length > 0) {
            container.rentalHistory.forEach(rental => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${rental.company}</td>
                    <td>${rental.product}</td>
                    <td>${rental.start_date}</td>
                    <td>${rental.end_date}</td>
                `;
                rentalHistoryTable.appendChild(row);
            });
        } else {
            const row = document.createElement('tr');
            row.innerHTML = '<td colspan="4">No rental history available.</td>';
            rentalHistoryTable.appendChild(row);
        }

        // Populate transactions table
        transactionsTable.innerHTML = '';
        if (container.transactions && container.transactions.length > 0) {
            container.transactions.forEach(transaction => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${transaction.id || 'N/A'}</td>
                    <td>${transaction.work_order_number || 'N/A'}</td>
                    <td>${transaction.bill_of_lading_number || 'N/A'}</td>
                    <td>${transaction.charge_permit_number || 'N/A'}</td>
                    <td>${transaction.discharge_permit_number || 'N/A'}</td>
                    <td>${transaction.type}</td>
                    <td>${transaction.quantity}</td>
                    <td>${transaction.date}</td>
                    <td>${transaction.company || 'N/A'}</td>
                    <td>${transaction.product || 'N/A'}</td>
                `;
                transactionsTable.appendChild(row);
            });
        } else {
            const row = document.createElement('tr');
            row.innerHTML = '<td colspan="10">No transactions available.</td>';
            transactionsTable.appendChild(row);
        }
    }

    let selectedContainer = null;
    moreDetailsButton.addEventListener('click', function() {
        selectedContainer = tanks.find(t => t.id === document.getElementById('detail-id').textContent);
        if (selectedContainer) {
            populateDetailedAnalysis(selectedContainer);
            detailedAnalysisSection.style.display = 'block';
            setTimeout(() => {
                detailedAnalysisSection.classList.add('visible');
            }, 0);
            moreDetailsButton.style.display = 'none';
        }
    });

    backButton.addEventListener('click', () => {
        console.log('Back to gallery clicked');
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
        renderGallery(tanks);
    });

    let searchTimeout;
    searchInput.addEventListener('input', () => {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            const searchTerm = searchInput.value.toLowerCase();
            console.log('Search term:', searchTerm);
            const filteredTanks = tanks.filter(container =>
                container.id.toLowerCase().includes(searchTerm) ||
                container.content.toLowerCase().includes(searchTerm) ||
                container.status.toLowerCase().includes(searchTerm)
            );
            renderGallery(filteredTanks);
        }, 300);
    });
});
</script>
@endsection
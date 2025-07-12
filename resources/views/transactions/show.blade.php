@extends('layouts.panel')

@section('title', 'Transaction Details')

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
        /* background: linear-gradient(135deg, #000b43 0%, #1e3a8a 100%);
        color: #f8f8f8; */
        /* padding: 2.5rem;
        /* border-radius: 16px; */
        margin-bottom: 2rem;
        /* box-shadow: 0 6px 20px rgba(0, 11, 67, 0.2);  */

    }

    .hero-header h1 {
        font-size: 2.25rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
    }

    .hero-header h4 {
        font-size: 1.5rem;
        font-weight: 600;
        margin-bottom: 0;
    }

    .card {
        border: none;
        border-radius: 16px;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        border-left: 4px solid #000b43;
    }

    .card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
    }

    .card-loading {
        border-left-color: #008001;
    }

    .card-discharging {
        border-left-color: #fef200;
        color: #1f2937;

    }

    .card-transfer {
        border-left-color: #000b43;
    }

    .card-header {
        background-color: #000b43;
        color: #000b43;
        border-radius: 16px 16px 0 0;
        padding: 1rem 1.5rem;
        font-weight: 600;
    }

    .card-body p {
        margin-bottom: 0.75rem;
        font-size: 1rem;
    }

    .card-body strong {
        color: #000b43;
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
        color: #000000 !important;
    }

    .badge-transfer {
        background-color: #000b43;
        color: #f8f8f8;
    }

    .btn {
        border-radius: 10px;
        padding: 0.6rem 1.5rem;
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
    
    .list-group-item {
        border-radius: 10px;
        margin-bottom: 0.5rem;
        border: 1px solid #d1d5db;
        transition: background-color 0.2s ease;
    }

    .list-group-item a {
        color: #000b43;
        text-decoration: none;
        font-weight: 500;
    }

    .list-group-item:hover {
        background-color: #f3f4f6;
    }

    @media (max-width: 768px) {
        .hero-header h1 {
            font-size: 1.75rem;
        }

        .hero-header h4 {
            font-size: 1.25rem;
        }

        .card-body p {
            font-size: 0.875rem;
        }

        .btn {
            padding: 0.5rem 1rem;
        }
    }

</style>

@section('content')
<div class="main-content side-content my-2 pt-0">
    <div class="container-fluid px-4 py-4">
        <div class="inner-body">
            <!-- Page Header -->
            <div class="hero-header">
                <h1>Transaction Details</h1>
                <h4 class="mb-0">Transaction #{{ $transaction->id }} - <span class="badge badge-{{ $transaction->type }}">{{ ucfirst($transaction->type) }}</span></h4>
            </div>

            <!-- Transaction Details -->
            <div class="card card-{{ $transaction->type }} mb-4">
                <div class="card-header">Transaction Information</div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Source Tank:</strong> {{ $transaction->tank->number ?? 'N/A' }}</p>

                            @if($transaction->type === 'transfer')
                            <p><strong>Destination Tank:</strong> {{ $transaction->destinationTank->number ?? 'N/A' }}</p>
                            @endif

                            <p><strong>Original Vessel:</strong> {{ $transaction->originalVessel->name ?? 'N/A' }}</p>
                            <p><strong>Company:</strong> {{ $transaction->company->name ?? 'N/A' }}</p>
                            <p><strong>Product:</strong> {{ $transaction->product->name ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Quantity:</strong> {{ $transaction->quantity }} MT</p>
                            <p><strong>Date:</strong> {{ $transaction->date->format('Y-m-d H:i') }}</p>
                            <p><strong>Work Order Number:</strong> {{ $transaction->work_order_number ?? 'N/A' }}</p>
                            <p><strong>Bill of Lading Number:</strong> {{ $transaction->bill_of_lading_number ?? 'N/A' }}</p>
                            <p><strong>Customs Release Number:</strong> {{ $transaction->customs_release_number ?? 'N/A' }}</p>

                            @if(in_array($transaction->type, ['transfer', 'loading']))
                            <p><strong>Charge Permit Number:</strong> {{ $transaction->charge_permit_number ?? 'N/A' }}</p>
                            @endif

                            @if(in_array($transaction->type, ['transfer', 'discharging']))
                            <p><strong>Discharge Permit Number:</strong> {{ $transaction->discharge_permit_number ?? 'N/A' }}</p>
                            @endif

                            <p><strong>Engineer:</strong> {{ $transaction->engineer->full_name ?? 'N/A' }}</p>
                            <p><strong>Technician:</strong> {{ $transaction->technician->full_name ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            @if($transaction->shipment)
            <div class="card card-{{ $transaction->type }} mb-4">
                <div class="card-header">Shipment Details</div>
                <div class="card-body">
                    <p><strong>Transport Type:</strong> {{ ucfirst($transaction->shipment->transport_type) }}</p>
                    @if($transaction->shipment->vessel_id)
                    <p><strong>Vessel:</strong> {{ $transaction->shipment->vessel->name ?? 'N/A' }}</p>
                    @endif
                    @if($transaction->shipment->truck_number)
                    <p><strong>Truck Number:</strong> {{ $transaction->shipment->truck_number }}</p>
                    @endif
                    @if($transaction->shipment->trailer_number)
                    <p><strong>Trailer Number:</strong> {{ $transaction->shipment->trailer_number }}</p>
                    @endif
                    @if($transaction->shipment->driver_name)
                    <p><strong>Driver Name:</strong> {{ $transaction->shipment->driver_name }}</p>
                    @endif
                    @if($transaction->shipment->berth_number)
                    <p><strong>Port of Discharge:</strong> {{ $transaction->shipment->berth_number }}</p>
                    @endif
                    @if($transaction->shipment->arrival_date)
                    <p><strong>Arrival Date:</strong> {{ $transaction->shipment->arrival_date->format('Y-m-d H:i') }}</p>
                    @endif
                    <p><strong>Total Quantity:</strong> {{ $transaction->shipment->total_quantity }} MT</p>
                </div>
            </div>
            @endif

            @if($transaction->delivery)
            <div class="card card-{{ $transaction->type }} mb-4">
                <div class="card-header">Delivery Details</div>
                <div class="card-body">
                    <p><strong>Transport Type:</strong> {{ ucfirst($transaction->delivery->transport_type) }}</p>
                    @if($transaction->delivery->vessel_id)
                    <p><strong>Vessel:</strong> {{ $transaction->delivery->vessel->name ?? 'N/A' }}</p>
                    @endif
                    @if($transaction->delivery->truck_number)
                    <p><strong>Truck Number:</strong> {{ $transaction->delivery->truck_number }}</p>
                    @endif
                    @if($transaction->delivery->trailer_number)
                    <p><strong>Trailer Number:</strong> {{ $transaction->delivery->trailer_number }}</p>
                    @endif
                    @if($transaction->delivery->driver_name)
                    <p><strong>Driver Name:</strong> {{ $transaction->delivery->driver_name }}</p>
                    @endif
                    <p><strong>Quantity:</strong> {{ $transaction->delivery->quantity }} MT</p>
                    <p><strong>Delivery Date:</strong> {{ $transaction->delivery->delivery_date->format('Y-m-d H:i') }}</p>
                </div>
            </div>
            @endif

            @if($transaction->documents->count() > 0)
            <div class="card card-{{ $transaction->type }} mb-4">
                <div class="card-header">Documents</div>
                <div class="card-body">
                    <ul class="list-group">
                        @foreach($transaction->documents as $doc)
                        <li class="list-group-item">
                            <a href="{{ asset('storage/' . $doc->file_path) }}" target="_blank">{{ $doc->file_name }} ({{ $doc->type }})</a>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
            @endif

            <a href="{{ route('transactions.index') }}" class="btn btn-primary">Back to List</a>
        </div>
    </div>
</div>
@endsection

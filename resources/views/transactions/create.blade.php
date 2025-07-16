@extends('layouts.panel')

@section('title', isset($transaction) ? 'Duplicate Transaction' : 'Create Transaction')

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

    .form-control, .form-select {
        border-radius: 8px;
        border: 1px solid #d1d5db;
        transition: border-color 0.2s ease, box-shadow 0.2s ease;
        height: 38px;
        font-size: 0.875rem;
    }

    .form-control:focus, .form-select:focus {
        border-color: #2563eb;
        box-shadow: 0 0 0 0.2rem rgba(37, 99, 235, 0.2);
        outline: none;
    }

    .form-label {
        font-size: 0.875rem;
        font-weight: 500;
        color: #1f2937;
        margin-bottom: 0.5rem;
    }

    .required::after {
        content: '*';
        color: #dc2626;
        font-weight: 700;
        margin-left: 0.25rem;
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

    .text-danger, .file-note {
        font-size: 0.75rem;
        margin-top: 0.25rem;
    }

    .file-note {
        color: #6b7280;
    }

    .section-header {
        font-size: 1.25rem;
        font-weight: 600;
        color: #1f2937;
        margin-bottom: 1rem;
    }

    .tank-info {
        font-size: 0.875rem;
        color: #4b5563;
        margin-left: 0.5rem;
        font-weight: 400;
    }

    .company-logo {
        width: 50px;
        height: 50px;
        object-fit: contain;
        border-radius: 4px;
        border: 1px solid #e5e7eb;
        background-color: #ffffff;
        vertical-align: middle;
    }

    .transaction-type-container {
        display: flex;
        justify-content: space-between;
        align-items: end;
        width: 100%;
        gap: 1rem;
    }

    @media (max-width: 768px) {
        .hero-header h2 {
            font-size: 1.75rem;
        }
        .form-control, .form-select {
            font-size: 0.75rem;
            height: 34px;
        }
        .form-label {
            font-size: 0.75rem;
        }
        .btn {
            padding: 0.4rem 0.8rem;
            font-size: 0.875rem;
        }
        .section-header {
            font-size: 1.125rem;
        }
        .tank-info {
            font-size: 0.75rem;
        }
        .file-note {
            font-size: 0.7rem;
        }
        .company-logo {
            width: 40px;
            height: 40px;
        }
        .transaction-type-container {
            gap: 0.5rem;
        }
    }
</style>
@endsection

@section('content')
<div class="main-content side-content my-2 pt-0">
    <div class="container-fluid px-4 py-4">
        <div class="inner-body">
            @can('create', \App\Models\Transaction::class)
            <div class="hero-header">
                <div class="container">
                    <div class="d-flex justify-content-between align-items-center">
                        <h2 class="my-3 my-md-0">
                            <i class="fas fa-exchange-alt me-2"></i>{{ isset($transaction) ? 'Duplicate Transaction' : 'Create Transaction' }}
                        </h2>
                        <a href="{{ route('transactions.index') }}" class="btn btn-secondary d-flex align-items-center">
                            <i class="fas fa-arrow-left me-2"></i>Back to Transactions
                        </a>
                    </div>
                </div>
            </div>

            @include('components.alerts')

            <div class="card">
                <div class="card-body p-4">
                    <form action="{{ route('transactions.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <!-- Transaction Type Dropdown -->
                        <div class="row mb-3">
                            <div class="col-12 col-md-6">
                                <div class="transaction-type-container">
                                    <div style="flex-grow: 1;">
                                        <label for="type" class="form-label required">Transaction Type</label>
                                        <select name="type" id="type" class="form-select" required>
                                            <option value="">Select Type</option>
                                            <option value="loading" {{ (isset($transaction) && $transaction->type == 'loading') || old('type') == 'loading' ? 'selected' : '' }}>Loading</option>
                                            <option value="discharging" {{ (isset($transaction) && $transaction->type == 'discharging') || old('type') == 'discharging' ? 'selected' : '' }}>Discharging</option>
                                            <option value="transfer" {{ (isset($transaction) && $transaction->type == 'transfer') || old('type') == 'transfer' ? 'selected' : '' }}>Transfer</option>
                                        </select>
                                        @error('type')
                                        <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <img id="company-logo" src="{{ isset($transaction) && $transaction->company && $transaction->company->logo_url ? $transaction->company->logo_url : '' }}" alt="Company Logo" class="company-logo" style="{{ isset($transaction) && $transaction->company && $transaction->company->logo_url ? 'display: inline;' : 'display: none;' }}">
                                </div>
                            </div>
                        </div>

                        <!-- Transaction Details -->
                        <div class="form-field" style="display: none;">
                            <h4 class="section-header">Transaction Details</h4>
                            <div class="row mb-3">
                                <div class="col-12 col-md-6 mb-3 mb-md-0">
                                    <label for="tank_id" class="form-label required">
                                        Source Tank
                                        <span id="source-tank-info" class="tank-info"></span>
                                    </label>
                                    <select name="tank_id" id="tank_id" class="form-select" required>
                                        <option value="">Select Tank</option>
                                        @foreach($tanks as $tank)
                                        <option value="{{ $tank->id }}" {{ (isset($transaction) && $transaction->tank_id == $tank->id) || old('tank_id') == $tank->id ? 'selected' : '' }}>{{ $tank->number }}</option>
                                        @endforeach
                                    </select>
                                    @error('tank_id')
                                    <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-12 col-md-6">
                                    <label for="original_vessel_id" class="form-label">Original Vessel</label>
                                    <select name="original_vessel_id" id="original_vessel_id" class="form-select">
                                        <option value="">None</option>
                                        @foreach($vessels as $vessel)
                                        <option value="{{ $vessel->id }}" {{ (isset($transaction) && $transaction->original_vessel_id == $vessel->id) || old('original_vessel_id') == $vessel->id ? 'selected' : '' }}>{{ $vessel->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('original_vessel_id')
                                    <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-12 col-md-6 mb-3 mb-md-0">
                                    <label for="company_name" class="form-label">Company</label>
                                    <input type="text" id="company_name" class="form-control" value="{{ isset($transaction) && $transaction->company ? $transaction->company->name : old('company_name') }}" disabled>
                                    @error('company_name')
                                    <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-12 col-md-6">
                                    <label for="product_name" class="form-label">Product</label>
                                    <input type="text" id="product_name" class="form-control" value="{{ isset($transaction) && $transaction->product ? $transaction->product->name : old('product_name') }}" disabled>
                                    @error('product_name')
                                    <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="row mb-3" id="destination_tank_id_field" style="display: none;">
                                <div class="col-12 col-md-6">
                                    <label for="destination_tank_id" class="form-label required">
                                        Destination Tank
                                        <span id="destination-tank-info" class="tank-info"></span>
                                    </label>
                                    <select name="destination_tank_id" id="destination_tank_id" class="form-select">
                                        <option value="">Select Tank</option>
                                        @if(isset($transaction) && $transaction->type == 'transfer')
                                        @foreach($tanks as $tank)
                                        <option value="{{ $tank->id }}" {{ $transaction->destination_tank_id == $tank->id ? 'selected' : '' }}>{{ $tank->number }}</option>
                                        @endforeach
                                        @endif
                                    </select>
                                    @error('destination_tank_id')
                                    <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-12 col-md-6 mb-3 mb-md-0">
                                    <label for="quantity" class="form-label required">Quantity (MT)</label>
                                    <input type="number" name="quantity" id="quantity" class="form-control" step="0.01" min="0.01" value="{{ isset($transaction) ? $transaction->quantity : old('quantity') }}" required>
                                    <span id="quantity-error" class="text-danger" style="display: none;"></span>
                                    @error('quantity')
                                    <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-12 col-md-6">
                                    <label for="date" class="form-label required">Date</label>
                                    <input type="datetime-local" name="date" id="date" class="form-control" value="{{ isset($transaction) ? $transaction->date->format('Y-m-d\TH:i') : old('date') }}" required>
                                    @error('date')
                                    <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Shipment Details Section (Shown for Loading) -->
                        <div id="shipment_details" class="form-field" style="display: none;">
                            <h4 class="section-header">Shipment Details</h4>
                            <div class="row mb-3">
                                <div class="col-12 col-md-6 mb-3 mb-md-0">
                                    <label for="shipment_transport_type" class="form-label">Transport Type</label>
                                    <select name="shipment[transport_type]" id="shipment_transport_type" class="form-select">
                                        <option value="">Select Transport Type</option>
                                        <option value="vessel" {{ (isset($transaction) && $transaction->shipment && $transaction->shipment->transport_type == 'vessel') || old('shipment.transport_type') == 'vessel' ? 'selected' : '' }}>Vessel</option>
                                        <option value="truck" {{ (isset($transaction) && $transaction->shipment && $transaction->shipment->transport_type == 'truck') || old('shipment.transport_type') == 'truck' ? 'selected' : '' }}>Truck</option>
                                    </select>
                                    @error('shipment.transport_type')
                                    <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-12 col-md-6" id="shipment_vessel_id_field" style="display: none;">
                                    <label for="shipment_vessel_id" class="form-label">Vessel</label>
                                    <select name="shipment[vessel_id]" id="shipment_vessel_id" class="form-select">
                                        <option value="">Select Vessel</option>
                                        @foreach($vessels as $vessel)
                                        <option value="{{ $vessel->id }}" {{ (isset($transaction) && $transaction->shipment && $transaction->shipment->vessel_id == $vessel->id) || old('shipment.vessel_id') == $vessel->id ? 'selected' : '' }}>{{ $vessel->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('shipment.vessel_id')
                                    <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div id="shipment_truck_fields" class="form-field" style="display: none;">
                                <div class="row mb-3">
                                    <div class="col-12 col-md-6 mb-3 mb-md-0">
                                        <label for="shipment_truck_number" class="form-label">Truck</label>
                                        <select name="shipment[truck_number]" id="shipment_truck_number" class="form-select">
                                            <option value="">Select Truck</option>
                                            @foreach($trucks as $truck)
                                            <option value="{{ $truck->truck_number }}" {{ (isset($transaction) && $transaction->shipment && $transaction->shipment->truck_number == $truck->truck_number) || old('shipment.truck_number') == $truck->truck_number ? 'selected' : '' }}>{{ $truck->truck_number }}</option>
                                            @endforeach
                                        </select>
                                        @error('shipment.truck_number')
                                        <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <label for="shipment_trailer_number" class="form-label">Trailer</label>
                                        <select name="shipment[trailer_number]" id="shipment_trailer_number" class="form-select">
                                            <option value="">Select Trailer</option>
                                            @foreach($trailers as $trailer)
                                            <option value="{{ $trailer->trailer_number }}" {{ (isset($transaction) && $transaction->shipment && $transaction->shipment->trailer_number == $trailer->trailer_number) || old('shipment.trailer_number') == $trailer->trailer_number ? 'selected' : '' }}>{{ $trailer->trailer_number }}</option>
                                            @endforeach
                                        </select>
                                        @error('shipment.trailer_number')
                                        <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-12 col-md-6">
                                        <label for="shipment_driver_name" class="form-label">Driver</label>
                                        <select name="shipment[driver_name]" id="shipment_driver_name" class="form-select">
                                            <option value="">Select Driver</option>
                                            @foreach($drivers as $driver)
                                            <option value="{{ $driver->name }}" {{ (isset($transaction) && $transaction->shipment && $transaction->shipment->driver_name == $driver->name) || old('shipment.driver_name') == $driver->name ? 'selected' : '' }}>{{ $driver->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('shipment.driver_name')
                                        <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-3" id="shipment_berth_number_field" style="display: none;">
                                <div class="col-12 col-md-6">
                                    <label for="shipment_berth_number" class="form-label">Berth Number</label>
                                    <input type="text" name="shipment[berth_number]" id="shipment_berth_number" class="form-control" value="{{ isset($transaction) && $transaction->shipment ? $transaction->shipment->berth_number : old('shipment.berth_number') }}">
                                    @error('shipment.berth_number')
                                    <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Delivery Details Section (Shown for Discharging) -->
                        <div id="delivery_details" class="form-field" style="display: none;">
                            <h4 class="section-header">Delivery Details</h4>
                            <div class="row mb-3">
                                <div class="col-12 col-md-6 mb-3 mb-md-0">
                                    <label for="delivery_transport_type" class="form-label">Transport Type</label>
                                    <select name="delivery[transport_type]" id="delivery_transport_type" class="form-select">
                                        <option value="">Select Transport Type</option>
                                        <option value="vessel" {{ (isset($transaction) && $transaction->delivery && $transaction->delivery->transport_type == 'vessel') || old('delivery.transport_type') == 'vessel' ? 'selected' : '' }}>Vessel</option>
                                        <option value="truck" {{ (isset($transaction) && $transaction->delivery && $transaction->delivery->transport_type == 'truck') || old('delivery.transport_type') == 'truck' ? 'selected' : '' }}>Truck</option>
                                    </select>
                                    @error('delivery.transport_type')
                                    <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-12 col-md-6" id="delivery_vessel_id_field" style="display: none;">
                                    <label for="delivery_vessel_id" class="form-label">Vessel</label>
                                    <select name="delivery[vessel_id]" id="delivery_vessel_id" class="form-select">
                                        <option value="">Select Vessel</option>
                                        @foreach($vessels as $vessel)
                                        <option value="{{ $vessel->id }}" {{ (isset($transaction) && $transaction->delivery && $transaction->delivery->vessel_id == $vessel->id) || old('delivery.vessel_id') == $vessel->id ? 'selected' : '' }}>{{ $vessel->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('delivery.vessel_id')
                                    <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div id="delivery_truck_fields" class="form-field" style="display: none;">
                                <div class="row mb-3">
                                    <div class="col-12 col-md-6 mb-3 mb-md-0">
                                        <label for="delivery_truck_number" class="form-label">Truck</label>
                                        <select name="delivery[truck_number]" id="delivery_truck_number" class="form-select">
                                            <option value="">Select Truck</option>
                                            @foreach($trucks as $truck)
                                            <option value="{{ $truck->truck_number }}" {{ (isset($transaction) && $transaction->delivery && $transaction->delivery->truck_number == $truck->truck_number) || old('delivery.truck_number') == $truck->truck_number ? 'selected' : '' }}>{{ $truck->truck_number }}</option>
                                            @endforeach
                                        </select>
                                        @error('delivery.truck_number')
                                        <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <label for="delivery_trailer_number" class="form-label">Trailer</label>
                                        <select name="delivery[trailer_number]" id="delivery_trailer_number" class="form-select">
                                            <option value="">Select Trailer</option>
                                            @foreach($trailers as $trailer)
                                            <option value="{{ $trailer->trailer_number }}" {{ (isset($transaction) && $transaction->delivery && $transaction->delivery->trailer_number == $trailer->trailer_number) || old('delivery.trailer_number') == $trailer->trailer_number ? 'selected' : '' }}>{{ $trailer->trailer_number }}</option>
                                            @endforeach
                                        </select>
                                        @error('delivery.trailer_number')
                                        <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-12 col-md-6">
                                        <label for="delivery_driver_name" class="form-label">Driver</label>
                                        <select name="delivery[driver_name]" id="delivery_driver_name" class="form-select">
                                            <option value="">Select Driver</option>
                                            @foreach($drivers as $driver)
                                            <option value="{{ $driver->name }}" {{ (isset($transaction) && $transaction->delivery && $transaction->delivery->driver_name == $driver->name) || old('delivery.driver_name') == $driver->name ? 'selected' : '' }}>{{ $driver->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('delivery.driver_name')
                                        <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Personnel Section -->
                        <div class="form-field" style="display: none;">
                            <h4 class="section-header">Personnel</h4>
                            <div class="row mb-3">
                                <div class="col-12 col-md-6 mb-3 mb-md-0">
                                    <label for="engineer_id" class="form-label">Engineer</label>
                                    <select name="engineer_id" id="engineer_id" class="form-select">
                                        <option value="">None</option>
                                        @foreach($engineers as $engineer)
                                        <option value="{{ $engineer->id }}" {{ (isset($transaction) && $transaction->engineer_id == $engineer->id) || old('engineer_id') == $engineer->id ? 'selected' : '' }}>{{ $engineer->full_name }}</option>
                                        @endforeach
                                    </select>
                                    @error('engineer_id')
                                    <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-12 col-md-6">
                                    <label for="technician_id" class="form-label">Technician</label>
                                    <select name="technician_id" id="technician_id" class="form-select">
                                        <option value="">None</option>
                                        @foreach($technicians as $technician)
                                        <option value="{{ $technician->id }}" {{ (isset($transaction) && $transaction->technician_id == $technician->id) || old('technician_id') == $technician->id ? 'selected' : '' }}>{{ $technician->full_name }}</option>
                                        @endforeach
                                    </select>
                                    @error('technician_id')
                                    <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Permits Section -->
                        <div class="form-field" style="display: none;">
                            <h4 class="section-header">Permits</h4>
                            <div class="row mb-3">
                                <div class="col-12 col-md-6 mb-3 mb-md-0">
                                    <label for="work_order_number" class="form-label">Work Order Number</label>
                                    <input type="text" name="work_order_number" id="work_order_number" class="form-control" value="{{ isset($transaction) ? $transaction->work_order_number : old('work_order_number') }}">
                                    @error('work_order_number')
                                    <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-12 col-md-6">
                                    <label for="bill_of_lading_number" class="form-label required">Bill of Lading Number</label>
                                    <input type="text" name="bill_of_lading_number" id="bill_of_lading_number" class="form-control" value="{{ isset($transaction) ? $transaction->bill_of_lading_number : old('bill_of_lading_number') }}" required>
                                    @error('bill_of_lading_number')
                                    <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-12 col-md-6 mb-3 mb-md-0">
                                    <label for="customs_release_number" class="form-label">Customs Release Number</label>
                                    <input type="text" name="customs_release_number" id="customs_release_number" class="form-control" value="{{ isset($transaction) ? $transaction->customs_release_number : old('customs_release_number') }}">
                                    @error('customs_release_number')
                                    <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-12 col-md-6" id="charge_permit_number_field" style="display: none;">
                                    <label for="charge_permit_number" class="form-label">Charge Permit Number</label>
                                    <input type="text" name="charge_permit_number" id="charge_permit_number" class="form-control" value="{{ isset($transaction) ? $transaction->charge_permit_number : old('charge_permit_number') }}">
                                    @error('charge_permit_number')
                                    <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-12 col-md-6" id="discharge_permit_number_field" style="display: none;">
                                    <label for="discharge_permit_number" class="form-label">Discharge Permit Number</label>
                                    <input type="text" name="discharge_permit_number" id="discharge_permit_number" class="form-control" value="{{ isset($transaction) ? $transaction->discharge_permit_number : old('discharge_permit_number') }}">
                                    @error('discharge_permit_number')
                                    <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Documents Section -->
                        <div class="form-field" style="display: none;">
                            <h4 class="section-header">Documents</h4>
                            <div class="row mb-3">
                                <div class="col-12 col-md-6 mb-3 mb-md-0">
                                    <label for="measurement_report" class="form-label">Measurement Report</label>
                                    <input type="file" name="measurement_report" id="measurement_report" class="form-control" accept=".pdf,image/*" max-size="2048">
                                    <div class="file-note">Accepted formats: PDF, JPG, PNG. Maximum file size: 2MB.</div>
                                    @error('measurement_report')
                                    <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-12 col-md-6 mb-3 mb-md-0">
                                    <label for="inspection_form" class="form-label">Inspection Form</label>
                                    <input type="file" name="inspection_form" id="inspection_form" class="form-control" accept=".pdf,image/*" max-size="2048">
                                    <div class="file-note">Accepted formats: PDF, JPG, PNG. Maximum file size: 2MB.</div>
                                    @error('inspection_form')
                                    <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-12 col-md-6">
                                    <label for="customs_release_form" class="form-label">Customs Release Form</label>
                                    <input type="file" name="customs_release_form" id="customs_release_form" class="form-control" accept=".pdf,image/*" max-size="2048">
                                    <div class="file-note">Accepted formats: PDF, JPG, PNG. Maximum file size: 2MB.</div>
                                    @error('customs_release_form')
                                    <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-12 col-md-6" id="charge_permit_document_field" style="display: none;">
                                    <label for="charge_permit_document" class="form-label">Charge Permit Document</label>
                                    <input type="file" name="charge_permit_document" id="charge_permit_document" class="form-control" accept=".pdf,image/*" max-size="2048">
                                    <div class="file-note">Accepted formats: PDF, JPG, PNG. Maximum file size: 2MB.</div>
                                    @error('charge_permit_document')
                                    <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-12 col-md-6" id="discharge_permit_document_field" style="display: none;">
                                    <label for="discharge_permit_document" class="form-label">Discharge Permit Document</label>
                                    <input type="file" name="discharge_permit_document" id="discharge_permit_document" class="form-control" accept=".pdf,image/*" max-size="2048">
                                    <div class="file-note">Accepted formats: PDF, JPG, PNG. Maximum file size: 2MB.</div>
                                    @error('discharge_permit_document')
                                    <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Form Buttons -->
                        <div class="d-flex gap-2 mt-4 form-field" style="display: none;">
                            <button type="submit" class="btn btn-primary">{{ isset($transaction) ? 'Save Duplicated Transaction' : 'Save Transaction' }}</button>
                            <a href="{{ route('transactions.index') }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
            @endcan
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const typeSelect = document.getElementById('type');
    const formFields = document.querySelectorAll('.form-field');
    const shipmentDetails = document.getElementById('shipment_details');
    const deliveryDetails = document.getElementById('delivery_details');
    const destinationTankField = document.getElementById('destination_tank_id_field');
    const chargePermitNumberField = document.getElementById('charge_permit_number_field');
    const dischargePermitNumberField = document.getElementById('discharge_permit_number_field');
    const chargePermitDocumentField = document.getElementById('charge_permit_document_field');
    const dischargePermitDocumentField = document.getElementById('discharge_permit_document_field');
    const shipmentTransportType = document.getElementById('shipment_transport_type');
    const deliveryTransportType = document.getElementById('delivery_transport_type');
    const sourceTankInfo = document.getElementById('source-tank-info');
    const destinationTankInfo = document.getElementById('destination-tank-info');
    const sourceTankSelect = document.getElementById('tank_id');
    const destinationTankSelect = document.getElementById('destination_tank_id');
    const quantityInput = document.getElementById('quantity');
    const companyLogo = document.getElementById('company-logo');
    const form = document.querySelector('form');

    let sourceTankData = { current_level: 0, max_capacity: 0, free_space: 0 };
    let destinationTankData = { current_level: 0, max_capacity: 0, free_space: 0 };
    let lastSourceTankId = null;

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

    formFields.forEach(field => {
        if (!field.querySelector('#type')) {
            field.style.display = 'none';
        }
    });

    typeSelect.addEventListener('change', function() {
        const type = this.value;

        formFields.forEach(field => {
            if (!field.querySelector('#type')) {
                field.style.display = 'none';
            }
        });

        destinationTankField.style.display = 'none';
        chargePermitNumberField.style.display = 'none';
        dischargePermitNumberField.style.display = 'none';
        chargePermitDocumentField.style.display = 'none';
        dischargePermitDocumentField.style.display = 'none';
        shipmentDetails.style.display = 'none';
        deliveryDetails.style.display = 'none';
        sourceTankInfo.textContent = '';
        destinationTankInfo.textContent = '';
        companyLogo.style.display = 'none';
        sourceTankData = { current_level: 0, max_capacity: 0, free_space: 0 };
        destinationTankData = { current_level: 0, max_capacity: 0, free_space: 0 };
        sourceTankSelect.innerHTML = '<option value="">Select Tank</option>';
        if (destinationTankSelect) {
            destinationTankSelect.innerHTML = '<option value="">Select Tank</option>';
        }
        quantityInput.removeAttribute('max');
        document.getElementById('quantity-error').style.display = 'none';
        lastSourceTankId = null;

        const fieldsToReset = [
            'destination_tank_id', 'work_order_number', 'customs_release_number',
            'charge_permit_number', 'discharge_permit_number',
            'measurement_report', 'inspection_form', 'customs_release_form',
            'charge_permit_document', 'discharge_permit_document',
            'shipment_vessel_id', 'shipment_truck_number', 'shipment_trailer_number',
            'shipment_driver_name', 'shipment_berth_number',
            'delivery_vessel_id', 'delivery_truck_number', 'delivery_trailer_number',
            'delivery_driver_name'
        ];
        fieldsToReset.forEach(id => {
            const element = document.getElementById(id);
            if (element) element.required = false;
        });

        document.getElementById('bill_of_lading_number').required = true;

        if (type) {
            formFields.forEach(field => {
                if (field.querySelector('#tank_id') || field.querySelector('#original_vessel_id') ||
                    field.querySelector('#company_name') || field.querySelector('#product_name') ||
                    field.querySelector('#quantity') || field.querySelector('#date') ||
                    field.querySelector('#work_order_number') || field.querySelector('#bill_of_lading_number') ||
                    field.querySelector('#customs_release_number') || field.querySelector('#engineer_id') ||
                    field.querySelector('#technician_id') ||
                    field.querySelector('#measurement_report') || field.querySelector('#inspection_form') ||
                    field.querySelector('#customs_release_form') ||
                    field.tagName === 'DIV' && field.classList.contains('d-flex')) {
                    field.style.display = 'block';
                }
            });

            if (type === 'transfer') {
                destinationTankField.style.display = 'block';
                chargePermitNumberField.style.display = 'block';
                dischargePermitNumberField.style.display = 'block';
                chargePermitDocumentField.style.display = 'block';
                dischargePermitDocumentField.style.display = 'block';
                document.getElementById('destination_tank_id').required = true;
            } else if (type === 'loading') {
                chargePermitNumberField.style.display = 'block';
                chargePermitDocumentField.style.display = 'block';
                shipmentDetails.style.display = 'block';
            } else if (type === 'discharging') {
                dischargePermitNumberField.style.display = 'block';
                dischargePermitDocumentField.style.display = 'block';
                deliveryDetails.style.display = 'block';
            }

            fetchAvailableTanks(type);
        }
    });

    const fetchAvailableTanks = debounce(function(type) {
        if (!type) return;

        const sourceTankId = type === 'transfer' ? sourceTankSelect.value : '';
        const url = `/api/tanks/available?type=${type}${sourceTankId ? `&source_tank_id=${sourceTankId}` : ''}`;

        fetch(url, {
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
            .then(response => response.json())
            .then(data => {
                const sourceTankId = sourceTankSelect.value;
                const destinationTankId = destinationTankSelect?.value || '';
                sourceTankSelect.innerHTML = '<option value="">Select Tank</option>';
                if (destinationTankSelect) {
                    destinationTankSelect.innerHTML = '<option value="">Select Tank</option>';
                }

                data.source_tanks.forEach(tank => {
                    const option = document.createElement('option');
                    option.value = tank.id;
                    option.textContent = tank.number;
                    if (tank.id == sourceTankId) option.selected = true;
                    sourceTankSelect.appendChild(option);
                });

                if (type === 'transfer' && data.destination_tanks) {
                    data.destination_tanks.forEach(tank => {
                        const option = document.createElement('option');
                        option.value = tank.id;
                        option.textContent = tank.number;
                        if (tank.id == destinationTankId) option.selected = true;
                        destinationTankSelect.appendChild(option);
                    });
                }

                if (sourceTankId && sourceTankId === lastSourceTankId) {
                    fetchTankData(sourceTankId, true);
                }
                if (type === 'transfer' && destinationTankId) {
                    fetchTankData(destinationTankId, false);
                }
            })
            .catch(error => {
                console.error('Error fetching available tanks:', error);
                sourceTankSelect.innerHTML = '<option value="">Select Tank</option>';
                if (destinationTankSelect) {
                    destinationTankSelect.innerHTML = '<option value="">Select Tank</option>';
                }
            });
    }, 300);

    function fetchTankData(tankId, isSourceTank = true) {
        if (!tankId) {
            if (isSourceTank) {
                sourceTankInfo.textContent = '';
                document.getElementById('product_name').value = 'N/A';
                document.getElementById('company_name').value = 'N/A';
                companyLogo.style.display = 'none';
                sourceTankData = { current_level: 0, max_capacity: 0, free_space: 0 };
                lastSourceTankId = null;
            } else {
                destinationTankInfo.textContent = '';
                destinationTankData = { current_level: 0, max_capacity: 0, free_space: 0 };
            }
            updateQuantityValidation();
            if (isSourceTank && typeSelect.value === 'transfer') {
                fetchAvailableTanks('transfer');
            }
            return;
        }

        if (isSourceTank) {
            if (tankId === lastSourceTankId) {
                return;
            }
            lastSourceTankId = tankId;
        }

        fetch(`/api/tanks/${tankId}/capacity`, {
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
            .then(response => response.json())
            .then(data => {
                const type = typeSelect.value;
                const currentLevel = parseFloat(data.current_level) || 0;
                const maxCapacity = parseFloat(data.max_capacity) || 0;
                const freeSpace = maxCapacity - currentLevel;

                if (isSourceTank) {
                    sourceTankData = { current_level: currentLevel, max_capacity: maxCapacity, free_space: freeSpace };
                    if (type === 'loading') {
                        sourceTankInfo.textContent = maxCapacity > 0 ? `(Free Space: ${freeSpace.toFixed(2)} MT)` : '(No capacity)';
                    } else if (type === 'discharging' || type === 'transfer') {
                        sourceTankInfo.textContent = maxCapacity > 0 ? `(Current Capacity: ${currentLevel.toFixed(2)} MT)` : '(No capacity)';
                    }
                } else {
                    destinationTankData = { current_level: currentLevel, max_capacity: maxCapacity, free_space: freeSpace };
                    if (type === 'transfer') {
                        destinationTankInfo.textContent = maxCapacity > 0 ? `(Free Space: ${freeSpace.toFixed(2)} MT)` : '(No capacity)';
                    }
                }
                updateQuantityValidation();

                if (isSourceTank && type === 'transfer') {
                    fetchAvailableTanks('transfer');
                }
            })
            .catch(error => {
                console.error('Error fetching tank capacity:', error);
                if (isSourceTank) {
                    sourceTankInfo.textContent = '';
                    sourceTankData = { current_level: 0, max_capacity: 0, free_space: 0 };
                } else {
                    destinationTankInfo.textContent = '';
                    destinationTankData = { current_level: 0, max_capacity: 0, free_space: 0 };
                }
                updateQuantityValidation();
            });

        fetch(`/api/tanks/${tankId}/product`, {
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
            .then(response => response.json())
            .then(data => {
                if (isSourceTank) {
                    document.getElementById('product_name').value = data.product_name || 'N/A';
                }
            })
            .catch(error => {
                console.error('Error fetching product:', error);
                if (isSourceTank) {
                    document.getElementById('product_name').value = 'N/A';
                }
            });

        if (isSourceTank) {
            fetch(`/api/tanks/${tankId}/company`, {
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
                .then(response => response.json())
                .then(data => {
                    document.getElementById('company_name').value = data.company_name || 'N/A';
                    if (data.logo_url) {
                        companyLogo.src = data.logo_url;
                        companyLogo.alt = data.company_name || 'Company Logo';
                        companyLogo.style.display = 'inline';
                    } else {
                        companyLogo.style.display = 'none';
                    }
                })
                .catch(error => {
                    console.error('Error fetching company:', error);
                    document.getElementById('company_name').value = 'N/A';
                    companyLogo.style.display = 'none';
                });
        }
    }

    function updateQuantityValidation() {
        const type = typeSelect.value;
        const quantityError = document.getElementById('quantity-error');
        quantityInput.removeAttribute('max');
        quantityError.style.display = 'none';

        if (!type || sourceTankData.max_capacity === 0) {
            quantityInput.disabled = sourceTankData.max_capacity === 0;
            if (sourceTankData.max_capacity === 0) {
                quantityError.textContent = 'Selected tank has no capacity';
                quantityError.style.display = 'block';
                quantityInput.setCustomValidity('Selected tank has no capacity');
            }
            return;
        }

        let maxQuantity = 0;
        if (type === 'loading') {
            maxQuantity = sourceTankData.free_space;
            quantityInput.setAttribute('max', maxQuantity.toFixed(2));
            quantityError.textContent = `Maximum quantity: ${maxQuantity.toFixed(2)} MT (free space)`;
        } else if (type === 'discharging') {
            maxQuantity = sourceTankData.current_level;
            quantityInput.setAttribute('max', maxQuantity.toFixed(2));
            quantityError.textContent = `Maximum quantity: ${maxQuantity.toFixed(2)} MT (current capacity)`;
        } else if (type === 'transfer') {
            const sourceMax = sourceTankData.current_level;
            const destMax = destinationTankData.max_capacity > 0 ? destinationTankData.free_space : Infinity;
            maxQuantity = Math.min(sourceMax, destMax);
            quantityInput.setAttribute('max', maxQuantity.toFixed(2));
            quantityError.textContent = `Maximum quantity: ${maxQuantity.toFixed(2)} MT (limited by ${sourceMax <= destMax ? 'source current capacity' : 'destination free space'})`;
        }

        const currentQuantity = parseFloat(quantityInput.value) || 0;
        if (currentQuantity > maxQuantity && maxQuantity > 0) {
            quantityError.style.display = 'block';
            quantityInput.setCustomValidity(`Quantity cannot exceed ${maxQuantity.toFixed(2)} MT`);
        } else {
            quantityError.style.display = 'none';
            quantityInput.setCustomValidity('');
        }
        quantityInput.disabled = false;
    }

    sourceTankSelect.addEventListener('change', function() {
        fetchTankData(this.value, true);
    });

    if (destinationTankSelect) {
        destinationTankSelect.addEventListener('change', function() {
            fetchTankData(this.value, false);
        });
    }

    quantityInput.addEventListener('input', updateQuantityValidation);

    form.addEventListener('submit', function(e) {
        const currentQuantity = parseFloat(quantityInput.value) || 0;
        const maxQuantity = parseFloat(quantityInput.getAttribute('max')) || Infinity;
        if (currentQuantity > maxQuantity && maxQuantity > 0) {
            e.preventDefault();
            updateQuantityValidation();
        }
        if (sourceTankData.max_capacity === 0) {
            e.preventDefault();
            quantityError.textContent = 'Selected tank has no capacity';
            quantityError.style.display = 'block';
            quantityInput.setCustomValidity('Selected tank has no capacity');
        }
    });

    function toggleShipmentFields() {
        const transportType = shipmentTransportType?.value || '';
        const vesselField = document.getElementById('shipment_vessel_id_field');
        const truckFields = document.getElementById('shipment_truck_fields');
        const portField = document.getElementById('shipment_berth_number_field');

        vesselField.style.display = 'none';
        truckFields.style.display = 'none';
        portField.style.display = 'none';

        const shipmentFields = ['shipment_vessel_id', 'shipment_truck_number', 'shipment_trailer_number', 'shipment_driver_name', 'shipment_berth_number'];
        shipmentFields.forEach(id => {
            const element = document.getElementById(id);
            if (element) element.required = false;
        });

        if (transportType === 'vessel') {
            vesselField.style.display = 'block';
            portField.style.display = 'block';
            document.getElementById('shipment_vessel_id').required = true;
            document.getElementById('shipment_berth_number').required = true;
        } else if (transportType === 'truck') {
            truckFields.style.display = 'block';
            document.getElementById('shipment_truck_number').required = true;
            document.getElementById('shipment_trailer_number').required = true;
            document.getElementById('shipment_driver_name').required = true;
        }
    }

    function toggleDeliveryFields() {
        const transportType = deliveryTransportType?.value || '';
        const vesselField = document.getElementById('delivery_vessel_id_field');
        const truckFields = document.getElementById('delivery_truck_fields');

        vesselField.style.display = 'none';
        truckFields.style.display = 'none';

        const deliveryFields = ['delivery_vessel_id', 'delivery_truck_number', 'delivery_trailer_number', 'delivery_driver_name'];
        deliveryFields.forEach(id => {
            const element = document.getElementById(id);
            if (element) element.required = false;
        });

        if (transportType === 'vessel') {
            vesselField.style.display = 'block';
            document.getElementById('delivery_vessel_id').required = true;
        } else if (transportType === 'truck') {
            truckFields.style.display = 'block';
            document.getElementById('delivery_truck_number').required = true;
            document.getElementById('delivery_trailer_number').required = true;
            document.getElementById('delivery_driver_name').required = true;
        }
    }

    const fileInputs = [
        'measurement_report', 'inspection_form', 'customs_release_form',
        'charge_permit_document', 'discharge_permit_document'
    ];
    fileInputs.forEach(id => {
        const input = document.getElementById(id);
        if (input) {
            input.addEventListener('change', function() {
                const file = this.files[0];
                if (file) {
                    const validTypes = ['application/pdf', 'image/jpeg', 'image/png'];
                    const maxSize = 2 * 1024 * 1024; // 2MB
                    if (!validTypes.includes(file.type)) {
                        alert('Invalid file type. Only PDF, JPG, and PNG are accepted.');
                        this.value = '';
                    } else if (file.size > maxSize) {
                        alert('File size exceeds 2MB limit.');
                        this.value = '';
                    }
                }
            });
        }
    });

    if (shipmentTransportType) {
        shipmentTransportType.addEventListener('change', toggleShipmentFields);
    }
    if (deliveryTransportType) {
        deliveryTransportType.addEventListener('change', toggleDeliveryFields);
    }

    toggleShipmentFields();
    toggleDeliveryFields();

    if (typeSelect.value) {
        typeSelect.dispatchEvent(new Event('change'));
    }
    if (sourceTankSelect.value) {
        sourceTankSelect.dispatchEvent(new Event('change'));
    }
    if (destinationTankSelect?.value) {
        destinationTankSelect.dispatchEvent(new Event('change'));
    }
    if (shipmentTransportType?.value) {
        shipmentTransportType.dispatchEvent(new Event('change'));
    }
    if (deliveryTransportType?.value) {
        deliveryTransportType.dispatchEvent(new Event('change'));
    }

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
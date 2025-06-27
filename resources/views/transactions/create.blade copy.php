@extends('layouts.panel')

@section('title', 'Create Transaction')

@section('content')
<div class="main-content side-content mt-2 pt-0">
    <div class="container-fluid">
        <div class="inner-body">
            <h1>Create Transaction</h1>
            @include('components.alerts')
            <form action="{{ route('transactions.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-3">
                    <label for="type" class="form-label">Transaction Type</label>
                    <select name="type" id="type" class="form-control" required onchange="toggleFields()">
                        <option value="loading">Loading</option>
                        <option value="discharging">Discharging</option>
                        <option value="transfer">Transfer</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="tank_id" class="form-label">Source Tank</label>
                    <select name="tank_id" id="tank_id" class="form-control" required>
                        @foreach($tanks as $tank)
                        <option value="{{ $tank->id }}">{{ $tank->number }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3" id="destination_tank_id_field" style="display: none;">
                    <label for="destination_tank_id" class="form-label">Destination Tank</label>
                    <select name="destination_tank_id" id="destination_tank_id" class="form-control">
                        @foreach($tanks as $tank)
                        <option value="{{ $tank->id }}">{{ $tank->number }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label for="quantity" class="form-label">Quantity (MT)</label>
                    <input type="number" name="quantity" id="quantity" class="form-control" step="0.01" required>
                </div>
                <div class="mb-3">
                    <label for="date" class="form-label">Date</label>
                    <input type="date" name="date" id="date" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="work_order_number" class="form-label">Work Order Number</label>
                    <input type="text" name="work_order_number" id="work_order_number" class="form-control">
                </div>
                <div class="mb-3">
                    <label for="bill_of_lading_number" class="form-label">Bill of Lading Number</label>
                    <input type="text" name="bill_of_lading_number" id="bill_of_lading_number" class="form-control">
                </div>
                <div class="mb-3">
                    <label for="customs_release_number" class="form-label">Customs Release Number</label>
                    <input type="text" name="customs_release_number" id="customs_release_number" class="form-control">
                </div>
                <div class="mb-3" id="charge_permit_number_field" style="display: none;">
                    <label for="charge_permit_number" class="form-label">Charge Permit Number</label>
                    <input type="text" name="charge_permit_number" id="charge_permit_number" class="form-control">
                </div>
                <div class="mb-3" id="discharge_permit_number_field" style="display: none;">
                    <label for="discharge_permit_number" class="form-label">Discharge Permit Number</label>
                    <input type="text" name="discharge_permit_number" id="discharge_permit_number" class="form-control">
                </div>
                <div class="mb-3">
                    <label for="engineer_id" class="form-label">Engineer</label>
                    <select name="engineer_id" id="engineer_id" class="form-control">
                        <option value="">None</option>
                        @foreach($engineers as $engineer)
                        <option value="{{ $engineer->id }}">{{ $engineer->first_name }} {{ $engineer->last_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label for="technician_id" class="form-label">Technician</label>
                    <select name="technician_id" id="technician_id" class="form-control">
                        <option value="">None</option>
                        @foreach($technicians as $technician)
                        <option value="{{ $technician->id }}">{{ $technician->first_name }} {{ $technician->last_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label for="measurement_report" class="form-label">Measurement Report</label>
                    <input type="file" name="measurement_report" id="measurement_report" class="form-control">
                </div>
                <div class="mb-3">
                    <label for="general_discharge_permit" class="form-label">General Discharge Permit</label>
                    <input type="file" name="general_discharge_permit" id="general_discharge_permit" class="form-control">
                </div>
                <div class="mb-3">
                    <label for="inspection_form" class="form-label">Inspection Form</label>
                    <input type="file" name="inspection_form" id="inspection_form" class="form-control">
                </div>
                <div class="mb-3">
                    <label for="customs_release_form" class="form-label">Customs Release Form</label>
                    <input type="file" name="customs_release_form" id="customs_release_form" class="form-control">
                </div>
                <div class="mb-3" id="charge_permit_document_field" style="display: none;">
                    <label for="charge_permit_document" class="form-label">Charge Permit Document</label>
                    <input type="file" name="charge_permit_document" id="charge_permit_document" class="form-control">
                </div>
                <div class="mb-3" id="discharge_permit_document_field" style="display: none;">
                    <label for="discharge_permit_document" class="form-label">Discharge Permit Document</label>
                    <input type="file" name="discharge_permit_document" id="discharge_permit_document" class="form-control">
                </div>
                <div class="mb-3">
                    <h4>Shipment Details</h4>
                    <div class="mb-3">
                        <label for="shipment_transport_type" class="form-label">Transport Type</label>
                        <select name="shipment[transport_type]" id="shipment_transport_type" class="form-control" onchange="toggleShipmentFields()">
                            <option value="">None</option>
                            <option value="vessel">Vessel</option>
                            <option value="truck">Truck</option>
                        </select>
                    </div>
                    <div id="shipment_vessel_id_field" style="display: none;">
                        <label for="shipment_vessel_id" class="form-label">Vessel</label>
                        <select name="shipment[vessel_id]" id="shipment_vessel_id" class="form-control">
                            @foreach($vessels as $vessel)
                            <option value="{{ $vessel->id }}">{{ $vessel->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div id="shipment_truck_fields" style="display: none;">
                        <div class="mb-3">
                            <label for="shipment_truck_number" class="form-label">Truck Number</label>
                            <input type="text" name="shipment[truck_number]" id="shipment_truck_number" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label for="shipment_trailer_number" class="form-label">Trailer Number</label>
                            <input type="text" name="shipment[trailer_number]" id="shipment_trailer_number" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label for="shipment_driver_name" class="form-label">Driver Name</label>
                            <input type="text" name="shipment[driver_name]" id="shipment_driver_name" class="form-control">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="shipment_product_id" class="form-label">Product</label>
                        <select name="shipment[product_id]" id="shipment_product_id" class="form-control">
                            @foreach($products as $product)
                            <option value="{{ $product->id }}">{{ $product->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="shipment_total_quantity" class="form-label">Total Quantity (MT)</label>
                        <input type="number" name="shipment[total_quantity]" id="shipment_total_quantity" class="form-control" step="0.01">
                    </div>
                    <div class="mb-3">
                        <label for="shipment_port_of_discharge" class="form-label">Port of Discharge</label>
                        <input type="text" name="shipment[port_of_discharge]" id="shipment_port_of_discharge" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label for="shipment_arrival_date" class="form-label">Arrival Date</label>
                        <input type="date" name="shipment[arrival_date]" id="shipment_arrival_date" class="form-control">
                    </div>
                </div>
                <div class="mb-3">
                    <h4>Delivery Details</h4>
                    <div class="mb-3">
                        <label for="delivery_transport_type" class="form-label">Transport Type</label>
                        <select name="delivery[transport_type]" id="delivery_transport_type" class="form-control" onchange="toggleDeliveryFields()">
                            <option value="">None</option>
                            <option value="vessel">Vessel</option>
                            <option value="truck">Truck</option>
                        </select>
                    </div>
                    <div id="delivery_vessel_id_field" style="display: none;">
                        <label for="delivery_vessel_id" class="form-label">Vessel</label>
                        <select name="delivery[vessel_id]" id="delivery_vessel_id" class="form-control">
                            @foreach($vessels as $vessel)
                            <option value="{{ $vessel->id }}">{{ $vessel->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div id="delivery_truck_fields" style="display: none;">
                        <div class="mb-3">
                            <label for="delivery_truck_number" class="form-label">Truck Number</label>
                            <input type="text" name="delivery[truck_number]" id="delivery_truck_number" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label for="delivery_trailer_number" class="form-label">Trailer Number</label>
                            <input type="text" name="delivery[trailer_number]" id="delivery_trailer_number" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label for="delivery_driver_name" class="form-label">Driver Name</label>
                            <input type="text" name="delivery[driver_name]" id="delivery_driver_name" class="form-control">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="delivery_company_id" class="form-label">Company</label>
                        <select name="delivery[company_id]" id="delivery_company_id" class="form-control">
                            @foreach($companies as $company)
                            <option value="{{ $company->id }}">{{ $company->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="delivery_product_id" class="form-label">Product</label>
                        <select name="delivery[product_id]" id="delivery_product_id" class="form-control">
                            @foreach($products as $product)
                            <option value="{{ $product->id }}">{{ $product->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="delivery_quantity" class="form-label">Quantity (MT)</label>
                        <input type="number" name="delivery[quantity]" id="delivery_quantity" class="form-control" step="0.01">
                    </div>
                    <div class="mb-3">
                        <label for="delivery_delivery_date" class="form-label">Delivery Date</label>
                        <input type="date" name="delivery[delivery_date]" id="delivery_delivery_date" class="form-control">
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Save</button>
            </form>
        </div>
    </div>
</div>

<script>
    function toggleFields() {
        const type = document.getElementById('type').value;
        const destinationField = document.getElementById('destination_tank_id_field');
        const chargePermitField = document.getElementById('charge_permit_number_field');
        const dischargePermitField = document.getElementById('discharge_permit_number_field');
        const chargePermitDocField = document.getElementById('charge_permit_document_field');
        const dischargePermitDocField = document.getElementById('discharge_permit_document_field');

        if (type === 'transfer') {
            destinationField.style.display = 'block';
            chargePermitField.style.display = 'block';
            dischargePermitField.style.display = 'block';
            chargePermitDocField.style.display = 'block';
            dischargePermitDocField.style.display = 'block';
            document.getElementById('destination_tank_id').required = true;
            document.getElementById('charge_permit_number').required = true;
            document.getElementById('discharge_permit_number').required = true;
            document.getElementById('charge_permit_document').required = true;
            document.getElementById('discharge_permit_document').required = true;
        } else {
            destinationField.style.display = 'none';
            chargePermitField.style.display = 'none';
            dischargePermitField.style.display = 'none';
            chargePermitDocField.style.display = 'none';
            dischargePermitDocField.style.display = 'none';
            document.getElementById('destination_tank_id').required = false;
            document.getElementById('charge_permit_number').required = false;
            document.getElementById('discharge_permit_number').required = false;
            document.getElementById('charge_permit_document').required = false;
            document.getElementById('discharge_permit_document').required = false;
        }
    }

    function toggleShipmentFields() {
        const transportType = document.getElementById('shipment_transport_type').value;
        const vesselField = document.getElementById('shipment_vessel_id_field');
        const truckFields = document.getElementById('shipment_truck_fields');

        if (transportType === 'vessel') {
            vesselField.style.display = 'block';
            truckFields.style.display = 'none';
            document.getElementById('shipment_vessel_id').required = true;
            document.getElementById('shipment_truck_number').required = false;
            document.getElementById('shipment_trailer_number').required = false;
            document.getElementById('shipment_driver_name').required = false;
        } else if (transportType === 'truck') {
            vesselField.style.display = 'none';
            truckFields.style.display = 'block';
            document.getElementById('shipment_vessel_id').required = false;
            document.getElementById('shipment_truck_number').required = true;
            document.getElementById('shipment_trailer_number').required = true;
            document.getElementById('shipment_driver_name').required = true;
        } else {
            vesselField.style.display = 'none';
            truckFields.style.display = 'none';
            document.getElementById('shipment_vessel_id').required = false;
            document.getElementById('shipment_truck_number').required = false;
            document.getElementById('shipment_trailer_number').required = false;
            document.getElementById('shipment_driver_name').required = false;
        }
    }

    function toggleDeliveryFields() {
        const transportType = document.getElementById('delivery_transport_type').value;
        const vesselField = document.getElementById('delivery_vessel_id_field');
        const truckFields = document.getElementById('delivery_truck_fields');

        if (transportType === 'vessel') {
            vesselField.style.display = 'block';
            truckFields.style.display = 'none';
            document.getElementById('delivery_vessel_id').required = true;
            document.getElementById('delivery_truck_number').required = false;
            document.getElementById('delivery_trailer_number').required = false;
            document.getElementById('delivery_driver_name').required = false;
        } else if (transportType === 'truck') {
            vesselField.style.display = 'none';
            truckFields.style.display = 'block';
            document.getElementById('delivery_vessel_id').required = false;
            document.getElementById('delivery_truck_number').required = true;
            document.getElementById('delivery_trailer_number').required = true;
            document.getElementById('delivery_driver_name').required = true;
        } else {
            vesselField.style.display = 'none';
            truckFields.style.display = 'none';
            document.getElementById('delivery_vessel_id').required = false;
            document.getElementById('delivery_truck_number').required = false;
            document.getElementById('delivery_trailer_number').required = false;
            document.getElementById('delivery_driver_name').required = false;
        }
    }

</script>
@endsection

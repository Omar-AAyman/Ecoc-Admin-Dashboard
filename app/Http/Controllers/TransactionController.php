<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Product;
use App\Models\Tank;
use App\Models\User;
use App\Models\Vessel;
use App\Services\TransactionService;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    protected $transactionService;

    public function __construct(TransactionService $transactionService)
    {
        $this->middleware('restrict.to.role:super_admin,ceo,client');
        $this->transactionService = $transactionService;
    }

    public function create()
    {
        $this->authorize('create', \App\Models\Transaction::class);
        $tanks = Tank::all();
        $products = Product::all();
        $companies = Company::all();
        $vessels = Vessel::all();
        $engineers = User::whereHas('role', function ($query) {
            $query->where('name', 'super_admin');
        })->get();
        $technicians = User::whereHas('role', function ($query) {
            $query->where('name', 'super_admin');
        })->get();
        return view('transactions.create', compact('tanks', 'products', 'companies', 'vessels', 'engineers', 'technicians'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', \App\Models\Transaction::class);
        $rules = [
            'type' => 'required|in:loading,discharging,transfer',
            'tank_id' => 'required|exists:tanks,id',
            'destination_tank_id' => 'required_if:type,transfer|exists:tanks,id|different:tank_id',
            'quantity' => 'required|numeric|min:0',
            'date' => 'required|date',
            'work_order_number' => 'nullable|string|max:100',
            'bill_of_lading_number' => 'nullable|string|max:100',
            'customs_release_number' => 'nullable|string|max:100',
            'engineer_id' => 'nullable|exists:users,id',
            'technician_id' => 'nullable|exists:users,id',
            'measurement_report' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
            'general_discharge_permit' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
            'inspection_form' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
            'customs_release_form' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
            'shipment.transport_type' => 'nullable|in:vessel,truck',
            'shipment.vessel_id' => 'required_if:shipment.transport_type,vessel|exists:vessels,id|nullable',
            'shipment.truck_number' => 'required_if:shipment.transport_type,truck|string|max:50|nullable',
            'shipment.trailer_number' => 'required_if:shipment.transport_type,truck|string|max:50|nullable',
            'shipment.driver_name' => 'required_if:shipment.transport_type,truck|string|max:255|nullable',
            'shipment.product_id' => 'required_with:shipment.transport_type|exists:products,id',
            'shipment.total_quantity' => 'required_with:shipment.transport_type|numeric|min:0',
            'shipment.port_of_discharge' => 'required_with:shipment.transport_type|string|max:255',
            'shipment.arrival_date' => 'required_with:shipment.transport_type|date',
            'delivery.transport_type' => 'nullable|in:vessel,truck',
            'delivery.vessel_id' => 'required_if:delivery.transport_type,vessel|exists:vessels,id|nullable',
            'delivery.truck_number' => 'required_if:delivery.transport_type,truck|string|max:50|nullable',
            'delivery.trailer_number' => 'required_if:delivery.transport_type,truck|string|max:50|nullable',
            'delivery.driver_name' => 'required_if:delivery.transport_type,truck|string|max:255|nullable',
            'delivery.company_id' => 'required_with:delivery.transport_type|exists:companies,id',
            'delivery.product_id' => 'required_with:delivery.transport_type|exists:products,id',
            'delivery.quantity' => 'required_with:delivery.transport_type|numeric|min:0',
            'delivery.delivery_date' => 'required_with:delivery.transport_type|date',
        ];

        if ($request->input('type') === 'transfer') {
            $rules['charge_permit_number'] = 'required|string|max:100';
            $rules['discharge_permit_number'] = 'required|string|max:100';
            $rules['charge_permit_document'] = 'required|file|mimes:pdf,doc,docx|max:2048';
            $rules['discharge_permit_document'] = 'required|file|mimes:pdf,doc,docx|max:2048';
        } else {
            $rules['charge_permit_number'] = 'nullable|string|max:100';
            $rules['discharge_permit_number'] = 'nullable|string|max:100';
            $rules['charge_permit_document'] = 'nullable|file|mimes:pdf,doc,docx|max:2048';
            $rules['discharge_permit_document'] = 'nullable|file|mimes:pdf,doc,docx|max:2048';
        }

        $validated = $request->validate($rules);

        try {
            $files = $request->only([
                'measurement_report',
                'general_discharge_permit',
                'inspection_form',
                'customs_release_form',
                'charge_permit_document',
                'discharge_permit_document'
            ]);
            $this->transactionService->createTransaction($validated, $files, $request->user());
            return redirect()->route('transactions.create')->with('success', 'Transaction created successfully');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }
    }
}

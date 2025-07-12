<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Driver;
use App\Models\Product;
use App\Models\Tank;
use App\Models\Trailer;
use App\Models\Transaction;
use App\Models\Truck;
use App\Models\User;
use App\Models\Vessel;
use App\Services\TankService;
use App\Services\TransactionService;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    protected $transactionService;
    protected $tankService;

    public function __construct(TransactionService $transactionService, TankService $tankService)
    {
        $this->middleware('restrict.to.role:super_admin,ceo,client');
        $this->transactionService = $transactionService;
        $this->tankService = $tankService;
    }

    public function index(Request $request)
    {
        $filters = $request->only([
            'type',
            'tank_id',
            'destination_tank_id',
            'original_vessel_id',
            'company_id',
            'product_id',
            'engineer_id',
            'technician_id',
            'transport_type',
            'search',
            'from',
            'to'
        ]);
        $transactions = $this->transactionService->getTransactionsQuery($filters)->paginate(10);
        $tanks = Tank::all(['id', 'number']);
        $vessels = Vessel::all(['id', 'name']);
        $companies = Company::all(['id', 'name']);
        $companies = Company::whereHas('users', function ($query) {
            $query->whereNull('users.deleted_at');
        })
        ->get(['id', 'name']);
        $products = Product::all(['id', 'name']);
        $engineers = User::where('position', 'engineer')->get(['id', 'first_name', 'last_name']);
        $technicians = User::where('position', 'technician')->get(['id', 'first_name', 'last_name']);

        return view('transactions.index', compact(
            'transactions',
            'filters',
            'tanks',
            'vessels',
            'companies',
            'products',
            'engineers',
            'technicians'
        ));
    }

    public function statistics(Request $request)
    {
        $filters = $request->only([
            'type',
            'tank_id',
            'destination_tank_id',
            'original_vessel_id',
            'company_id',
            'product_id',
            'engineer_id',
            'technician_id',
            'transport_type',
            'search',
            'from',
            'to'
        ]);
        return response()->json($this->transactionService->getStatistics($filters));
    }

    public function create()
    {
        $this->authorize('create', \App\Models\Transaction::class);
        $tanks = $this->tankService->getAssignedTanks(auth()->user());
        $products = Product::all();
        $companies = Company::all();
        $vessels = Vessel::all();
        $trucks = Truck::all();
        $trailers = Trailer::all();
        $drivers = Driver::all();
        $engineers = User::where('position', 'Engineer')->whereNull('deleted_at')->get();
        $technicians = User::where('position', 'Technician')->whereNull('deleted_at')->get();
        return view('transactions.create', compact(
            'tanks',
            'products',
            'companies',
            'vessels',
            'trucks',
            'trailers',
            'drivers',
            'engineers',
            'technicians'
        ));
    }

    public function duplicate($id)
    {
        $this->authorize('create', \App\Models\Transaction::class); // Same permission as create
        $transaction = Transaction::with(['tank', 'destinationTank', 'originalVessel', 'shipment', 'delivery', 'engineer', 'technician'])->findOrFail($id);

        $tanks = $this->tankService->getAssignedTanks(auth()->user());
        $products = Product::all();
        $companies = Company::all();
        $vessels = Vessel::all();
        $trucks = Truck::all();
        $trailers = Trailer::all();
        $drivers = Driver::all();
        $engineers = User::where('position', 'Engineer')->whereNull('deleted_at')->get();
        $technicians = User::where('position', 'Technician')->whereNull('deleted_at')->get();

        return view('transactions.create', compact(
            'transaction',
            'tanks',
            'products',
            'companies',
            'vessels',
            'trucks',
            'trailers',
            'drivers',
            'engineers',
            'technicians'
        ));
    }

    public function edit($id)
    {
        $this->authorize('update', \App\Models\Transaction::class);
        $transaction = Transaction::with(['tank', 'destinationTank', 'originalVessel', 'shipment', 'delivery', 'engineer', 'technician', 'documents'])->findOrFail($id);
        $tanks = $this->tankService->getAssignedTanks(auth()->user());
        $products = Product::all();
        $companies = Company::all();
        $vessels = Vessel::all();
        $trucks = Truck::all();
        $trailers = Trailer::all();
        $drivers = Driver::all();
        $engineers = User::where('position', 'Engineer')->whereNull('deleted_at')->get();
        $technicians = User::where('position', 'Technician')->whereNull('deleted_at')->get();

        return view('transactions.edit', compact(
            'transaction',
            'tanks',
            'products',
            'companies',
            'vessels',
            'trucks',
            'trailers',
            'drivers',
            'engineers',
            'technicians'
        ));
    }

    public function store(Request $request)
    {
        // dd($request->all());
        $this->authorize('create', \App\Models\Transaction::class);
        $rules = [
            'type' => 'required|in:loading,discharging,transfer',
            'tank_id' => [
                'required',
                'exists:tanks,id',
                function ($attribute, $value, $fail) {
                    $tank = Tank::find($value);
                    if (!$tank || !$tank->company_id) {
                        $fail('The selected tank must be assigned to a company.');
                    }
                },
            ],
            'original_vessel_id' => 'nullable|exists:vessels,id',
            'quantity' => 'required|numeric|min:0',
            'date' => 'required|date',
            'work_order_number' => 'nullable|string|max:100',
            'bill_of_lading_number' => 'nullable|string|max:100',
            'customs_release_number' => 'nullable|string|max:100',
            'engineer_id' => 'nullable|exists:users,id',
            'technician_id' => 'nullable|exists:users,id',
            'measurement_report' => 'nullable|file|mimes:pdf,jpeg,jpg,png|max:2048',
            'inspection_form' => 'nullable|file|mimes:pdf,jpeg,jpg,png|max:2048',
            'customs_release_form' => 'nullable|file|mimes:pdf,jpeg,jpg,png|max:2048',
        ];

        if ($request->input('type') === 'loading') {
            $rules['shipment.transport_type'] = 'required|in:vessel,truck';
            $rules['shipment.vessel_id'] = 'required_if:shipment.transport_type,vessel|exists:vessels,id|nullable';
            $rules['shipment.truck_number'] = 'required_if:shipment.transport_type,truck|string|max:50|nullable';
            $rules['shipment.trailer_number'] = 'required_if:shipment.transport_type,truck|string|max:50|nullable';
            $rules['shipment.driver_name'] = 'required_if:shipment.transport_type,truck|string|max:255|nullable';
            $rules['shipment.berth_number'] = 'required_if:shipment.transport_type,vessel|string|max:255|nullable';
            $rules['charge_permit_number'] = 'required|string|max:100';
            $rules['charge_permit_document'] = 'required|file|mimes:pdf,jpeg,jpg,png|max:2048';
        } elseif ($request->input('type') === 'discharging') {
            $rules['delivery.transport_type'] = 'required|in:vessel,truck';
            $rules['delivery.vessel_id'] = 'required_if:delivery.transport_type,vessel|exists:vessels,id|nullable';
            $rules['delivery.truck_number'] = 'required_if:delivery.transport_type,truck|string|max:50|nullable';
            $rules['delivery.trailer_number'] = 'required_if:delivery.transport_type,truck|string|max:50|nullable';
            $rules['delivery.driver_name'] = 'required_if:delivery.transport_type,truck|string|max:255|nullable';
            $rules['discharge_permit_number'] = 'required|string|max:100';
            $rules['discharge_permit_document'] = 'required|file|mimes:pdf,jpeg,jpg,png|max:2048';
        } elseif ($request->input('type') === 'transfer') {
            $rules['destination_tank_id'] = 'required|exists:tanks,id|different:tank_id';
            $rules['charge_permit_number'] = 'required|string|max:100';
            $rules['discharge_permit_number'] = 'required|string|max:100';
            $rules['charge_permit_document'] = 'required|file|mimes:pdf,jpeg,jpg,png|max:2048';
            $rules['discharge_permit_document'] = 'required|file|mimes:pdf,jpeg,jpg,png|max:2048';
        }

        $validated = $request->validate($rules);

        try {
            $files = $request->only([
                'measurement_report',
                'inspection_form',
                'customs_release_form',
                'charge_permit_document',
                'discharge_permit_document'
            ]);
            $this->transactionService->createTransaction($validated, $files, $request->user());
            return redirect()->route('transactions.index')->with('success', 'Transaction created successfully');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function update(Request $request, $id)
    {
        $this->authorize('update', \App\Models\Transaction::class);
        $transaction = Transaction::findOrFail($id);

        $rules = [
            'type' => 'required|in:loading,discharging,transfer',
            'tank_id' => [
                'required',
                'exists:tanks,id',
                function ($attribute, $value, $fail) {
                    $tank = Tank::find($value);
                    if (!$tank || !$tank->company_id) {
                        $fail('The selected tank must be assigned to a company.');
                    }
                },
            ],
            'original_vessel_id' => 'nullable|exists:vessels,id',
            'quantity' => 'required|numeric|min:0.01',
            'date' => 'required|date',
            'work_order_number' => 'nullable|string|max:100',
            'bill_of_lading_number' => 'nullable|string|max:100',
            'customs_release_number' => 'nullable|string|max:100',
            'engineer_id' => 'nullable|exists:users,id',
            'technician_id' => 'nullable|exists:users,id',
            'measurement_report' => 'nullable|file|mimes:pdf,jpeg,jpg,png|max:2048',
            'inspection_form' => 'nullable|file|mimes:pdf,jpeg,jpg,png|max:2048',
            'customs_release_form' => 'nullable|file|mimes:pdf,jpeg,jpg,png|max:2048',
        ];

        if ($request->input('type') === 'loading') {
            $rules['shipment.transport_type'] = 'required|in:vessel,truck';
            $rules['shipment.vessel_id'] = 'required_if:shipment.transport_type,vessel|exists:vessels,id|nullable';
            $rules['shipment.truck_number'] = 'required_if:shipment.transport_type,truck|string|max:50|nullable';
            $rules['shipment.trailer_number'] = 'required_if:shipment.transport_type,truck|string|max:50|nullable';
            $rules['shipment.driver_name'] = 'required_if:shipment.transport_type,truck|string|max:255|nullable';
            $rules['shipment.berth_number'] = 'required_if:shipment.transport_type,vessel|string|max:255|nullable';
            $rules['charge_permit_number'] = 'required|string|max:100';
            $rules['charge_permit_document'] = 'nullable|file|mimes:pdf,jpeg,jpg,png|max:2048';
        } elseif ($request->input('type') === 'discharging') {
            $rules['delivery.transport_type'] = 'required|in:vessel,truck';
            $rules['delivery.vessel_id'] = 'required_if:delivery.transport_type,vessel|exists:vessels,id|nullable';
            $rules['delivery.truck_number'] = 'required_if:delivery.transport_type,truck|string|max:50|nullable';
            $rules['delivery.trailer_number'] = 'required_if:delivery.transport_type,truck|string|max:50|nullable';
            $rules['delivery.driver_name'] = 'required_if:delivery.transport_type,truck|string|max:255|nullable';
            $rules['discharge_permit_number'] = 'required|string|max:100';
            $rules['discharge_permit_document'] = 'nullable|file|mimes:pdf,jpeg,jpg,png|max:2048';
        } elseif ($request->input('type') === 'transfer') {
            $rules['destination_tank_id'] = 'required|exists:tanks,id|different:tank_id';
            $rules['charge_permit_number'] = 'required|string|max:100';
            $rules['discharge_permit_number'] = 'required|string|max:100';
            $rules['charge_permit_document'] = 'nullable|file|mimes:pdf,jpeg,jpg,png|max:2048';
            $rules['discharge_permit_document'] = 'nullable|file|mimes:pdf,jpeg,jpg,png|max:2048';
        }

        $validated = $request->validate($rules);

        try {
            $files = $request->only([
                'measurement_report',
                'inspection_form',
                'customs_release_form',
                'charge_permit_document',
                'discharge_permit_document'
            ]);
            $this->transactionService->updateTransaction($transaction, $validated, $files, $request->user());
            return redirect()->route('transactions.index')->with('success', 'Transaction updated successfully');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function show($id)
    {
        $transaction = Transaction::with([
            'tank',
            'destinationTank',
            'company',
            'product',
            'originalVessel',
            'engineer',
            'technician',
            'shipment',
            'delivery',
            'documents'
        ])->findOrFail($id);
        $this->authorize('view', $transaction);

        return response()->json($transaction);
    }


    public function showDetails($id)
    {
        $transaction = Transaction::with([
            'tank',
            'destinationTank',
            'company',
            'product',
            'originalVessel',
            'engineer',
            'technician',
            'shipment',
            'delivery',
            'documents'
        ])->findOrFail($id);

        $this->authorize('view', $transaction); // Pass the transaction instance

        return view('transactions.show', compact('transaction'));
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Product;
use App\Models\Tank;
use App\Services\TankService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class TankController extends Controller
{
    protected $tankService;

    public function __construct(TankService $tankService)
    {
        $this->middleware('restrict.to.role:super_admin,ceo');
        $this->tankService = $tankService;
    }

    public function settings(Request $request)
    {
        $this->authorize('viewAny', Tank::class);
        $perPage = $request->input('per_page', 10);
        $search = $request->input('search');

        $tanks = $this->tankService->getPaginatedTanks($request->user(), $perPage, $search);

        if ($request->ajax()) {
            $table = View::make('tanks.partials.table', compact('tanks'))->render();
            $pagination = $tanks->appends(['per_page' => $perPage, 'search' => $search])->links('pagination::bootstrap-5')->render();
            return response()->json([
                'table' => $table,
                'pagination' => $pagination,
                'first_item' => $tanks->firstItem() ?? 0,
                'last_item' => $tanks->lastItem() ?? 0,
                'total' => $tanks->total()
            ]);
        }

        return view('tanks.settings', compact('tanks'));
    }

    // public function create()
    // {
    //     $this->authorize('create', Tank::class);
    //     $products = Product::all(['id', 'name']);
    //     $companies = Company::whereHas('users')->get(['id', 'name']);
    //     return view('tanks.create', compact('products', 'companies'));
    // }

    // public function store(Request $request)
    // {
    //     $this->authorize('create', Tank::class);
    //     $validated = $request->validate([
    //         'number' => 'required|string|unique:tanks|max:255',
    //         'cubic_meter_capacity' => 'required|numeric|min:0',
    //         'current_level' => 'required|numeric|min:0',
    //         'temperature' => 'nullable|numeric|min:-50|max:100',
    //         'product_id' => 'nullable|exists:products,id',
    //         'company_id' => 'nullable|exists:companies,id',
    //     ]);

    //     try {
    //         $this->tankService->createTank($validated, $request->user());
    //         return redirect()->route('tanks.settings')->with('success', 'Tank created successfully');
    //     } catch (\Exception $e) {
    //         return back()->withErrors(['error' => $e->getMessage()])->withInput();
    //     }
    // }

    public function edit($id)
    {
        $this->authorize('update', Tank::class);
        $tank = $this->tankService->getTank($id);
        $products = Product::all(['id', 'name']);
        $companies = Company::whereHas('users')->get(['id', 'name']);
        return view('tanks.edit', compact('tank', 'products', 'companies'));
    }

    public function update(Request $request, $id)
    {
        $this->authorize('update', Tank::class);
        $validated = $request->validate([
            'cubic_meter_capacity' => 'required|numeric|min:0',
            'current_level' => 'required|numeric|min:0',
            'temperature' => 'nullable|numeric|min:-50|max:100',
            'product_id' => 'nullable|exists:products,id',
            'company_id' => 'nullable|exists:companies,id',
        ]);

        try {
            $this->tankService->updateTank($id, $validated, $request->user());
            return redirect()->route('tanks.settings')->with('success', 'Tank updated successfully');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    public function resetTank(Request $request, $id)
    {
        $this->authorize('update', Tank::class);
        try {
            $this->tankService->resetTank($id, $request->user());
            return redirect()->route('tanks.settings')->with('success', 'Tank reset successfully');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function destroy(Request $request, $id)
    {
        $this->authorize('delete', Tank::class);
        try {
            $this->tankService->deleteTank($id, $request->user());
            return redirect()->route('tanks.settings')->with('success', 'Tank deleted successfully');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function getCompany($id)
    {
        $tank = Tank::findOrFail($id);
        return response()->json(['company_name' => $tank->company ? $tank->company->name : null, 'logo_url' => $tank->company->users->first()->image_url]);
    }

    public function getProduct($id)
    {
        $tank = Tank::findOrFail($id);
        return response()->json(['product_name' => $tank->product ? $tank->product->name : null]);
    }

    public function getCapacity($id)
    {
        $tank = $this->tankService->getTank($id);
        return response()->json([
            'current_level' => $tank->current_level,
            'temperature' => $tank->temperature !== null ? number_format($tank->temperature, 2) : null,
            'temperature_fahrenheit' => $tank->temperature_fahrenheit !== null ? number_format($tank->temperature_fahrenheit, 2) : null,
            'max_capacity' => $tank->maxCapacity()
        ]);
    }

    public function getDetails($id)
    {
        $tank = Tank::with(['product', 'company', 'tankRentals.company', 'tankRentals.product', 'transactions', 'destinationTransactions'])
            ->findOrFail($id);

        // Ensure user has permission
        $user = auth()->user();
        if ($user->isClient() && $tank->company_id !== $user->company_id) {
            return response()->json(['error' => 'Unauthorized access to tank details'], 403);
        }

        $rentals = $tank->tankRentals->map(function ($rental) {
            return [
                'start_date' => $rental->start_date->toDateTimeString(),
                'end_date' => $rental->end_date ? $rental->end_date->toDateTimeString() : null,
                'company_name' => $rental->company ? $rental->company->name : 'N/A',
                'product_name' => $rental->product ? $rental->product->name : 'N/A',
                'details' => $rental->details
            ];
        });

        $transactions = $tank->transactions->merge($tank->destinationTransactions)->map(function ($transaction) {
            return [
                'id' => $transaction->id,
                'amount' => $transaction->amount,
                'created_at' => $transaction->created_at->toDateTimeString(),
                'type' => $transaction->destination_tank_id ? 'Transfer (Destination)' : 'Transfer (Source)'
            ];
        });

        return response()->json([
            'id' => $tank->number,
            'maxCapacity' => number_format($tank->maxCapacity(), 1) . ' mt',
            'currentLevel' => number_format($tank->current_level ?? 0, 1) . ' mt',
            'temperatureCelsius' => $tank->temperature !== null ? number_format($tank->temperature, 2) . '°C' : 'N/A',
            'temperatureFahrenheit' => $tank->temperature_fahrenheit !== null ? number_format($tank->temperature_fahrenheit, 2) . '°F' : 'N/A',
            'capacityUtilization' => $tank->cubic_meter_capacity > 0 && $tank->product && $tank->product->density ?
                number_format(($tank->current_level / ($tank->cubic_meter_capacity * $tank->product->density)) * 100, 0) . '%' : ($tank->cubic_meter_capacity > 0 ? number_format(($tank->current_level / $tank->cubic_meter_capacity) * 100, 0) . '%' : '0%'),
            'rentals' => $rentals,
            'transactions' => $transactions,
        ]);
    }

    public function getAvailableTanks(Request $request)
    {
        $type = $request->query('type');
        $sourceTankId = $request->query('source_tank_id');
        $sourceTanks = Tank::select('id', 'number', 'current_level')
            ->whereNotNull('company_id')
            ->whereNotNull('product_id')
            ->when($type === 'discharging' || $type === 'transfer', function ($query) {
                return $query->where('current_level', '>', 0);
            })
            ->when($type === 'loading', function ($query) {
                return $query->whereRaw('cubic_meter_capacity * (SELECT density FROM products WHERE products.id = tanks.product_id) - current_level > 0');
            })
            ->get()
            ->map(function ($tank) {
                return [
                    'id' => $tank->id,
                    'number' => $tank->number,
                    'current_level' => $tank->current_level,
                    'temperature' => $tank->temperature !== null ? number_format($tank->temperature, 2) : null,
                    'temperature_fahrenheit' => $tank->temperature_fahrenheit !== null ? number_format($tank->temperature_fahrenheit, 2) : null,
                    'max_capacity' => $tank->maxCapacity()
                ];
            });

        $destinationTanks = [];
        if ($type === 'transfer') {
            $query = Tank::select('id', 'number', 'current_level')
                ->whereNotNull('company_id')
                ->whereNotNull('product_id')
                ->whereRaw('cubic_meter_capacity * (SELECT density FROM products WHERE products.id = tanks.product_id) - current_level > 0');

            if ($sourceTankId) {
                $sourceTank = Tank::find($sourceTankId);
                if ($sourceTank && $sourceTank->product_id) {
                    $query->where('product_id', $sourceTank->product_id)
                          ->where('id', '!=', $sourceTankId);
                } else {
                    // If source_tank_id is invalid or has no product, return empty destination tanks
                    $query->whereRaw('1 = 0');
                }
            } else {
                // If no source_tank_id provided, return no destination tanks until source is selected
                $query->whereRaw('1 = 0');
            }

            $destinationTanks = $query->get()
                ->map(function ($tank) {
                    return [
                        'id' => $tank->id,
                        'number' => $tank->number,
                        'current_level' => $tank->current_level,
                        'temperature' => $tank->temperature !== null ? number_format($tank->temperature, 2) : null,
                        'temperature_fahrenheit' => $tank->temperature_fahrenheit !== null ? number_format($tank->temperature_fahrenheit, 2) : null,
                        'max_capacity' => $tank->maxCapacity()
                    ];
                });
        }

        return response()->json([
            'source_tanks' => $sourceTanks,
            'destination_tanks' => $destinationTanks,
        ]);
    }
}
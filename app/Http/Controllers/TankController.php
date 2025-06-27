<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Product;
use App\Services\TankService;
use Illuminate\Http\Request;

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
        $this->authorize('settings', \App\Models\Tank::class);
        $tanks = $this->tankService->getTanks($request->user());
        $companies = Company::all();
        $products = Product::all();
        return view('tanks.settings', compact('tanks', 'products', 'companies'));
    }

    public function updateSettings(Request $request, $id)
    {
        $this->authorize('update', \App\Models\Tank::class);
        $validated = $request->validate([
            'company_id' => 'nullable|exists:companies,id',
            'product_id' => 'required|exists:products,id',
        ]);

        try {
            $this->tankService->updateTankSettings($id, $validated, $request->user());
            return redirect()->route('tanks.settings')->with('success', 'Tank settings updated successfully');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function edit($id)
    {
        $this->authorize('update', \App\Models\Tank::class);
        $tank = $this->tankService->getTank($id); // Assumed method
        $products = Product::all();
        $companies = Company::all();
        return view('tanks.edit', compact('tank', 'products', 'companies'));
    }

    public function create()
    {
        $this->authorize('create', \App\Models\Tank::class);
        $products = Product::all();
        $companies = Company::all();
        return view('tanks.create', compact('products', 'companies'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', \App\Models\Tank::class);
        $validated = $request->validate([
            'number' => 'required|string|unique:tanks|max:50',
            'cubic_meter_capacity' => 'required|numeric|min:0',
            'status' => 'required|in:Available,In Use',
            'product_id' => 'nullable|exists:products,id',
            'company_id' => 'nullable|exists:companies,id',
        ]);

        $this->tankService->createTank($validated, $request->user());
        return redirect()->route('tanks.settings')->with('success', 'Tank created successfully');
    }

    public function destroy(Request $request, $id)
    {
        $this->authorize('delete', \App\Models\Tank::class);
        try {
            $this->tankService->deleteTank($id, $request->user());
            return redirect()->route('tanks.settings')->with('success', 'Tank deleted successfully');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}

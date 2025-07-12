<?php

namespace App\Http\Controllers;

use App\Services\TruckService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class TruckController extends Controller
{
    protected $truckService;

    public function __construct(TruckService $truckService)
    {
        $this->middleware('restrict.to.role:super_admin');
        $this->truckService = $truckService;
    }

    public function index(Request $request)
    {
        $this->authorize('viewAny', \App\Models\Truck::class);
        $perPage = $request->input('per_page', 10);
        $search = $request->input('search');

        $trucks = $this->truckService->getPaginatedTrucks($search, $perPage);

        if ($request->ajax()) {
            $table = View::make('trucks.partials.table', compact('trucks'))->render();
            $pagination = $trucks->appends(['per_page' => $perPage, 'search' => $search])->links('pagination::bootstrap-5')->render();
            return response()->json([
                'table' => $table,
                'pagination' => $pagination,
                'first_item' => $trucks->firstItem() ?? 0,
                'last_item' => $trucks->lastItem() ?? 0,
                'total' => $trucks->total()
            ]);
        }

        return view('trucks.index', compact('trucks'));
    }

    public function create()
    {
        $this->authorize('create', \App\Models\Truck::class);
        return view('trucks.create');
    }

    public function store(Request $request)
    {
        $this->authorize('create', \App\Models\Truck::class);
        $validated = $request->validate([
            'truck_number' => 'required|string|unique:trucks|max:255',
        ]);

        try {
            $this->truckService->createTruck($validated, $request->user());
            return redirect()->route('trucks.index')->with('success', 'Truck created successfully');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function edit($id)
    {
        $this->authorize('update', \App\Models\Truck::class);
        $truck = $this->truckService->getTruck($id);
        return view('trucks.edit', compact('truck'));
    }

    public function update(Request $request, $id)
    {
        $this->authorize('update', \App\Models\Truck::class);
        $validated = $request->validate([
            'truck_number' => 'required|string|max:255|unique:trucks,truck_number,' . $id,
        ]);

        try {
            $this->truckService->updateTruck($id, $validated, $request->user());
            return redirect()->route('trucks.index')->with('success', 'Truck updated successfully');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function destroy(Request $request, $id)
    {
        $this->authorize('delete', \App\Models\Truck::class);
        try {
            $this->truckService->deleteTruck($id, $request->user());
            return redirect()->route('trucks.index')->with('success', 'Truck deleted successfully');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}

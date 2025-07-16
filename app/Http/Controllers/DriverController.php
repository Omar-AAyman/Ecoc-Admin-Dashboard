<?php

namespace App\Http\Controllers;

use App\Services\DriverService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class DriverController extends Controller
{
    protected $driverService;

    public function __construct(DriverService $driverService)
    {
        $this->middleware('restrict.to.role:super_admin');
        $this->driverService = $driverService;
    }

    public function index(Request $request)
    {
        $this->authorize('viewAny', \App\Models\Driver::class);
        $perPage = $request->input('per_page', 10);
        $search = $request->input('search');

        $drivers = $this->driverService->getPaginatedDrivers($search, $perPage);

        if ($request->ajax()) {
            $table = View::make('drivers.partials.table', compact('drivers'))->render();
            $pagination = $drivers->appends(['per_page' => $perPage, 'search' => $search])->links('pagination::bootstrap-5')->render();
            return response()->json([
                'table' => $table,
                'pagination' => $pagination,
                'first_item' => $drivers->firstItem() ?? 0,
                'last_item' => $drivers->lastItem() ?? 0,
                'total' => $drivers->total()
            ]);
        }

        return view('drivers.index', compact('drivers'));
    }

    public function create()
    {
        $this->authorize('create', \App\Models\Driver::class);
        return view('drivers.create');
    }

    public function store(Request $request)
    {
        $this->authorize('create', \App\Models\Driver::class);
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:drivers,name',
        ]);

        try {
            $this->driverService->createDriver($validated, $request->user());
            return redirect()->route('drivers.index')->with('success', 'Driver created successfully');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function edit($id)
    {
        $this->authorize('update', \App\Models\Driver::class);
        $driver = $this->driverService->getDriver($id);
        return view('drivers.edit', compact('driver'));
    }

    public function update(Request $request, $id)
    {
        $this->authorize('update', \App\Models\Driver::class);
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:drivers,name,' . $id,
        ]);

        try {
            $this->driverService->updateDriver($id, $validated, $request->user());
            return redirect()->route('drivers.index')->with('success', 'Driver updated successfully');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function destroy(Request $request, $id)
    {
        $this->authorize('delete', \App\Models\Driver::class);
        try {
            $this->driverService->deleteDriver($id, $request->user());
            return redirect()->route('drivers.index')->with('success', 'Driver deleted successfully');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}

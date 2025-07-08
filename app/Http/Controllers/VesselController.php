<?php

namespace App\Http\Controllers;

use App\Services\VesselService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class VesselController extends Controller
{
    protected $vesselService;

    public function __construct(VesselService $vesselService)
    {
        $this->middleware('restrict.to.role:super_admin');
        $this->vesselService = $vesselService;
    }

    public function index(Request $request)
    {
        $this->authorize('viewAny', \App\Models\Vessel::class);
        $perPage = $request->input('per_page', 10);
        $search = $request->input('search');

        $vessels = $this->vesselService->getPaginatedVessels($search, $perPage);

        if ($request->ajax()) {
            $table = View::make('vessels.partials.table', compact('vessels'))->render();
            $pagination = $vessels->appends(['per_page' => $perPage, 'search' => $search])->links('pagination::bootstrap-5')->render();
            return response()->json([
                'table' => $table,
                'pagination' => $pagination,
                'first_item' => $vessels->firstItem() ?? 0,
                'last_item' => $vessels->lastItem() ?? 0,
                'total' => $vessels->total()
            ]);
        }

        return view('vessels.index', compact('vessels'));
    }

    public function create()
    {
        $this->authorize('create', \App\Models\Vessel::class);
        return view('vessels.create');
    }

    public function store(Request $request)
    {
        $this->authorize('create', \App\Models\Vessel::class);
        $validated = $request->validate([
            'name' => 'required|string|unique:vessels|max:255',
            'nationality' => 'required|string|max:255',
        ]);

        try {
            $this->vesselService->createVessel($validated, $request->user());
            return redirect()->route('vessels.index')->with('success', 'Vessel created successfully');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function edit($id)
    {
        $this->authorize('update', \App\Models\Vessel::class);
        $vessel = $this->vesselService->getVessel($id);
        return view('vessels.edit', compact('vessel'));
    }

    public function update(Request $request, $id)
    {
        $this->authorize('update', \App\Models\Vessel::class);
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:vessels,name,' . $id,
            'nationality' => 'required|string|max:255',
        ]);

        try {
            $this->vesselService->updateVessel($id, $validated, $request->user());
            return redirect()->route('vessels.index')->with('success', 'Vessel updated successfully');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function destroy(Request $request, $id)
    {
        $this->authorize('delete', \App\Models\Vessel::class);
        try {
            $this->vesselService->deleteVessel($id, $request->user());
            return redirect()->route('vessels.index')->with('success', 'Vessel deleted successfully');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}

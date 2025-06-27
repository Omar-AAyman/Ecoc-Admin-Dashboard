<?php

namespace App\Http\Controllers;

use App\Services\VesselService;
use Illuminate\Http\Request;

class VesselController extends Controller
{
    protected $vesselService;

    public function __construct(VesselService $vesselService)
    {
        $this->middleware('restrict.to.role:super_admin');
        $this->vesselService = $vesselService;
    }

    public function index()
    {
        $vessels = $this->vesselService->getVessels();
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

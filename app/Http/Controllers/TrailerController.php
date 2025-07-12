<?php

namespace App\Http\Controllers;

use App\Services\TrailerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class TrailerController extends Controller
{
    protected $trailerService;

    public function __construct(TrailerService $trailerService)
    {
        $this->middleware('restrict.to.role:super_admin');
        $this->trailerService = $trailerService;
    }

    public function index(Request $request)
    {
        $this->authorize('viewAny', \App\Models\Trailer::class);
        $perPage = $request->input('per_page', 10);
        $search = $request->input('search');

        $trailers = $this->trailerService->getPaginatedTrailers($search, $perPage);

        if ($request->ajax()) {
            $table = View::make('trailers.partials.table', compact('trailers'))->render();
            $pagination = $trailers->appends(['per_page' => $perPage, 'search' => $search])->links('pagination::bootstrap-5')->render();
            return response()->json([
                'table' => $table,
                'pagination' => $pagination,
                'first_item' => $trailers->firstItem() ?? 0,
                'last_item' => $trailers->lastItem() ?? 0,
                'total' => $trailers->total()
            ]);
        }

        return view('trailers.index', compact('trailers'));
    }

    public function create()
    {
        $this->authorize('create', \App\Models\Trailer::class);
        return view('trailers.create');
    }

    public function store(Request $request)
    {
        $this->authorize('create', \App\Models\Trailer::class);
        $validated = $request->validate([
            'trailer_number' => 'required|string|unique:trailers|max:255',
        ]);

        try {
            $this->trailerService->createTrailer($validated, $request->user());
            return redirect()->route('trailers.index')->with('success', 'Trailer created successfully');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function edit($id)
    {
        $this->authorize('update', \App\Models\Trailer::class);
        $trailer = $this->trailerService->getTrailer($id);
        return view('trailers.edit', compact('trailer'));
    }

    public function update(Request $request, $id)
    {
        $this->authorize('update', \App\Models\Trailer::class);
        $validated = $request->validate([
            'trailer_number' => 'required|string|max:255|unique:trailers,trailer_number,' . $id,
        ]);

        try {
            $this->trailerService->updateTrailer($id, $validated, $request->user());
            return redirect()->route('trailers.index')->with('success', 'Trailer updated successfully');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function destroy(Request $request, $id)
    {
        $this->authorize('delete', \App\Models\Trailer::class);
        try {
            $this->trailerService->deleteTrailer($id, $request->user());
            return redirect()->route('trailers.index')->with('success', 'Trailer deleted successfully');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}

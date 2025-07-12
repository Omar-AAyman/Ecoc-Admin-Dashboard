<?php

namespace App\Http\Controllers;

use App\Services\SearchService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class SearchController extends Controller
{
    protected $searchService;

    public function __construct(SearchService $searchService)
    {
        $this->middleware(['auth', 'localeSessionRedirect', 'localizationRedirect', 'restrict.client.no.tanks', 'restrict.to.role:super_admin,ceo,client']);
        $this->searchService = $searchService;
    }

    public function ajaxSearch(Request $request)
    {
        $query = $request->input('query');
        $category = $request->input('category', 'all');

        $results = $this->searchService->search($request->user(), $query, $category, 10);

        return response()->json(['results' => $results]);
    }

    public function results(Request $request)
    {
        $query = $request->input('query');
        $category = $request->input('category', 'all');
        $perPage = $request->input('per_page', 10);

        $results = $this->searchService->search($request->user(), $query, $category, $perPage, true);

        // Convert array to collection if necessary
        if (is_array($results)) {
            $results = collect($results)->paginate($perPage);
        }

        if ($request->ajax()) {
            $table = View::make('search.partials.table', compact('results', 'query', 'category', 'perPage'))->render();
            $pagination = $results->appends(['per_page' => $perPage, 'query' => $query, 'category' => $category])->links('pagination::bootstrap-5')->render();
            return response()->json([
                'table' => $table,
                'pagination' => $pagination,
                'first_item' => $results->firstItem() ?? 0,
                'last_item' => $results->lastItem() ?? 0,
                'total' => $results->total()
            ]);
        }

        return view('search.results', compact('results', 'query', 'category', 'perPage'));
    }
}

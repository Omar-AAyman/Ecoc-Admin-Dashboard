<?php

namespace App\Http\Controllers;

use App\Services\ProductService;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    protected $productService;

    public function __construct(ProductService $productService)
    {
        $this->middleware('restrict.to.role:super_admin');
        $this->productService = $productService;
    }

    public function index()
    {
        $products = $this->productService->getProducts();
        return view('products.index', compact('products'));
    }

    public function create()
    {
        $this->authorize('create', \App\Models\Product::class);
        return view('products.create');
    }

    public function store(Request $request)
    {
        $this->authorize('create', \App\Models\Product::class);
        $validated = $request->validate([
            'name' => 'required|string|unique:products|max:255',
            'density' => 'required|numeric|min:0.1',
        ]);

        try {
            $this->productService->createProduct($validated, $request->user());
            return redirect()->route('products.index')->with('success', 'Product created successfully');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function edit($id)
    {
        $this->authorize('update', \App\Models\Product::class);
        $product = $this->productService->getProduct($id);
        return view('products.edit', compact('product'));
    }

    public function update(Request $request, $id)
    {
        $this->authorize('update', \App\Models\Product::class);
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:products,name,' . $id,
            'density' => 'required|numeric|min:0.1',
        ]);

        try {
            $this->productService->updateProduct($id, $validated, $request->user());
            return redirect()->route('products.index')->with('success', 'Product updated successfully');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function destroy(Request $request, $id)
    {
        $this->authorize('delete', \App\Models\Product::class);
        try {
            $this->productService->deleteProduct($id, $request->user());
            return redirect()->route('products.index')->with('success', 'Product deleted successfully');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}

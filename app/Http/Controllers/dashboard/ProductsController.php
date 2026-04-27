<?php

namespace App\Http\Controllers\dashboard;

use App\Http\Controllers\Controller;
use App\Models\Dashboard\Brand;
use App\Models\Dashboard\Category;
use App\Services\dashboard\ProductService;
use Illuminate\Http\Request;

class ProductsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    protected $service;

    public function __construct(ProductService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            return $this->service->getTableData();
        }

        return view('dashboard.products.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {


        return view('dashboard.products.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $product = $this->service->getShowDetails($id);
        
        return view('dashboard.products.show', compact('product'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $product = $this->service->getShowDetails($id);
        return view('dashboard.products.edit', compact('product'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function toggleStatus(string $id)
    {
        try {
            $this->service->toggleStatus($id);
            return response()->json(['success' => true, 'message'=>__('messages.done')]);
        } catch (\Throwable $th) {
            return response()->json(['success' => false, 'message'=>__('messages.error')]);
        }
    }
}

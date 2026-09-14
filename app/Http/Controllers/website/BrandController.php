<?php

namespace App\Http\Controllers\website;

use App\Http\Controllers\Controller;
use App\Models\Dashboard\Brand;
use App\Services\website\CatalogService;
use Illuminate\View\View;

class BrandController extends Controller
{
    public function __construct(
        private CatalogService $catalogService
    ) {}

    public function index(): View
    {
        $brands = Brand::query()
            ->select(['id', 'name', 'slug', 'logo'])
            ->where('status', true)
            ->oldest('id')
            ->get();

        return view('website.brands.index', compact('brands'));
    }

    public function getProductsByBrand(string $slug): View
    {
        $products = $this->catalogService->getProductsByBrand($slug);

        return view('website.products', compact('products'));
    }
}

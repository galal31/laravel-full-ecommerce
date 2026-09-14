<?php

namespace App\Http\Controllers\website;

use App\Http\Controllers\Controller;
use App\Models\Dashboard\Category;
use App\Services\website\CatalogService;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function __construct(
        private CatalogService $catalogService
    ) {}

    public function index(): View
    {
        $categories = Category::query()
            ->select(['id', 'name', 'slug', 'icon'])
            ->where('status', true)
            ->oldest('id')
            ->get();

        return view('website.categories.index', compact('categories'));
    }

    public function getProductsByCategory(string $slug): View
    {
        $products = $this->catalogService->getProductsByCategory($slug);

        return view('website.products', compact('products'));
    }
}

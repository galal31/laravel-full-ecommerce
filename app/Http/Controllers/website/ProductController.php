<?php

namespace App\Http\Controllers\website;

use App\Http\Controllers\Controller;
use App\Services\website\ProductService;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function __construct(
        private ProductService $productService
    ) {}

    public function show(string $slug): View
    {
        $product = $this->productService->getProductBySlug($slug);

        $relatedProducts = $this->productService->getRelatedProducts($product);

        return view(
            'website.products.show',
            compact('product', 'relatedProducts')
        );
    }
}

<?php

namespace App\Services\website;

use App\Models\Dashboard\Product;
use App\Reposetories\website\ProductRepository;
use Illuminate\Database\Eloquent\Collection;

class ProductService
{
    public function __construct(
        private ProductRepository $productRepository
    ) {}

    public function getProductBySlug(string $slug): Product
    {
        return $this->productRepository->getActiveProductBySlug($slug);
    }

    public function getRelatedProducts(
        Product $product,
        int $limit = 4
    ): Collection {
        return $this->productRepository->getRelatedProducts($product, $limit);
    }
}

<?php

namespace App\Reposetories\website;

use App\Models\Dashboard\Brand;
use App\Models\Dashboard\Category;
use App\Models\Dashboard\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

class CatalogRepository
{
    public function getProductsByBrandSlug(string $slug, int $perPage = 20): LengthAwarePaginator
    {
        $brand = Brand::query()
            ->select('id')
            ->where('slug', $slug)
            ->where('status', true)
            ->firstOrFail();

        return $this->productsQuery()
            ->where('brand_id', $brand->id)
            ->paginate($perPage);
    }

    public function getProductsByCategorySlug(string $slug, int $perPage = 20): LengthAwarePaginator
    {
        $category = Category::query()
            ->select('id')
            ->where('slug', $slug)
            ->where('status', true)
            ->firstOrFail();

        return $this->productsQuery()
            ->where('category_id', $category->id)
            ->paginate($perPage);
    }

    private function productsQuery(): Builder
    {
        return Product::query()
            ->select([
                'id', 'name', 'slug', 'price', 'discount',
                'start_discount', 'end_discount',
                'brand_id', 'category_id', 'manage_stock',
                'quantity', 'available_in_stock', 'created_at',
            ])
            ->with(['brand:id,name', 'category:id,name'])
            ->withStorefrontData()
            ->withWishlistStatus()
            ->latest();
    }
}

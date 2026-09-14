<?php

namespace App\Reposetories\website;

use App\Models\Dashboard\Brand;
use App\Models\Dashboard\Category;
use App\Models\Dashboard\Product;
use App\Models\Dashboard\Slider;
use Illuminate\Database\Eloquent\Collection;

class HomeRepository
{
    public function getSliders(): Collection
    {
        return Slider::query()->latest()->get();
    }

    public function getActiveParentCategories(): Collection
    {
        return Category::query()
            ->select(['id', 'name', 'slug', 'icon'])
            ->where('status', true)
            ->whereNull('parent_id')
            ->oldest('id')
            ->get();
    }

    public function getActiveBrands(): Collection
    {
        return Brand::query()
            ->select(['id', 'name', 'slug', 'logo'])
            ->where('status', true)
            ->oldest('id')
            ->get();
    }

    public function getNewestProducts(
        int $limit = 8,
        ?int $userId = null,
        array $excludedProductIds = []
    ): Collection
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
            ->withWishlistStatus($userId)
            ->when(
                $excludedProductIds !== [],
                fn ($query) => $query->whereNotIn('products.id', $excludedProductIds)
            )
            ->latest()
            ->limit($limit)
            ->get();
    }

    // get products that discount ends today
    public function getDiscountEndingTodayProducts(int $limit = 8, ?int $userId = null): Collection
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
            ->withWishlistStatus($userId)
            ->where('discount', '>', 0)
            ->whereDate('end_discount', now()->toDateString())
            ->latest()
            ->limit($limit)
            ->get();
    }
}

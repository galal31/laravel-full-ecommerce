<?php

namespace App\Reposetories\website;

use App\Models\Dashboard\Product;
use Illuminate\Database\Eloquent\Collection;

class ProductRepository
{
    public function getActiveProductBySlug(string $slug): Product
    {
        return Product::query()
            ->select([
                'id', 'category_id', 'brand_id', 'name', 'slug',
                'small_desc', 'desc', 'price', 'discount',
                'start_discount', 'end_discount', 'manage_stock',
                'quantity', 'available_in_stock', 'created_at',
            ])
            ->with([
                'brand:id,name,slug',
                'category:id,name,slug',
                'variants:id,product_id,price,stock',
                'variants.images' => function ($query) {
                    return $query
                        ->select(['id', 'product_variant_id', 'file_name'])
                        ->oldest('id');
                },
                'variants.attributeValues:id,attribute_id,value',
                'variants.attributeValues.attribute:id,name',
            ])
            ->withStorefrontData()
            ->where('slug', $slug)
            ->firstOrFail();
    }

    public function getRelatedProducts(Product $product, int $limit = 4): Collection
    {
        // لو المنتج بدون فئة مش هنقدر نحدد منتجات مرتبطة به.
        if (! $product->category_id) {
            return new Collection;
        }

        return Product::query()
            ->select([
                'id', 'category_id', 'brand_id', 'name', 'slug',
                'price', 'discount', 'start_discount', 'end_discount',
                'manage_stock', 'quantity', 'available_in_stock', 'created_at',
            ])
            ->with('brand:id,name')
            ->withStorefrontData()
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->latest()
            ->limit($limit)
            ->get();
    }
}

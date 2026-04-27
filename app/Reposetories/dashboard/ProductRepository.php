<?php

namespace App\Reposetories\dashboard;

use App\Models\Dashboard\Product;
use App\Models\Dashboard\ProductImage;
use App\Models\Dashboard\ProductVariant;
use App\Models\Dashboard\Tag;
use App\Models\Dashboard\VariantImage;

class ProductRepository
{
    public function createProduct(array $data)
    {
        return Product::create($data);
    }

    public function syncTags(Product $product, array $tagIds)
    {
        $product->tags()->sync($tagIds);
    }

    public function createVariant(Product $product, array $data)
    {
        return $product->variants()->create($data);
    }

    public function attachVariantAttributes($variant, array $attributeIds)
    {
        $variant->attributeValues()->attach($attributeIds);
    }

    public function firstOrCreateTag(string $tagName)
    {
        return Tag::firstOrCreate(['name' => $tagName]);
    }

    // datatables
    public function getAllProductsQuery()
    {
        return Product::with(['category', 'brand', 'variants']);
    }

    public function toggleStatus(string $id)
    {
        $product = Product::findOrFail($id);
        $product->status = ! $product->status;
        $product->save();

        return $product;
    }

    public function getProductWithDetails($id)
    {
        return Product::with([
            'category',
            'brand',
            'tags',
            'images',
            'variants.attributeValues.attribute',
        ])->findOrFail($id);
    }

    public function getProductForEdit($id)
    {
        // جلب المنتج مع علاقاته (إذا كنت تستخدم Spatie Media Library أو Tags لاحقاً يمكنك إضافتها هنا)
        return Product::with(['category', 'brand','tags', 'variants'])->findOrFail($id);
    }

    /**
     * تحديث بيانات المنتج
     */
    public function update(Product $product, array $data)
    {
        $product->update($data);
        return $product;
    }
    public function getVariantById($id)
    {
        return ProductVariant::with('images')->find($id);
    }

    public function getProductImageById($id)
    {
        return ProductImage::find($id);
    }

    public function getVariantImageById($id)
    {
        return VariantImage::find($id);
    }

    public function deleteVariant(ProductVariant $variant)
    {
        return $variant->delete();
    }

    public function deleteProductImage(ProductImage $image)
    {
        return $image->delete();
    }

    public function deleteVariantImage(VariantImage $image)
    {
        return $image->delete();
    }

    
}

<?php

namespace App\Models\Dashboard;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class VariantImage extends Model
{
    protected $table = 'variant_images';
    
    protected $fillable = ['file_name', 'product_variant_id'];

    // علاقة الصورة بالمتغير (كل صورة تخص متغير واحد)
    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    // تجهيز رابط الصورة حتى نستخدمه مباشرة داخل صفحة المنتج.
    protected function imageUrl(): Attribute
    {
        return Attribute::make(
            get: function (): string {
                $fileName = $this->getRawOriginal('file_name');

                if ($fileName && Storage::disk('products')->exists($fileName)) {
                    return asset('storage/products/'.$fileName);
                }

                return asset(
                    'website-assets/assets/images/homepage-one/product-img/product-img-1.webp'
                );
            },
        );
    }
}

<?php

namespace App\Models\Dashboard;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ProductImage extends Model
{
    protected $table = 'product_images';
    protected $fillable = ['file_name'];
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

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

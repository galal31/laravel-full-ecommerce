<?php

namespace App\Models\Dashboard;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Product extends Model
{
    use HasTranslations;
    protected $fillable = [
        'category_id',
        'brand_id',
        'name',
        'small_desc',
        'desc',
        'status',
        'sku',
        'available_for',
        'views',
        'price',
        'discount',
        'start_discount',
        'end_discount',
        'manage_stock',
        'quantity',
        'available_in_stock',
    ]  ;
    public $translatable = ['name', 'desc','small_desc'];


    //relationships
    public function variants(){
        return $this->hasMany(ProductVariant::class);
    }
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function images(){
        return $this->hasMany(ProductImage::class);
    }

    public function tags(){
        return $this->belongsToMany(Tag::class,'product_tags');
    }
}

<?php

namespace App\Models\Dashboard;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Product extends Model
{
    use HasTranslations;
    use HasSlug;
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
        'slug'
    ];
    public $translatable = ['name', 'desc', 'small_desc'];
    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom(function (Product $model) {
                return $model->getTranslation('name', 'en');
            })
            ->saveSlugsTo('slug');
    }

    //relationships
    public function variants()
    {
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

    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'product_tags');
    }
}

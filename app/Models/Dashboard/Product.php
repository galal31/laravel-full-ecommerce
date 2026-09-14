<?php

namespace App\Models\Dashboard;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Spatie\Translatable\HasTranslations;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Product extends Model
{
    use HasTranslations;
    use HasSlug;
    protected $fillable = ['category_id', 'brand_id', 'name', 'small_desc', 'desc', 'status', 'sku', 'available_for', 'views', 'price', 'discount', 'start_discount', 'end_discount', 'manage_stock', 'quantity', 'available_in_stock', 'slug'];
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

    public function hasActiveDiscount(): bool
    {
        if ($this->discount === null || (float) $this->discount <= 0) {
            return false;
        }

        $today = Carbon::today();
        $startsAt = $this->start_discount
            ? Carbon::parse($this->start_discount)->startOfDay()
            : null;
        $endsAt = $this->end_discount
            ? Carbon::parse($this->end_discount)->endOfDay()
            : null;

        return ($startsAt === null || $today->greaterThanOrEqualTo($startsAt))
            && ($endsAt === null || $today->lessThanOrEqualTo($endsAt));
    }

    public function getPriceAfterDiscount(): float
    {
        $price = (float) $this->price;

        if (! $this->hasActiveDiscount()) {
            return $price;
        }

        $discountAmount = $price * ((float) $this->discount / 100);

        return round(max(0, $price - $discountAmount), 2);
    }

    public function getDiscountDaysRemaining(): ?int
    {
        if (! $this->hasActiveDiscount() || ! $this->end_discount) {
            return null;
        }

        return (int) Carbon::today()->diffInDays(
            Carbon::parse($this->end_discount)->startOfDay()
        );
    }
}

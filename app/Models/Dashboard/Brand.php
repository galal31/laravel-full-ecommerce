<?php

namespace App\Models\Dashboard;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Brand extends Model
{
    use HasTranslations;

    public $translatable = ['name'];
    protected $fillable = ['name','logo','slug','status'];

    // products relation

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function status():Attribute{
        return Attribute::make(
            get: fn ($value) => $value == 1 ? __('brands.active') : __('brands.inactive'),
        );
    }
}

<?php

namespace App\Models\Dashboard;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

class ProductVariant extends Model
{
    protected $table = 'product_variants';

    protected $fillable = [
        'product_id',
        'price',
        'stock',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    
    public function attributeValues(){
        return $this->belongsToMany(AttributeValue::class,'variant_attributes','product_variant_id','attribute_value_id');
    }

    public function images()
    {
        return $this->hasMany(VariantImage::class, 'product_variant_id');
    }


}

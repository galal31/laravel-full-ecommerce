<?php

namespace App\Models\Dashboard;

use Illuminate\Database\Eloquent\Model;

class VariantImage extends Model
{
    protected $table = 'variant_images';
    
    protected $fillable = [
        'file_name', 
        'product_variant_id'
    ];

    // علاقة الصورة بالمتغير (كل صورة تخص متغير واحد)
    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }
}

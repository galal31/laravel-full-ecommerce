<?php

namespace App\Models\Dashboard;

use Illuminate\Database\Eloquent\Model;

class ProductImage extends Model
{
    protected $table = 'product_images';
    protected $fillable = ['file_name'];
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}

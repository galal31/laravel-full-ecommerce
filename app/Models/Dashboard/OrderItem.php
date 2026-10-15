<?php

namespace App\Models\Dashboard;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class OrderItem extends Model
{
    use HasTranslations;
    protected $table = 'order_items';
    protected $fillable = ['order_id', 'product_id', 'product_variant_id', 'product_name', 'product_quantity', 'product_price', 'data'];
    protected $translatable = ['product_name', 'data'];
    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }
}

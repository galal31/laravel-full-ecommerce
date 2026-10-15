<?php

namespace App\Models\Dashboard;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    //
    protected $table = 'orders';
    protected $fillable = ['user_id', 'coupon_id', 'coupon_code', 'coupon_discount_percentage', 'coupon_discount_amount', 'coupon_usage_status', 'payment_review_reason', 'payment_expires_at', 'user_name', 'user_phone', 'user_email', 'price', 'shipping_price', 'total_price', 'note', 'status', 'country', 'governorate', 'city', 'street'];

    protected function casts(): array
    {
        return [
            'payment_expires_at' => 'datetime',
        ];
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class, 'order_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}

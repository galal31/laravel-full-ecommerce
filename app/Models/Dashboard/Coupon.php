<?php

namespace App\Models\Dashboard;

use App\Models\Cart;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    use HasFactory;
    protected $table = 'coupons';
    protected $guarded = [];

    // اكسيسور نغيري بيه طريقة عرض ال is active
    public function status(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->is_active == 1 ? __('messages.active') : __('messages.inactive'),
        );
    }

    public function cart()
    {
        return $this->hasMany(Cart::class);
    }
}

<?php

namespace App\Models\Dashboard;

use Illuminate\Database\Eloquent\Model;

class ShippingGovernorate extends Model
{
    protected $table = 'shipping_governorates';
    protected $fillable = ['price'];
    public function governorate()
    {
        // one to one
        return $this->belongsTo(Governorate::class);
    }
}

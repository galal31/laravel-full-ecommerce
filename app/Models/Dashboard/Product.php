<?php

namespace App\Models\Dashboard;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    //


    // brands relation
    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }
}

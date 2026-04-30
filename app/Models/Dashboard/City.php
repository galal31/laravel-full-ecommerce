<?php

namespace App\Models\Dashboard;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class City extends Model
{
    use HasTranslations;
    protected $table = 'cities';
    public $translatable = ['name'];

    public function governorate()
    {
        return $this->belongsTo(Governorate::class);
    }

    public function users()
    {
        return $this->hasMany(User::class, 'city_id');
    }
}

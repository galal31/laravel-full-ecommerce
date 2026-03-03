<?php

namespace App\Models\Dashboard;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Country extends Model
{
    use HasTranslations;
    protected $table = 'countries';
    protected $guarded = [];
    public $translatable = ['name'];

    public function governorates()
    {
        return $this->hasMany(Governorate::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function status(){
        return Attribute::make(
            get: fn ($value) => $value == 1 ? 'Active' : 'Inactive',
        );
    }
}

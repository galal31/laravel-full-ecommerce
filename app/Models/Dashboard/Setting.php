<?php

namespace App\Models\Dashboard;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Setting extends Model
{
    use HasTranslations;
    public $table = 'settings';
    public $timestamps = false;
    public $translatable = ['site_name', 'address'];

    protected $fillable = ['site_name', 'phone', 'address', 'email', 'email_support', 'facebook', 'twitter', 'youtube', 'logo', 'favicon'];

    public function logo():Attribute{
        return Attribute::make(
            get: fn ($value) => asset('storage/settings/'.$value)
        );
    }
    public function favicon():Attribute{
        return Attribute::make(
            get: fn ($value) => asset('storage/settings/'.$value)
        );
    }
}

<?php

namespace App\Models\Dashboard;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Spatie\Translatable\HasTranslations;

class Slider extends Model
{
    use HasTranslations;
    protected $fillable = ['file_name', 'note'];
    public $translatable = ['note'];
    public function getFileNameAttribute($value)
    {
        return asset('storage/sliders/' . $value);
    }
}

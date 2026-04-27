<?php

namespace App\Models\Dashboard;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Attribute extends Model
{
    use HasTranslations;

    public $translatable = ['name'];
    public $timestamps = false;
    protected $fillable = ['name'];

    public function values()
    {
        return $this->hasMany(AttributeValue::class);
    }
}

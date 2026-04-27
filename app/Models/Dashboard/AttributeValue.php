<?php

namespace App\Models\Dashboard;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class AttributeValue extends Model
{
    use HasTranslations;
    public $translatable = ['value'];
    public $fillable = ['attribute_id','value'];
    public $timestamps = false;

    public function attribute()
    {
        return $this->belongsTo(Attribute::class);
    }
}

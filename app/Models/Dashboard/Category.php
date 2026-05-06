<?php

namespace App\Models\Dashboard;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Category extends Model
{
    use HasTranslations;
    public $translatable = ['name'];
    protected $fillable = ['name','slug','parent_id','status','icon'];

    public function parent(){
        return $this->belongsTo(Category::class,'parent_id');
    }

    public function children(){
        return $this->hasMany(Category::class,'parent_id');
    }

    // getteer for icon 
    protected function icon(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => asset('storage/categories/' . $value),
        );
    }
    protected $casts = [
        'name'=>'array'
    ];
}

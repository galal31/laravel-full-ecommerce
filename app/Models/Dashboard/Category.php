<?php

namespace App\Models\Dashboard;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
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

    protected function icon(): Attribute
    {
        return Attribute::make(
            get: function (?string $value): string {
                $fileName = $value && Storage::disk('categories')->exists($value)
                    ? $value
                    : 'default.png';

                return asset('storage/categories/'.$fileName);
            },
        );
    }
    protected $casts = [
        'name'=>'array'
    ];
}

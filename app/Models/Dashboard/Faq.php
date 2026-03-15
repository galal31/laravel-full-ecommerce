<?php

namespace App\Models\Dashboard;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Faq extends Model
{
    use HasTranslations;
    public $translatable = ['question', 'answer'];
    protected $fillable = ['question', 'answer', 'created_at', 'updated_at'];
}

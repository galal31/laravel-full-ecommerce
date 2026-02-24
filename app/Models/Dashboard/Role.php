<?php

namespace App\Models\Dashboard;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $fillable = ['name', 'permissions'];

    protected $casts = [
        'permissions' => 'array'
    ];

    public function admins(){
        return $this->hasMany(Admin::class);
    }
}

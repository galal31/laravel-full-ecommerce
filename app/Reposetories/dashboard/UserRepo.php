<?php

namespace App\Reposetories\dashboard;

use App\Models\User;

class UserRepo
{
    /**
     * Create a new class instance.
     */
    public function getAllUsers()
    {
        return User::latest()->select('id', 'name', 'email', 'created_at', 'is_active', 'city_id')->with('city')->get();
    }

    public function findById($id)
    {
        return User::findOrFail($id);
    }

    // تحديث بيانات المستخدم
    public function update(User $user, array $data)
    {
        return $user->update($data);
    }

    // store user
    public function store(array $data)
    {
        return User::create($data);
    }
    
}

<?php

namespace App\reposetories\dashboard;

use App\Models\Dashboard\Role;

class RoleRepo
{
    /**
     * Create a new class instance.
     */

    public function index()
    {
        $roles = Role::get();
        return $roles;
    }

    public function store($name,$permissions){
        $role = Role::create([
            'name'=>$name,
            'permissions'=>$permissions
        ]);
    }

    public function update($id,$name,$permissions){
        $role = Role::find($id);
        $role->update([
            'name'=>$name,
            'permissions'=>$permissions
        ]);
    }

    public function destroy($id){
        $role = Role::find($id);
        $role->delete();
    }

    public function find($id){
        $role = Role::find($id);
        return $role;
    }
}

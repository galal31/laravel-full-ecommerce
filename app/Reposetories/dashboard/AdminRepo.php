<?php

namespace App\Reposetories\dashboard;

use App\Models\Dashboard\Admin;
use App\Models\Dashboard\Role;

class AdminRepo
{
    public function index()
    {
        $admins = Admin::with('role:id,name')->get();
        $roles = Role::select('id', 'name')->get();
        return ['admins' => $admins, 'roles' => $roles];
    }

    public function store($data)
    {
        $admin = Admin::create($data);
        return $admin;
    }

    public function update($data, $id)
    {
        try {
            $admin = Admin::findOrFail($id);
            if (!empty($data['password'])) {
                $data['password'] = bcrypt($data['password']);
            } else {
                unset($data['password']);
            }
            $admin->update($data);
            return $admin;
        } catch (\Throwable $th) {
            return false;
        }
    }

    public function destroy($id)
    {
        $admin = Admin::findOrFail($id);
        $admin->delete();
        return $admin;
    }

    public function toggleStatus($id)
    {
        $admin = Admin::findOrFail($id);
        $admin->status = !$admin->status;
        if ($admin->save()) {
            return $admin;
        }
        return false;

    }
}

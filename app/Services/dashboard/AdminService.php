<?php

namespace App\Services\dashboard;

use App\Reposetories\dashboard\AdminRepo;

class AdminService
{
    /**
     * Create a new class instance.
     */
    protected $adminsRepo;
    public function __construct(AdminRepo $adminsRepo)
    {
        $this->adminsRepo = $adminsRepo;
    }

    public function index()
    {
        $admins = $this->adminsRepo->index();
        if(!$admins){
            return false;
        }
        return $admins;
    }

    public function store($data){
        $admin = $this->adminsRepo->store($data);
        if(!$admin){
            return false;
        }
        return $admin;
    }

    public function update($data,$id){
        $admin = $this->adminsRepo->update($data,$id);
        if(!$admin){
            return false;
        }
        return $admin;
    }

    public function destroy($id){
        $admin = $this->adminsRepo->destroy($id);
        if(!$admin){
            return false;
        }
        return $admin;
    }

    public function toggleStatus($id){
        $admin = $this->adminsRepo->toggleStatus($id);
        if(!$admin){
            return false;
        }
        return $admin;
    }
}

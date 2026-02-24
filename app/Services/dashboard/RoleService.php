<?php

namespace App\services\dashboard;

use App\Reposetories\dashboard\AuthRepo;
use App\reposetories\dashboard\RoleRepo;
use PSpell\Config;

class RoleService
{
    /**
     * Create a new class instance.
     */
    protected $roleRepo;
    public function __construct(RoleRepo $roleRepo)
    {
        $this->roleRepo = $roleRepo;   
    }

    public function index(){
        return $this->roleRepo->index();
    }

    public function store($name,$permissions){
        return $this->roleRepo->store($name,$permissions);
    }

    public function update($id,$name,$permissions){
        return $this->roleRepo->update($id,$name,$permissions);
    }

    public function destroy($id){
        $role = $this->roleRepo->find($id);
        if($role->admins()->count() > 0){
            return false;
        }
        return $this->roleRepo->destroy($id);
    }
}

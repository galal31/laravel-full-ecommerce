<?php

namespace App\Http\Controllers\dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\dashboard\RoleRequest;
use App\services\dashboard\RoleService;
use Illuminate\Http\Request;

class RolesController extends Controller
{
    protected $rolesService;
    public function __construct(RoleService $rolesService){
        $this->rolesService = $rolesService;
    }
    public function index(){
        $roles = $this->rolesService->index();
        return view('dashboard.roles.index',compact('roles'));
    }

    public function store(RoleRequest $request){
        $this->rolesService->store($request->name,$request->permissions);
        return redirect()->route('dashboard.roles.index')->with('success',__('messages.done'));
    }

    public function update(RoleRequest $request,$id){
        $name = $request->name;
        $permissions = $request->permissions;
        $this->rolesService->update($id,$name,$permissions);
        return redirect()->route('dashboard.roles.index')->with('success',__('messages.done'));
    }

    public function destroy($id){
        if($this->rolesService->destroy($id)){
            return redirect()->route('dashboard.roles.index')->with('success',__('messages.done'));
        }
        return redirect()->route('dashboard.roles.index')->with('error',__('messages.error'));
    }
}

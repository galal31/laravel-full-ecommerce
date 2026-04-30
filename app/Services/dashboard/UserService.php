<?php

namespace App\Services\dashboard;

use App\Reposetories\dashboard\UserRepo;

class UserService
{
    /**
     * Create a new class instance.
     */
    protected $userRepo;
    public function __construct(UserRepo $userRepo)
    {
        $this->userRepo = $userRepo;
    }
    public function getAllUsersForDataTable()
    {
        $users = $this->userRepo->getAllUsers();
        return datatables()->of($users)
            ->addColumn('city', function ($user) {
                return $user->city ? $user->city->name : '---';
            })
            
            ->addColumn('status', function ($user) {
                if ($user->is_active == 1) {
                    return '<span class="badge badge-success">Active</span>';
                } else {
                    return '<span class="badge badge-danger">Inactive</span>';
                }
            })
            ->editColumn('created_at', function ($user) {
                return $user->created_at->format('Y-m-d H:i:s');
            })
            ->addColumn('actions', function ($user) {
                return view('dashboard.users._actions', compact('user'));
            })
            ->addIndexColumn()
            ->rawColumns(['status'])
            ->make(true);
    }

    // toggle user status
    public function toggleStatus($id)
    {
        $user = $this->userRepo->findById($id);
        $newStatus = $user->is_active == 1 ? 0 : 1;
        $this->userRepo->update($user, ['is_active' => $newStatus]);
        return $newStatus;
    }

    // store user
    public function store(array $data)
    {
        return $this->userRepo->store($data);
    }
}

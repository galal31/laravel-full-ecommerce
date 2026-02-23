<?php

namespace App\Services\dashboard;

use App\Reposetories\dashboard\AuthRepo;

class AuthService
{
    /**
     * Create a new class instance.
     */
    protected $authRepo;
    public function __construct(AuthRepo $authRepo)
    {
        $this->authRepo = $authRepo;
    }

    public function recoverPassword($email){
        return $this->authRepo->recoverPassword($email);
    }

    public function resetPassword($request){
        return $this->authRepo->resetPassword($request);
    }
}

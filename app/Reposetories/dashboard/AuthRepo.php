<?php

namespace App\Reposetories\dashboard;

use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class AuthRepo
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public function recoverPassword($email){
        return Password::broker('admins')->sendResetLink(['email' => $email]);
    }


    public function resetPassword($request){
        $status = Password::broker('admins')->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($admin, $password) {
                // الكلوجر ده بيتنفذ لو التوكن سليم والإيميل صح
                $admin->forceFill([
                    'password' => Hash::make($password) // تشفير الباسورد الجديد
                ])->setRememberToken(Str::random(60));

                $admin->save(); // الحفظ في الداتابيز
    
                event(new PasswordReset($admin));
            }
        );

        return $status;
    }
}

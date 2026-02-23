<?php

namespace App\Http\Controllers\dashboard\auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\dashboard\AdminLoginRequest;
use App\Services\dashboard\AuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\PasswordReset;
class AuthController extends Controller
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }
    public function showLogin()
    {
        return view('dashboard.auth.login');
    }

    public function login(AdminLoginRequest $request)
    {
        // 
        $credentials = $request->only('email', 'password');
        $remember = $request->has('remember');
        if (auth('admin')->attempt(['email' => $credentials['email'], 'password' => $credentials['password']], true)) {
            return redirect()->route('dashboard.welcome');
        } else {
            dd('failed');
        }
    }

    public function showRecoverPassword()
    {
        return view('dashboard.auth.recover-password');
    }


    public function recoverPassword(Request $request)
    {

        $request->validate([
            'email' => 'required|email|exists:admins,email'
        ], [
            'email.required' => trans('validation.required'),
            'email.email' => trans('validation.email'),
            'email.exists' => trans('validation.exists')
        ]);

        $status = $this->authService->recoverPassword($request->email);
        if ($status == Password::RESET_LINK_SENT) {
            return back()->with('status', trans($status));
        } else {
            return back()->withInput($request->only('email'))->withErrors([
                'email' => trans($status)
            ]);
        }

    }


    // 1. عرض صفحة إدخال الباسورد الجديد
    public function showResetPasswordForm(Request $request, $token)
    {
        // بنبعت التوكن والإيميل للصفحة عشان نستخدمهم في الفورم المخفية
        return view('dashboard.auth.reset-password', [
            'token' => $token,
            'email' => $request->email
        ]);
    }

    // 2. تحديث الباسورد في الداتابيز
    public function resetPassword(Request $request)
    {
        // 1. التحقق من البيانات (الفاليديشن)
        $request->validate([
            'token' => 'required',
            'email' => 'required|email|exists:admins,email',
            'password' => 'required|min:8|confirmed', // confirmed يعني لازم يتطابق مع حقل password_confirmation
        ], [
            'password.confirmed' => trans('validation.confirmed'),
            'password.min' => trans('validation.min.string', ['min' => 8])
        ]);

        // 2. استخدام لارافيل لتغيير الباسورد
        $status =$this->authService->resetPassword($request);

        // 3. التوجيه بناءً على النتيجة
        if ($status === Password::PASSWORD_RESET) {
            // لو نجح، نوديه لصفحة اللوجين مع رسالة نجاح
            return redirect()->route('dashboard.login')->with('status', __($status));
        }

        // لو فشل (التوكن منتهي مثلاً)، نرجعه مع إيرور
        return back()->withErrors(['email' => __($status)]);
    }
}

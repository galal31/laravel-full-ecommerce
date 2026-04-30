<?php

namespace App\Http\Controllers\dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\StoreUserRequest;
use App\Services\dashboard\UserService;
use Illuminate\Http\Request;

class UsersController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $users = $this->userService->getAllUsersForDataTable();

            return $users;
        } else {
            return view('dashboard.users.index');
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request)
    {
        try {
            $this->userService->store($request->validated());

            return response()->json([
                'status' => 'success',
                'message' => __('users.user_added'),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => __('messages.error'),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    // toggle user status
    public function toggleStatus($id)
    {
        try {
            // استدعاء دالة تغيير الحالة من الـ Service
            $this->userService->toggleStatus($id);

            // إرجاع استجابة نجاح للأجاكس
            return response()->json([
                'success' => true,
                'message' => __('users.status_updated'), // رسالة النجاح
            ]);

        } catch (\Exception $e) {
            // إرجاع استجابة خطأ للأجاكس في حال حدوث مشكلة
            return response()->json([
                'success' => false,
                'message' => __('messages.error_occurred'),
            ], 500);
        }
    }
}

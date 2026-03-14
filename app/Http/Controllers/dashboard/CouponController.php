<?php

namespace App\Http\Controllers\dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\dashboard\CouponsRequest;
use App\Services\dashboard\CouponService;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    //
    protected $couponService;
    public function __construct(CouponService $couponService)
    {
        $this->couponService = $couponService;
    }
    public function index()
    {
        if (request()->ajax()) {
            return $this->couponService->index();
        }
        return view('dashboard.coupons.index');
    }

    public function store(CouponsRequest $request)
    {
        
        $data = $request->validated();
        try {
            $this->couponService->store($data);
            return response()->json(
                [
                    'success' => true,
                    'message' => __('messages.done')
                ]
            );
        } catch (\Throwable $th) {
            return response()->json(
                [
                    'success' => false,
                    'message' => $th->getMessage()
                ]
            );
        }

    }

    public function update($id, CouponsRequest $request)
    {
        $data = $request->validated();
        try {
            $this->couponService->update($id, $data);
            return response()->json(
                [
                    'success' => true,
                    'message' => __('messages.done')
                ]
            );
        } catch (\Throwable $th) {
            return response()->json(
                [
                    'success' => false,
                    'message' => $th->getMessage()
                ]
            );
        }
    }

    public function destroy($id)
    {
        try {
            $this->couponService->destroy($id);
            return response()->json(
                [
                    'success' => true,
                    'message' => __('messages.done')
                ]
            );


        } catch (\Throwable $th) {
            return response()->json(
                [
                    'success' => false,
                    'message' => $th->getMessage()
                ]
            );
        }
    }

    public function toggleStatus($id)
    {
        try {
            $this->couponService->toggleStatus($id);
            return response()->json(
                [
                    'success' => true,
                    'message' => __('messages.done')
                ]
            );
        } catch (\Throwable $th) {
            return response()->json(
                [
                    'success' => false,
                    'message' => $th->getMessage()
                ]
            );
        }
    }
}

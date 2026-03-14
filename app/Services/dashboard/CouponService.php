<?php

namespace App\Services\dashboard;

use App\Models\Dashboard\Coupon;
use App\Reposetories\dashboard\CouponRepo;
use Yajra\DataTables\Facades\DataTables;

class CouponService
{
    /**
     * Create a new class instance.
     */
    protected $couponRepo;
    public function __construct(CouponRepo $couponRepo){
        $this->couponRepo = $couponRepo;
    }


    public function index(){
        $coupons = $this->couponRepo->index();
        return DataTables::of($coupons)
        ->addColumn('actions', fn($coupon) => view('dashboard.coupons._actions', ['coupon' => $coupon]))
        ->addColumn('status',fn($coupon)=>$coupon->status)
        ->addIndexColumn()
        ->rawColumns(['actions'])
        ->make(true);
    }

    public function store($data){
        $coupon = $this->couponRepo->store($data);
        return $coupon;
    }

    public function update($id,$data){
        $coupon = $this->couponRepo->update($id,$data);
        return $coupon;
    }

    public function destroy($id){
        $coupon = $this->couponRepo->destroy($id);
        return $coupon;
    }

    public function toggleStatus($id){
        $coupon = $this->couponRepo->toggleStatus($id);
        return $coupon;
    }
}

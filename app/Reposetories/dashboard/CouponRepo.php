<?php

namespace App\Reposetories\dashboard;

use App\Models\Dashboard\Coupon;

class CouponRepo
{
    /**
     * Create a new class instance.
     */

    public function getCoupon($id){
        $coupon = Coupon::find($id);
        return $coupon;
    }
    public function index(){
        $coupons = Coupon::get();
        return $coupons;
    }

    public function store($data){
        $coupon = Coupon::create($data);
        return $coupon;
    }

    public function update($id,$data){
        $coupon = $this->getCoupon($id);
        $coupon->update($data);
        return $coupon;
    }

    public function destroy($id){
        $coupon = $this->getCoupon($id);
        $coupon->delete();
        return $coupon;
    }

    public function toggleStatus($id){
        $coupon = $this->getCoupon($id);
        $coupon->is_active = !$coupon->is_active;
        $coupon->save();
        return $coupon;
    }
}

<?php

namespace App\Services\website;

use App\Models\Cart;
use App\Models\Dashboard\Coupon;
use App\Models\Dashboard\Order;
use DomainException;
use Illuminate\Support\Carbon;

class CouponService
{
    public function applyCoupon(int $userId, string $code): Coupon
    {
        $coupon = $this->validateCoupon($code);

        $cart = Cart::query()
            ->where('user_id', $userId)
            ->whereHas('items')
            ->first();

        if (! $cart) {
            throw new DomainException(__('website.coupon_cart_not_found'));
        }
        // check if there is already a coupon applied to the cart, if so, replace it with the new one
        $cart->coupon_id = $coupon->id;
        $cart->save();

        return $coupon;
    }

    public function removeCoupon(int $userId)
    {
        $cart = Cart::query()
            ->where('user_id', $userId)
            ->whereHas('items')
            ->first();

        if (! $cart) {
            throw new DomainException(__('website.coupon_cart_not_found'));
        }

        $cart->coupon_id = null;
        $cart->save();
    }

    public function validateCoupon(string $code): Coupon
    {
        $code = trim($code);

        if ($code === '') {
            throw new DomainException(__('website.coupon_code_required'));
        }

        $coupon = Coupon::query()
            ->where('code', $code)
            ->first();

        return $this->validateCouponData($coupon);
    }

    // تُستدعى داخل معاملة حفظ الطلب لقراءة الكوبون الحالي وقفل سجله حتى تنتهي المعاملة.
    public function validateCouponForOrder(int $couponId): Coupon
    {
        $coupon = Coupon::query()->lockForUpdate()->find($couponId);

        return $this->validateCouponData($coupon);
    }

    // الحجز لا يزيد الاستخدام المؤكد؛ وكل التغييرات تتراجع لو فشل حفظ الطلب.
    public function reserveCouponForOrder(int $couponId): Coupon
    {
        $coupon = $this->validateCouponForOrder($couponId);
        $coupon->increment('reserved_uses');

        return $coupon;
    }

    // الطلب وسجل الكوبون مقفولان داخل معاملة اعتماد الدفع.
    public function confirmCouponUsage(Order $order): void
    {
        if ($order->coupon_usage_status === 'used') {
            return;
        }

        if ($order->coupon_id === null) {
            if ($order->coupon_code !== null) {
                throw new DomainException('coupon_unavailable');
            }

            return;
        }

        $coupon = Coupon::query()->lockForUpdate()->find($order->coupon_id);
        if (! $coupon) {
            throw new DomainException('coupon_unavailable');
        }

        if ($order->coupon_usage_status === 'reserved') {
            // نحترم الخصم المحفوظ، حتى لو تغيرت صلاحية الكوبون بعد الحجز.
            if ($coupon->reserved_uses < 1) {
                throw new DomainException('coupon_unavailable');
            }
            $coupon->decrement('reserved_uses');
        } elseif ($coupon->limit !== null && $coupon->times_used + $coupon->reserved_uses >= $coupon->limit) {
            // لو نجحت محاولة لاحقة بعد تحرير الحجز، لا نستهلك حجز طلب آخر.
            throw new DomainException('coupon_unavailable');
        }

        $coupon->increment('times_used');
        $order->update(['coupon_usage_status' => 'used']);
    }

    public function releaseCouponReservation(Order $order): void
    {
        if ($order->coupon_usage_status !== 'reserved') {
            return;
        }

        $coupon = Coupon::query()->lockForUpdate()->find($order->coupon_id);
        if ($coupon) {
            if ($coupon->reserved_uses < 1) {
                throw new \RuntimeException('Coupon reservation counter is inconsistent.');
            }
            $coupon->decrement('reserved_uses');
        }

        $order->update(['coupon_usage_status' => 'released']);
    }

    // نفس شروط الصلاحية عند تطبيق الكوبون في السلة وعند إنشاء الطلب.
    private function validateCouponData(?Coupon $coupon): Coupon
    {
        if (! $coupon) {
            throw new DomainException(__('website.coupon_not_found'));
        }

        if (! $coupon->is_active) {
            throw new DomainException(__('website.coupon_inactive'));
        }

        $today = now()->startOfDay();
        $startsAt = Carbon::parse($coupon->start_date)->startOfDay();
        $expiresAt = Carbon::parse($coupon->expire_date)->endOfDay();

        if ($today->lt($startsAt)) {
            throw new DomainException(__('website.coupon_not_started'));
        }

        if (now()->gt($expiresAt)) {
            throw new DomainException(__('website.coupon_expired'));
        }

        if ($coupon->limit !== null && $coupon->times_used + $coupon->reserved_uses >= $coupon->limit) {
            throw new DomainException(__('website.coupon_usage_limit_reached'));
        }

        if (
            $coupon->discount_percentage === null
            || $coupon->discount_percentage < 1
            || $coupon->discount_percentage > 100
        ) {
            throw new DomainException(__('website.coupon_invalid_percentage'));
        }

        return $coupon;
    }

    public function calculateDiscount(float $subtotal, int $discountPercentage)
    {
        return $subtotal * ($discountPercentage / 100);
    }

    public function getAppliedCoupon(int $userId) : Coupon|null
    {
        $cart = Cart::query()
            ->where('user_id', $userId)
            ->whereHas('items')
            ->first();
        if ($cart && $cart->coupon_id) {
            return Coupon::where('id', $cart->coupon_id)->first();
        }
        return null;
    }
}   

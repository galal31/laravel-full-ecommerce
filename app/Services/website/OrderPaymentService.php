<?php

namespace App\Services\website;

use App\Models\Dashboard\Order;
use App\Models\Dashboard\Transaction;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class OrderPaymentService
{
    public function __construct(
        private MyFatoorahService $myFatoorahService,
        private OrderService $orderService,
        private CouponService $couponService,
    ) {}

    public function checkOrder(int $orderId): string
    {
        // 1. فحص حالة الطلب والوقت
        $order = Order::findOrFail($orderId);
        
        if (! $this->hasPendingReservation($order)) {
            return 'skipped';
        }
        if (! $order->payment_expires_at) {
            return 'unknown';
        }
        if ($order->payment_expires_at->isFuture()) {
            return 'waiting';
        }

        // 2. فحص المعاملة في قاعدة البيانات
        $transactions = Transaction::where('order_id', $order->id)->get();
        if ($transactions->count() !== 1) {
            return 'unknown';
        }

        $transaction = $transactions->first();
        if ($transaction->payment_method !== 'myfatoorah' || ! $transaction->invoice_id) {
            return 'unknown';
        }

        // لو فيه رقم عملية دفع مسجل والطلب لسه معلق -> نحوله للمراجعة
        if ($transaction->transaction_id !== null) {
            return DB::transaction(function () use ($order) {
                $currentOrder = Order::whereKey($order->id)->lockForUpdate()->firstOrFail();
                if (! $this->hasPendingReservation($currentOrder)) {
                    return 'skipped';
                }

                $currentOrder->update(['payment_review_reason' => 'payment_recorded_order_pending']);
                return 'review';
            });
        }

        // 3. سؤال بوابة الدفع (ماي فاتورة) عن حالة الفاتورة
        $invoiceStatus = $this->myFatoorahService->getInvoiceStatus($transaction->invoice_id);
        $result = $this->myFatoorahService->getInvoicePaymentResult($invoiceStatus);

        // لو الفاتورة طلعت مدفوعة عند ماي فاتورة -> نعتمد الدفع عندنا
        if ($result['status'] === 'paid') {
            $paymentStatus = $this->myFatoorahService->getPaymentStatus($result['payment_id']);
            $payment = $this->myFatoorahService->isPaymentSuccessful($paymentStatus);

            if (! $payment || $payment['invoice_id'] !== $transaction->invoice_id) {
                throw new RuntimeException('Verified payment does not match the invoice inquiry.');
            }

            $paidOrder = $this->orderService->completePayment($payment);
            return $paidOrder->payment_review_reason ? 'review' : 'paid';
        }

        if ($result['status'] !== 'unpaid') {
            return $result['status'];
        }

        // 4. الفاتورة غير مدفوعة ووقتها انتهى -> نفك حجز الكوبون
        if (! $transaction->invoice_expires_at || ! $transaction->invoice_expires_at->equalTo($order->payment_expires_at)) {
            return 'unknown';
        }

        return DB::transaction(function () use ($transaction, $order) {
            $currentTransaction = Transaction::whereKey($transaction->id)->lockForUpdate()->firstOrFail();
            $currentOrder = Order::whereKey($order->id)->lockForUpdate()->firstOrFail();

            // نتأكد فقط إن الطلب مدفعش في اللحظة اللي كنا بنكلم فيها ماي فاتورة
            if (! $this->hasPendingReservation($currentOrder) || $currentTransaction->transaction_id !== null) {
                return 'skipped';
            }

            $this->couponService->releaseCouponReservation($currentOrder);
            return 'released';
        });
    }

    private function hasPendingReservation(Order $order): bool
    {
        return $order->status === 'pending'
            && $order->coupon_usage_status === 'reserved'
            && $order->payment_review_reason === null;
    }
}
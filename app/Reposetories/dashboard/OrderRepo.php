<?php

namespace App\Reposetories\dashboard;

use App\Models\Dashboard\Order;

class OrderRepo
{
    public function getOrder($id)
    {
        return Order::with(['user:id,name,email', 'items', 'transactions'])->findOrFail($id);
    }

    public function getOrderCounts()
    {
        return [
            'completed' => Order::where('status', 'completed')->count(),
            'needs_review' => Order::whereNotNull('payment_review_reason')->count(),
        ];
    }

    public function getOrders(bool $needsReview = false)
    {
        // نعرض بيانات العميل المحفوظة وقت الطلب، ونبحث عن دليل دفع في كل معاملاته.
        $orders = Order::query()
            ->select(['id', 'user_name', 'user_phone', 'created_at', 'total_price', 'status', 'payment_review_reason'])
            ->withExists(['transactions as payment_recorded' => function ($query) {
                $query->whereNotNull('transaction_id')->where('transaction_id', '!=', '');
            }]);

        if ($needsReview) {
            $orders->whereNotNull('payment_review_reason');
        }

        return $orders;
    }

    public function MarkOrderDelivered($id)
    {
        Order::findOrFail($id);

        return Order::whereKey($id)->where('status', 'completed')->update(['status' => 'delivered']);
    }
}

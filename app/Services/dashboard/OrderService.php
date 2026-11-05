<?php

namespace App\Services\dashboard;

use App\Models\Dashboard\Order;
use App\Models\Dashboard\Transaction;
use App\Reposetories\dashboard\OrderRepo;
use App\Services\website\CouponService;
use App\Services\website\MyFatoorahService;
use App\Services\website\OrderService as WebsiteOrderService;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use RuntimeException;
use Yajra\DataTables\Facades\DataTables;

class OrderService
{
    public function __construct(private OrderRepo $orderRepo, private WebsiteOrderService $orderService, private MyFatoorahService $myFatoorahService, private CouponService $couponService) {}

    public function getOrder($id)
    {
        return $this->orderRepo->getOrder($id);
    }

    public function markDelivered($id)
    {
        if (! $this->orderRepo->MarkOrderDelivered($id)) {
            throw ValidationException::withMessages(['order' => __('orders.delivery_requires_completed')]);
        }
    }

    public function getOrderCounts()
    {
        return $this->orderRepo->getOrderCounts();
    }

    public function recheckOrderStock($orderId): void
    {
        try {
            DB::transaction(function () use ($orderId) {
                $order = Order::whereKey($orderId)->lockForUpdate()->firstOrFail();

                if ($order->status !== 'pending' || $order->payment_review_reason !== 'stock_unavailable') {
                    throw ValidationException::withMessages(['order' => __('orders.stock_recheck_requires_review')]);
                }

                $paymentRecorded = $order->transactions()
                    ->whereNotNull('transaction_id')->where('transaction_id', '!=', '')->exists();
                if (! $paymentRecorded) {
                    throw ValidationException::withMessages(['order' => __('orders.stock_recheck_requires_payment')]);
                }

                $this->orderService->decreaseOrderStock($order);
                $order->update(['payment_review_reason' => null, 'status' => 'completed']);
            });
        } catch (\DomainException $e) {
            throw ValidationException::withMessages(['order' => __('orders.stock_recheck_unavailable')]);
        }
    }

    public function cancelOrder($orderId): void
    {
        $order = Order::with('transactions')->findOrFail($orderId);
        if ($order->status !== 'pending') {
            throw ValidationException::withMessages(['order' => __('orders.cancellation_requires_pending')]);
        }

        if ($order->transactions->isEmpty()) {
            throw ValidationException::withMessages(['order' => __('orders.no_transactions')]);
        }

        $transaction = $order->transactions->first();
        if ($order->transactions->count() !== 1 || $transaction->payment_method !== 'myfatoorah' || ! $transaction->invoice_id) {
            throw ValidationException::withMessages(['order' => __('orders.cancellation_payment_unknown')]);
        }

        if (filled($transaction->transaction_id)) {
            throw ValidationException::withMessages(['order' => __('orders.cancellation_payment_detected')]);
        }

        try {
            $invoiceStatus = $this->myFatoorahService->getInvoiceStatus($transaction->invoice_id);
        } catch (ConnectionException|RuntimeException $e) {
            throw ValidationException::withMessages(['order' => __('orders.cancellation_payment_unknown')]);
        }

        $result = $this->myFatoorahService->getInvoicePaymentResult($invoiceStatus);
        $error = match ($result['status']) {
            'paid' => 'orders.cancellation_payment_detected',
            'processing' => 'orders.cancellation_payment_processing',
            'unpaid' => null,
            default => 'orders.cancellation_payment_unknown',
        };
        if ($error !== null) {
            throw ValidationException::withMessages(['order' => __($error)]);
        }

        DB::transaction(function () use ($order, $transaction) {
            // نفس ترتيب الأقفال المستخدم عند اعتماد الدفع.
            $currentTransaction = Transaction::whereKey($transaction->id)->lockForUpdate()->firstOrFail();
            $currentOrder = Order::whereKey($order->id)->lockForUpdate()->firstOrFail();

            if ($currentOrder->status !== 'pending') {
                throw ValidationException::withMessages(['order' => __('orders.cancellation_requires_pending')]);
            }

            if (filled($currentTransaction->transaction_id)) {
                throw ValidationException::withMessages(['order' => __('orders.cancellation_payment_detected')]);
            }

            if ($currentOrder->payment_expires_at?->isFuture()) {
                throw ValidationException::withMessages(['order' => __('orders.cancellation_requires_expired_payment')]);
            }

            if (! $currentOrder->payment_expires_at?->equalTo($order->payment_expires_at)
                || ! $currentTransaction->invoice_expires_at?->equalTo($currentOrder->payment_expires_at)) {
                throw ValidationException::withMessages(['order' => __('orders.cancellation_payment_unknown')]);
            }

            $currentOrder->update(['status' => 'cancelled']);
            $this->couponService->releaseCouponReservation($currentOrder);
        });
    }

    public function getOrdersForDataTable(bool $needsReview = false)
    {
        return DataTables::eloquent($this->orderRepo->getOrders($needsReview))
            ->editColumn('created_at', fn ($order) => $order->created_at->format('Y-m-d H:i:s'))
            ->editColumn('total_price', fn ($order) => number_format((float) $order->total_price, 2)
                .' '.config('services.myfatoorah.currency'))
            ->editColumn('status', fn ($order) => view('dashboard.orders._status', compact('order')))
            ->addColumn('payment_status', fn ($order) => $order->payment_recorded
                ? __('orders.payment_recorded') : __('orders.payment_unconfirmed'))
            ->addColumn('review', fn ($order) => view('dashboard.orders._review', compact('order')))
            ->addColumn('actions', fn ($order) => '<a class="btn btn-sm btn-outline-primary text-nowrap" href="'
                .e(route('dashboard.orders.show', $order->id)).'">'.e(__('orders.view_details')).'</a>')
            ->removeColumn('payment_recorded')
            ->removeColumn('payment_review_reason')
            ->rawColumns(['status', 'review', 'actions'])
            ->make(true);
    }
}

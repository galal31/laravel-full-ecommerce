<?php

namespace App\Console\Commands;

use App\Models\Dashboard\Order;
use App\Services\website\OrderPaymentService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Throwable;

class CheckCouponReservations extends Command
{
    protected $signature = 'orders:check-coupon-reservations {--order= : Check one order only}';

    protected $description = 'Check payments for expired pending orders and release unpaid coupon reservations';

    public function handle(OrderPaymentService $paymentService): int
    {
        $query = Order::query();
        $orderId = $this->option('order');
        if ($orderId !== null) {
            if (! Order::whereKey($orderId)->exists()) {
                $this->error('A valid existing order ID is required.');

                return self::FAILURE;
            }
            $query->whereKey($orderId);
        } else {
            $query->where('status', 'pending')
                ->whereNull('payment_review_reason')
                ->where('payment_expires_at', '<=', now());
        }

        $failed = false;
        $query->select('id')->chunkById(100, function ($orders) use ($paymentService, &$failed) {
            foreach ($orders as $order) {
                try {
                    $result = $paymentService->checkOrder($order->id);
                    $this->line("Order {$order->id}: {$result}");
                } catch (Throwable $exception) {
                    // نكمل باقي الطلبات، ولا نطبع بيانات بوابة الدفع أو المفاتيح.
                    $failed = true;
                    Log::warning('Order payment review failed.', [
                        'order_id' => $order->id, 'exception_type' => get_class($exception),
                    ]);
                    $this->warn("Order {$order->id}: check failed; order unchanged.");
                }
            }
        });

        return $failed ? self::FAILURE : self::SUCCESS;
    }
}

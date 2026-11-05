<?php

namespace App\Http\Controllers\dashboard;

use App\Http\Controllers\Controller;
use App\Services\dashboard\OrderService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function __construct(private OrderService $orderService) {}

    public function index(Request $request)
    {
        if ($request->ajax()) {
            return $this->orderService->getOrdersForDataTable($request->boolean('needs_review'));
        }

        $orderCounts = $this->orderService->getOrderCounts();

        return view('dashboard.orders.index', compact('orderCounts'));
    }

    public function show($id)
    {
        $order = $this->orderService->getOrder($id);

        return view('dashboard.orders.show', compact('order'));
    }

    public function markDelivered($id)
    {
        $this->orderService->markDelivered($id);

        return redirect()->route('dashboard.orders.show', $id)->with('success', __('orders.delivery_success'));
    }

    public function recheckOrderStock($id)
    {
        $this->orderService->recheckOrderStock($id);

        return redirect()->route('dashboard.orders.show', $id)->with('success', __('orders.stock_recheck_success'));
    }

    public function cancel($id)
    {
        $this->orderService->cancelOrder($id);

        return redirect()->route('dashboard.orders.show', $id)->with('success', __('orders.cancellation_success'));
    }
}

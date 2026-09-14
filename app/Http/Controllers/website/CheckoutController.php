<?php

namespace App\Http\Controllers\website;

use App\Http\Controllers\Controller;
use App\Services\website\CheckoutService;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    public function __construct(
        private CheckoutService $checkoutService
    ) {}

    public function index(): View
    {
        $userId = (int) auth()->id();
        $cartItems = $this->checkoutService->getCartItems($userId);

        return view('website.checkout.index', compact('cartItems'));
    }
}

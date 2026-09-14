<?php

namespace App\Services\website;

use App\Reposetories\website\CheckoutRepository;
use Illuminate\Support\Collection;

class CheckoutService
{
    public function __construct(
        private CheckoutRepository $checkoutRepository
    ) {}

    public function getCartItems(int $userId): Collection
    {
        return $this->checkoutRepository->getCartItems($userId);
    }
}

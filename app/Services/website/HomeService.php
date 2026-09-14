<?php

namespace App\Services\website;

use App\Reposetories\website\HomeRepository;
use Illuminate\Support\Facades\Cache;

class HomeService
{
    public function __construct(
        private HomeRepository $homeRepository
    ) {}

    public function getHomeData(): array
    {
        $userId = auth()->id();
        $discountEndingTodayProducts = $this->homeRepository
            ->getDiscountEndingTodayProducts(8, $userId);

        return [
            'sliders' => Cache::rememberForever(
                'sliders',
                fn () => $this->homeRepository->getSliders()
            ),
            'categories' => Cache::rememberForever(
                'home_categories',
                fn () => $this->homeRepository->getActiveParentCategories()
            ),
            'brands' => Cache::rememberForever(
                'home_brands',
                fn () => $this->homeRepository->getActiveBrands()
            ),
            'newestProducts' => $this->homeRepository->getNewestProducts(
                8,
                $userId,
                $discountEndingTodayProducts->modelKeys()
            ),
            'discountEndingTodayProducts' => $discountEndingTodayProducts,
        ];
    }
}

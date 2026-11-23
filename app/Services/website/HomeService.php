<?php

namespace App\Services\website;

use App\Reposetories\website\HomeRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;

class HomeService
{
    public function __construct(
        private HomeRepository $homeRepository
    ) {}

    public function getCategories(): Collection
    {
        return Cache::rememberForever(
            'home_categories',
            fn () => $this->homeRepository->getActiveParentCategories()
        );
    }

    public function getHomeData(string $searchQuery = ''): array
    {
        $userId = auth()->id();
        $discountEndingTodayProducts = $this->homeRepository
            ->getDiscountEndingTodayProducts(8, $userId);

        return [
            'searchQuery' => $searchQuery,
            'searchProducts' => $searchQuery !== ''
                ? $this->homeRepository->searchProducts($searchQuery, $userId)
                : collect(),
            'sliders' => Cache::rememberForever(
                'sliders',
                fn () => $this->homeRepository->getSliders()
            ),
            'categories' => $this->getCategories(),
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

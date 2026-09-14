<?php

namespace App\Services\website;

use App\Reposetories\website\CatalogRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class CatalogService
{
    public function __construct(
        private CatalogRepository $catalogRepository
    ) {}

    public function getProductsByBrand(string $slug): LengthAwarePaginator
    {
        return $this->catalogRepository->getProductsByBrandSlug($slug);
    }

    public function getProductsByCategory(string $slug): LengthAwarePaginator
    {
        return $this->catalogRepository->getProductsByCategorySlug($slug);
    }
}

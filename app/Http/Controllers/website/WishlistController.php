<?php

namespace App\Http\Controllers\website;

use App\Http\Controllers\Controller;
use App\Services\website\WishlistService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WishlistController extends Controller
{
    public function __construct(
        private WishlistService $wishlistService
    ) {}

    public function index(): View
    {
        return view('website.wishlist.index');
    }

    public function toggle(Request $request): JsonResponse
    {
        $data = $request->validate([
            'product_id' => ['required', 'integer'],
        ]);

        $added = $this->wishlistService->toggle(
            $request->user()->id,
            (int) $data['product_id']
        );

        return response()->json([
            'added' => $added,
            'message' => $added
                ? __('website.wishlist_added')
                : __('website.wishlist_removed'),
        ]);
    }
}

<?php

namespace App\Http\Controllers\website;

use App\Http\Controllers\Controller;
use App\Services\website\HomeService;
use Illuminate\View\View;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function __construct(
        private HomeService $homeService
    ) {}

    public function index(Request $request): View
    {
        $data = $request->validate(['q' => ['nullable', 'string', 'max:100']]);
        $searchQuery = trim($data['q'] ?? '');

        return view('website.home', $this->homeService->getHomeData($searchQuery));
    }
}

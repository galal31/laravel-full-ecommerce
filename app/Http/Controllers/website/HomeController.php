<?php

namespace App\Http\Controllers\website;

use App\Http\Controllers\Controller;
use App\Services\website\HomeService;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __construct(
        private HomeService $homeService
    ) {}

    public function index(): View
    {
        return view('website.home', $this->homeService->getHomeData());
    }
}

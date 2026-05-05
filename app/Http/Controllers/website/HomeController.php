<?php

namespace App\Http\Controllers\website;

use App\Http\Controllers\Controller;
use App\Models\Dashboard\Slider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class HomeController extends Controller
{
    public function index(){
        $sliders = Cache::rememberForever('sliders',function(){
            return Slider::all();
        });
        return view('website.home', compact('sliders'));
    }
}

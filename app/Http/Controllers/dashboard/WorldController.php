<?php

namespace App\Http\Controllers\dashboard;

use App\Http\Controllers\Controller;
use App\services\dashboard\WorldService;
use Illuminate\Http\Request;

class WorldController extends Controller
{
    protected $worldService;
    public function __construct(WorldService $worldService)
    {
        $this->worldService = $worldService;
    }

    public function getAllCountries()
    {
        $countries = $this->worldService->getAllCountries();
        return view('dashboard.world.countries', compact('countries'));
    }

    public function gettAllGovernorates($country_id = null)
    {
        $governorates = $this->worldService->gettAllGovernorates($country_id);
        return view('dashboard.world.governorates', compact('governorates'));
    }

    public function GovernorateChangePrice(Request $request,$id){
        $request->validate([
            'price' => 'required|numeric',
        ]);
        $price = $request->input('price');
        $new_price = $this->worldService->GovernorateChangePrice($price,$id);
        return response()->json([
            'success' => true,
            'price' => $new_price
        ]);
    }

    public function getAllCities($governorate_id = null)
    {
        $cities = $this->worldService->getAllCities($governorate_id);
        return view('dashboard.world.cities', compact('cities'));
    }

    // TOGGLE STATUS

    public function toggleStatus(Request $request, $id)
    {
        try {
            $type = $request->input('type');


            $newStatus = $this->worldService->toggleStatus($type, $id);


            return response()->json([
                'success' => true,
                'status' => $newStatus
            ]);

        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء تحديث الحالة: ' . $e->getMessage()
            ], 500);
        }
    }
}

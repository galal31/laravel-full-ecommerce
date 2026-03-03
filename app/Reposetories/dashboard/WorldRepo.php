<?php

namespace App\reposetories\dashboard;

use App\Models\Dashboard\City;
use App\Models\Dashboard\Country;
use App\Models\Dashboard\Governorate;
use App\Models\Dashboard\ShippingGovernorate;
use Symfony\Component\HttpFoundation\Request;

class WorldRepo
{
    /**
     * Create a new class instance.
     */
    public function getAllCountries()
    {
        $countries = Country::withCount(['governorates', 'users'])->get();
        return $countries;
    }

    public function gettAllGovernorates($country_id = null)
    {
        $governorates = Governorate::withCount(['cities', 'users'])->with(['country:id,name', 'shippingGovernorate'])
            ->when($country_id, function ($query) use ($country_id) {
                $query->where('country_id', $country_id);
            })->get();
        return $governorates;
    }
    public function GovernorateChangePrice($price, $id)
    {
        try {
            $governorate = ShippingGovernorate::where('governorate_id', $id)->first();
            $governorate->price = $price;
            $governorate->save();
            return $governorate->price;
        } catch (\Throwable $th) {
            return $th;
        }

    }

    public function getAllCities($governorate_id = null)
    {
        $cities = City::withCount(['users'])->with('governorate:id,name')
            ->when($governorate_id, function ($query) use ($governorate_id) {
                $query->where('governorate_id', $governorate_id);
            })->get();
        return $cities;
    }




    // toggle status

    public function toggleStatus($type, $id)
    {
        try {
            if ($type == 'country') {
                $country = Country::find($id);
                $country->status = !$country->status;
                $country->save();
                return $country->status;
            } elseif ($type == 'governorate') {
                $governorate = Governorate::find($id);
                $governorate->status = !$governorate->status;
                $governorate->save();
                return $governorate->status;
            } else {
                $city = City::find($id);
                $city->status = !$city->status;
                $city->save();
                return $city->status;
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}

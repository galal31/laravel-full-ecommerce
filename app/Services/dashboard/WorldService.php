<?php

namespace App\services\dashboard;

use App\reposetories\dashboard\WorldRepo;

class WorldService
{
    /**
     * Create a new class instance.
     */
    protected $worldRepo;
    public function __construct(WorldRepo $worldRepo)
    {
        $this->worldRepo = $worldRepo;
    }

    public function getAllCountries()
    {
        $countries = $this->worldRepo->getAllCountries();
        return $countries;
    }

    public function gettAllGovernorates($country_id = null)
    {
        $governorates = $this->worldRepo->gettAllGovernorates($country_id);
        return $governorates;
    }

    public function GovernorateChangePrice($price, $id)
    {
        $governorate = $this->worldRepo->GovernorateChangePrice($price, $id);
        return $governorate;
    }

    public function getAllCities($governorate_id = null)
    {
        $cities = $this->worldRepo->getAllCities($governorate_id);
        return $cities;
    }

    public function toggleStatus($type, $id)
    {
        $country = $this->worldRepo->toggleStatus($type, $id);
        return $country;
    }
}

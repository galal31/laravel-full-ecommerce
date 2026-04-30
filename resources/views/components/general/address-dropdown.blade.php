<?php

use Livewire\Component;

use App\Models\Dashboard\Country;
use App\Models\Dashboard\Governorate;
use App\Models\Dashboard\City;

new class extends Component {
    public $country_id = null;
    public $governorate_id = null;
    public $city_id = null;

    // إعادة ضبط الحقول عند تغيير الدولة
    public function updatedCountryId()
    {
        $this->governorate_id = null;
        $this->city_id = null;
    }

    // إعادة ضبط الحقول عند تغيير المحافظة
    public function updatedGovernorateId()
    {
        $this->city_id = null;
    }

    // جلب البيانات بناءً على الاختيارات
    public function with()
    {
        return [
            'countries' => Country::where('status', 1)->get(),
            'governorates' => $this->country_id ? Governorate::where('country_id', $this->country_id)->where('status', 1)->get() : [],
            'cities' => $this->governorate_id ? City::where('governorate_id', $this->governorate_id)->where('status', 1)->get() : [],
        ];
    }
}; ?>

<div class="row w-100 m-0">
    {{-- حقل الدولة --}}
    <div class="col-md-4 form-group pl-0">
        <label>{{ __('users.country') }}</label>
        <select name="country_id" wire:model.live="country_id" class="form-control" required>
            <option value="">{{ __('users.select_country') }}</option>
            @foreach ($countries as $country)
                <option value="{{ $country->id }}">{{ $country->getTranslation('name', app()->getLocale()) }}</option>
            @endforeach
        </select>
    </div>

    {{-- حقل المحافظة --}}
    <div class="col-md-4 form-group">
        <label>{{ __('users.governorate') }}</label>
        <select name="governorate_id" wire:model.live="governorate_id" class="form-control" required>
            <option value="">{{ __('users.select_governorate') }}</option>
            @foreach ($governorates as $governorate)
                <option value="{{ $governorate->id }}">{{ $governorate->getTranslation('name', app()->getLocale()) }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-md-4 form-group pr-0">
        <label>{{ __('users.city') }}</label>
        <select name="city_id" wire:model.live="city_id" class="form-control" required>
            <option value="">{{ __('users.select_city') }}</option>
            @foreach ($cities as $city)
                <option value="{{ $city->id }}">{{ $city->getTranslation('name', app()->getLocale()) }}</option>
            @endforeach
        </select>
    </div>
</div>

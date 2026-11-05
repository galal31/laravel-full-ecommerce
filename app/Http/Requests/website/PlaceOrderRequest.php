<?php

namespace App\Http\Requests\website;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PlaceOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_name' => ['required', 'string', 'max:255'],
            'user_email' => ['required', 'email', 'max:255'],
            'user_phone' => ['required', 'string', 'max:30'],
            'street' => ['required', 'string', 'max:255'],
            'note' => ['nullable', 'string', 'max:2000'],
            'country_id' => [
                'required',
                'integer',
                Rule::exists('countries', 'id')->where('status', 1),
            ],
            'governorate_id' => [
                'required',
                'integer',
                Rule::exists('governorates', 'id')->where(fn ($query) => $query
                    ->where('country_id', $this->input('country_id'))
                    ->where('status', 1)),
            ],
            'city_id' => [
                'required',
                'integer',
                Rule::exists('cities', 'id')->where(fn ($query) => $query
                    ->where('governorate_id', $this->input('governorate_id'))
                    ->where('status', 1)),
            ],
        ];
    }

    public function attributes(): array
    {
        return [
            'user_name' => __('website.checkout_full_name'),
            'user_email' => __('website.checkout_email'),
            'user_phone' => __('website.checkout_phone'),
            'street' => __('website.checkout_street'),
            'note' => __('website.checkout_note'),
            'country_id' => __('users.country'),
            'governorate_id' => __('users.governorate'),
            'city_id' => __('users.city'),
        ];
    }
}

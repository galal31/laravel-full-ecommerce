<?php

namespace App\Http\Requests\Dashboard;

use App\Models\Dashboard\City;
use App\Models\Dashboard\Governorate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {


        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'country_id' => 'required|exists:countries,id',
            'governorate_id' => 'required',
            Rule::exists('governorates','id')->where(function ($query) {
                $query->where('country_id', $this->input('country_id'));
            }),
            'city_id' => 'required',
            Rule::exists('cities','id')->where(function ($query) {
                $query->where('governorate_id', $this->input('governorate_id'));
            }),
            'is_active' => 'required|in:0,1',
        ];
    }
}

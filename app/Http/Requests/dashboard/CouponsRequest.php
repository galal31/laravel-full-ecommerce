<?php

namespace App\Http\Requests\dashboard;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CouponsRequest extends FormRequest
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
        $id = $this->route('id');
        return [
            //
            'code'=>['required','max:255',Rule::unique('coupons','code')->ignore($id)],
            'discount_percentage'=>['required','numeric','min:1','max:100'],
            'start_date'=>['required','date','before:expire_date'],
            'expire_date'=>['required','date','after:start_date'],
            'limit'=>['required','numeric','min:1'],
        ];
    }
}

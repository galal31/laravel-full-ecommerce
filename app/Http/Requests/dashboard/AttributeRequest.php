<?php

namespace App\Http\Requests\dashboard;

use Illuminate\Foundation\Http\FormRequest;

class AttributeRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $id = $this->route('attribute') ? $this->route('attribute') : null;

        return [
            'name.ar' => 'required|string|max:255',
            'name.en' => 'required|string|max:255',
            'values' => 'required|array|min:1',
            'values.*.ar' => 'required|string|max:255',
            'values.*.en' => 'required|string|max:255',
        ];
    }

    public function messages()
    {
        return [
            'values.required' => __('يجب إضافة قيمة واحدة على الأقل للسمة.'),
            'values.*.ar.required' => __('جميع حقول القيم باللغة العربية مطلوبة.'),
            'values.*.en.required' => __('جميع حقول القيم باللغة الإنجليزية مطلوبة.'),
        ];
    }
}
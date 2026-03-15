<?php

namespace App\Http\Requests\dashboard;

use Illuminate\Foundation\Http\FormRequest;

class FaqRequest extends FormRequest
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
            // التأكد إن الحقل الأساسي عبارة عن مصفوفة ومطلوب
            'question' => 'required|array',
            'answer' => 'required|array',

            // تحديد اللغات بشكل صريح
            'question.ar' => 'required|string|max:255',
            'question.en' => 'required|string|max:255',
            
            'answer.ar' => 'required|string',
            'answer.en' => 'required|string',
        ];
    }
}

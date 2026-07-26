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
            'question' => ['required', 'array'],
            'question.ar' => ['required', 'string', 'max:255'],
            'question.en' => ['required', 'string', 'max:255'],
            'answer' => ['required', 'array'],
            'answer.ar' => ['required', 'string'],
            'answer.en' => ['required', 'string'],
        ];
    }

    public function attributes(): array
    {
        return [
            'question.ar' => __('faqs.question_ar'),
            'question.en' => __('faqs.question_en'),
            'answer.ar' => __('faqs.answer_ar'),
            'answer.en' => __('faqs.answer_en'),
        ];
    }
}

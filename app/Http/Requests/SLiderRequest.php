<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SliderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'note' => ['required', 'array'],
            'note.ar' => ['required', 'string', 'min:3', 'max:35'],
            'note.en' => ['required', 'string', 'min:3', 'max:35'],

            'file_name' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];
    }
}

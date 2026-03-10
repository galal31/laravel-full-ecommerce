<?php

namespace App\Http\Requests\dashboard;

use App\Models\Dashboard\Category;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateCategoryRequest extends FormRequest
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
        $cat_id = $this->route('category');
        // عشان لو ده route model binding
        if (is_object($cat_id)) {
            $cat_id = $cat_id->id;
        }

        $rules = [
            'name.en' => 'required|string|max:255',
            'name.ar' => 'required|string|max:255',
            'slug' => ['required', 'string', 'max:255', Rule::unique(Category::class, 'slug')->ignore($cat_id)],
            'parent_id' => ['nullable',Rule::exists('categories', 'id')->where(function($query) use ($cat_id) {
                $query->whereNull('parent_id');
                if($cat_id) {
                    $query->where('id', '!=', $cat_id);
                }
            })],
        ];

        // if($cat_id) {
        //     $rules['parent_id'][] = ['nullable',Rule::exists('categories', 'id')->where('id', '!=', $cat_id)];
        // }

        return $rules;
    }
}

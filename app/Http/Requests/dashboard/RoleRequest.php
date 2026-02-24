<?php

namespace App\Http\Requests\dashboard;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $valid_permissions = array_keys(config('permissions'));
        
        return [
            'name' => ['required', 'max:255'],
            'permissions' => ['required', 'array', 'min:1'],
            'permissions.*' => ['required', Rule::in($valid_permissions)],
        ];
    }
}
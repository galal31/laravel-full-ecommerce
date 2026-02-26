<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

use function PHPUnit\Framework\isObject;

class AdminRequest extends FormRequest
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
        $id = $this->route('admin');
        if($this->method() == 'POST'){
            return [
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:admins,email',
                'password' => 'required|string|min:3',
                'role_id' => 'required|exists:roles,id',
            ];
        }else{
            return [
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:admins,email,'.$id,
                'role_id' => 'required|exists:roles,id',
                'password' => 'nullable|string|min:3',
            ];
        }
    }
}

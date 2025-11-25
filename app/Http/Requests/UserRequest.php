<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $rules = [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'is_admin' => 'nullable|boolean',
            'email' => [
                'required',
                'string',
                'email',
                Rule::unique('users', 'email'),
            ],
            'password' => 'required|string|min:8|confirmed',
            'role_id' => 'nullable|exists:roles,id', // Validate role_id if provided
        ];

        if (in_array($this->method(), ['PUT', 'PATCH'])) {
            $user = $this->route()->parameter('user');

            // Modify email validation to ignore the current user's email
            $rules['email'] = [
                'required',
                'string',
                'email',
                Rule::unique('users', 'email')->ignore($user->id),
            ];

            // Remove password validation on update
            unset($rules['password']);
        }

        return $rules;
    }
}
